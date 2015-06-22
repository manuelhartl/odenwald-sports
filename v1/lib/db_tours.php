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
function getAttendees($pdo, $tourid) {
	$stmt = $pdo->prepare ( "select user.id as id, user.username as username from tour_attendee left join user ON (fk_user_id=user.id) where fk_tour_id = ?" );
	if (! ex2er ( $stmt, array (
			$tourid 
	) )) {
		return false;
	}
	return $stmt->fetchAll ( PDO::FETCH_ASSOC );
}

?>