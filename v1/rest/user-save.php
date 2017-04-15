<?php
require_once __DIR__ . '/restlib.php';
jsonCheckAuth ();

if ($_SERVER ['REQUEST_METHOD'] != 'POST') {
	exit ();
}

header ( 'Content-type: application/json' );
$pdo = db_open ();

$json = file_get_contents ( 'php://input' );
$body = json_decode ( $json, true );

$users = $body ['users'];
$user_list = array ();
foreach ( $users as $user ) {
	// check if tour already exists
	// is there an ID ?
	$isNew = true;
	if (array_key_exists ( 'id', $user )) {
		if ($user ['id'] != authUser ()->id) {
			http_response_code ( 401 );
			exit ();
		}
		$dbuser = getUserById ( $pdo, $user ['id'] );
		if (! $dbuser) {
			http_response_code ( 400 );
			exit ();
		}
		$isNew = false;
	} else {
		if ($user ['username'] != authUser ()->username) {
			http_response_code ( 401 );
			exit ();
		}
		$dbuser = getUserByName ( $pdo, authUser ()->username );
		if (! $dbuser) {
			$dbuser = new DBTour ();
			$isNew = true;
		} else {
			$isNew = false;
		}
	}
	
	if ($isNew) {
		http_response_code ( 400 );
		exit ();
	}
	$userextra = new DBUserExtra ();
	$userextra->id = authUser ()->id;
	$userextra->birtdate = DateTime::createFromFormat ( DateTime::ISO8601, $user ['birthdate'] );
	$userextra->realname = $user ['realname'];
	$userextra->address = $user ['address'];
	$userextra->address_lat = $user ['address_gps']['lat'];
	$userextra->address_long = $user ['address_gps']['long'];
	if (! userExtraExists ( $pdo, $userextra->id )) {
		addUserExtra ( $pdo, $userextra->id );
	}
	$userextra->mailing = $user['mailing']=='true';
	$userextra->phone = $user['phone'];
	updateUserExtra ( $pdo, $userextra );
	
	$stmt = getUserStmt ( $pdo, $userextra->id );
	$row = $stmt->fetch ( PDO::FETCH_ASSOC );
	$user_list [] = row2user ( $pdo, $row );
}
$json = array ();
$json ['users'] = $user_list;
echo json_encode ( $json );

?>