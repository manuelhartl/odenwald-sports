<?php
require_once 'config/settings.php';
session_start ();
date_default_timezone_set ( "Europe/Berlin" );
class User {
	public $id;
	public $username;
	public $email;
}
function hasAuth() {
	if (! isset ( $_SESSION )) {
		return false;
	}
	return array_key_exists ( 'userObj', $_SESSION );
}
function requireAuth() {
	if (! hasAuth ()) {
		echo "Du musst dich einloggen";
		exit ();
	}
}
function login($pdo, $username) {
	$user = getUserByName( $pdo, $username );
	$_SESSION ['userObj'] = getUserObject ( $user );
}
function authUser() {
	return $_SESSION ['userObj'];
}

?>