<?php
require_once __DIR__ . '/utilities.php';
class Member {
	private $id;
	private $username;
	private $email;
	private $hasAdditionalInformation = false;
	/**
	 *
	 * @var \DateTime
	 */
	private $birtdate = null;
	private $realname = "";
	private $address = "";
	private $address_lat = 0;
	private $address_long = 0;
	private $mailing = true;
	private $phone = "";
	
	// future values
	private $rights;
	
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
	 * @param string $id
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
	public function getUsername() {
		return $this->username;
	}
	public function setUsername($username) {
		$this->username = $username;
	}
	public function getEmail() {
		return $this->email;
	}
	public function setEmail($email) {
		$this->email = $email;
	}
	public function getBirtdate() {
		return $this->birtdate;
	}
	public function setBirtdate(DateTime $birtdate) {
		$this->birtdate = Utilities::getValidatedDate ( $birtdate );
	}
	public function hasAdditionalInformation() {
		return $this->hasAdditionalInformation;
	}
	public function setAdditionalInformation($hasAdditionalInformation) {
		$this->hasAdditionalInformation = Utilities::getValue ( $hasAdditionalInformation, false );
	}
	public function getRealname() {
		return $this->realname;
	}
	public function setRealname($realname) {
		$this->realname = Utilities::getValue ( $realname, "" );
	}
	public function hasAdress() {
		return ($this->address_lat != 0 && $this->address_long != 0);
	}
	public function getAddress() {
		return $this->address;
	}
	public function setAddress($address) {
		$this->address = Utilities::getValue ( $address, "" );
	}
	public function getAddress_lat() {
		return $this->address_lat;
	}
	public function setAddress_lat($address_lat) {
		$this->address_lat = Utilities::getValue ( $address_lat, 0 );
	}
	public function getAddress_long() {
		return $this->address_long;
	}
	public function setAddress_long($address_long) {
		$this->address_long = Utilities::getValue ( $address_long, 0 );
	}
	public function getMailing() {
		return $this->mailing;
	}
	public function setMailing($mailing) {
		$this->mailing = Utilities::getValue ( $mailing, true );
	}
	public function getPhone() {
		return $this->phone;
	}
	public function setPhone($phone) {
		$this->phone = Utilities::getValue ( $phone, "" );
	}
	public function getRights() {
		return $this->rights;
	}
	/**
	 *
	 * @param string $right
	 * @return boolean if the member has the requested right
	 */
	public function hasRight($right) {
		return (true);
	}
	public function setRights($rights) {
		$this->rights = $rights;
	}
	public function getRegister_date() {
		return $this->register_date;
	}
	public function getModify_date() {
		return $this->modify_date;
	}
	
