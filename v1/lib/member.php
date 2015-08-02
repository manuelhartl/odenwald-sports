<?php
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
	 * @param String $id
	 * @param \DateTime $register_date
	 * @param \DateTime $modify_date
	 */
	public function __construct($id, $register_date, $modify_date) {
		this . $id = $id;
		this . $register_date = getValidatedDate ( $register_date );
		this . $modify_date = getValidatedDate ( isset ( $modify_date ) ? $modify_date : $register_date );
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
	public function setBirtdate($birtdate) {
		$this->birtdate = getValidatedDate ( $birtdate );
	}
	public function hasAdditionalInformation() {
		return $this->hasAdditionalInformation;
	}
	public function hasAdditionalInformation($hasAdditionalInformation) {
		$this->hasAdditionalInformation = getValue ( $hasAdditionalInformation, false );
	}
	public function getRealname() {
		return $this->realname;
	}
	public function setRealname($realname) {
		$this->realname = getValue ( $realname, "" );
	}
	public function getAddress() {
		return $this->address;
	}
	public function setAddress($address) {
		$this->address = getValue ( $adress, "" );
	}
	public function getAddress_lat() {
		return $this->address_lat;
	}
	public function setAddress_lat($address_lat) {
		$this->address_lat = getValue ( $address_lat, 0 );
	}
	public function getAddress_long() {
		return $this->address_long;
	}
	public function setAddress_long($address_long) {
		$this->address_long = getValue ( $address_long, 0 );
	}
	public function getMailing() {
		return $this->mailing;
	}
	public function setMailing($mailing) {
		$this->mailing = getValue ( $mailing, true );
	}
	public function getPhone() {
		return $this->phone;
	}
	public function setPhone($phone) {
		$this->phone = getValue ( $phone, "" );
	}
	public function getRights() {
		return $this->rights;
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
}

?>