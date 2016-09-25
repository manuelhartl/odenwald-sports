<?php
require_once __DIR__ . '/../lib/global.php';
require_once __DIR__ . '/../lib/tours.php';
function jsonCheckAuth() {
	if (! hasAuth ()) {
		$json ['authenticated'] = hasAuth ();
		header ( 'Content-type: application/json' );
		echo json_encode ( $json );
		http_response_code ( 400 );
		exit ();
	}
}
function getTourStmt($pdo, $id = -1) {
	$reference = getDBPlaceById ( $pdo, 1 );
	if (hasAuth ()) {
		$userextra = getDBUserExtraById ( $pdo, authUser ()->id );
		if (isset ( $userextra->address_lat )) {
			$reference->gps->lat = $userextra->address_lat;
			$reference->gps->long = $userextra->address_long;
		}
	}
	
	$stmt = $pdo->prepare ( 'select *,111195 * ST_Distance(POINT(?,?), meetingpoint_coord) as refm, t.status as tourstatus,t.id as id, g.id as guide, g.username as guidename' . //
',X(meetingpoint_coord) as meetingpoint_lat,Y(meetingpoint_coord) as meetingpoint_long, ss.id as sportid' . //
' from tour t' . //
' left join user g ON (t.fk_guide_id=g.id) ' . //
' left join sport_subtype ss ON (t.fk_sport_subtype_id=ss.id) ' . //
' left join sport s ON (ss.fk_sport_id=s.id) ' . //
' WHERE true ' . //
($id > - 1 ? ' AND t.id =' . $id : '') . //
(! hasAuth () ? ' AND t.status = "active"' : '') . //
(' ORDER BY startdate ASC') . //
'' ); //
	ex2er ( $stmt, array (
			$reference->gps->lat,
			$reference->gps->long 
	) );
	return $stmt;
}
function row2tour($pdo, $row) {
	if (hasAuth ()) {
		$tour ['id'] = $row ['id'];
		$tour ['canceled'] = $row ['tourstatus'] == 'canceled';
	}
	$tour ['desc'] = $row ['description'];
	$tour ['datetime'] = date_create ( $row ['startdate'] )->format ( DateTime::ISO8601 );
	$tour ['distance'] = $row ['distance'];
	$tour ['speed'] = $row ['speed'];
	$tour ['duration'] = $row ['duration'];
	$tour ['elevation'] = $row ['elevation'];
	$tour ['skill'] = $row ['skill'];
	$tour ['sport'] ['id'] = $row ['sportid'];
	$tour ['sport'] ['name'] = $row ['sportname'];
	$tour ['sport'] ['subname'] = $row ['sportsubname'];
	$users = getAttendees ( $pdo, $row ['id'] );
	$tour ['attendees_count'] = count ( $users );
	if (hasAuth ()) {
		$tour ['meetingpoint'] ['name'] = $row ['meetingpoint'];
		$tour ['meetingpoint'] ['desc'] = $row ['meetingpoint_desc'];
		if (isset ( $row ['meetingpoint_lat'] )) {
			$tour ['meetingpoint'] ['lat'] = $row ['meetingpoint_lat'];
		}
		if (isset ( $row ['meetingpoint_long'] )) {
			$tour ['meetingpoint'] ['long'] = $row ['meetingpoint_long'];
		}
		$tour ['guide'] ['id'] = $row ['guide'];
		$tour ['guide'] ['name'] = $row ['guidename'];
		$tour ['attendees'] = array ();
		foreach ( $users as $user ) {
			$attendee = array ();
			$attendee ['id'] = $user ['id'];
			$attendee ['name'] = $user ['username'];
			$tour ['attendees'] [] = $attendee;
		}
	}
	return $tour;
}
?>
