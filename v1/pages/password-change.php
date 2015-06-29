<h1>Passwort &auml;ndern</h1>

<form action="index.php" method="post">
	<input type="hidden" name="action" value="password-change-save" />
	<table>
		<tr>
			<td style="text-align: right;">Altes Passwort</td>
			<td><input type="password" name="oldpassword"></td>
		</tr>
		<tr>
			<td style="text-align: right;">Neues Passwort</td>
			<td><input type="password" name="newpassword"></td>
		</tr>
		<tr>
			<td style="text-align: right;">Neues Passwort (nochmal eingeben)</td>
			<td><input type="password" name="newpassword2"></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="&Auml;ndern" /></td>
		</tr>
	</table>
</form>