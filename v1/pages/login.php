<div id="login">
	<form name="login" action="index.php" method="post">
		<input name="action" type="hidden" value="login" />
		<table>
			<tr>
				<td>User Name:</td>
				<td><input name="username" type="text" value="<?php echo isset($input['username']) ? $input['username'] : '';?>" /></td>
			</tr>
			<tr>
				<td>Passwort:</td>
				<td><input name="password" type="password" /></td>
			</tr>
			<tr>
				<td colspan="2"><input name="submit" type="submit" value="Login" />
					</form>
					<form name="password-reset" action="index.php" method="post">
						<input name="action" type="hidden" value="password-reset" /> <input name="submit" type="submit"
							value="Passwort vergessen?" />
					</form>
					<form name="home" action="index.php" method="post">
						<input name="action" type="hidden" value="home" /> <input name="submit" type="submit" value="Abbrechen" />
					</form></td>
			</tr>
		</table>

		<hr />

</div>
