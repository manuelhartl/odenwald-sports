<?php
require_once __DIR__ . '/../config/settings.php';
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
	$user = getUserByName ( $pdo, $username );
	$_SESSION ['userObj'] = getUserObject ( $user );
}
function authUser() {
	return $_SESSION ['userObj'];
}
// got from stackoverflow comment
function get_current_url($strip = true) {
	// filter function
	static $filter;
	$filter = function ($input) use($strip) {
		$input = str_ireplace ( array (
				"\0",
				'%00',
				"\x0a",
				'%0a',
				"\x1a",
				'%1a' 
		), '', urldecode ( $input ) );
		if ($strip) {
			$input = strip_tags ( $input );
		}
		// or whatever encoding you use instead of utf-8
		$input = htmlentities ( $input, ENT_QUOTES, 'utf-8' );
		return trim ( $input );
	};
	
	return 'http' . (($_SERVER ['SERVER_PORT'] == '443') ? 's' : '') . '://' . $_SERVER ['SERVER_NAME'] . $filter ( $_SERVER ['REQUEST_URI'] );
}
function getUrlPrefix() {
	return dirname ( get_current_url () );
}
function validatePassword($input) {
	// TODO: check for character classes
	return strlen ( $input ) >= 6;
}
function validateEmail($input) {
	return filter_var ( $input, FILTER_VALIDATE_EMAIL );
}

?>