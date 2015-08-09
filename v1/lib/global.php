<?php
require_once __DIR__ . '/../config/settings.php';
require_once __DIR__ . '/db_users.php';

$cookielifetime = 60 * 60 * 24; // 24h
if ($_SERVER ['SERVER_NAME'] == '127.0.0.1') {
	session_set_cookie_params ( $cookielifetime, '/' . explode ( '/', $_SERVER ['REQUEST_URI'] . '/' ) [1], '127.0.0.1', false );
	session_start ();
	setcookie ( session_name (), session_id (), time () + $cookielifetime, '/' . explode ( '/', $_SERVER ['REQUEST_URI'] . '/' ) [1], '127.0.0.1', false );
} else {
	session_set_cookie_params ( $cookielifetime, '/' . explode ( '/', $_SERVER ['REQUEST_URI'] . '/' ) [1], $_SERVER ['SERVER_NAME'] );
	session_start ();
	// reset cookie parameters because of a quirk in php - if you dont do this the cookie lifetime wont be updated on subsequent page requests
	setcookie ( session_name (), session_id (), time () + $cookielifetime, '/' . explode ( '/', $_SERVER ['REQUEST_URI'] . '/' ) [1], '.' . $_SERVER ['SERVER_NAME'] );
}
date_default_timezone_set ( "Europe/Berlin" );
class SessionInfo {
	
	/**
	 *
	 * @param string $index
	 * @param string $default
	 * @return string
	 */
	public static function getInVa($index, $default = null) {
		global $input;
		return isset ( $input [$index] ) ? $input [$index] : ($default == null) ? "" : $default;
	}
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
	$_SESSION ['userObj'] = getDBUser ( $user );
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