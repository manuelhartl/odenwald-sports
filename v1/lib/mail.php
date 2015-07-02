<?php
require_once 'config/settings.php';
function sendmail($to, $subject, $body) {
	sendmail_local ( $to, $subject, $body );
}
function sendmail_local($to, $subject, $body) {
	global $config;
	$from = $config ['emailFrom'];
	// $headers = 'From: ' . $from . "\r\n" . //
	// 'MIME-Version: 1.0' . "\r\n" . //
	// 'Content-type: text/html; charset=UTF-8' . "\r\n" . //
	// 'X-Mailer: PHP/' . phpversion () . "\r\n"; //
	
	$from = $config ['emailFrom'];
	$headers = 'From: ' . $from . "\n" . //
'MIME-Version: 1.0' . "\n" . //
'Content-type: text/html; charset=UTF-8' . "\n" . //
'X-Mailer: PHP/' . phpversion () . "\n"; //
	
	mail ( $to, $subject, $body, $headers );
}
?>