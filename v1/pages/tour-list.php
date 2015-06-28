<table>
	<tr>
		<th></th>
		<th>Datum</th>
		<th>Sport</th>
		<th>Treffpunkt</th>
		<th>Entfernung zum B&ouml;lle</th>
		<th>Beschreibung</th>
		<th>Dauer</th>
		<th>Guide</th>
		<th>Teilnehmer</th>
		<th><?php echo '<form action="" method="post"><input type="hidden" name="action" value="tour-new"/><input type="submit" value="Neue Tour"/></form>';?></th>
	</tr>
<?php
require_once 'lib/global.php';
require_once 'lib/tours.php';

$reference = getPlaceById ( $pdo, 1 );
$stmt = $pdo->prepare ( 'select *,111195 * ST_Distance(POINT(?,?), meetingpoint_coord) as refm, t.status as tourstatus,t.id as id, g.id as guide, g.username as guidename' . //
' from tour t' . //
' left join user g ON (t.fk_guide_id=g.id) ' . //
' left join sport_subtype ss ON (t.fk_sport_subtype_id=ss.id) ' . //
' left join sport s ON (ss.fk_sport_id=s.id) ' . //
' WHERE startdate>now()' . //
' order by startdate ASC' ); //
ex2er ( $stmt, array (
		$reference->gps->lat,
		$reference->gps->long 
) );

$authuserid = authUser ()->id;
while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
	// print_r($row);
	$tour = getTourObject ( $row );
	echo '<tr class=' . ($tour->canceled ? 'canceled' : '') . '>';
	$startdate = $tour->startDateTime;
	if (isset ( $lastdate ) && $lastdate->format ( 'ymd' ) == $startdate->format ( 'ymd' )) {
		echo '<td></td><td></td>';
	} else {
		echo "<td>" . getWeekDay ( $startdate ) . "</td>";
		echo "<td>" . $startdate->format ( 'd.m.Y H:i' ) . "</td>";
	}
	
	echo "<td>" . $tour->sport->sportsubname . "</td>"; // . $tour->sport->sportname.' '
	echo "<td>" . $tour->meetingPoint . "</td>";
	echo "<td>" . formatMeters ( $row ['refm'] ) . "</td>";
	echo "<td>" . $tour->description . "</td>";
	echo "<td>" . formatMinutes ( $tour->duration ) . "</td>";
	echo "<td>" . $tour->guide->username . "</td>";
	$users = getAttendees ( $pdo, $tour->id );
	echo "<td>";
	$joinedTour = false;
	foreach ( $users as $user ) {
		echo $user ['username'] . " ";
		if ($user ['id'] == $authuserid) {
			$joinedTour = true;
		}
	}
	echo "</td>";
	echo '<td>';
	if ($tour->guide->id == $authuserid) {
		// edit
		if (! $tour->canceled) {
			echo '<form action="" method="post"><input type="hidden" name="action" value="tour-edit"><input type="hidden" name="tourid" value="' . $tour->id . '"><input type="submit" value="Edit"/></form>';
			echo '<form action="" method="post"><input type="hidden" name="action" value="tour-cancel"><input type="hidden" name="tourid" value="' . $tour->id . '"><input type="submit" value="Absagen"/></form>';
		}
	} else {
		// Join/leave
		if (! $tour->canceled) {
			$tooltipdate = getWeekDay ( $startdate ) . ', ' . $startdate->format ( 'd.m.Y H:i' );
			if ($joinedTour) {
				$tooltip = 'Mich bei der Tour von ' . $tour->guide->username . ' am ' . $tooltipdate . ' abmelden';
				echo '<form action="" method="post"><input type="hidden" name="action" value="tour-leave"><input type="hidden" name="tourid" value="' . $tour->id . '"><input type="submit" id="tour-leave" value="-" title="' . $tooltip . '"/></form>';
			} else {
				$tooltip = 'Mich bei der Tour von ' . $tour->guide->username . ' am ' . $tooltipdate . ' anmelden';
				echo '<form action="" method="post"><input type="hidden" name="action" value="tour-join"><input type="hidden" name="tourid" value="' . $tour->id . '"><input type="submit" id="tour-join" value="+" title="' . $tooltip . '"/></form>';
			}
		}
	}
	echo "<td></tr>";
	$lastdate = $startdate;
}
?>
</table>