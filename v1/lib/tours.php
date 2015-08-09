<?php
require_once __DIR__ . '/db_tours.php';
require_once __DIR__ . '/utilities.php';
require_once __DIR__ . '/mail.php';
function getStars($number, $id, $readonly = true) {
	$result = ''; // '<div>';
	$disabled = $readonly ? 'disabled' : '';
	for($i = 1; $i <= 6; $i ++) {
		if ($number == $i) {
			$result = $result . '<input id="' . $id . '" name="' . $id . '" type="radio" value="' . $i . '" class="star" ' . $disabled . ' checked/>';
		} else {
			$result = $result . '<input id="' . $id . '" name="' . $id . '" type="radio" value="' . $i . '" class="star" ' . $disabled . '/>';
		}
	}
	return $result; // . '</div>';
}
function formatMinutes($minutes) {
	// if ($minutes < 60) {
	// return $minutes . 'min';
	// } else {
	// return round ( $minutes / 60, 1 ) . 'h';
	// }
	return sprintf ( "%d", $minutes / 60 ) . ':' . sprintf ( "%02d", $minutes % 60 ) . " h";
}
function formatMeters($meters) {
	if ($meters < 2000) {
		return round ( $meters, 0 ) . ' m';
	} else if ($meters < 5000) {
		return round ( $meters, 0 ) . ' m';
	} else {
		return round ( $meters / 1000, 1 ) . ' km';
	}
}
function getMailText($tour, $attendees = 0) {
	return '<html><body><table>' . //
'<tr><td>Sport:</td><td>' . $tour->sport->sportsubname . '</td></tr>' . //
'<tr><td>Datum/Uhrzeit:</td><td>' . Utilities::getWeekDay ( $tour->startDateTime ) . ', ' . $tour->startDateTime->format ( 'd.m.Y H:i' ) . '</td></tr>' . //
'<tr><td>Treffpunkt:</td><td>' . htmlspecialchars ( $tour->meetingPoint ) . '</td></tr>' . //
($attendees == 0 ? '' : '<tr><td>Aktuell angemeldet:</td><td>' . $attendees . '</td></tr>') . //
'<tr><td>Dauer:</td><td>' . formatMinutes ( $tour->duration ) . '</td></tr>' . //
'<tr><td>Distanz:</td><td>' . formatMeters ( $tour->distance ) . '</td></tr>' . //
'<tr><td>Bergauf:</td><td>' . formatMeters ( $tour->elevation ) . '</td></tr>' . //
'<tr><td>Technik:</td><td>' . $tour->skill . '/6</td></tr>' . //
'<tr><td>Pace:</td><td>' . $tour->speed . '/6</td></tr>' . //
'<tr><td>Beschreibung:</td><td>' . htmlspecialchars ( $tour->description ) . '</td></tr>' . //
'</table>' . //
'<a href="' . getUrlPrefix () . '/index.php?action=tour-list">Touren auflisten</a></body><html>'; //
}
function mailNewTour($pdo, $tour) {
	$subject = $tour->guide->username . ' hat neue Tour eingestellt';
	$text = getMailText ( $tour );
	$stmt = $pdo->prepare ( "select username, email, mailing from user u left join user_extra ue ON (ue.fk_user_id=u.id) WHERE status='verified' ORDER BY username ASC" );
	$stmt->execute ( array () );
	while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
		if (! isset ( $row ['mailing'] ) || $row ['mailing']) {
			sendmail ( $row ['email'], $subject, $text );
		}
	}
}
function mailUpdateTour($pdo, $tour) {
	$subject = $tour->guide->username . ' hat eine Tour aktualisiert';
	$text = getMailText ( $tour );
	$users = getAttendees ( $pdo, $tour->id );
	foreach ( $users as $user ) {
		sendmail ( $user ['email'], $subject, $text );
	}
}
function mailCancelTour($pdo, $tour) {
	$subject = $tour->guide->username . ' hat eine Tour abgesagt';
	$text = getMailText ( $tour );
	$users = getAttendees ( $pdo, $tour->id );
	foreach ( $users as $user ) {
		sendmail ( $user ['email'], $subject, $text );
	}
}

?>
