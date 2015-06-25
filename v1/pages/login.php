
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
				<td></td>
				<td><input id="submit" name="submit" type="submit" value="Login" /></td>
			</tr>
		</table>
	</form>
	or
	<form name="register" action="index.php" method="post">
		<input name="action" type="hidden" value="register" />
		<tr>
			<td><input id="submit" name="submit" type="submit" value="Register" /></td>
		</tr>
		</table>
	</form>
</div>
