<head>
<script src="//cdn.ckeditor.com/4.5.1/standard/ckeditor.js"></script>
</head>


<?php
require_once __DIR__ . '/../lib/db_tours.php';

?>
<h1><?php echo isset($input['tourid']) ? 'Tour editieren' : 'Neue Tour';?></h1>

<form action="index.php" method="post" name="form" style="width: 100%;">

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
		<tr>
			<td>Treffpunkt</td>
			<td>
				<table style="width: 100%">
					<tr>
						<td style="min-width: 125px;">Vordefiniert:</td>
						<td style="width: 100%;"><select id="place"
							onchange="var lat = document.getElementById('meetingpoint-lat'); lat.value=this.value.split(';')[0].replace(',','.');
		var lon = document.getElementById('meetingpoint-lon'); lon.value=this.value.split(';')[1].replace(',','.'); 
		lat.dispatchEvent(new Event('change'));lon.dispatchEvent(new Event('change'));
		var mp_desc = document.getElementById('meetingpoint_desc');
		mp_desc.value=this.options[this.selectedIndex].text;">
		<?php
		$pdo = db_open ();
		$places = getPlaces ( $pdo );
		echo '<option></option>';
		foreach ( $places as $place ) {
			echo '<option value="' . $place->lat . ';' . $place->lon . '">' . $place->name . '</option>';
		}
		?>
		</select></td>
					</tr>
					<tr>
						<td>Treffpunkt Info</td>
						<td><input type="text" id="meetingpoint_desc" style="width: 100%"
							name="meetingpoint_desc"
							value="<?php echo isset($input['meetingpoint_desc']) ? $input['meetingpoint_desc'] : '';?>" /></td>
					</tr>
					<tr>
						<td>Adresse</td>
						<td><input type="text" id="meetingpoint" style="width: 100%"
							name="meetingpoint"
							value="<?php echo isset($input['meetingpoint']) ? $input['meetingpoint'] : '';?>" /></td>
					</tr>

				</table> <input type="hidden" id="meetingpoint-radius" /> <input
				type="hidden" id="meetingpoint-lat" name="meetingpoint-lat" /> <input
				type="hidden" id="meetingpoint-lon" name="meetingpoint-lon" />
				<div id="meetingpoint-map" style="width: 100%; height: 400px;"></div>
				<script>$('#meetingpoint-map').locationpicker({
	location: {latitude: <?php echo isset($input['meetingpoint-lat']) ? $input['meetingpoint-lat'] : '49.85212170040001';?>, longitude: <?php echo isset($input['meetingpoint-lon']) ? $input['meetingpoint-lon'] : '8.670546531677246';?>},	
	radius: 50,
	inputBinding: {
        latitudeInput: $('#meetingpoint-lat'),
        longitudeInput: $('#meetingpoint-lon'),
        radiusInput: $('#meetingpoint-radius'),
        locationNameInput: $('#meetingpoint')
    },
    onchanged: function (currentLocation, radius, isMarkerDropped) {
     	 var addressComponents = $('#meetingpoint-map').locationpicker('map').location.addressComponents;
      	document.getElementById('meetingpoint').value = addressComponents.addressLine1+', '+addressComponents.postalCode+' '+addressComponents.city+', '+addressComponents.country; 
    },
	enableAutocomplete: true
	});
</script>
			</td>
		</tr>
		<tr>
			<td>Beschreibung</td>
			<?php
			if (isset ( $config ['HTMLEditor'] ) && $config ['HTMLEditor']) {
				echo '', PHP_EOL;
				echo '<td>', PHP_EOL;
				echo '<textarea  id="editor" name="description" rows="10" cols="50" style="width: 100%">', PHP_EOL;
				echo isset ( $input ['description'] ) ? $input ['description'] : '', PHP_EOL;
				echo '', PHP_EOL;
				echo '</textarea>', PHP_EOL;
				echo '</td>', PHP_EOL;
				
				echo '<script>', PHP_EOL;
				echo 'CKEDITOR.replace( "editor", {', PHP_EOL;
				echo 'height: "300px",', PHP_EOL;
				echo 'width: "100%"', PHP_EOL;
				echo '} );', PHP_EOL;
				echo '</script>', PHP_EOL;
			} else {
				echo '', PHP_EOL;
				echo '<td>', PHP_EOL;
				echo '<textarea name="description" rows="10" cols="50" style="width: 100%">', PHP_EOL;
				echo isset ( $input ['description'] ) ? $input ['description'] : '', PHP_EOL;
				echo '', PHP_EOL;
				echo '</textarea>', PHP_EOL;
				echo '</td>', PHP_EOL;
			}
			?>
		</tr>
		<tr>
			<td>Datum/Uhrzeit</td>
			<td><?php
			// echo $input ['startdate']->format ( 'd.m.Y H:i' );
			?>
				<div class="row">
					<div class='col-sm-6'>
						<input type="text" class="form-control" style="width: 150px;"
							id="startdate" maxlength="16" name="startdate"
							value="<?php echo isset($input['startdate']) ? $input['startdate'] : '';?>" />
					</div>
					<script type="text/javascript">
            $(function () {
                $('#startdate').datetimepicker({
    					format: 'DD.MM.YYYY HH:mm',
						defaultDate: moment().add(60,'minute'),
						minDate: moment().add(15,'minute'),
						showTodayButton: true,
						calendarWeeks: true,
						useCurrent: false,
                		sideBySide: true
                		}
                      );
            });
        </script>
				</div></td>
		</tr>
		<tr>
			<td>Geschwindigkeit</td>
			<td><?php echo getStars($input['speed'], 'speed',false); ?></td>
		</tr>
		<tr>
			<td>Technik</td>
			<td><?php echo getStars($input['skill'], 'skill',false); ?><span
				id="skill-tip"></span> <a target="_blank"
				href="http://www.singletrail-skala.de/">SingleTrail Skala 1-6
					(S0-S5)</a> <script type="text/javascript"></script></td>
		</tr>
		<tr>
			<td>Distanz</td>
			<td><input type="text" name="distance"
				value="<?php echo isset($input['distance']) ? $input['distance'] : '';?>" />
				Kilometer</td>
		</tr>
		<tr>
			<td>H&ouml;henmeter</td>
			<td><input type="text" name="elevation"
				value="<?php echo isset($input['elevation']) ? $input['elevation'] : '';?>" />
				Meter</td>
		</tr>
		<tr>
			<td>Dauer</td>
			<td><input type="text" name="duration"
				value="<?php echo isset($input['duration']) ? $input['duration'] : '';?>" />
				Minuten</td>
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
	</table>