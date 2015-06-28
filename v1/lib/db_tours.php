<?php
require_once 'db.php';
require_once 'db_tours.php';
class Tour {
	public $id;
	public $startDateTime;
	public $duration;
	public $sport;
	public $meetingPoint;
	public $description;
	public /*User */$guide;
	public $attendees;
	public $canceled;
	public $meetingPoint_lat;
	public $meetingPoint_long;
}
class Gps {
	public $lat;
	public $long;
}
class Place {
	public $id;
	public $name;
	public $gps; // Gps
}
class Sport {
	public $sportsubid;
	public $sportsubname;
	public $sportname;
}
function getTourObject($row) {
	$tourObj = new Tour ();
	$tourObj->id = $row ['id'];
	$tourObj->startDateTime = $row ['startdate'];
	$tourObj->duration = $row ['duration'];
	$tourObj->sport = new Sport ();
	$tourObj->sport->sportname = $row ['sportname'];
	// $tourObj->sport->sportsubid = $row ['sportsubid'];
	$tourObj->sport->sportsubname = $row ['sportsubname'];
	$tourObj->meetingPoint = $row ['meetingpoint'];
	if (isset ( $row ['meetingpoint_lat'] )) {
		$tourObj->meetingPoint_lat = $row ['meetingpoint_lat'];
	}
	if (isset ( $row ['meetingpoint_long'] )) {
		$tourObj->meetingPoint_long = $row ['meetingpoint_long'];
	}
	$tourObj->description = $row ['description'];
	$tourObj->guide = new User ();
	$tourObj->guide->id = $row ['guide'];
	$tourObj->guide->username = $row ['guidename'];
	$tourObj->attendees = array ();
	array_push ( $tourObj->attendees, new User () );
	$tourObj->canceled = ($row ['tourstatus'] == 'canceled');
	// print_r($row);
	// print_r($tourObj);
	return $tourObj;
}
function getPlaceObject($row) {
	$place = new Place ();
	$place->id = $row ['id'];
	$place->name = $row ['name'];
	$place->gps = new Gps ();
	$place->gps->lat = $row ['lat'];
	$place->gps->long = $row ['lon'];
	return $place;
}
function insertTour($pdo, Tour $tour) {
	$stmt = $pdo->prepare ( "insert into tour (fk_guide_id,startdate,duration,meetingpoint,description, meetingpoint_coord,fk_sport_subtype_id) VALUES(?,?,?,?,?,PointFromText(?),?)" );
	$stmt->bindParam ( 1, $tour->guide->id );
	$stmt->bindParam ( 2, $tour->startDateTime );
	$stmt->bindParam ( 3, $tour->duration );
	$stmt->bindParam ( 4, $tour->meetingPoint );
	$stmt->bindParam ( 5, $tour->description );
	$point = 'POINT(' . $tour->meetingPoint_lat . " " . $tour->meetingPoint_long . ')';
	$stmt->bindParam ( 6, $point );
	$stmt->bindParam ( 7, $tour->sport->sportsubid );
	if (! ex2er ( $stmt )) {
		return false;
	}
	return true;
}
function updateTour($pdo, Tour $tour) {
	$stmt = $pdo->prepare ( "update tour set duration=?, meetingpoint=?, meetingpoint_coord = PointFromText(?), description=? where id=? and status='active'" );
	$point = 'POINT(' . $tour->meetingPoint_lat . " " . $tour->meetingPoint_long . ')';
	if (! ex2er ( $stmt, array (
			$tour->duration,
			$tour->meetingPoint,
			$point,
			$tour->description,
			$tour->id 
	) )) {
		return false;
	}
	return true;
}
function getTourById($pdo, $tourid) {
	$stmt = $pdo->prepare ( //
'select *,sportname,ss.sportsubname as sportsubname, ss.id as sportsubid, X(meetingpoint_coord) as meetingpoint_lat,Y(meetingpoint_coord) as meetingpoint_long,t.status as tourstatus, t.id as id, g.id as guide, g.username as guidename' . //
' from tour t left join user g ON (t.fk_guide_id=g.id)' . //
' left join sport_subtype ss ON (t.fk_sport_subtype_id=ss.id) ' . //
' left join sport s ON (ss.fk_sport_id=s.id) ' . //
' where t.id=?' );
	$stmt->execute ( array (
			$tourid 
	) );
	return getTourObject ( $stmt->fetch ( PDO::FETCH_ASSOC ) );
}
function getPlaceById($pdo, $id) {
	$stmt = $pdo->prepare ( "select id,name,X(coord) as lat ,Y(coord) as lon from place where id=?" );
	$stmt->execute ( array (
			$id 
	) );
	return getPlaceObject ( $stmt->fetch ( PDO::FETCH_ASSOC ) );
}
function getAttendees($pdo, $tourid) {
	$stmt = $pdo->prepare ( "select user.id as id, user.username as username, user.email as email from tour_attendee left join user ON (fk_user_id=user.id) where fk_tour_id = ?" );
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