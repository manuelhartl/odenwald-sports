<?php
function htmlHeader($redirect, $to = 'index.php') {
	?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="refresh"
	content="<?php echo $redirect.'; url='.$to ?>">
<title>Touren</title>
</head>
<body>
<?php
}

require_once 'lib/db_users.php';

if (array_key_exists ( 'token', $_GET )) {
	$token = $_GET ['token'];
	$pdo = db_open ();
	$redirectInSeconds;
	if (activate ( $pdo, $token )) {
		$redirectInSeconds = 2;
		echo 'aktiviert ';
	} else {
		$redirectInSeconds = 180;
		echo 'Aktivierung fehlgeschlagen - Bitte Administrator an wenden (webmaster)';
	}
	$to = 'ko/pages';
	htmlHeader ( $redirectInSeconds, $to );
	echo '- Du wirst in ' . $redirectInSeconds . ' Sekunden an die Anmeldung umgelenkt (oder direkt zu: <a href="' . $to . '">Login</a>)';
}
?>
</body>
</html>
