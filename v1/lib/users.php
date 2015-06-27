<?php
require_once 'lib/mail.php';
function sendActivationMail($username, $token, $email) {
	$text = '<a href="' . getUrlPrefix () . '/activate.php?token=' . urlencode ( $token ) . '">Aktivierung</a>';
	$subject = 'Aktivierung des Touren-Kontos von ' . $username;
	sendmail ( $email, $subject, $text );
}

?>
