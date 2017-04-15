<?php
require_once __DIR__ . '/restlib.php';
require_once __DIR__ . '/../lib/users.php';
jsonCheckAuth ();

if ($_SERVER ['REQUEST_METHOD'] != 'POST') {
	exit ();
}

header ( 'Content-type: application/json' );
$pdo = db_open ();

$json = file_get_contents ( 'php://input' );
$body = json_decode ( $json, true );

$result = array ();
$result ['result'] = "failed";
http_response_code ( 400 );

$destination = $body ['destination'];
$mailsubject = $body ['subject'];
$mailbody = $body ['body'];

if (empty ( $mailsubject )) {
	$result ['text'] = 'mail subject cannot be empty';
} else if (empty ( $mailbody )) {
	$result ['text'] = 'mail body cannot be empty';
} else if ($destination == 'user') {
	if (! array_key_exists ( 'userid', $body )) {
		$result ['text'] = 'userid not specified';
		echo json_encode ( $result );
		exit ();
	}
	$userid = $body ['userid'];
	if (authUser ()->username == $userid) {
		$result ['text'] = 'users cannot write mails to themselves: ' . $userid;
		echo json_encode ( $result );
		exit ();
	}
	$touser = getUserByName ( $pdo, $userid );
	if (! $touser) {
		$result ['text'] = 'user not found: ' . $userid;
		echo json_encode ( $result );
		exit ();
	}
	// TODO: check if user is active/verified or maybe add status='verified' to getUserById-select statement
	sendmail ( $touser ['email'], 'Nachricht von ' . authUser ()->username . ' mit Betreff: ' . $mailsubject, $mailbody );
	$result ['text'] = 'mail sent to ' . $touser ['username'];
	$result ['result'] = 'ok';
	http_response_code ( 200 );
} else if ($destination == 'tour') {
	if (! array_key_exists ( 'tourid', $body )) {
		$result ['text'] = 'tourid not specified';
		echo json_encode ( $result );
		exit ();
	}
	
	$tourid = $body ['tourid'];
	$tour = getDBTourById ( $pdo, $tourid );
	if (! $tour) {
		$result ['text'] = 'tour not found: ' . $tourid;
		echo json_encode ( $result );
		exit ();
	}
	if (($tour->canceled) || ($tour->startDateTime < new DateTime ())) {
		// security: this never should happen
		$result ['text'] = 'you cannot cancel and old or cancelled tour';
		echo json_encode ( $result );
		exit ();
	}
	sendmail ( $tour->guide->email, 'Nachricht von ' . authUser ()->username . ' mit Betreff: ' . $mailsubject, $mailbody );
	$users = getAttendees ( $pdo, $tour->id );
	foreach ( $users as $user ) {
		sendmail ( $user ['email'], 'Nachricht von ' . authUser ()->username . ' mit Betreff: ' . $mailsubject, $mailbody );
	}
	
	$tourDescription = $tour->sport->sportsubname . '-Tour am ' . $tour->startDateTime->format ( 'd.m.Y H:i' );
	$result ['text'] = 'Mail sent to guide of tour ' . $tourDescription;
	$result ['result'] = 'ok';
	http_response_code ( 200 );
}
echo json_encode ( $result );

?>