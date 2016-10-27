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
$password = $body ['password'];
// validate
if (! preg_match ( "/^([\w])([_\-\w]+)$/", $username )) {
	$result ['text'] = 'Der Benutzername darf nur A-Z und 0-9, sowie "_" und "-" ab der zweiten Stelle enthalten';
} else if (strlen ( $username ) < 2) {
	$result ['text'] = 'Der Benutzername mu&szlig; mindestens aus 2 Zeichen bestehen';
} else if (strlen ( $username ) > 20) {
	$result ['text'] = 'Der Benutzername darf nur 20 Zeichen lang sein';
} else if (! validatePassword ( $password )) {
	$result ['text'] = 'Das Passwort mu&szlig; mindestens 6 Zeichen lang sein';
} else if (! validateEmail ( $email )) {
	$result ['text'] = 'Die E-Mail Adresse ist nicht g&uuml;ltig';
} else 
// check if email already registered
if (userExists ( $pdo, $username )) {
	$result ['text'] = $username . ' ist schon registriert';
} else { 
// check if name is already registered
// if (emailExists ( $pdo, $email )) {
// 	$result ['text'] = $email . ' is schon registriert';
// } else {
	// register
	$hashedPassword = password_hash ( $password, PASSWORD_BCRYPT );
	$token = bin2hex ( openssl_random_pseudo_bytes ( 16 ) );
	$userId = registerUser ( $pdo, $username, $hashedPassword, $email );
	if (! $userId) {
		$result ['text'] = "could not store user";
	} else {
		addActivationToken ( $pdo, $userId, $token );
		sendActivationMail ( $username, $token, $email );
		$result ['result'] = "ok";
		http_response_code ( 200 );
	}
}

echo json_encode ( $result );

?>