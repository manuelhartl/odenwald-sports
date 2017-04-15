<?php
require_once __DIR__ . '/restlib.php';

if ($_SERVER ['REQUEST_METHOD'] != 'POST') {
	exit ();
}

header ( 'Content-type: application/json' );
$pdo = db_open ();

$json = file_get_contents ( 'php://input' );
$body = json_decode ( $json, true );

$token = $body ['token'];
$newpassword = $body ['newpassword'];
$result = array ();
$result ['result'] = "failed";
http_response_code ( 400 );
if (! validatePassword ( $newpassword )) {
	$result ['text'] = "new password doesnt meet requirements";
} else {
	$hashedpassword = password_hash ( $newpassword, PASSWORD_BCRYPT );
	if (resetPassword ( $pdo, $token, $hashedpassword )) {
		$result ['result'] = "ok";
		http_response_code ( 200 );
	} else {
		$result ['text'] = "token invalid";
	}
}

echo json_encode ( $result );

?>