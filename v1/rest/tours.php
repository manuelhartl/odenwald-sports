<?php
require_once __DIR__ . '/../lib/global.php';
require_once __DIR__ . '/../lib/tours.php';

if (!hasAuth()) {
	echo "not logged in";
	exit;
}

if ($_SERVER['REQUEST_METHOD'] != "GET"){
	exit;
}

$pdo = db_open();

$showold = isset ($_GET ["showold"]) ? $_GET ["showold"] : "false";
$showcanceled = isset ( $_GET ["showcanceled"] ) ? $_GET ["showcanceled"] : "false";

$reference = getDBPlaceById ( $pdo, 1 );
if (hasAuth ()) {
	$userextra = getDBUserExtraById ( $pdo, authUser ()->id );
	if (isset ( $userextra->address_lat )) {
		$reference->gps->lat = $userextra->address_lat;
		$reference->gps->long = $userextra->address_long;
	}
}

$stmt = $pdo->prepare ( 'select *,111195 * ST_Distance(POINT(?,?), meetingpoint_coord) as refm, t.status as tourstatus,t.id as id, g.id as guide, g.username as guidename' . //
',X(meetingpoint_coord) as meetingpoint_lat,Y(meetingpoint_coord) as meetingpoint_long' . // 
' from tour t' . //
' left join user g ON (t.fk_guide_id=g.id) ' . //
' left join sport_subtype ss ON (t.fk_sport_subtype_id=ss.id) ' . //
' left join sport s ON (ss.fk_sport_id=s.id) ' . //
' WHERE true ' . //
(' ORDER BY startdate ASC') . //
'' ); //
ex2er ( $stmt, array (
		$reference->gps->lat,
		$reference->gps->long
) );

$tour_list = array();
while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
	unset($tour);
	// build tour

	$tour['id']=$row ['id'];
	$tour['desc']=$row ['description'];
	$tour['datetime'] = date_create ( $row ['startdate'] )->format(DateTime::ISO8601);
	$tour['distance'] = $row['distance'];
	$tour['speed'] = $row ['speed'];
	$tour['duration'] = $row ['duration'];
	$tour['elevation'] = $row ['elevation'];
	$tour['skill'] = $row ['skill'];
	$tour['canceled']  = $row ['tourstatus'] == 'canceled';
	$tour['sport']['name'] = $row ['sportname'];
	$tour['sport']['subname'] = $row ['sportsubname'];
	$tour['meetingpoint']['name'] = $row ['meetingpoint'];
	$tour['meetingpoint']['desc'] = $row ['meetingpoint_desc'];
	if (isset ( $row ['meetingpoint_lat'] )) {
		$tour['meetingpoint']['lat'] =  $row ['meetingpoint_lat'];
	}
	if (isset ( $row ['meetingpoint_long'] )) {
		$tour['meetingpoint']['long'] = $row ['meetingpoint_long'];
	}
	$tour['guide']['id']=$row ['guide'];
	$tour['guide']['name']= $row ['guidename'];

	$users = getAttendees ( $pdo, $row ['id'] );
	$tour['attendees']=array();
	foreach ($users as $user) {
		$attendee = array();
		$attendee['id']=$user['id'];
		$attendee['name']=$user['username'];
		$tour['attendees'][]=$attendee;
	}
	// push to array
	$tour_list[]=$tour;
}
$json['tours']=$tour_list;

header('Content-type: application/json');
echo json_encode($json);
