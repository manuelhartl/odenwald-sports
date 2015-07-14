<?php
require_once __DIR__ . '/../lib/global.php';
require_once __DIR__ . '/../lib/tours.php';

if (hasAuth ()) {
	echo '<form name="tour-list-update" action="" method="post">';
	echo '<input name="action" type="hidden" value="tour-list-canceled" />';
	echo '<input name="showold" type="hidden" value="' . getInVa ( 'showold' ) . '" />';
	if (getInVa ( 'showcanceled' ) == 'true') {
		echo '<input name="showcanceled" type="hidden" value="false" />';
		echo '<input name="submit-tour-list-update" type="submit" value="Abgesagte Touren verstecken" />';
	} else {
		echo '<input name="showcanceled" type="hidden" value="true" />';
		echo '<input name="submit-tour-list-update" type="submit" value="Abgesagte Touren anzeigen" />';
	}
	echo '</form>';
	echo '<form name="tour-list-update" action="" method="post">';
	echo '<input name="action" type="hidden" value="tour-list-old" />';
	echo '<input name="showcanceled" type="hidden" value="' . getInVa ( 'showold' ) . '" />';
	if (getInVa ( 'showold' ) == 'true') {
		echo '<input name="showold" type="hidden" value="false" />';
		echo '<input name="submit-tour-list-update" type="submit" value="Aktuelle Touren anzeigen" />';
	} else {
		echo '<input name="showold" type="hidden" value="true" />';
		echo '<input name="submit-tour-list-update" type="submit" value="Alte Touren anzeigen" />';
	}
	echo '</form>';
}
?>
<?php

echo '<a href="' . dirname ( get_current_url () ) . '/rss/">Subscribe to RSS-feed</a>';
if (hasAuth ()) {
	echo '<form action="" method="post"><input type="hidden" name="action" value="tour-new"/><input type="submit" value="Neue Tour"/></form>';
}

?>
<table style='width: 100%;'>
	<tr>
		<th></th>
		<th>Datum</th>
		<th>Sport</th>
		<?php
		if (hasAuth ()) {
			echo '<th>Treffpunkt</th>';
			echo '<th>Entfernung zu mir</th>';
		}
		?>
		<th>Beschreibung</th>
		<th style='text-align: right;'>Dauer<br>(hh:mm)
		</th>
		<th style='text-align: right;'>Distanz</th>
		<th style='text-align: right;'>Bergauf</th>
		<th>Pace</th>
		<th>Technik</th>
		<?php
		if (hasAuth ()) {
			echo '<th>Guide</th>';
			echo '<th>Teilnehmer</th>';
			echo '<th></th>';
		} else {
			echo '<th>Teilnehmer</th>';
		}
		?>
	</tr>
<?php
$reference = getPlaceById ( $pdo, 1 );
if (hasAuth ()) {
	$userextra = getUserExtraById ( $pdo, authUser ()->id );
	if (isset ( $userextra->address_lat )) {
		$reference->gps->lat = $userextra->address_lat;
		$reference->gps->long = $userextra->address_long;
	}
}
$stmt = $pdo->prepare ( 'select *,111195 * ST_Distance(POINT(?,?), meetingpoint_coord) as refm, t.status as tourstatus,t.id as id, g.id as guide, g.username as guidename' . //
' from tour t' . //
' left join user g ON (t.fk_guide_id=g.id) ' . //
' left join sport_subtype ss ON (t.fk_sport_subtype_id=ss.id) ' . //
' left join sport s ON (ss.fk_sport_id=s.id) ' . //
' WHERE true ' . //
(! hasAuth () ? ' AND t.status = "active"' : '') . //
(getInVa ( 'showcanceled' ) == 'true' ? '' : ' AND (t.status = "active")') . //
(getInVa ( 'showold' ) == 'true' ? ' AND startdate<now() ORDER BY startdate DESC LIMIT 100' : ' AND startdate>=now() ORDER BY startdate ASC') . //
'' ); //
ex2er ( $stmt, array (
		$reference->gps->lat,
		$reference->gps->long 
) );

while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
	// print_r($row);
	$tour = getTourObject ( $row );
	$joinedTour = false;
	$attendeeString = '';
	$authuserid = hasAuth () ? authUser ()->id : '';
	$users = getAttendees ( $pdo, $tour->id );
	foreach ( $users as $user ) {
		$attendeeString = $attendeeString . $user ['username'] . " ";
		if ($user ['id'] == $authuserid) {
			$joinedTour = true;
		}
	}
	echo '<tr class="' . ($tour->canceled ? 'canceled' : ($joinedTour || ($tour->guide->id == $authuserid) ? 'joined' : 'notjoined')) . '">';
	$startdate = $tour->startDateTime;
	if (isset ( $lastdate ) && $lastdate->format ( 'ymd' ) == $startdate->format ( 'ymd' )) {
		echo '<td></td>';
		echo "<td style='text-align: right;'>" . $startdate->format ( 'H:i' ) . "</td>";
	} else {
		echo "<td>" . getWeekDay ( $startdate ) . "</td>";
		echo "<td style='text-align: right;'>" . $startdate->format ( 'd.m.Y H:i' ) . "</td>";
	}
	
	echo "<td>" . $tour->sport->sportsubname . "</td>";
	if (hasAuth ()) {
		$meetingpoint_short = ! empty ( $tour->meetingPoint_desc ) ? $tour->meetingPoint_desc : $tour->meetingPoint;
		$meetingpoint_long = $tour->meetingPoint;
		// echo '<td class="tooltip">' . $meetingpoint_short . '<span class="info">' . $meetingpoint_long . '</span></td>';
		echo '<td title="' . htmlentities($meetingpoint_long) . '"><a href="?action=tour-view&tourid=' . $tour->id . '">' . htmlentities($meetingpoint_short) . '</a></td>';
		echo "<td style='text-align: right;'>" . formatMeters ( $row ['refm'] ) . "</td>";
	}
	echo "<td style='max-width: 300px; word-wrap: break-word;'>" . nl2br(htmlentities($tour->description)) . "</td>";
	echo "<td style='text-align: right;'>" . formatMinutes ( $tour->duration ) . "</td>";
	echo "<td style='text-align: right;'>" . formatMeters ( $tour->distance ) . "</td>";
	echo "<td style='text-align: right;'>" . formatMeters ( $tour->elevation ) . "</td>";
	echo "<td style='width: 110px;'>" . getStars ( $tour->speed, 'speed' . $tour->id ) . "</td>";
	echo "<td style='width: 110px;'>" . getStars ( $tour->skill, 'skill' . $tour->id ) . "</td>";
	
	if (hasAuth ()) {
		// guide
		echo "<td>" . $tour->guide->username . "</td>";
		// attendees
		echo '<td>' . $attendeeString . '</td>';
		// functions
		echo '<td>';
		if ($tour->startDateTime >= new DateTime ()) {
			if (($tour->guide->id == $authuserid)) {
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
		}
		echo "</td>";
	} else {
		echo "<td>" . count ( $users ) . "</td>";
	}
	echo "</tr>";
	$lastdate = $startdate;
}
?>
</table>