<?php
require_once __DIR__ . '/db_tours.php';
require_once __DIR__ . '/mail.php';
function getStars($number, $id, $readonly = true) {
	$result = ''; //'<div>';
	$disabled = $readonly ? 'disabled' : '';
	for($i = 1; $i <= 5; $i ++) {
		if ($number == $i) {
			$result = $result . '<input name="' . $id . '" type="radio" value="' . $i . '" class="star" ' . $disabled . ' checked/>';
		} else {
			$result = $result . '<input name="' . $id . '" type="radio" value="' . $i . '" class="star" ' . $disabled . '/>';
		}
	}
	return $result; // . '</div>';
}
function formatMinutes($minutes) {
	if ($minutes < 60) {
		return $minutes . 'min';
	} else {
		return round ( $minutes / 60, 1 ) . 'h';
	}
}
function formatMeters($meters) {
	if ($meters < 2000) {
		return round ( $meters, 0 ) . 'm';
	} else if ($meters < 5000) {
		return round ( $meters, 0 ) . 'm';
	} else {
		return round ( $meters / 1000, 1 ) . 'km';
	}
}
function getMailText($tour) {
	return '<html><body><table>' . //
'<tr><td>Sport:</td><td>' . $tour->sport->sportsubname . '</td>' . //
'<tr><td>Datum/Uhrzeit:</td><td>' . $tour->startDateTime->format ( 'd.m.Y H:i' ) . '</td>' . //
'<tr><td>Treffpunkt:</td><td>' . $tour->meetingPoint . '</td>' . //
'<tr><td>Beschreibung:</td><td>' . $tour->description . '</td>' . //
'<tr><td>Dauer:</td><td>' . formatMinutes ( $tour->duration ) . '</td>' . //
'</table>' . //
'<a href="' . getUrlPrefix () . '/index.php?action=tour-list">Touren auflisten</a></body><html>'; //
}
function mailNewTour($pdo, $tour) {
	$subject = $tour->guide->username . ' hat neue Tour eingestellt';
	$text = getMailText ( $tour );
	$stmt = $pdo->prepare ( "select username,email from user WHERE status='verified' ORDER BY username ASC" );
	$stmt->execute ( array () );
	while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
		sendmail ( $row ['email'], $subject, $text );
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
