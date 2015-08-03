<?php
require_once __DIR__ . '/db.php';
// private function
function getMemberByStatement($pdo, $selectstatement, $parameter) {
	// first get user data
	$stmt = $pdo->prepare ( $selectstatement );
	$stmt->execute ( array (
			$parameter 
	) );
	$row = $stmt->fetch ( PDO::FETCH_ASSOC );
	
	$member = new Member ( $row ['id'], $row ['register_date'], $row ['modify_date'] );
	$member->setUsername ( $row ['username'] );
	$member->setEmail ( $row ['email'] );
	
	// get user extra data
	$stmt = $pdo->prepare ( 'select *,X(address_coord) as address_lat,Y(address_coord) as address_long from user_extra where user_extra.fk_user_id = ?' );
	$stmt->execute ( array (
			$member->getId () 
	) );
	$row = $stmt->fetch ( PDO::FETCH_ASSOC );
	if ($row) {
		$member->hasAdditionalInformation ( true );
		$member->setRealname ( $realname );
		$member->setBirtdate ( $birtdate );
		$member->setAddress ( $address );
		$member->setAddress_lat ( $address_lat );
		$member->setAddress_long ( $address_long );
		$member->setMailing ( $mailing );
		$member->setPhone ( $phone );
	}
	return ($member);
}
/**
 *
 * @param DB connection $pdo
 * @param String $username
 * @return Member
 */
function getMemberByName($pdo, $username) {
	return (getMemberByStatement ( $pdo, "select id,username,email,register_date, modify_date from user where LOWER(username) = LOWER(?)", $username ));
}
/**
 *
 * @param DB connection $pdo
 * @param String $userId
 * @return Member
 */
function getMemberById($pdo, $userId) {
	return (getMemberByStatement ( $pdo, "select id,username,email,register_date from user where id = ?", $username ));
}

?>