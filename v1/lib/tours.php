<?php
require_once 'lib/db_tours.php';
require_once 'lib/mail.php';
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
	$text = '<table>' . //
'<tr><td>Wann ? </td><td>' . $tour->startDateTime . '</td>' . //
'<tr><td>Wo ? </td><td>' . $tour->meetingPoint . '</td>' . //
'<tr><td>Was ? </td><td>' . $tour->description . '</td>' . //
'<tr><td>Wie lange ? </td><td>' . formatMinutes ( $tour->duration ) . '</td>' . //
'</table>' . //
'<a href="'.getUrlPrefix() . '/index.php?action=tour-list">Touren auflisten</a>' //
;
	
	$stmt = $pdo->prepare ( "select username,email from user WHERE status='verified' ORDER BY username ASC" );
	$stmt->execute ( array () );
	while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
		sendmail ( $row ['email'], $subject, $text );
	}
}
function mailCancelTour($pdo, $tour) {
	$subject = $tour->guide->username . ' hat eine Tour abgesagt';
	$text = '<table>' . //
'<tr><td>Wann ? </td><td>' . $tour->startDateTime . '</td>' . //
'<tr><td>Wo ? </td><td>' . $tour->meetingPoint . '</td>' . //
'<tr><td>Was ? </td><td>' . $tour->description . '</td>' . //
'<tr><td>Wie lange ? </td><td>' . formatMinutes ( $tour->duration ) . '</td>' . //
'</table>';
	
	$users = getAttendees ( $pdo, $tour->id );
	foreach ( $users as $user ) {
		sendmail ( $user ['email'], $subject, $text );
	}
}

?>
