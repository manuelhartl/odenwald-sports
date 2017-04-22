<?php
require_once __DIR__ . '/../lib/db_tours.php';

$attendeeString = '';
$users = getAttendees ( $pdo, $tour->id );
foreach ( $users as $user ) {
	$attendeeString = $attendeeString . $user ['username'] . " ";
}

?>
<h1><?php
$title = $tour->sport->sportsubname . "-Tour von " . $tour->guide->username . " am " . $tour->startDateTime->format ( 'd.m.Y' ) . ' um ' . $tour->startDateTime->format ( 'H:i' );
echo $title;
?></h1>
<table style="width: 100%; height: 100%;">
	<tr style="background-color: gray;">
		<?php
		if (hasAuth ()) {
			echo '<th>Treffpunkt</th>';
		}
		?>
		<th>Beschreibung</th>
		<th style='text-align: right;'>Dauer<br>(hh:mm)
		</th>
		<th style='text-align: right;'>Distanz</th>
		<th style='text-align: right;'>Bergauf</th>
		<th>Pace</th>
		<th>Technik</th>
		<th>Teilnehmer</th>
	</tr>
<?php
echo '<tr>';

if (hasAuth ()) {
	$meetingpoint_short = ! empty ( $tour->meetingPoint ) ? $tour->meetingPoint : $tour->meetingPoint_desc;
	$meetingpoint_long = $tour->meetingPoint_desc;
	echo '<td title="' . $meetingpoint_long . '">' . $meetingpoint_short . '<br>(' . $meetingpoint_long . ')</td>';
}
echo "<td>" . $tour->description . "</td>";
echo "<td style='text-align: right;'>" . formatMinutes ( $tour->duration ) . "</td>";
echo "<td style='text-align: right;'>" . formatMeters ( $tour->distance ) . "</td>";
echo "<td style='text-align: right;'>" . formatMeters ( $tour->elevation ) . "</td>";
echo "<td style='width: 110px;'>" . getStars ( $tour->speed, 'speed' . $tour->id ) . "</td>";
echo "<td style='width: 110px;'>" . getStars ( $tour->skill, 'skill' . $tour->id ) . "</td>";

if (hasAuth ()) {
	// attendees
	echo '<td>' . $attendeeString . '</td>';
} else {
	echo "<td>" . count ( $users ) . "</td>";
}
echo "</tr>";

?>
	
	<tr>
		<td colspan="13"><div id="meetingpoint-map" style="width: 100%; min-height: 500px; height: 100%;"></div> <script
				src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true"></script> <script>
function initialize() {
  var myLatlng = new google.maps.LatLng(<?php echo $tour->meetingPoint_lat;?>, <?php echo $tour->meetingPoint_long;?>);
  var mapOptions = {
    zoom: 15,
    center: myLatlng
  }
  var map = new google.maps.Map(document.getElementById('meetingpoint-map'), mapOptions);

  var marker = new google.maps.Marker({
      position: myLatlng,
      map: map,
      title: '<?php echo $title;?>'
  });
}

google.maps.event.addDomListener(window, 'load', initialize);

    </script>
		
		
		<?php
		/*
		 * <div id="meetingpoint-map" style="width: 100%; min-height: 500px; height: 100%;"></div> <script>$('#meetingpoint-map').locationpicker({
		 * location: {latitude: <?php echo $tour->meetingPoint_lat;?>, longitude: <?php echo $tour->meetingPoint_long;?>},
		 * radius: 50,
		 *
		 * });
		 * </script>
		 */
		?>
		</td>
	</tr>
</table>