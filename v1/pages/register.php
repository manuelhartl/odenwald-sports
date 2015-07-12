
<form name="register" action="index.php" method="post">
	<input name="action" type="hidden" value="register-save" />
	<table>
		<tr>
			<td style="text-align: right;">User name:</td>
			<td><input name="username" type="text" value="<?php echo isset($input['username']) ? $input['username'] : '';?>" /></td>
		</tr>
		<tr>
			<td style="text-align: right;">Passwort:</td>
			<td><input name="password" type="password" /></td>
		</tr>
		<tr>
			<td style="text-align: right;">Passwort wiederholen:</td>
			<td><input name="password2" type="password" /></td>
		</tr>
		<tr>
			<td style="text-align: right;">Email-Adresse:</td>
			<td><input name="email" type="text" value="<?php echo isset($input['email']) ? $input['email'] : '';?>" /></td>
		</tr>
		<tr>
			<td style="text-align: right;">Ich habe die <a href="html/disclaimer.php" target="_blank">Regeln</a><br> gelesen und akzeptiere
				sie:
			</td>
			<td><input name="acceptrules" type="checkbox" value="true"
				<?php echo isset($input['acceptrules']) && $input['acceptrules'] ? 'checked' : '';?>></td>
		</tr>
		<tr>
			<td></td>
			<td><input id="submit" name="submit" type="submit" value="Registrieren" />
				</form>
				<form name="home" action="index.php" method="post">
					<input name="action" type="hidden" value="home" /> <input id="submit" name="submit" type="submit" value="Abbrechen" />
				</form></td>
		</tr>
	</table>