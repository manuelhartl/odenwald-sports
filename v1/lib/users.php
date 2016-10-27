<?php
require_once __DIR__ . '/mail.php';
function sendActivationMail($username, $token, $email) {
	$text = '<html><body>Vielen Dank, dass Du Dich auf der Seite sport2gether registriert hast.' . //
' Zum Abschluss Deiner Registrierung klicke bitte auf folgenden <a href="' . getUrlPrefix () . '/activate.php?token=' . urlencode ( $token ) . '">Link</a>.' . //
' Danach &ouml;ffnet sich die sport2gether-Seite und Du kannst Dich mit Deinem gew&auml;hlten User und Passwort anmelden. Viel Spa&szlig;!</body><html>';
	$subject = 'Aktivierung des Touren-Kontos von ' . $username;
	sendmail ( $email, $subject, $text, false );
}
function sendPasswordresetMail($username, $token, $email, $forwardTo = '/passwordreset.php') {
	$text = '<html>body><a href="' . getUrlPrefix () . $forwardTo . '?token=' . urlencode ( $token ) . '">Password reset</a></body><html>';
	$subject = 'Passwortreset angefodert f&uuml;r Touren-Konto von ' . $username;
	sendmail ( $email, $subject, $text, false );
}
function createUserProfilLink($user, $style, $tooltip = "Profil anzeigen") {
	// var_dump($user);
	return ('<a ' . $style . ' href="?action=user-view&userid=' . $user->id . '">' . '<span class="flex" title="' . $tooltip . '">' . $user->username . '</span>' . '</a>');
}
function createUserInfo(DBuser $user, DBUserextra $userextra) {
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
	
	return (htmlspecialchars ( $userinfo ));
}
?>
