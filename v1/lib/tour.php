<?php
require_once __DIR__ . '/member.php';
/**
 *
 * @author g.wischnewski
 *        
 */
class Tour {
	private $id;
	private $startDateTime;
	private $duration;
	private $sport;
	private $meetingPoint;
	private $meetingPoint_desc;
	private $description;
	private /*User */ $guide;
	private $tourmembers = array ();
	private $status;
	private $meetingPoint_lat;
	private $meetingPoint_long;
	private $elevation;
	private $distance;
	private $skill;
	private $speed;
	
	// read only values
	/**
	 *
	 * @var \DateTime
	 */
	private $register_date;
	/**
	 *
	 * @var \DateTime
	 */
	private $modify_date;
	
	// constructor
	/**
	 *
	 * @param String $id
	 * @param \DateTime $register_date
	 * @param \DateTime $modify_date
	 */
	public function __construct($id, $register_date, $modify_date) {
		$this->id = $id;
		$this->register_date = Utilities::getValidatedDate ( $register_date );
		$this->modify_date = Utilities::getValidatedDate ( isset ( $modify_date ) ? $modify_date : $register_date );
	}
	public function getId() {
		return $this->id;
	}
	public function setId(int $id) {
		$this->id = $id;
	}
	public function getStartDateTime() {
		return $this->startDateTime;
	}
	public function setStartDateTime(DateTime $startDateTime) {
		$this->startDateTime = Utilities::getValidatedDate ( $startDateTime );
	}
	public function getDuration() {
		return $this->duration;
	}
	public function setDuration($duration) {
		$this->duration = $duration;
	}
	public function getSport() {
		return $this->sport;
	}
	public function setSport($sport) {
		$this->sport = $sport;
	}
	public function getMeetingPoint() {
		return $this->meetingPoint;
	}
	public function setMeetingPoint($meetingPoint) {
		$this->meetingPoint = $meetingPoint;
	}
	public function getMeetingPoint_desc() {
		return $this->meetingPoint_desc;
	}
	public function setMeetingPoint_desc($meetingPoint_desc) {
		$this->meetingPoint_desc = $meetingPoint_desc;
	}
	public function getDescription() {
		return $this->description;
	}
	public function setDescription($description) {
		$this->description = $description;
	}
	public function getTourmembers() {
		return $this->tourmembers;
	}
	public function addMember(Member $member) {
		// array_push ( $this . tourmembers, $member );
	}
	public function setTourmembers(array $tourmembers) {
		$this->tourmembers = $tourmembers;
	}
	public function getStatus() {
		return $this->status;
	}
	public function setStatus($status) {
		$this->status = $status;
	}
	public function getMeetingPoint_lat() {
		return $this->meetingPoint_lat;
	}
	public function setMeetingPoint_lat($meetingPoint_lat) {
		$this->meetingPoint_lat = $meetingPoint_lat;
	}
	public function getMeetingPoint_long() {
		return $this->meetingPoint_long;
	}
	public function setMeetingPoint_long($meetingPoint_long) {
		$this->meetingPoint_long = $meetingPoint_long;
	}
	public function getElevation() {
		return $this->elevation;
	}
	public function setElevation($elevation) {
		$this->elevation = $elevation;
	}
	public function getDistance() {
		return $this->distance;
	}
	public function setDistance($distance) {
		$this->distance = $distance;
	}
	public function getSkill() {
		return $this->skill;
	}
	public function setSkill($skill) {
		$this->skill = $skill;
	}
	public function getSpeed() {
		return $this->speed;
	}
	public function setSpeed($speed) {
		$this->speed = $speed;
	}
	public function getRegister_date() {
		return ($this->register_date);
	}
	public function getModify_date() {
		return ($this->modify_date);
	}
	public function toTable() {
		echo '<table>' . PHP_EOL;
		
		echo '  <thead>' . PHP_EOL;
		echo '    <tr>' . PHP_EOL;
		echo '      <td>' . 'id' . '</td>' . PHP_EOL;
		echo '      <td>' . 'startDateTime' . '</td>' . PHP_EOL;
		echo '      <td>' . 'duration' . '</td>' . PHP_EOL;
		echo '      <td>' . 'sport' . '</td>' . PHP_EOL;
		echo '      <td>' . 'meetingPoint' . '</td>' . PHP_EOL;
		echo '      <td>' . 'meetingPoint_desc' . '</td>' . PHP_EOL;
		echo '      <td>' . 'description' . '</td>' . PHP_EOL;
		echo '      <td>' . 'guide' . '</td>' . PHP_EOL;
		echo '      <td>' . 'tourmembers' . '</td>' . PHP_EOL;
		echo '      <td>' . 'status' . '</td>' . PHP_EOL;
		echo '      <td>' . 'meetingPoint_lat' . '</td>' . PHP_EOL;
		echo '      <td>' . 'meetingPoint_long' . '</td>' . PHP_EOL;
		echo '      <td>' . 'elevation' . '</td>' . PHP_EOL;
		echo '      <td>' . 'distance' . '</td>' . PHP_EOL;
		echo '      <td>' . 'speed' . '</td>' . PHP_EOL;
		echo '      <td>' . 'register_date' . '</td>' . PHP_EOL;
		echo '      <td>' . 'modify_date' . '</td>' . PHP_EOL;
		echo '    </tr>' . PHP_EOL;
		echo '  </thead>' . PHP_EOL;
		
		echo '  <tbody>' . PHP_EOL;
		echo '    <tr>' . PHP_EOL;
		echo '      <td>' . $this->id . '</td>' . PHP_EOL;
		echo '      <td>' . Utilities::diyplayDateTime ( $this->startDateTime, 'd.m.Y H:i' ) . '</td>' . PHP_EOL;
		echo '      <td>' . $this->duration . '</td>' . PHP_EOL;
		echo '      <td>' . $this->sport->sportname . '</td>' . PHP_EOL;
		echo '      <td>' . $this->meetingPoint . '</td>' . PHP_EOL;
		echo '      <td>' . $this->meetingPoint_desc . '</td>' . PHP_EOL;
		echo '      <td>' . $this->description . '</td>' . PHP_EOL;
		echo '      <td>' . $this->guide . '</td>' . PHP_EOL;
		echo '      <td>';
		if (isset ( $this->tourmembers )) {
			$last = count ( $this->tourmembers );
			foreach ( $this->tourmembers as $tourmember ) {
				echo $tourmember->getUsername ();
				if ($last > 1) {
					echo ' ,';
				}
			}
		}
		echo '</td>' . PHP_EOL;
		echo '      <td>' . $this->status . '</td>' . PHP_EOL;
		echo '      <td>' . $this->meetingPoint_lat . '</td>' . PHP_EOL;
		echo '      <td>' . $this->meetingPoint_long . '</td>' . PHP_EOL;
		echo '      <td>' . $this->elevation . '</td>' . PHP_EOL;
		echo '      <td>' . $this->distance . '</td>' . PHP_EOL;
		echo '      <td>' . $this->speed . '</td>' . PHP_EOL;
		echo '      <td>' . Utilities::diyplayDateTime ( $this->register_date, 'd.m.Y H:i' ) . '</td>' . PHP_EOL;
		echo '      <td>' . Utilities::diyplayDateTime ( $this->modify_date, 'd.m.Y H:i' ) . '</td>' . PHP_EOL;
		echo '    </tr>' . PHP_EOL;
		echo '  </tbody>' . PHP_EOL;
		
		echo '</table>' . PHP_EOL;
	}
}

