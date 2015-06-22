<html>
<body>
	<form name="register" action="index.php" method="post">
		<input name="action" type="hidden" value="register-save" />
		<table>
			<tr>
				<td>User name:</td>
				<td><input name="username" type="text" /></td>
			</tr>
			<tr>
				<td>Passwort:</td>
				<td><input name="password" type="password" /></td>
			</tr>
			<tr>
				<td>Email-Adresse:</td>
				<td><input name="email" type="text" /></td>
			</tr>
			<tr>
				<td></td>
				<td><input id="submit" name="submit" type="submit"
					value="Registrieren" /></td>
			</tr>
		</table>
	</form>
</body>
</html>