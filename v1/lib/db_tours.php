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
	public $canceled;
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
 	$tourObj->canceled = ($row ['tourstatus'] == 'canceled');
// 	print_r($row);
// 	print_r($tourObj);
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
function updateTour($pdo, Tour $tour) {
	$stmt = $pdo->prepare ( "update tour set duration=?,meetingpoint=?,description=? where id=?" );
	if (! ex2er ( $stmt, array (
			$tour->duration,
			$tour->meetingPoint,
			$tour->description, 
			$tour->id
	) )) {
		return false;
	}
	return true;
}
function getTourById($pdo, $tourid) {
	$stmt = $pdo->prepare ( "select *,t.status as tourstatus, t.id as id, g.id as guide, g.username as guidename from tour t left join user g ON (t.fk_guide_id=g.id) where t.id=?" );
	$stmt->execute ( array (
			$tourid 
	) );
	return getTourObject ( $stmt->fetch ( PDO::FETCH_ASSOC ) );
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