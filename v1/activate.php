<?php
require_once 'lib/db_users.php';

if (array_key_exists ( 'token', $_GET )) {
	$token = $_GET ['token'];
	$pdo = db_open ();
	if (activate ( $pdo, $token )) {
		echo "activated, now login";
		die ();
	}
}
echo "activation failed";
?>