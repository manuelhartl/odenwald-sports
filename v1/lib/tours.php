<?php
require_once __DIR__ . '/db_tours.php';
require_once __DIR__ . '/mail.php';
function formatMinutes($minutes) {
	if ($minutes < 60) {
		return $minutes . 'min';
	} else {
		return ( int ) ($minutes / 60) . 'h' . ( int ) ($minutes % 60) . 'm';
	}
}
function formatMeters($meters) {
	if ($meters < 1000) {
		return ( int ) $meters . 'm';
	} else {
		return ( int ) ($meters / 1000) . 'km';
	}
}
function mailNewTour($pdo, $tour) {
	$subject = $tour->guide->username . ' hat neue Tour eingestellt';
	$text = '<html><body><table>' . //
'<tr><td>Sport:</td><td>' . $tour->sport->sportsubname . '</td>' . //
'<tr><td>Datum/Uhrzeit:</td><td>' . $tour->startDateTime->format('d.m.Y H:i') . '</td>' . //
'<tr><td>Treffpunkt:</td><td>' . $tour->meetingPoint . '</td>' . //
'<tr><td>Beschreibung:</td><td>' . $tour->description . '</td>' . //
'<tr><td>Dauer:</td><td>' . formatMinutes ( $tour->duration ) . '</td>' . //
'</table>' . //
'<a href="' . getUrlPrefix () . '/index.php?action=tour-list">Touren auflisten</a></body><html>'; //
	
	$stmt = $pdo->prepare ( "select username,email from user WHERE status='verified' ORDER BY username ASC" );
	$stmt->execute ( array () );
	while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
		sendmail ( $row ['email'], $subject, $text );
	}
}
function mailCancelTour($pdo, $tour) {
	$subject = $tour->guide->username . ' hat eine Tour abgesagt';
	$text = '<html><body><table>' . //
'<tr><td>Sport:</td><td>' . $tour->sport->sportsubname . '</td>' . //
'<tr><td>Datum/Uhrzeit:</td><td>' . $tour->startDateTime->format('d.m.Y H:i') . '</td>' . //
'<tr><td>Treffpunkt:</td><td>' . $tour->meetingPoint . '</td>' . //
'<tr><td>Beschreibung:</td><td>' . $tour->description . '</td>' . //
'<tr><td>Dauer:</td><td>' . formatMinutes ( $tour->duration ) . '</td>' . //
'</table>' . //
'<a href="' . getUrlPrefix () . '/index.php?action=tour-list">Touren auflisten</a></body><html>'; //
		
	$users = getAttendees ( $pdo, $tour->id );
	foreach ( $users as $user ) {
		sendmail ( $user ['email'], $subject, $text );
	}
}

?>
