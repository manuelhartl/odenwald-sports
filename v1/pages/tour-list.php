<?php
require_once __DIR__ . '/../lib/global.php';
require_once __DIR__ . '/../lib/tours.php';
require_once __DIR__ . '/../lib/utilities.php';

// get value from session
$showold = isset ( $_SESSION ["showold"] ) ? $_SESSION ["showold"] : "false";
// get value from session
$showcanceled = isset ( $_SESSION ["showcanceled"] ) ? $_SESSION ["showcanceled"] : "false";

if (hasAuth ()) {
	// new DBtour
	echo '<div id="tourbutton">' . PHP_EOL;
	echo '  <form action="" method="post"><input type="hidden" name="action" value="tour-new"/><input type="submit" value="Neue Tour"/></form>';
	echo '</div> <!--End div tourbutton -->' . PHP_EOL;
	// checkbox
	
	echo '<div id="checkbox">' . PHP_EOL;
	echo '  <div id="topcheckbox">' . PHP_EOL;
	echo '    <form name="oldTours" action="" method="post">' . PHP_EOL;
	echo '    <input name="action" type="hidden" value="tour-list-old" />';
	echo '     <fieldset>' . PHP_EOL;
	echo '        <label for="check1">' . PHP_EOL;
	echo '          <input name="cb_oldTours" type="checkbox" value="trcue" onclick="this.form.submit()" id="check1"' . (($showold == 'true') ? ' checked="checked"' : "") . '/>' . PHP_EOL;
	echo '            alte Touren anzeigen' . PHP_EOL;
	echo '        </label>' . PHP_EOL;
	echo '     </fieldset>' . PHP_EOL;
	echo '    </form>' . PHP_EOL;
	echo '  </div> <!--End div topcheckbox -->' . PHP_EOL;
	
	echo '  <div id="bottomcheckbox">' . PHP_EOL;
	echo '    <form name="hiddenTours" action="" method="post">' . PHP_EOL;
	echo '    <input name="action" type="hidden" value="tour-list-canceled" />';
	echo '     <fieldset>' . PHP_EOL;
	echo '        <label for="check2">' . PHP_EOL;
	echo '          <input name="cb_hiddenTours" type="checkbox" value="true" onclick="this.form.submit()" id="check2"' . (($showcanceled == 'true') ? ' checked="checked"' : "") . '/>' . PHP_EOL;
	echo '            abgesagte Touren einblenden' . PHP_EOL;
	echo '        </label>' . PHP_EOL;
	echo '      </fieldset>' . PHP_EOL;
	echo '    </form>' . PHP_EOL;
	echo '  </div> <!--End div bottomcheckbox -->' . PHP_EOL;
	echo '</div> <!--End div checkbox -->' . PHP_EOL;
	
	// show RSS-feed
	echo '<div id="rssbutton">' . PHP_EOL;
	echo '  <a href="rss/">RSS</a>';
	echo '</div> <!--End div rssbutton -->' . PHP_EOL;
}
?>
<?php

?>
<table style='width: 100%; table-layout: fixed; text-align: right;'>
	<tr id="tourheader">
		<th style='width: 4em;'>Sport</th>
		<th style='width: 3em;'>Datum</th>
		<?php
		if (hasAuth ()) {
			echo "<th style='width: 30px;'></th>" . PHP_EOL;
			echo "<th style='width: 7%;'>Guide</th>" . PHP_EOL;
			echo "<th style='width: 15%;'>Treffpunkt</th>" . PHP_EOL;
		}
		?>
		<th style='width: 25%;'>Beschreibung</th>
		<th class='medium' style='text-align: center; width: 5em;'>Dauer</th>
		<th class='medium' style='text-align: center; width: 5em;'>Distanz</th>
		<th class='medium' style='text-align: center; width: 5em;'>Bergauf</th>
		<th class='big'
			style='text-align: center; width: 60px; min-width: 60px;'>Pace</th>
		<th class='big'
			style='text-align: center; width: 60px; min-width: 60px;'>Technik</th>
		<?php
		if (hasAuth ()) {
			echo '<th style="width: 15%;">Teilnehmer</th>' . PHP_EOL;
			echo '<th style="width: 110px;"></th>' . PHP_EOL;
		} else {
			echo '<th>Teilnehmer</th>' . PHP_EOL;
		}
		?>
	</tr>
<?php
$reference = getDBPlaceById ( $pdo, 1 );
if (hasAuth ()) {
	$userextra = getDBUserExtraById ( $pdo, authUser ()->id );
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
($showcanceled == 'true' ? '' : ' AND (t.status = "active")') . //
($showold == 'true' ? ' AND startdate<now() ORDER BY startdate DESC LIMIT 100' : ' AND startdate>=now() ORDER BY startdate ASC') . //
'' ); //
ex2er ( $stmt, array (
		$reference->gps->lat,
		$reference->gps->long 
) );

