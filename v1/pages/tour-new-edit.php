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
			<td>Sport</td>
			<td>
			<?php
			if ($update) {
				echo $tour->sport->sportname . '/' . $tour->sport->sportsubname;
			} else {
				echo '<select name="sport">';
				foreach ( $sports as $sport ) {
					$selected = $sport->sportsubid == $input ['sport'] ? ' selected ' : '';
					echo '<option value="' . $sport->sportsubid . '"' . $selected . '>' . $sport->sportname . '/' . $sport->sportsubname . '</option>';
				}
				echo '</select>';
			}
			?>
			</td>
		</tr>
		<td>Treffpunkt</td>
		<td>
			<div id="meetingpoint-map" style="width: 500px; height: 400px;"></div>
			<input type="text" id="meetingpoint" style="width: 400px"
			name="meetingpoint"
			value="<?php echo isset($input['meetingpoint']) ? $input['meetingpoint'] : '';?>" />
			<input type="hidden" id="meetingpoint-radius" /> <input type="hidden"
			id="meetingpoint-lat" name="meetingpoint-lat" /><input type="hidden"
			id="meetingpoint-lon" name="meetingpoint-lon" /> <script>$('#meetingpoint-map').locationpicker({
	location: {latitude: <?php echo isset($input['meetingpoint-lat']) ? $input['meetingpoint-lat'] : '49.85212170040001';?>, longitude: <?php echo isset($input['meetingpoint-lon']) ? $input['meetingpoint-lon'] : '8.670546531677246';?>},	
	radius: 50,
	inputBinding: {
        latitudeInput: $('#meetingpoint-lat'),
        longitudeInput: $('#meetingpoint-lon'),
        radiusInput: $('#meetingpoint-radius'),
        locationNameInput: $('#meetingpoint')
    },
	enableAutocomplete: true
	});
</script>
		</td>
		</tr>
		<tr>
			<td>Beschreibung</td>
			<td><textarea name="description" rows="10" cols="50"><?php echo isset($input['description']) ? $input['description'] : '';?></textarea></td>
		</tr>
		<tr>
			<td>Datum/Uhrzeit</td>
			<td><?php
			if ($update) {
				echo $input ['startdate'];
			} else {
				?>
				<div class="row">
					<div class='col-sm-6'>
						<input type="text" class="form-control" style="width: 150px;" id="startdate" maxlength="10" 
							name="startdate"
							value="<?php echo isset($input['startdate']) ? $input['startdate'] : '';?>" />
					</div>
					<script type="text/javascript">
            $(function () {
                $('#startdate').datetimepicker({
						format: 'DD.MM.YYYY HH:mm',
                		showTodayButton: true,
                		sideBySide: true,
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