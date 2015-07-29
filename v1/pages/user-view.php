<h1><?php echo isset($input['userid']) ? 'Profil anzeigen' :'';?></h1>

<table style="width: 100%">
	<tr>
		<td style="width: 150px;">UserName</td>
		<td><?php echo $input['username']; ?></td>
	</tr>
	<tr>
		<td>Echter Name</td>
		<td><?php echo (isset($input['realname'])&&strlen($input['realname'])>0) ?$input['realname']: 'unbekannt';?></td>
	</tr>
	<tr>
		<td>Telefon</td>
		<td><?php echo (isset($input['phone'])&&strlen($input['phone'])>0) ?$input['phone']: 'unbekannt';?></td>
	</tr>
	<tr>
		<td>Geburtsjahr</td>
		<td><?php echo (is_int($input['birthdate'])) ?$input ['birthdate']->format ( 'Y' ):'unbekannt';?></td>
	</tr>
	<tr>
		<td>Wohnanschrift)</td>
		<td><?php echo (isset($input['address'])&&strlen($input['address'])>0) ?$input['address']: 'unbekannt';?></td>
	</tr>

</table>