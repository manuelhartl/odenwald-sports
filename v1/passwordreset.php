<?php
require_once 'lib/global.php';
require_once 'lib/db_users.php';

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

$token = $_REQUEST ['token'];
$password = isset ( $_REQUEST ['password'] ) ? $_REQUEST ['password'] : '';
$password2 = isset ( $_REQUEST ['password2'] ) ? $_REQUEST ['password2'] : '';
if (isset ( $_REQUEST ['action'] ) && $_REQUEST ['action'] == 'password-reset-form') {
	if (! validatePassword ( $password )) {
		echo 'password must be at least 6 characters';
	} else if ($password != $password2) {
		echo 'passwords do not match';
	} else {
		$pdo = db_open ();
		
		$hashedpassword = password_hash ( $password, PASSWORD_BCRYPT );
		if (resetPassword ( $pdo, $token, $hashedpassword )) {
			htmlHeader(5);
			echo 'Passwort zur&uuml;ckgesetzt.';
			die ();
		} else {
			htmlHeader(15);
			echo 'Passwort nicht zur&uuml;ckgesetzt (Token ungueltig)';
		}
	}
}

?>
<!DOCTYPE html>
<html lang="de">
<body>
	<h1>Passwort zur&uuml;cksetzen</h1>

	<form action="" method="post">
		<input type="hidden" name="action" value="password-reset-form" /> <input
			type="hidden" name="token" value="<?php echo $token;?>" />
		<table>
			<tr>
				<td style="text-align: right;">Neues Passwort</td>
				<td><input type="password" name="password"></td>
			</tr>
			<tr>
				<td style="text-align: right;">Neues Passwort (nochmal eingeben)</td>
				<td><input type="password" name="password2"></td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" value="&Auml;ndern" /></td>
			</tr>
		</table>
	</form>
</body>
</html>
