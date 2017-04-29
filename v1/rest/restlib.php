<?php
require_once __DIR__ . '/../lib/global.php';
require_once __DIR__ . '/../lib/tours.php';
require_once __DIR__ . '/../lib/db_tours.php'; // for dbgps...
function htmlHeader($redirect, $to = 'index.php') {
	?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="refresh" content="<?php echo $redirect.'; url='.$to ?>">
<title>Touren</title>
</head>
<body>
<?php
}
function error($str) {
	header ( 'Content-type: application/json' );
	$json = array ();
	$json ['text'] = $str;
	echo json_encode ( $json );
	http_response_code ( 400 );
	exit ();
}
function jsonCheckAuth() {
	if (! hasAuth ()) {
		$json ['authenticated'] = hasAuth ();
		header ( 'Content-type: application/json' );
		echo json_encode ( $json );
		http_response_code ( 400 );
		exit ();
	}
}
function getTourLastModified($pdo) {
	$stmt = $pdo->prepare ( 'select modifydate from tour ORDER BY modifydate DESC LIMIT 1' );
	ex2er ( $stmt, array () );
	$row = $stmt->fetch ( PDO::FETCH_ASSOC );
	return $row ['modifydate'];
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
	
	$stmt = $pdo->prepare ( //
'select t.id,t.fk_guide_id,t.fk_guide_id,t.fk_sport_subtype_id,t.startdate,t.duration,t.meetingpoint,t.meetingpoint_desc,t.description,t.status,t.skill,t.speed,t.distance,t.elevation,t.bringlight' . //
',s.sportname,ss.sportsubname' . //
',111195 * ST_Distance(POINT(?,?), meetingpoint_coord) as refm, t.status as tourstatus,t.id as id, g.id as guide, g.username as guidename' . //
',X(meetingpoint_coord) as meetingpoint_lat,Y(meetingpoint_coord) as meetingpoint_long, ss.id as sportid, bringlight' . //
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
function getUserStmt($pdo, $id = -1) {
	$reference = new DBGps ();
	$hasAdress = false;
	$userextra = getDBUserExtraById ( $pdo, authUser ()->id );
	if (isset ( $userextra->address_lat )) {
		$hasAdress = true;
		$reference->lat = $userextra->address_lat;
		$reference->long = $userextra->address_long;
	}
	
	$stmt = $pdo->prepare ( //
'select username,id,register_date,realname,birthdate,fk_user_id,' . //
'111195 * ST_Distance(POINT(?,?), address_coord) as dist, address, X(address_coord) as address_lat,Y(address_coord) as address_long,' . //
'mailing, phone ' . //
' from user u' . //
' left join user_extra ue ON (ue.fk_user_id=u.id) ' . //
' WHERE status = "verified"' . //
' ORDER BY lower(u.username) ASC' ); //
	ex2er ( $stmt, array (
			$reference->lat,
			$reference->long 
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
	$tour ['bringlight'] = $row ['bringlight'] ? 'true' : 'false';
	;
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
function row2user($pdo, $row) {
	$user ['id'] = $row ['id'];
	$user ['username'] = $row ['username'];
	$user ['registerdate'] = date_create ( $row ['register_date'] )->format ( DateTime::ISO8601 );
	if (isset ( $row ['realname'] )) {
		$user ['realname'] = $row ['realname'];
	}
	if (isset ( $row ['birthdate'] )) {
		$user ['birthdate'] = date_create ( $row ['birthdate'] )->format ( DateTime::ISO8601 );
	}
	if (isset ( $row ['dist'] )) {
		$user ['distance'] = $row ['dist'];
	}
	if (isset ( $row ['address'] )) {
		$user ['address'] = $row ['address'];
	}
	if (isset ( $row ['address_lat'] )) {
		$user ['address_gps'] ['lat'] = $row ['address_lat'];
		$user ['address_gps'] ['long'] = $row ['address_long'];
	}
	if (isset ( $row ['phone'] )) {
		$user ['phone'] = $row ['phone'];
	}
	if (isset ( $row ['mailing'] )) {
		$user ['mailing'] = $row ['mailing'] ? 'true' : 'false';
	}
	return $user;
}
?>
