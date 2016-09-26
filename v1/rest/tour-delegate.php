<?php
require_once __DIR__ . '/restlib.php';
jsonCheckAuth ();

if ($_SERVER ['REQUEST_METHOD'] != 'POST') {
	exit ();
}

header ( 'Content-type: application/json' );
http_response_code ( 500 );
$pdo = db_open ();

$body = json_decode ( file_get_contents ( 'php://input' ), true );
$tourid = $body ['id'];
$guideid = $body ['guideid'];

$json = array ();

$tour = getDBTourById ( $pdo, $tourid );
if (! $tour) {
	$json ['error'] ['text'] = 'tourid is unknown';
	http_response_code ( 400 );
} else {
	if ($tour->guide->id == $guideid) {
		http_response_code ( 200 );
	} else if ($tour->startDateTime >= new DateTime ()) {
		$attendees = getAttendees ( $pdo, $tour->id );
		foreach($attendees as $attendee) {
			echo $attendee['id']."-".$guideid;
			if ($attendee['id'] == $guideid) {
				echo "hit";
				if (tourChangeGuide ( $pdo, $tourid, $guideid )) {
					http_response_code ( 200 );
					tourLeave($pdo, $guideid, $tourid);
					tourJoin($pdo, $tour->guide->id, $tourid);
					break;
				}
			}
		}
		$json ['error'] ['text'] = 'new guide id is not an attendee';
		http_response_code ( 400 );
	} else {
		$json ['error'] ['text'] = 'tour too old';
		http_response_code ( 400 );
	}
}

$stmt = getTourStmt ( $pdo, $tourid );
$row = $stmt->fetch ( PDO::FETCH_ASSOC );
$json ['tour'] = row2tour ( $pdo, $row );
echo json_encode ( $json );

?>