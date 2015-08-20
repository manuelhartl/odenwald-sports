<h1>Mail schreiben</h1>

<form action="index.php" method="post" style="width: 100%;">

	<input type="hidden" name="action" value="mail-send" /> <input type="hidden" name="to"
		value="<?php echo $input['to'];?>" />
	<?php
	if (isset ( $input ['touserid'] )) {
		echo '<input type="hidden" name="touserid" value="' . $input ['touserid'] . '"/>';
	}
	if (isset ( $input ['totourid'] )) {
		echo '<input type="hidden" name="totourid" value="' . $input ['totourid'] . '"/>';
	}
	?>
	<table style="width: 100%">
		<tr>
			<td style="width: 1%">Von</td>
			<td><?php echo authUser ()->username; ?></td>
		</tr>
		<tr>
			<td>An</td>
			<td><?php echo $input['to']; ?></td>
		</tr>
		<tr>
			<td>Betreff</td>
			<td><input type="text" style="width: 100%" name="subject"
				value="<?php echo isset($input['subject']) ? $input['subject'] : '';?>" /></td>
		</tr>
		<tr>
			<td>Nachricht</td>
			<td><textarea name="body" rows="10" cols="50" style="width: 100%"><?php echo isset($input['body']) ? $input['body'] : '';?></textarea></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Senden" />

				</form>
				<form name="login" action="index.php" method="post">
					<input name="action" type="hidden" value="home" /> <input name="submit" type="submit" value="Abbrechen" />
				</form></td>
		</tr>
	</table>