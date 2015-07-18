<h1><?php echo isset($input['userid']) ? 'Profil anzeigen' :'';?></h1>

<table style="width: 100%">
	<tr>
		<td style="width: 1%">UserName</td>
		<td><?php echo $input['username']; ?></td>
	</tr>
	<tr>
		<td>Echter Name</td>
		<td><?php echo isset($input['realname']) ? $input['realname'] : 'unbekannt';?></td>
	</tr>
	<tr>
		<td>Telefon</td>
		<td>"<?php echo isset($input['phone']) ? $input['phone'] : 'unbekannt';?></td>
	</tr>
	<tr>
		<td>Geburtsjahr</td>
		<td>"<?php echo isset($input['birthdate']) ? $input ['birthdate']->format ( 'Y' ):'unbekannt';?>"</td>
	</tr>
	<td>Treffpunktreferenz bzw. Wohnanschrift)</td>
	<td><?php echo isset($input['address']) ? $input['address'] : 'unbekannt';?>"</td>
	</tr>

</table>