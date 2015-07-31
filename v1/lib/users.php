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
function createUserProfilLink($user, $tooltip = "Profil anzeigen") {
	// var_dump($user);
	return ('<a style = "display: block;" href="?action=user-view&userid=' . $user->id . '">' . '<span class="flex" title="' . $tooltip . '">' . $user->username . '</span>' . '</a>');
}
function createUserInfo($user, $userextra) {
	$userinfo = "";
	if (isset ( $userextra )) {
		
		// ignore address
		// if (isset ( $userextra->address ) && strlen ( $userextra->address ) > 0) {
		// $userinfo = "Adresse: " . $userextra->address;
		// }
		if (isset ( $userextra->phone ) && strlen ( $userextra->phone ) > 0) {
			$userinfo = (strlen ( $userinfo ) > 0 ? $userinfo . ", " : "") . "Telefon: " . $userextra->phone;
		}
	}
	if (strlen ( $userinfo ) > 0) {
		$userinfo = $user->username . ": " . $userinfo;
	} else {
		$userinfo = $user->username;
	}
	
	return ($userinfo);
}
function getVal($value, $defaultValue) {
	return (isset ( $value ) ? $value : $defaultValue);
}
function isValidDate($value, $defaultValue) {
	if (is_a ( $value, 'DateTime' )) {
		return ($value);
	}
	return (new DateTime ( $defaultValue ));
}
?>
