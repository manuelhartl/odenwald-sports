<?php
require_once 'config/settings.php';
function sendmail($to, $subject, $body) {
	sendmail_local ( $to, $subject, $body );
}
function sendmail_local($to, $subject, $body) {
	global $config;
	$from = $config ['emailFrom'];
	// $headers = 'From: $from\r\n' . 'Reply-To: $from\r\n' . 'Content-type: text/html\n' . 'X-Mailer: PHP/' . phpversion ();
	$headers = 'From: ' . $from . "\r\n" . //
'Content-type: text/html; charset=UTF-8' . "\r\n" . //
'X-Mailer: PHP/' . phpversion () . "\r\n"; //
	
	mail ( $to, $subject, $body, $headers );
}
?>