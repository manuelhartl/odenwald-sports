<?php
require_once __DIR__ . '/restlib.php';
function getRestMailText(DBTour $tour, $attendees = 0) {
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
'<a href="' . getUrlPrefix () . '/../ko/pages/tour-list.html?action=tour-join&tourid=' . $tour->id . '">Mich bei dieser Tour anmelden</a><br/>' . //
'<a href="' . getUrlPrefix () . '/../ko/pages/tour-list.html?action=tour-view&tourid=' . $tour->id . '">Diese Tour anzeigen</a><br/>' . //
'<a href="' . getUrlPrefix () . '/../ko/pages/tour-list.html?action=tour-list">Touren auflisten</a>' . //
'</body><html>'; //
}
function restMailNewTour($pdo, $tour) {
	$subject = $tour->guide->username . ' hat neue Tour eingestellt';
	$text = getRestMailText ( $tour );
	$stmt = $pdo->prepare ( "select username, email, mailing from user u left join user_extra ue ON (ue.fk_user_id=u.id) WHERE status='verified' ORDER BY username ASC" );
	$stmt->execute ( array () );
	while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
		if (! isset ( $row ['mailing'] ) || $row ['mailing']) {
			sendmail ( $row ['email'], $subject, $text, false );
		}
	}
}
function restMailUpdateTour($pdo, $tour) {
	$subject = $tour->guide->username . ' hat eine Tour aktualisiert';
	$text = getRestMailText ( $tour );
	$users = getAttendees ( $pdo, $tour->id );
	foreach ( $users as $user ) {
		sendmail ( $user ['email'], $subject, $text, false );
	}
}
function restMailCancelTour($pdo, $tour) {
	$subject = $tour->guide->username . ' hat eine Tour abgesagt';
	$text = getRestMailText ( $tour );
	$users = getAttendees ( $pdo, $tour->id );
	foreach ( $users as $user ) {
		sendmail ( $user ['email'], $subject, $text, false );
	}
}

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
		if ($dbtour->guide->id != authUser ()->id) {
			http_response_code ( 401 );
			exit ();
		}
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
	$dbtour->bringlight = $tour ['bringlight'] == 'true';
	
	if ($isNew) {
		if ($dbtour->id = insertTour ( $pdo, $dbtour )) {
			restMailNewTour ( $pdo, $dbtour );
			http_response_code ( 200 );
		} else {
			// most likely duplicate key
			http_response_code ( 400 );
			exit ();
		}
	} else {
		if (! updateTour ( $pdo, $dbtour )) {
			http_response_code ( 500 );
			exit ();
		} else {
			restMailUpdateTour ( $pdo, $dbtour );
		}
	}
	
	$stmt = getTourStmt ( $pdo, $dbtour->id );
	$row = $stmt->fetch ( PDO::FETCH_ASSOC );
	$tour_list [] = row2tour ( $pdo, $row );
}
$json = array ();
$json ['tours'] = $tour_list;
echo json_encode ( $json );

?>