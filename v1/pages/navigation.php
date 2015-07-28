<div id="navigation-top">
<?php
echo '<div id="message">' . $_SESSION ['message'] . '</div> <!-- Ende div message -->';
echo '<div id="logo"><img src="img/logo, einfach.png"></div> <!-- Ende div logo -->';
?>
</div> <!-- Ende div navigation-top -->
<div id="navigation-bottom">
	<div id="navigation-buttons">
		<form name="home" action="" method="post">
			<input name="action" type="hidden" value="home" /> <input id="submit" name="submit" type="submit" value="Touren" />
		</form>

<?php
function getBreadCrumbText() {
	return ""; // makes no sense at the moment to have complex breadcrumbs
	switch (getPage ()) {
		case 'home' :
			return 'Home';
		case 'login' :
			return 'Login';
		case 'password-change' :
			return 'Passwort &auml;ndern';
		case 'password-reset' :
			return 'Passwort Reset';
		case 'register' :
			return 'Registrieren';
		case 'register-save' :
			return '';
		case 'home' :
			return 'Home';
		case 'tour-edit' :
			return 'Tour / Edit';
		case 'tour-new' :
			return 'Tour / Neu';
	}
}
if (hasAuth ()) {
	?>
		<form name="user-list" action="index.php" method="post">
			<input name="action" type="hidden" value="user-list" /> <input name="submit" type="submit" value="Benutzerliste" />
		</form>
	</div> <!-- Ende div navigation-buttons -->
	<div id="navigation-buttons-logout">
		<form name="user-edit" action="index.php" method="post">
			<input name="action" type="hidden" value="user-edit" /> <input name="submit" type="submit" value="Mein Profil" />
		</form>
		<form name="logout" action="index.php" method="post">
			<input name="action" type="hidden" value="logout" /> <input id="submit-logout" name="submit" type="submit"
				value="Logout" />
		</form>
<?php
	echo 'Angemeldet als [' . authUser ()->username . ']';
	echo '</div> <!-- Ende div navigation-buttons or navigation-buttons-logout --> ';
} else {
	?>
	</div> <!-- Ende div navigation-buttons -->
	<div id="navigation-buttons-logout">
		<form name="register" action="index.php" method="post">
			<input name="action" type="hidden" value="register" /> <i1nput name="register" type="submit" value="Register" />
		</form>
		<form name="login" action="index.php" method="post">
			<input name="action" type="hidden" value="goto-login" /> <input id="submit-login" name="submit" type="submit"
				value="Login" />
		</form>
	</div> <!-- Ende div navigation-buttons-logout -->
<?php
}
?>
</div> <!-- Ende div navigation-top -->
