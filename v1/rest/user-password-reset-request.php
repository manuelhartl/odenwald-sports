<?php
require_once __DIR__ . '/restlib.php';
require_once __DIR__ . '/../lib/users.php';

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

$email = $body ['email'];
$username = $body ['username'];

$userrow = getUserByName ( $pdo, $username );
$userid = $userrow ['id'];
if (! validateEmail ( $email )) {
	$result ['text'] = "no valid email adress";
} else if ($userrow ['email'] != $email) {
	$result ['text'] = "email does not match stored one";
} else {
	$token = bin2hex ( openssl_random_pseudo_bytes ( 16 ) );
	addPasswordresetToken ( $pdo, $userid, $token );
	sendPasswordresetMail ( $username, $token, $email );
	$result ['result'] = "ok";
	http_response_code ( 200 );
}

echo json_encode ( $result );

?>