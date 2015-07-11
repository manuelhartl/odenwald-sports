<?php
require_once __DIR__ . '/mail.php';
function sendActivationMail($username, $token, $email) {
	$text = '<html><body><a href="' . getUrlPrefix () . '/activate.php?token=' . urlencode ( $token ) . '">Aktivierung</a></body><html>';
	$subject = 'Aktivierung des Touren-Kontos von ' . $username;
	sendmail ( $email, $subject, $text );
}
function sendPasswordresetMail($username, $token, $email) {
	$text = '<html>body><a href="' . getUrlPrefix () . '/passwordreset.php?token=' . urlencode ( $token ) . '">Password reset</a></body><html>';
	$subject = 'Passwortreset angefoedert des Touren-Kontos von ' . $username;
	sendmail ( $email, $subject, $text );
}
?>