$daystyle = 'even';
$linestyle = '';
$cancelstyle = '';
$countAttendees = 0;
while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
	// print_r($row);
	$tour = getDBTour ( $row );
	$tourDescription = "Fragen zur Tour am " . $tour->startDateTime->format ( 'd.m.Y H:i' );
	$joinedTour = false;
	$attendeeString = '';
	$authuserid = hasAuth () ? authUser ()->id : '';
	$users = getAttendees ( $pdo, $tour->id );
	$countAttendees = count ( $users );
	foreach ( $users as $user ) {
		// $tourmember = DB_member::getMemberById ( $pdo, $user ['id'] );
		$u = new DBUser ();
		$u->id = $user ['id'];
		$u->email = $user ['email'];
		$u->username = $user ['username'];
		$ue = getDBUserExtraById ( $pdo, $u->id );
		
		$emailString = '<a id="tour-mail1" class="icon-attendee-mail" title="Mail an ' . $u->username . '" href="?action=mail-user&toid=' . $u->id . '&subject=' . $tourDescription . '"> </a>';
		$attendeeString = $attendeeString . '<div class="attendee"><div class="attendee_mail">' . $emailString . '</div><div class="attendee_profil">' . createUserProfilLink ( $u, "style='border: none; line-height: 18px;'", createUserInfo ( $u, $ue ) ) . ", </div></div> ";
		if ($user ['id'] == $authuserid) {
			$joinedTour = true;
		}
	}
	
	$startdate = $tour->startDateTime;
	
	// has day changed?
	if (! isset ( $lastdate ) || $lastdate->format ( 'ymd' ) != $startdate->format ( 'ymd' )) {
		$daystyle = ($daystyle == 'even') ? 'odd' : 'even';
		$linestyle = '';
	} else {
		$linestyle = 'line';
	}
	$cancelstyle = ($tour->canceled) ? 'canceled' : '';
	$dstyle = $daystyle . (strlen ( $linestyle ) > 0 ? ' ' . $linestyle : "") . (strlen ( $cancelstyle ) > 0 ? ' ' . $cancelstyle : "");
	
	$firsttableentry = ! (isset ( $lastdate ) && $lastdate->format ( 'ymd' ) == $startdate->format ( 'ymd' ));
	if ($firsttableentry) {
		echo '<tr class="' . $daystyle . '">', PHP_EOL;
		echo '  <td colspan="' . (hasAuth () ? '13' : '9') . '" class="firstday">' . Utilities::getWeekDay ( $startdate ) . ', ' . $startdate->format ( 'd.m.Y' ) . '</td>', PHP_EOL;
		echo '  </tr>', PHP_EOL;
	}
	echo '<tr class="' . $dstyle . '">', PHP_EOL;
	echo '<td colspan="2">', PHP_EOL;
	echo '<table style="width: 100%; line-height: 1; margin: 0;" >', PHP_EOL;
	echo '  <tr>', PHP_EOL;
	echo '    <td title="' . $tour->sport->sportsubname . '" style="text-align: left;">' . PHP_EOL;
	echo Utilities::makeSportSubnameIconTag ( $tour->sport->sportsubname, $tour->canceled ), PHP_EOL;
	echo '    </td>', PHP_EOL;
	echo '    <td>', PHP_EOL;
	echo '      <table style="width: 100%;" >', PHP_EOL;
	echo '        <tr>', PHP_EOL;
	echo '          <td style="color: black;">' . $startdate->format ( 'H:i' ) . '</td>', PHP_EOL;
	echo '        <tr>', PHP_EOL;
	echo '        </tr>', PHP_EOL;
	echo '          <td style="color: black; font-size: 0.8em;">' . ($countAttendees > 0 ? $countAttendees . " TN" : "") . '</td>', PHP_EOL;
	echo '        </tr>', PHP_EOL;
	echo '      </table>', PHP_EOL;
	echo '    </td>', PHP_EOL;
	echo '  </tr>', PHP_EOL;
	echo '</table>', PHP_EOL;
	echo '</td>', PHP_EOL;
	
	if (hasAuth ()) {
		// guide
		$u = new DBUser ();
		$u->id = $tour->guide->id;
		$u->email = $tour->guide->email;
		$u->username = $tour->guide->username;
		$ue = getDBUserExtraById ( $pdo, $u->id );
		
		$emailString = '<a id="tour-mail" class="icon-guide-mail" title="Mail an ' . $u->username . '" href="?action=mail-user&toid=' . $u->id . '&subject=' . $tourDescription . '"></a>' . PHP_EOL;
		
		echo "<td width='30%' style='text-align: left;'>" . PHP_EOL;
		echo (($tour->guide->id == $authuserid) ? "" : $emailString) . PHP_EOL;
		echo "</td>" . PHP_EOL;
		
		echo "<td width='70%' style='text-align: left;'>" . PHP_EOL;
		echo createUserProfilLink ( $u, 'style = "display: block; line-height: 18px;"', createUserInfo ( $u, $ue ) ) . PHP_EOL;
		echo "</td>" . PHP_EOL;
		
		$meetingpoint_short = ! empty ( $tour->meetingPoint_desc ) ? $tour->meetingPoint_desc : $tour->meetingPoint;
		$meetingpoint_long = $tour->meetingPoint;
		
		if (isset ( $row ['refm'] )) {
			$dist = ", Tourstart ist in " . formatMeters ( $row ['refm'] ) . " Entfernung";
		} else {
			$dist = "";
		}
		echo "<td style='text-align: left;' title='" . htmlentities ( $meetingpoint_long . $dist ) . "'" . ">" . "<a style ='display: block; line-height: 18px;' href='?action=tour-view&tourid=" . $tour->id . "'><span class='flex'>" . Utilities::clearText4Display ( $meetingpoint_short ) . "</a>" . "</span></td>" . PHP_EOL;
	}
	// support HTML Editor
	if (isset ( $config ['HTMLEditor'] ) && $config ['HTMLEditor']) {
		echo "<td style='text-align: left;'><span class='flex1'>" . $tour->description . "</span></td>" . PHP_EOL;
	} else {
		echo "<td style='text-align: left;'><span class='flex'>" . Utilities::clearText4Display ( $tour->description ) . "</span></td>" . PHP_EOL;
	}
	echo "<td class='medium' >" . formatMinutes ( $tour->duration ) . "</td>" . PHP_EOL;
	echo "<td class='medium' >" . formatMeters ( $tour->distance ) . "</td>" . PHP_EOL;
	echo "<td class='medium' >" . formatMeters ( $tour->elevation ) . "</td>" . PHP_EOL;
	echo "<td class='big' >" . getStars ( $tour->speed, 'speed' . $tour->id ) . "</td>" . PHP_EOL;
	echo "<td class='big' >" . getStars ( $tour->skill, 'skill' . $tour->id ) . "</td>" . PHP_EOL;
	
	if (hasAuth ()) {
		// attendees
		echo "<td style='text-align: left;'><span class='attendees, clearfix'>" . $attendeeString . '</span></td>' . PHP_EOL;
		
		// functions
		echo "<td style='text-align: left;'>" . PHP_EOL;
		
		if ($tour->startDateTime >= new DateTime ()) {
			// Mail to all
			$userIDs = strval ( $tour->guide->id );
			$users = getAttendees ( $pdo, $tour->id );
			$countAttendees = count ( $users );
			foreach ( $users as $user ) {
				$userIDs .= "," . $user ['id'];
			}
			echo '<a id="tour-mail" class="icon-tour-mail" title="Mail an alle Mitfahrer und Guide" href="?action=mail-user&toids=' . $userIDs . '&subject=' . "Fragen (an alle) zur Tour am " . $tour->startDateTime->format ( 'd.m.Y H:i' ) . '"></a>' . PHP_EOL;
			
			if ($tour->guide->id == $authuserid) {
				// edit
				if (! $tour->canceled) {
					$tooltipdate = Utilities::getWeekDay ( $startdate ) . ', ' . $startdate->format ( 'd.m.Y H:i' );
					$tooltip = 'Die Tour  am ' . $tooltipdate . ' bearbeiten';
					echo '<form action="" method="post"><input type="hidden" name="action" value="tour-edit"><input type="hidden" name="tourid" value="' . $tour->id . '"><input type="submit" class="icon-tour-edit"  id="tour-edit" value="" title="' . $tooltip . '"/></form>' . PHP_EOL;
					$tooltip = 'Die Tour  am ' . $tooltipdate . ' absagen';
					echo '<form action="" method="post"><input type="hidden"name="action" value="tour-cancel"><input type="hidden" name="tourid" value="' . $tour->id . '"><input type="submit" class="icon-tour-cancel" id="tour-cancel" value="" title="' . $tooltip . '"/></form>' . PHP_EOL;
				}
			} else {
				// Join/leave
				if (! $tour->canceled) {
					$tooltipdate = Utilities::getWeekDay ( $startdate ) . ', ' . $startdate->format ( 'd.m.Y H:i' );
					if ($joinedTour) {
						$tooltip = 'Mich bei der Tour von ' . $tour->guide->username . ' am ' . $tooltipdate . ' abmelden';
						echo '<form action="" method="post"><input type="hidden" name="action" value="tour-leave"><input type="hidden" name="tourid" value="' . $tour->id . '"><input type="submit" id="tour-leave" class="icon-tour-leave" value="" title="' . $tooltip . '"/></form>' . PHP_EOL;
					} else {
						$tooltip = 'Mich bei der Tour von ' . $tour->guide->username . ' am ' . $tooltipdate . ' anmelden';
						echo '<form action="" method="post"><input type="hidden" name="action" value="tour-join"><input type="hidden" name="tourid" value="' . $tour->id . '"><input type="submit" id="tour-join" class="icon-tour-join" value="" title="' . $tooltip . '"/></form>' . PHP_EOL;
					}
				}
			}
		}
		echo "</td>" . PHP_EOL;
	} else {
		echo "<td style='text-align: left;'>" . count ( $users ) . "</td>" . PHP_EOL;
	}
	echo "</tr>" . PHP_EOL;
	$lastdate = $startdate;
}
?>
</table>