<?php
require_once __DIR__ . '/restlib.php';
jsonCheckAuth ();

if ($_SERVER ['REQUEST_METHOD'] != 'POST') {
	exit ();
}

header ( 'Content-type: application/json' );
$pdo = db_open ();

$json = file_get_contents ( 'php://input' );
$body = json_decode ( $json, true );

$tours = $body ['tours'];
$tour_list = array ();
foreach ( $tours as $tour ) {
	// print_r($tour);
	$tourDate = DateTime::createFromFormat ( DateTime::ISO8601, $tour ['datetime'] );
	// check if tour already exists
	// is there an ID ?
	$isNew = true;
	if (array_key_exists ( 'id', $tour )) {
		$dbtour = getDBTourById ( $pdo, $tour ['id'] );
		if (! $dbtour) {
			http_response_code ( 400 );
			exit ();
		}
		$isNew = false;
	} else {
		// guide and start date
		$dbtour = getDBTourByGuideAndDate ( $pdo, authUser ()->id, $tourDate );
		if (! $dbtour) {
			$dbtour = new DBTour ();
			$isNew = true;
		} else {
			$isNew = false;
		}
	}
	$dbtour->guide = authUser ();
	$dbtour->description = $tour ['desc'];
	$dbtour->duration = $tour ['duration'];
	$dbtour->sport = new DBSport ();
	$dbtour->sport->sportsubid = $tour ['sport'] ['id'];
	$dbtour->meetingPoint = $tour ['meetingpoint'] ['name'];
	$dbtour->meetingPoint_desc = $tour ['meetingpoint'] ['desc'];
	$dbtour->meetingPoint_lat = $tour ['meetingpoint'] ['lat'];
	$dbtour->meetingPoint_long = $tour ['meetingpoint'] ['long'];
	$dbtour->startDateTime = $tourDate;
	$dbtour->skill = $tour ['skill'];
	$dbtour->speed = $tour ['speed'];
	$dbtour->distance = $tour ['distance'];
	$dbtour->elevation = $tour ['elevation'];
	
	if ($isNew) {
		if ($dbtour->id = insertTour ( $pdo, $dbtour )) {
			mailNewTour ( $pdo, $dbtour );
			http_response_code ( 200 );
		} else {
			// most likely duplicate key
			http_response_code ( 400 );
			exit ();
		}
	} else {
		updateTour ( $pdo, $dbtour );
	}
	
	$stmt = getTourStmt ( $pdo, $dbtour->id );
	$row = $stmt->fetch ( PDO::FETCH_ASSOC );
	$tour_list [] = row2tour ( $pdo, $row );
}
$json = array ();
$json ['tours'] = $tour_list;
echo json_encode ( $json );

?>