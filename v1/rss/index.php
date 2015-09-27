<?php
require_once __DIR__ . '/../lib/global.php';
require_once __DIR__ . '/../lib/tours.php';
header ( "Content-Type: application/rss+xml; charset=UTF-8" );

$xml = new SimpleXMLElement ( '<rss/>' );
$xml->addAttribute ( "version", "2.0" );
$channel = $xml->addChild ( "channel" );
$channel->addChild ( "title", "Sport2gether" );
$channel->addChild ( "link", dirname ( get_current_url () ) );
$channel->addChild ( "description", "Tour list of Sport2gether" );
$channel->addChild ( "language", "en-us" );

$entries [0] ['title'] = 'title';
$entries [0] ['link'] = 'link';
$entries [0] ['description'] = 'description';

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

while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
	$tour = getDBTour ( $row );
	$users = getAttendees ( $pdo, $tour->id );
	
	$item = $channel->addChild ( "item" );
	
	$title = 	//
	Utilities::getWeekDay ( $tour->startDateTime ) . ' ' . 	//
	$tour->startDateTime->format ( 'd.m.Y H:i' ) . ' ' . 	//
	$tour->sport->sportsubname . '-Tour ' . 	//
	                                        // 'guided by ' . $tour->guide->username . ' ' . //
	'at ' . $tour->meetingPoint . ' '; //
	
	$item->addChild ( "title", $title );
	$item->addChild ( "link", dirname ( get_current_url () ) );
	$item->addChild ( "description", htmlspecialchars ( getMailText ( $tour, count ( $users ) ) ) );
}
echo $xml->asXML ();
?>