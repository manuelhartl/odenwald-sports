<?php
require_once 'db.php';
require_once 'db_tours.php';
class Tour {
	public $id;
	public $startDateTime;
	public $duration;
	public $meetingPoint;
	public $description;
	public /*User */$guide;
	public $attendees;
}
function getTourObject($row) {
	$tourObj = new Tour ();
	$tourObj->id = $row ['id'];
	$tourObj->startDateTime = $row ['startdate'];
	$tourObj->duration = $row ['duration'];
	$tourObj->meetingPoint = $row ['meetingpoint'];
	$tourObj->description = $row ['description'];
	$tourObj->guide = new User ();
	$tourObj->guide->id = $row ['guide'];
	$tourObj->guide->username = $row ['guidename'];
	$tourObj->attendees = array ();
	array_push ( $tourObj->attendees, new User () );
	return $tourObj;
}
function insertTour($pdo, Tour $tour) {
	$stmt = $pdo->prepare ( "insert into tour (fk_guide_id,startdate,duration,meetingpoint,description) VALUES(?,?,?,?,?)" );
	if (! ex2er ( $stmt, array (
			$tour->guide->id,
			$tour->startDateTime,
			$tour->duration,
			$tour->meetingPoint,
			$tour->description
	) )) {
		return false;
	}
	return true;
}
function getAttendees($pdo, $tourid) {
	$stmt = $pdo->prepare ( "select user.id as id, user.username as username from tour_attendee left join user ON (fk_user_id=user.id) where fk_tour_id = ?" );
	if (! ex2er ( $stmt, array (
			$tourid 
	) )) {
		return false;
	}
	return $stmt->fetchAll ( PDO::FETCH_ASSOC );
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

?>