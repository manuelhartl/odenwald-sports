<table>
	<tr>
		<td>id</td>
		<td>Datum</td>
		<td>Treffpunkt</td>
		<td>Beschreibung</td>
		<td>Guide</td>
		<td>Teilnehmer</td>
		<td><a href="?action=tour-new">Neue Tour</a></td>
	</tr>
<?php
require_once 'lib/global.php';
require_once 'lib/db_tours.php';

$stmt = $pdo->prepare ( "select *,t.id as id, g.id as guide, g.username as guidename from tour t left join user g ON (t.fk_guide_id=g.id) order by startdate DESC" );
$stmt->execute ( array () );

$authuserid = authUser ()->id;
while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
	$tour = getTourObject ( $row );
	echo "<tr>";
	echo "<td>" . $tour->id . "</td>";
	echo "<td>" . $tour->startDateTime . "</td>";
	echo "<td>" . $tour->meetingPoint . "</td>";
	echo "<td>" . $tour->description . "</td>";
	echo "<td>" . $tour->guide->username . "</td>";
	$users = getAttendees ( $pdo, $tour->id );
	echo "<td>";
	$joinedTour = false;
	foreach ( $users as $user ) {
		echo $user ['username'] . "<br/>";
		if ($user ['id'] == $authuserid) {
			$joinedTour = true;
		}
	}
	echo "</td>";
	if ($joinedTour) {
		echo '<td><form action="" method="post"><input type="hidden" name="action" value="tour-leave"><input type="hidden" name="tourid" value="' . $tour->id . '"><input type="submit" value="Abmelden"/></form></td>';
	} else {
		echo '<td><form action="" method="post"><input type="hidden" name="action" value="tour-join"><input type="hidden" name="tourid" value="' . $tour->id . '"><input type="submit" value="Anmelden"/></form></td>';
	}
	echo "</tr>";
}
?>
</table>