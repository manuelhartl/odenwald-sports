<h1>Neue Tour</h1>

<form action="index.php" method="post">
	<input type="hidden" name="action" value="tour-save" />
	<table>
		<tr>
			<td>Treffpunkt</td>
			<td><input type="text" name="meetingpoint" value="" /></td>
		</tr>
		<tr>
			<td>Beschreibung</td>
			<td><textarea name="description" rows="10" cols="50"></textarea></td>
		</tr>
		<tr>
			<td>Datum/Uhrzeit</td>
			<td><input type="text" name="startdate" value="" /></td>
		</tr>
		<tr>
			<td>Dauer (Minuten)</td>
			<td><input type="text" name="duration" value="" /></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Speichern" /></td>
		</tr>
		</table>
</form>

<?php
?>