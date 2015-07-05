<h1><?php echo isset($input['tourid']) ? 'Tour editieren' : 'Neue Tour';?></h1>

<form action="index.php" method="post" style="width: 100%;">

	<input type="hidden" name="action" value="tour-save" />
	<?php
	$update = isset ( $input ['tourid'] );
	if ($update) {
		echo '<input type="hidden" name="tourid" value="' . $input ['tourid'] . '"/>';
	}
	?>
	 
	<table style="width: 100%">
		<tr>
			<td style="width: 1%">Sport</td>
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
		<td><input type="text" id="meetingpoint" style="width: 100%" name="meetingpoint"
			value="<?php echo isset($input['meetingpoint']) ? $input['meetingpoint'] : '';?>" /> <input type="hidden"
			id="meetingpoint-radius" /> <input type="hidden" id="meetingpoint-lat" name="meetingpoint-lat" /><input type="hidden"
			id="meetingpoint-lon" name="meetingpoint-lon" />
			<div id="meetingpoint-map" style="width: 100%; height: 400px;"></div> <script>$('#meetingpoint-map').locationpicker({
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
</script></td>
		</tr>
		<tr>
			<td>Beschreibung</td>
			<td><textarea name="description" rows="10" cols="50" style="width: 100%"><?php echo isset($input['description']) ? $input['description'] : '';?></textarea></td>
		</tr>
		<tr>
			<td>Datum/Uhrzeit</td>
			<td><?php
			if ($update) {
				echo $input ['startdate']->format ( 'd.m.Y H:i' );
			} else {
				?>
				<div class="row">
					<div class='col-sm-6'>
						<input type="text" class="form-control" style="width: 150px;" id="startdate" maxlength="16" name="startdate"
							value="<?php echo isset($input['startdate']) ? $input['startdate'] : '';?>" />
					</div>
					<script type="text/javascript">
            $(function () {
                $('#startdate').datetimepicker({
						format: 'DD.MM.YYYY HH:mm',
                		showTodayButton: true,
                		sideBySide: true,
						minDate: moment().add(15,'minute')
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
			<td>Geschwindigkeit</td>
			<td><?php echo getStars($input['speed'], 'speed',false); ?></td>
		</tr>
		<tr>
			<td>Technik</td>
			<td><?php echo getStars($input['skill'], 'skill',false); ?><span id="skill-tip"></span> <a target="_blank"
				href="http://www.singletrail-skala.de/">SingleTrail Skala 1-6 (S0-S5)</a> <script type="text/javascript">
			
// 			$('#skill').rating({
// 						 focus: function(value, link) {
// 						 var tip = $('#skill-tip');
//  						 tip[0].data = tip[0].data || tip.html();
//           				 tip.html(link.title || 'value: '+value);
// 						  }
//  					 , blur: function(value, link) {
//  						 var tip = $('#skill-tip');
//   						 $('#skill-tip').html(tip[0].data || '');
// }
//  					 }
// 			);
			</script></td>
		</tr>
		<tr>
			<td>Distanz</td>
			<td><input type="text" name="distance" value="<?php echo isset($input['distance']) ? $input['distance'] : '';?>" />
				Kilometer</td>
		</tr>
		<tr>
			<td>H&ouml;henmeter</td>
			<td><input type="text" name="elevation" value="<?php echo isset($input['elevation']) ? $input['elevation'] : '';?>" />
				Meter</td>
		</tr>
		<tr>
			<td>Dauer</td>
			<td><input type="text" name="duration" value="<?php echo isset($input['duration']) ? $input['duration'] : '';?>" />
				Minuten</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Speichern" />
				</form>
				<form name="login" action="index.php" method="post">
					<input name="action" type="hidden" value="home" /> <input name="submit" type="submit" value="Abbrechen" />
				</form></td>
		</tr>
	</table>