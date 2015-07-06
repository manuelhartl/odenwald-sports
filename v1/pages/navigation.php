<form name="home" action="" method="post">
	<input name="action" type="hidden" value="home" /> <input id="submit"
		name="submit" type="submit" value="Start" />
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
<!-- 
	<button class="btn btn-default dropdown-toggle" type="button"
		id="dropdownMenuAccount" data-toggle="dropdown" aria-haspopup="true"
		aria-expanded="true">
		Account <span class="caret"></span>
	</button>
	<ul class="dropdown-menu dropdown-menu-right"
		aria-labelledby="dropdownMenuAccount">
		<li>
		 -->
<!-- 
		</li>
	</ul>
	 -->
<form name="user-list" action="index.php" method="post">
	<input name="action" type="hidden" value="user-list" /> <input
		name="submit" type="submit" value="Benutzerliste" />
</form>
	<form name="user-edit" action="index.php" method="post">
	<input name="action" type="hidden" value="user-edit" /> <input
		name="submit" type="submit" value="Mein Profil" />
</form>
<form name="password-change" action="index.php" method="post">
	<input name="action" type="hidden" value="password-change" /> <input
		id="submit-password-change" name="submit" type="submit"
		value="Passwort &auml;ndern" />
</form>
<form name="logout" action="index.php" method="post">
	<input name="action" type="hidden" value="logout" /> <input
		id="submit-logout" name="submit" type="submit" value="Logout" />
</form>
<?php
	echo 'Angemeldet als [' . authUser ()->username . ']';
} else {
	?>
<form name="register" action="index.php" method="post">
	<input name="action" type="hidden" value="register" /> <input
		name="register" type="submit" value="Register" />
</form>
<form name="login" action="index.php" method="post">
	<input name="action" type="hidden" value="goto-login" /> <input
		id="submit-login" name="submit" type="submit" value="Login" />
</form>
<?php
}
echo '<div style="width: 100px;"></div>';
echo getBreadCrumbText ();
echo '<div style="width: 100px;"></div>';
echo '<div id="message">' . $_SESSION ['message'] . '</div>';
?>
