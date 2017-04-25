<?php
require_once __DIR__ . '/restlib.php';
jsonCheckAuth ();

if ($_SERVER ['REQUEST_METHOD'] != 'POST') {
	exit ();
}

header ( 'Content-type: application/json' );
$pdo = db_open ();

$body = json_decode ( file_get_contents ( 'php://input' ), true );
$tourid = $body ['id'];

$json = array ();

$tour = getDBTourById ( $pdo, $tourid );

if (! $tour) {
	$json ['error'] ['text'] = 'tourid is unknown';
	http_response_code ( 400 );
} else if ($tour->guide->id == authUser ()->id) {
	$json ['error'] ['text'] = 'you cannot join tour when you are also guide';
	http_response_code ( 400 );
} else {
	if ((! $tour->canceled) && ($tour->startDateTime >= new DateTime ())) {
		if (tourJoin ( $pdo, authUser ()->id, $tourid )) {
			mailJoinTour($pdo, $tour, authUser());
			http_response_code ( 200 );
		} else {
			http_response_code ( 500 );
		}
	}
}

$stmt = getTourStmt ( $pdo, $tourid );
$row = $stmt->fetch ( PDO::FETCH_ASSOC );
$json ['tour'] = row2tour ( $pdo, $row );
echo json_encode ( $json );

?>