	/**
	 *
	 * @return string about the member
	 */
	function getAbout() {
		$about = "";
		if (strlen ( $this->getPhone () ) > 0) {
			$about = "Telefon: " . $this->getPhone ();
		}
		// add member name in front
		if (strlen ( $about ) > 0) {
			$about = $this->getUsername () . ": " . $about;
		} else {
			$about = $this->getUsername ();
		}
		
		return ($about);
	}
	/**
	 * @param double $latitude
	 * @param double $longitude
	 * @return the distance to the given location
	 */
	public function getDistanceTo($latitude, $longitude) {
		if ($this->hasAdress ()) {
			// return ($this->getDistanceBetweenPoints ( $this->address_lat, $this->address_long, $latitude, $longitude ));
			return (round ( $this->getDistance1 ( $this->address_lat, $this->address_long, $latitude, $longitude ), 2 ));
		}
		return ("");
	}
	/**
	 * @param unknown $latitude1 location 1
	 * @param unknown $longitude1 location 1
	 * @param unknown $latitude2 location 2
	 * @param unknown $longitude2 location 2
	 * @return the distance between location 1 & 2
	 */
	private function getDistance1($latitude1, $longitude1, $latitude2, $longitude2) {
		$earth_radius = 6371;
		
		$dLat = deg2rad ( $latitude2 - $latitude1 );
		$dLon = deg2rad ( $longitude2 - $longitude1 );
		
		$a = sin ( $dLat / 2 ) * sin ( $dLat / 2 ) + cos ( deg2rad ( $latitude1 ) ) * cos ( deg2rad ( $latitude2 ) ) * sin ( $dLon / 2 ) * sin ( $dLon / 2 );
		$c = 2 * asin ( sqrt ( $a ) );
		$d = $earth_radius * $c;
		
		return $d;
	}
	/**
	 * @param unknown $latitude1 location 1
	 * @param unknown $longitude1 location 1
	 * @param unknown $latitude2 location 2
	 * @param unknown $longitude2 location 2
	 * @param string $unit "Km" and "Mi"
	 * @return the distance between location 1 & 2
	 */
	private function getDistanceBetweenPoints($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Km') {
		$theta = $longitude1 - $longitude2;
		$distance = (sin ( deg2rad ( $latitude1 ) ) * sin ( deg2rad ( $latitude2 ) )) + (cos ( deg2rad ( $latitude1 ) ) * cos ( deg2rad ( $latitude2 ) ) * cos ( deg2rad ( $theta ) ));
		$distance = acos ( $distance );
		$distance = rad2deg ( $distance );
		$distance = $distance * 60 * 1.1515;
		switch ($unit) {
			case 'Mi' :
				break;
			case 'Km' :
				$distance = $distance * 1.609344;
		}
		return ($distance);
	}
}
/**
 *
 * @author g.wischnewski
 *        
 */
class MemberManager {
	/**
	 *
	 * @param unknown $pdo
	 * @param string $username
	 * @return Member
	 */
	public static function getMemberByUsername($pdo, $username) {
		$dbUser = getUserByName ( $pdo, $id );
		return (MemberManager::getMember ( $dbUser ));
	}
	/**
	 *
	 * @param unknown $pdo
	 * @param string $id
	 * @return Member
	 */
	public static function getMemberByID($pdo, $id) {
		$dbUser = getDBUser ( getUserById ( $pdo, $id ) );
		return (MemberManager::getMember ( $pdo, $dbUser ));
	}
	/**
	 *
	 * @param unknown $pdo
	 * @param DBUser $dbUser
	 * @return Member
	 */
	private static function getMember($pdo, DBUser $dbUser) {
		$member = new Member ( $dbUser->id, $dbUser->register_date, $dbUser->modify_date );
		$member->setUsername ( $dbUser->username );
		$member->setEmail ( $dbUser->email );
		
		if (userExtraExists ( $pdo, $dbUser->id )) {
			$DBUserExtra = getDBUserExtraById ( $pdo, $dbUser->id );
			$member->setAdditionalInformation ( true );
			$member->setBirtdate ( $DBUserExtra->birtdate );
			$member->setRealname ( $DBUserExtra->realname );
			$member->setAddress ( $DBUserExtra->address );
			$member->setAddress_long ( floatval ( $DBUserExtra->address_long ) );
			$member->setAddress_lat ( floatval ( $DBUserExtra->address_lat ) );
			$member->setMailing ( $DBUserExtra->mailing );
			$member->setPhone ( $DBUserExtra->phone );
		}
		return ($member);
	}
	/**
	 * @param unknown $pdo
	 * @param unknown $auth_id
	 * @param unknown $auth_lat
	 * @param unknown $auth_long
	 */
	public static function getUserlist($pdo, $auth_id, $auth_lat, $auth_long) {
		$result = getUserlist ( $pdo, $auth_id, $auth_lat, $auth_long );
		while ( $row = array_shift ( $result ) ) {
			// var_dump ( $row );
			$member = MemberManager::getMemberByID ( $pdo, ( string ) $row ['id'] );
			echo ($member->getUsername ()) . ' : ' . $member->getDistanceTo ( $auth_lat, $auth_long ) . ' (' . $row ['dist'] . ')' . PHP_EOL;
		}
	}
}

?>