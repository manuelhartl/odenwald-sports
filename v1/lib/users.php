<?php
require_once __DIR__ . '/mail.php';
function sendActivationMail($username, $token, $email) {
	$text = '<html><body>Vielen Dank, dass Du Dich auf der Seite Odenwald Sport Tours registriert hast.' . //
' Zum Abschluss Deiner Registrierung klicke bitte auf folgenden <a href="' . getUrlPrefix () . '/activate.php?token=' . urlencode ( $token ) . '">Link</a>.' . //
' Danach &ouml;ffnet sich die OST-Seite und Du kannst Dich mit Deinem gew&auml;hlten User und Passwort anmelden. Viel Spa&szlig;!</body><html>';
	$subject = 'Aktivierung des Touren-Kontos von ' . $username;
	sendmail ( $email, $subject, $text );
}
function sendPasswordresetMail($username, $token, $email) {
	$text = '<html>body><a href="' . getUrlPrefix () . '/passwordreset.php?token=' . urlencode ( $token ) . '">Password reset</a></body><html>';
	$subject = 'Passwortreset angefoedert des Touren-Kontos von ' . $username;
	sendmail ( $email, $subject, $text );
}
?>
