
<div id="login">
	<form name="login" action="index.php" method="post">
		<input name="action" type="hidden" value="login" />
		<table>
			<tr>
				<td>User Name:</td>
				<td><input name="username" type="text"
					value="<?php echo isset($input['username']) ? $input['username'] : '';?>" /></td>
			</tr>
			<tr>
				<td>Passwort:</td>
				<td><input name="password" type="password" /></td>
			</tr>
			<tr>
				<td colspan="2"><input name="submit" type="submit" value="Login" /></td>
			</tr>
		</table>
	</form>
	<hr/>
	<form name="password-reset" action="index.php" method="post">
		<input name="action" type="hidden" value="password-reset" /> <input
			name="submit" type="submit" value="Passwort vergessen?" />
	</form>
	<form name="register" action="index.php" method="post">
		<input name="action" type="hidden" value="register" /> <input
			name="submit" type="submit" value="Register" />
	</form>

</div>
