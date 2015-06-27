<h1><?php echo isset($input['tourid']) ? 'Tour editieren' : 'Neue Tour';?></h1>

<form action="index.php" method="post">

	<input type="hidden" name="action" value="tour-save" />
	<?php
	$update = isset ( $input ['tourid'] );
	if ($update) {
		echo '<input type="hidden" name="tourid" value="' . $input ['tourid'] . '"/>';
	}
	?>
	 
	<table>
		<tr>
			<td>Treffpunkt</td>
			<td><input type="text" name="meetingpoint"
				value="<?php echo isset($input['meetingpoint']) ? $input['meetingpoint'] : '';?>" /></td>
		</tr>
		<tr>
			<td>Beschreibung</td>
			<td><textarea name="description" rows="10" cols="50"><?php echo isset($input['description']) ? $input['description'] : '';?></textarea></td>
		</tr>
		<tr>
			<td>Datum/Uhrzeit</td>
			<td><?php
			if ($update) {
				echo $input['startdate'];
			} else {
				?>
				<div class="row">
					<div class='col-sm-6'>
						<input type="text" class="form-control" id='startdate'
							name="startdate"
							value="<?php echo isset($input['startdate']) ? $input['startdate'] : '';?>" />
					</div>
					<script type="text/javascript">
            $(function () {
                $('#startdate').datetimepicker({
                		showTodayButton: true,
						minDate: moment()
                		}
                      );
            });
        </script>
				</div>
				<?php
			}
			?>
			</td>
		</tr>
		<tr>
			<td>Dauer (Minuten)</td>
			<td><input type="text" name="duration"
				value="<?php echo isset($input['duration']) ? $input['duration'] : '';?>" /></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Speichern" /></td>
		</tr>
	</table>
</form>

<?php
?>