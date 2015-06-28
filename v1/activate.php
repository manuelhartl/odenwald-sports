<?php
function htmlHeader($redirect) {
	?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="refresh"
	content="<?php echo $redirect; ?>; url=index.php">
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
		echo 'activated ';
	} else {
		$redirectInSeconds = 180;
		echo 'activation failed - contact admin';
	}
	htmlHeader($redirectInSeconds);
	echo '- you will be redirected to the login page in '.$redirectInSeconds.' seconds (or go manually there: <a href="index.php">Login</a>)';
}
?>
</body>
</html>
