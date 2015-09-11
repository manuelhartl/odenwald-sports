<?php
require_once __DIR__ . '/db.php';
class DBTour {
	public $id;
	public $startDateTime;
	public $duration;
	public $sport;
	public $meetingPoint;
	public $meetingPoint_desc;
	public $description;
	public /*User */$guide;
	public $attendees;
	public $canceled;
	public $meetingPoint_lat;
	public $meetingPoint_long;
	public $elevation;
	public $distance;
	public $skill;
	public $speed;
	public $register_date;
	public $modify_date;
}
class DBGps {
	public $lat;
	public $long;
}
class DBPlace {
	public $id;
	public $name;
	public $gps; // Gps
}
class DBSport {
	public $sportsubid;
	public $sportsubname;
	public $sportname;
}
function getDBTour($row) {
	$tourObj = new DBTour ();
	$tourObj->id = $row ['id'];
	$tourObj->startDateTime = date_create ( $row ['startdate'] );
	$tourObj->duration = $row ['duration'];
	$tourObj->sport = new DBSport ();
	$tourObj->sport->sportname = $row ['sportname'];
	// $tourObj->sport->sportsubid = $row ['sportsubid'];
	$tourObj->sport->sportsubname = $row ['sportsubname'];
	$tourObj->meetingPoint = $row ['meetingpoint'];
	$tourObj->meetingPoint_desc = $row ['meetingpoint_desc'];
	if (isset ( $row ['meetingpoint_lat'] )) {
		$tourObj->meetingPoint_lat = $row ['meetingpoint_lat'];
	}
	if (isset ( $row ['meetingpoint_long'] )) {
		$tourObj->meetingPoint_long = $row ['meetingpoint_long'];
	}
	$tourObj->description = $row ['description'];
	$tourObj->guide = new DBUser ();
	$tourObj->guide->id = $row ['guide'];
	$tourObj->guide->username = $row ['guidename'];
	$tourObj->guide->email = $row ['email'];
	$tourObj->attendees = array ();
	array_push ( $tourObj->attendees, new DBUser () );
	$tourObj->canceled = ($row ['tourstatus'] == 'canceled');
	$tourObj->distance = $row ['distance'];
	$tourObj->elevation = $row ['elevation'];
	$tourObj->speed = $row ['speed'];
	$tourObj->skill = $row ['skill'];
	$tourObj->register_date = $row ['register_date'];
	if (isset ( $row ['modify_date'] )) {
		$tourObj->modify_date = $row ['modify_date'];
	}
	
	return $tourObj;
}
function getDBPlace($row) {
	$place = new DBPlace ();
	$place->id = $row ['id'];
	$place->name = $row ['name'];
	$place->gps = new DBGps ();
	$place->gps->lat = $row ['lat'];
	$place->gps->long = $row ['lon'];
	return $place;
}
function insertTour($pdo, DBTour $tour) {
	$stmt = $pdo->prepare ( "insert into tour (fk_guide_id,startdate,duration,meetingpoint,description, meetingpoint_coord,fk_sport_subtype_id, skill, speed, distance, elevation, meetingpoint_desc) VALUES(?,?,?,?,?,PointFromText(?),?,?,?,?,?,?)" );
	$stmt->bindParam ( 1, $tour->guide->id );
	$date = toDbmsDate ( $tour->startDateTime );
	$stmt->bindParam ( 2, $date );
	$stmt->bindParam ( 3, $tour->duration );
	$stmt->bindParam ( 4, $tour->meetingPoint );
	$stmt->bindParam ( 5, $tour->description );
	$point = 'POINT(' . $tour->meetingPoint_lat . " " . $tour->meetingPoint_long . ')';
	$stmt->bindParam ( 6, $point );
	$stmt->bindParam ( 7, $tour->sport->sportsubid );
	$stmt->bindParam ( 8, $tour->skill );
	$stmt->bindParam ( 9, $tour->speed );
	$stmt->bindParam ( 10, $tour->distance );
	$stmt->bindParam ( 11, $tour->elevation );
	$stmt->bindParam ( 12, $tour->meetingPoint_desc );
	if (! ex2er ( $stmt )) {
		return false;
	}
	return true;
}
function updateTour($pdo, DBTour $tour) {
	$stmt = $pdo->prepare ( "update tour set startdate = ?, duration=?, meetingpoint=?, meetingpoint_desc=? , meetingpoint_coord = PointFromText(?), description=?, skill = ? , speed = ? , distance = ?, elevation = ? where id=? and status='active'" );
	$date = toDbmsDate ( $tour->startDateTime );
	$point = 'POINT(' . $tour->meetingPoint_lat . " " . $tour->meetingPoint_long . ')';
	if (! ex2er ( $stmt, array (
			$date,
			$tour->duration,
			$tour->meetingPoint,
			$tour->meetingPoint_desc,
			$point,
			$tour->description,
			$tour->skill,
			$tour->speed,
			$tour->distance,
			$tour->elevation,
			$tour->id 
	) )) {
		return false;
	}
	return true;
}
function getDBTourById($pdo, $tourid) {
	$stmt = $pdo->prepare ( //
'select *,sportname,ss.sportsubname as sportsubname, ss.id as sportsubid, X(meetingpoint_coord) as meetingpoint_lat,Y(meetingpoint_coord) as meetingpoint_long,t.status as tourstatus, t.id as id, g.id as guide, g.username as guidename' . //
'register_date, modify_date' . //
' from tour t left join user g ON (t.fk_guide_id=g.id)' . //
' left join sport_subtype ss ON (t.fk_sport_subtype_id=ss.id) ' . //
' left join sport s ON (ss.fk_sport_id=s.id) ' . //
' where t.id=?' );
	$stmt->execute ( array (
			$tourid 
	) );
	return getDBTour ( $stmt->fetch ( PDO::FETCH_ASSOC ) );
}
function getCompleteTourlist($pdo) {
	// $stmt = $pdo->prepare ( 'select id,register_date,modify_date from tour ' );
	$stmt = $pdo->prepare ( 'select id, adddate as register_date, modifydate as modify_date from tour ' );
	ex2er ( $stmt, array () );
	return ($stmt->fetchAll ());
}
function getDBPlaceById($pdo, $id) {
	$stmt = $pdo->prepare ( "select id,name,X(coord) as lat ,Y(coord) as lon from place where id=?" );
	$stmt->execute ( array (
			$id 
	) );
	return getDBPlace ( $stmt->fetch ( PDO::FETCH_ASSOC ) );
}
function getPlaces($pdo) {
	$stmt = $pdo->prepare ( "SELECT id,name,X(coord) as lat,Y(coord) lon FROM place ORDER BY name ASC" );
	if (! ex2er ( $stmt, array () )) {
		return false;
	}
	return $stmt->fetchAll ( PDO::FETCH_OBJ );
}
function getAttendees($pdo, $tourid) {
	$stmt = $pdo->prepare ( "select user.id as id, user.username as username, user.email as email from tour_attendee ta left join user ON (ta.fk_user_id=user.id) where ta.fk_tour_id = ? ORDER BY ta.adddate ASC" );
	if (! ex2er ( $stmt, array (
			$tourid 
	) )) {
		return false;
	}
	return $stmt->fetchAll ( PDO::FETCH_ASSOC );
}
function getSports($pdo) {
	$stmt = $pdo->prepare ( "select sportname,sport_subtype.sportsubname as sportsubname, sport_subtype.id as sportsubid from sport_subtype left join sport ON (fk_sport_id=sport.id) ORDER BY sportname ASC, sportsubname ASC" );
	if (! ex2er ( $stmt, array () )) {
		return false;
	}
	return $stmt->fetchAll ( PDO::FETCH_OBJ );
}
function getSport($pdo, $id) {
	$stmt = $pdo->prepare ( "select sportname,sport_subtype.sportsubname as sportsubname, sport_subtype.id as sportsubid from sport_subtype left join sport ON (fk_sport_id=sport.id) and (sport_subtype.id = ?)" );
	if (! ex2er ( $stmt, array (
			$id 
	) )) {
		return false;
	}
	return $stmt->fetch ( PDO::FETCH_OBJ );
}
function tourJoin($pdo, $userid, $tourid) {
	$stmt = $pdo->prepare ( "insert into tour_attendee (fk_user_id,fk_tour_id) VALUES(?,?)" );
	if (! ex2er ( $stmt, array (
			$userid,
			$tourid 
	) )) {
		return false;
	}
	return true;
}
function tourLeave($pdo, $userid, $tourid) {
	$stmt = $pdo->prepare ( "delete from tour_attendee where fk_user_id = ? and fk_tour_id = ?" );
	if (! ex2er ( $stmt, array (
			$userid,
			$tourid 
	) )) {
		return false;
	}
	return true;
}
function tourCancel($pdo, $tourid) {
	$stmt = $pdo->prepare ( "update tour set status='canceled' where id = ?" );
	if (! ex2er ( $stmt, array (
			$tourid 
	) )) {
		return false;
	}
	return true;
}

?>