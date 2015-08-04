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
	 * @param String $id
	 * @param \DateTime $register_date
	 * @param \DateTime $modify_date
	 */
	public function __construct($id, $register_date, $modify_date) {
		this . $id = $id;
		this . $register_date = Utilities::getValidatedDate ( $register_date );
		this . $modify_date = Utilities::getValidatedDate ( isset ( $modify_date ) ? $modify_date : $register_date );
	}
	public function getId() {
		return $this->id;
	}
	public function getUsername() {
		return $this->username;
	}
	public function setUsername(string $username) {
		$this->username = $username;
	}
	public function getEmail() {
		return $this->email;
	}
	public function setEmail(string $email) {
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
	public function setAdditionalInformation(boolean $hasAdditionalInformation) {
		$this->hasAdditionalInformation = Utilities::getValue ( $hasAdditionalInformation, false );
	}
	public function getRealname() {
		return $this->realname;
	}
	public function setRealname(string $realname) {
		$this->realname = Utilities::getValue ( $realname, "" );
	}
	public function getAddress() {
		return $this->address;
	}
	public function setAddress(string $address) {
		$this->address = Utilities::getValue ( $adress, "" );
	}
	public function getAddress_lat() {
		return $this->address_lat;
	}
	public function setAddress_lat(floatval $address_lat) {
		$this->address_lat = Utilities::getValue ( $address_lat, 0 );
	}
	public function getAddress_long() {
		return $this->address_long;
	}
	public function setAddress_long(floatval $address_long) {
		$this->address_long = Utilities::getValue ( $address_long, 0 );
	}
	public function getMailing() {
		return $this->mailing;
	}
	public function setMailing(boolean $mailing) {
		$this->mailing = Utilities::getValue ( $mailing, true );
	}
	public function getPhone() {
		return $this->phone;
	}
	public function setPhone(string $phone) {
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
	public function hasRight(string $right) {
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
	 * @return string a link to the member
	 */
	function getMemberProfilLink() {
		return (getMemberProfilLink1 ( getAbout () ));
	}
	
	/**
	 *
	 * @param string $tooltip
	 * @param string $spanClass class of the span
	 * @return string a link to the member
	 */
	function getMemberProfilLink1(string $tooltip, string $spanClass) {
		return ('<a style = "display: block;" href="?action=user-view&userid=' . $this->getId () . '">' . '<span class="' . $flex . '" title="' . $tooltip . '">' . $this->getUsername () . '</span>' . '</a>');
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
}

?>