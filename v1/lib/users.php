<?php
require_once 'lib/mail.php';
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
function sendActivationMail($username, $token, $email) {
	$text = '<a href="'.dirname ( get_current_url () ) . '/activate.php?token=' . urlencode ( $token ).'">Aktivierung</a>';
	$subject = 'Aktivierung des Touren-Kontos von '.$username;
	sendmail ( $email, $subject, $text );
}

?>
