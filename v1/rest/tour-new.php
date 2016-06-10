<?php
require_once __DIR__ . '/../lib/global.php';
require_once __DIR__ . '/../lib/tours.php';

if (!hasAuth()) {
	echo "not logged in";
	exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	exit;
}

$pdo = db_open();

$json = file_get_contents('php://input');
$body = json_decode($json,true);

$tours = $body['tours'];

foreach ($tours as $tour) {
	$dbtour = new DBTour ();
	$dbtour->guide = authUser ();
	$dbtour->description = $tour ['desc'];
	$dbtour->duration = $tour ['duration'];
	$dbtour->sport = getSport ( $pdo, $tour ['sport']['id'] );
	$dbtour->meetingPoint = $tour ['meetingpoint']['name'];
	$dbtour->meetingPoint_desc = $tour ['meetingpoint']['desc'];
	$dbtour->meetingPoint_lat = $tour ['meetingpoint']['lat'];
	$dbtour->meetingPoint_long = $tour ['meetingpoint']['long'];
	$dbtour->startDateTime = DateTime::createFromFormat('Y-m-d\TH:i:s+', $tour ['datetime']);
	$dbtour->skill = $tour ['skill'];
	$dbtour->speed = $tour ['speed'];
	$dbtour->distance = $tour ['distance'] * 1000;
	$dbtour->elevation = $tour ['elevation'];
	if ($dbtour->id = insertTour ( $pdo, $dbtour )) {
		mailNewTour ( $pdo, $dbtour );
		http_response_code(200);
	} else {
		// mail admin?
		http_response_code(500);
	}
}

header('Content-type: application/json');
?>