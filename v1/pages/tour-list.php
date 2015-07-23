<?php
require_once __DIR__ . '/../lib/global.php';
require_once __DIR__ . '/../lib/tours.php';

if (hasAuth ()) {
	// new tour
	echo '<form action="" method="post"><input type="hidden" name="action" value="tour-new"/><input type="submit" value="Neue Tour"/></form>';
	
	// show actual/old tours
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
	
	// show hidden tours
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
	
	// show RSS-feed
	echo '<a href="rss/">Subscribe to RSS-feed</a>';
}
?>
<?php

?>
<table style='width: 100%; table-layout: fixed; text-align: right;'>
	<tr>
		<th style='width: 3em;'>Tag<br>Sport
		</th>
		<th style='width: 6em;'>Datum</th>
		<?php
		if (hasAuth ()) {
			echo "<th style='width: 6em;'>Guide</th>";
			echo "<th style='width: 15%;'>Treffpunkt</th>";
		}
		?>
		<th style='width: 25%;'>Beschreibung</th>
		<th style='text-align: right; width: 6em;'>Dauer<br>hh:mm
		</th>
		<th style='text-align: right; width: 6em;'>Distanz<br>km
		</th>
		<th style='text-align: right; width: 6em;'>Bergauf<br>Hm
		</th>
		<th>Pace</th>
		<th>Technik</th>
		<?php
		if (hasAuth ()) {
			echo '<th>Teilnehmer</th>';
			echo "<th></th>";
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

$daystyle = 'evenFirst';
while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
	// print_r($row);
	$tour = getTourObject ( $row );
	$joinedTour = false;
	$attendeeString = '';
	$authuserid = hasAuth () ? authUser ()->id : '';
	$users = getAttendees ( $pdo, $tour->id );
	foreach ( $users as $user ) {
		$u = new User ();
		$u->id = $user ['id'];
		$u->email = $user ['email'];
		$u->username = $user ['username'];
		$ue = getUserExtraById ( $pdo, $u->id );
		
		$attendeeString = $attendeeString . '<span title="' . createUserInfo ( $u, $userextra ) . '">' . createUserProfilLink ( $u ) . "</span>" . " ";
		if ($user ['id'] == $authuserid) {
			$joinedTour = true;
		}
	}
	// echo '<tr class="' . ($tour->canceled ? 'canceled' : ($joinedTour || ($tour->guide->id == $authuserid) ? 'joined' : 'notjoined')) . '">';
	$startdate = $tour->startDateTime;
	if (! isset ( $lastdate ) || $lastdate->format ( 'ymd' ) != $startdate->format ( 'ymd' )) {
		if ($daystyle == 'evenFirst' || $daystyle == 'even') {
			$daystyle = 'oddFirst';
		} else if ($daystyle == 'oddFirst' || $daystyle == 'odd') {
			$daystyle = 'evenFirst';
		}
	} else {
		if ($daystyle == 'evenFirst') {
			$daystyle = 'even';
		}
		if ($daystyle == 'oddFirst') {
			$daystyle = 'odd';
		}
	}
	echo '<tr class="' . $daystyle . '">';
	
	if (isset ( $lastdate ) && $lastdate->format ( 'ymd' ) == $startdate->format ( 'ymd' )) {
		echo "<td  style='text-align: left; '>";
		echo makeSportSubnameIconTag ( $tour->sport->sportsubname ) . "</td>";
		echo "<td>" . $startdate->format ( 'H:i' ) . "</td>";
	} else {
		echo "<td style='text-align: left;'>" . getWeekDay ( $startdate ) . "<br>" . makeSportSubnameIconTag ( $tour->sport->sportsubname ) . "</td>";
		echo "<td>" . $startdate->format ( 'd.m.Y H:i' ) . "</td>";
	}
	
	if (hasAuth ()) {
		// guide
		$u = new User();
		$u->id = $tour->guide->id;
		$u->email =$tour->guide->email;
		$u->username =$tour->guide->username;
		$ue = getUserExtraById ( $pdo, $u->id );
		
		echo "<td>" .'<span title="' . createUserInfo( $u, $ue) . '">' . createUserProfilLink($u) . "</span>". "</td>";
		$meetingpoint_short = ! empty ( $tour->meetingPoint_desc ) ? $tour->meetingPoint_desc : $tour->meetingPoint;
		$meetingpoint_long = $tour->meetingPoint;
		
		if (isset ( $row ['refm'] )) {
			$dist = ", Tourstart ist in " . formatMeters ( $row ['refm'] ) . " Entfernung";
		} else {
			$dist = "";
		}
		echo "<td style='text-align: left;' title='" . htmlentities ( $meetingpoint_long . $dist ) . "'" . "><span>" . "<a style ='display: block;' href='?action=tour-view&tourid=" . $tour->id . "'><span>" . htmlentities ( $meetingpoint_short ) . "</a>" . "</span></td>";
	}
	// http://jsfiddle.net/qEsQf/ multi line ellipses
	echo "<td style='text-align: left;'><span>" . htmlentities ( $tour->description ) . "</span></td>";
	echo "<td>" . formatMinutes ( $tour->duration ) . "</td>";
	echo "<td>" . formatMeters ( $tour->distance ) . "</td>";
	echo "<td>" . formatMeters ( $tour->elevation ) . "</td>";
	echo "<td>" . getStars ( $tour->speed, 'speed' . $tour->id ) . "</td>";
	echo "<td>" . getStars ( $tour->skill, 'skill' . $tour->id ) . "</td>";
	
	if (hasAuth ()) {
		// attendees
		echo "<td style='text-align: left;'>" . $attendeeString . '</td>';
		// functions
		echo "<td style='text-align: left;'>";
		if ($tour->startDateTime >= new DateTime ()) {
			if (($tour->guide->id == $authuserid)) {
				// edit
				if (! $tour->canceled) {
					$tooltipdate = getWeekDay ( $startdate ) . ', ' . $startdate->format ( 'd.m.Y H:i' );
					$tooltip = 'Die Tour  am ' . $tooltipdate . ' bearbeiten';
					echo '<form action="" method="post"><input type="hidden" name="action" value="tour-edit"><input type="hidden" name="tourid" value="' . $tour->id . '"><input type="submit"  id="tour-edit" value="" title="' . $tooltip . '"/></form>';
					$tooltip = 'Die Tour  am ' . $tooltipdate . ' absagen';
					echo '<form action="" method="post"><input type="hidden" name="action" value="tour-cancel"><input type="hidden" name="tourid" value="' . $tour->id . '"><input type="submit"  id="tour-cancel" value="" title="' . $tooltip . '"/></form>';
				}
			} else {
				// Join/leave
				if (! $tour->canceled) {
					$tooltipdate = getWeekDay ( $startdate ) . ', ' . $startdate->format ( 'd.m.Y H:i' );
					if ($joinedTour) {
						$tooltip = 'Mich bei der Tour von ' . $tour->guide->username . ' am ' . $tooltipdate . ' abmelden';
						echo '<form action="" method="post"><input type="hidden" name="action" value="tour-leave"><input type="hidden" name="tourid" value="' . $tour->id . '"><input type="submit" id="tour-leave" value="" title="' . $tooltip . '"/></form>';
					} else {
						$tooltip = 'Mich bei der Tour von ' . $tour->guide->username . ' am ' . $tooltipdate . ' anmelden';
						echo '<form action="" method="post"><input type="hidden" name="action" value="tour-join"><input type="hidden" name="tourid" value="' . $tour->id . '"><input type="submit" id="tour-join" value="" title="' . $tooltip . '"/></form>';
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
function makeSportSubnameIconTag($sportsubname) {
	// (1, 1, 'MTB'),
	// (3, 1, 'Rennrad'),
	// (4, 1, 'Crosser'),
	// (5, 2, 'Lauf'),
	// (6, 2, 'Trail-Running'),
	// (7, 3, 'Schwimmen'),
	// (8, 4, 'Triathlon'),
	// (9, 4, 'Duathlon'),
	// (10, 4, 'Swim & Bike'),
	// (11, 5, 'Langlauf');
	switch ($sportsubname) {
		case 'MTB' :
			$icon = "mtb";
			break;
		case 'Rennrad' :
			$icon = "rr";
			break;
		case 'Crosser' :
			$icon = "crosser";
			break;
		case 'Lauf' :
			$icon = "run";
			break;
		case 'Trail-Running' :
			$icon = "trailrun";
			break;
		case 'Schwimmen' :
			$icon = "swim";
			break;
		case 'Triathlon' :
			$icon = "3lon";
			break;
		case 'Duathlon' :
			$icon = "2lon";
			break;
		case 'Swim & Bike' :
			$icon = "edit";
			break;
		case 'Langlauf' :
			$icon = "ski";
			break;
		default :
			$icon = "mtb";
	}
	
	return ("<img src='img/big/" . $icon . ".png' align='middle' border='0' height='40px' width='40px'>");
}
?>
</table>