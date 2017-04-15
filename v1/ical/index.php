<?php
require_once __DIR__ . '/../lib/global.php';
require_once __DIR__ . '/../lib/tours.php';
function dateToCal($timestamp) {
	return date ( 'Ymd\THis\Z', $timestamp );
}

// Escapes a string of characters
function escapeString($string) {
	return preg_replace ( '/([\,;])/', '\\\$1', $string );
}

header ( "Content-Type: text/calendar; charset=UTF-8" );

$pdo = db_open ();
$stmt = $pdo->prepare ( 'select *,t.status as tourstatus,t.id as id, g.id as guide, g.username as guidename' . //
' from tour t' . //
' left join user g ON (t.fk_guide_id=g.id) ' . //
' left join sport_subtype ss ON (t.fk_sport_subtype_id=ss.id) ' . //
' left join sport s ON (ss.fk_sport_id=s.id) ' . //
' WHERE startdate > now()' . //
' AND t.status = "active"' . //
' order by startdate ASC' ); //
ex2er ( $stmt, array () );

// header
echo "BEGIN:VCALENDAR\r\n";
echo "VERSION:2.0\r\n";
echo "PRODID:sporttogether.de\r\n";
echo "CALSCALE:GREGORIAN\r\n";

while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
	$tour = getDBTour ( $row );
	$users = getAttendees ( $pdo, $tour->id );
	$title = $tour->sport->sportsubname . '-Tour at ' . $tour->meetingPoint . ' ';
	$description = getMailText ( $tour, count ( $users ) );
	echo "BEGIN:VEVENT\r\n";
	echo "UID:" . uniqid () . "\r\n";
	echo "DTSTAMP:" . dateToCal ( time () ) . "\r\n";
	echo "LOCATION:" . escapeString ( $address ) . "\r\n";
	echo "DESCRIPTION:" . escapeString ( "" ) . "\r\n";
	echo "URL;VALUE=URI:" . escapeString ( $uri ) . "\r\n";
	echo "SUMMARY:" . escapeString ( $title ) . "\r\n";
	echo "DTSTART:" . $tour->startDateTime->sub(new DateInterval('PT1H'))->format ( 'Ymd\THis\Z' ) . "\r\n";
	
	// $title = //
	// Utilities::getWeekDay ( $tour->startDateTime ) . ' ' . //
	// $tour->startDateTime->format ( 'd.m.Y H:i' ) . ' ' . //
	// $tour->sport->sportsubname . '-Tour ' . //
	// // 'guided by ' . $tour->guide->username . ' ' . //
	// 'at ' . $tour->meetingPoint . ' '; //
	
	// $item->addChild ( "title", $title );
	// $item->addChild ( "link", dirname ( get_current_url () ) );
	// $item->addChild ( "description", htmlspecialchars ( getMailText ( $tour, count ( $users ) ) ) );
	echo "END:VEVENT\r\n";
}
echo "END:VCALENDAR\r\n";
?>