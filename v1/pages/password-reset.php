<h1>Passwort zur&uuml;cksetzen</h1>

<form action="index.php" method="post">

	<input type="hidden" name="action" value="password-reset-save" />

	<table>
		<tr>
			<td>User name</td>
			<td><input name="username" type="text"
				value="<?php echo isset($input['username']) ? $input['username'] : '';?>" /></td>
		</tr>
		<tr>
			<td style="text-align: right;">Deine E-Mail Adresse</td>
			<td><input type="text" name="email"></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="&Auml;ndern" /></td>
		</tr>
	</table>
</form>