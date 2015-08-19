<h1><?php echo isset($input['userid']) ? 'Profil editieren' :'';?></h1>

<form action="index.php" method="post" style="width: 100%;">

	<input type="hidden" name="action" value="user-save" />
	<?php
	echo '<input type="hidden" name="userid" value="' . $input ['userid'] . '"/>';
	?>
	 
	<table style="width: 100%">
		<tr>
			<td style="width: 1%">UserName</td>
			<td>
			<?php
			echo authUser ()->username;
			?>
			</td>
		</tr>
		<tr>
			<td>Echter Name</td>
			<td><input type="text" style="width: 100%" name="realname"
				value="<?php echo isset($input['realname']) ? $input['realname'] : '';?>" /><br>
				(Dies ist f&uuml;r alle angemeldeten Benutzer sichtbar - Nur
				eintragen wenn dies in Ordnung ist)</td>
		</tr>
		<tr>
			<td>Telefon</td>
			<td><input type="text" style="width: 100%" name="phone"
				value="<?php echo isset($input['phone']) ? $input['phone'] : '';?>" /><br>
				(Dies ist f&uuml;r alle angemeldeten Benutzer sichtbar - Nur
				eintragen wenn dies in Ordnung ist)</td>
		</tr>
		<tr>
			<td>Geburtsjahr</td>
			<td><input type="text" class="form-control" style="width: 150px;"
				id="birthdate" maxlength="10" name="birthdate"
				value="<?php echo (Utilities::isValidDate($input ['birthdate'],"2000-1-1")->format ( 'Y' ));?>" />
				(oder leerlassen)<br>(Dies ist f&uuml;r alle angemeldeten Benutzer
				sichtbar - Nur eintragen wenn dies in Ordnung ist)</td>
		</tr>
		<tr>
			<td>Wohnanschrift</td>
			<td><input type="text" id="address" style="width: 100%"
				name="address"
				value="<?php echo isset($input['address']) ? $input['address'] : '';?>" />
				<input type="hidden" id="address-radius" /> <input type="hidden"
				id="address-lat" name="address-lat" /><input type="hidden"
				id="address-lon" name="address-lon" />
				<div id="address-map" style="width: 100%; height: 400px;"></div> <script>$('#address-map').locationpicker({
	location: {latitude: <?php echo isset($input['address-lat']) ? $input['address-lat'] : '49.85212170040001';?>, longitude: <?php echo isset($input['address-lon']) ? $input['address-lon'] : '8.670546531677246';?>},	
	radius: 50,
	inputBinding: {
        latitudeInput: $('#address-lat'),
        longitudeInput: $('#address-lon'),
        radiusInput: $('#address-radius'),
        locationNameInput: $('#address')
    },
	enableAutocomplete: true
	});
</script><br> (Dies ist f&uuml;r alle angemeldeten Benutzer sichtbar -
				Nur eintragen wenn dies in Ordnung ist)</td>
		</tr>
		<tr>
			<td>Tour Mailings</td>
			<td><input name="mailing" type="checkbox" value="true"
				<?php echo $input['mailing'] ? 'checked' : '';?>></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Speichern" />
				</form>
				<form name="login" action="index.php" method="post">
					<input name="action" type="hidden" value="home" /> <input
						name="submit" type="submit" value="Abbrechen" />
				</form></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<form name="password-change" action="index.php" method="post">
					<input name="action" type="hidden" value="password-change" /> <input
						id="submit-password-change" name="submit" type="submit"
						value="Passwort &auml;ndern" />
				</form>
			</td>
		</tr>
	</table>