/**
 *
 * @author g.wischnewski
 *        
 */
class TourManager {
	public static function getTourByID($pdo, $id) {
		$dbtour = getDBTourById ( $pdo, $id );
		
		$tour = new Tour ( $dbtour->id, $dbtour->register_date, $dbtour->modify_date );
		// set all information
		
		$tour->setStartDateTime ( $dbtour->startDateTime );
		$tour->setDuration ( $dbtour->duration );
		$tour->setSport ( $dbtour->sport );
		$tour->setMeetingPoint ( $dbtour->meetingPoint );
		$tour->setMeetingPoint_desc ( $dbtour->meetingPoint_desc );
		$tour->setDescription ( $dbtour->description );
		
		$users = getAttendees ( $pdo, $dbtour->id );
		if (count ( $users ) > 0) {
			$tourmembers = array ();
			foreach ( $users as $user ) {
				$tourmember = MemberManager::getMemberById ( $pdo, $user ['id'] );
				array_push ( $tourmembers, $tourmember );
			}
			$tour->setTourmembers ( $tourmembers );
		}
		//
		$tour->setStatus ( $dbtour->canceled );
		$tour->setMeetingPoint_lat ( $dbtour->meetingPoint_lat );
		$tour->setMeetingPoint_long ( $dbtour->meetingPoint_long );
		$tour->setElevation ( $dbtour->elevation );
		$tour->setDistance ( $dbtour->distance );
		$tour->setSkill ( $dbtour->skill );
		$tour->setSpeed ( $dbtour->speed );
		
		return ($tour);
	}
	public static function dumpAllTours($pdo) {
		$result = getCompleteTourlist ( $pdo );
		$number = 1;
		
		echo '<table border="1">' . PHP_EOL;
		echo '  <tbody>' . PHP_EOL;
		while ( $row = array_shift ( $result ) ) {
			$tour = TourManager::getTourByID ( $pdo, $row ['id'] );
			echo '    <tr>' . PHP_EOL;
			echo '      <td>' . $number . '</td>' . PHP_EOL;
			echo '      <td>';
			echo $tour->toTable ();
			echo '</td>' . PHP_EOL;
			echo '    </tr>' . PHP_EOL;
			$number += 1;
		}
		echo '  </tbody>' . PHP_EOL;
		echo '</table>' . PHP_EOL;
	}
}
