<?php
require_once __DIR__ . '/db.php';
class DBUser {
	public $id;
	public $username;
	public $email;
	public $register_date;
	public $modify_date;
}
class DBUserExtra {
	public $id;
	public $birtdate;
	public $realname;
	public $address;
	public $address_lat;
	public $address_long;
	public $mailing;
	public $phone;
}
function getDBUser($user) {
	$userObj = new DBUser ();
	$userObj->id = isset ( $user ['id'] ) ? $user ['id'] : false;
	$userObj->username = isset ( $user ['username'] ) ? $user ['username'] : false;
	$userObj->email = isset ( $user ['email'] ) ? $user ['email'] : false;
	$userObj->register_date = isset ( $user ['register_date'] ) ? date_create ( $user ['register_date'] ) : false;
	$userObj->modify_date = isset ( $user ['modify_date'] ) ? date_create ( $user ['modify_date'] ) : false;
	
	return $userObj;
}
function getDBUserExtra($row) {
	$userextra = new DBUserExtra ();
	if ($row && ! is_null ( $row ['fk_user_id'] )) {
		$userextra->id = isset ( $row ['fk_user_id'] ) ? $row ['fk_user_id'] : false;
		$userextra->realname = isset ( $row ['realname'] ) ? $row ['realname'] : false;
		$userextra->birtdate = isset ( $row ['birthdate'] ) ? date_create ( $row ['birthdate'] ) : false;
		$userextra->address = isset ( $row ['address'] ) ? $row ['address'] : false;
		$userextra->address_lat = isset ( $row ['address_lat'] ) ? $row ['address_lat'] : false;
		$userextra->address_long = isset ( $row ['address_long'] ) ? $row ['address_long'] : false;
		// boolean, default: true
		$userextra->mailing = isset ( $row ['mailing'] ) ? $row ['mailing'] : true;
		$userextra->phone = isset ( $row ['phone'] ) ? $row ['phone'] : false;
	}
	return $userextra;
}
function checkAuth($pdo, $username, $password) {
	$stmt = $pdo->prepare ( 'select hashedpassword from user where status=? and LOWER(username)=LOWER(?)' );
	$stmt->execute ( array (
			'verified',
			$username 
	) );
	$user = $stmt->fetch ( PDO::FETCH_OBJ );
	if ($user) {
		return password_verify ( $password, $user->hashedpassword );
	} else {
		return false;
	}
}
function userExists($pdo, $username) {
	$stmt = $pdo->prepare ( 'select username from user where LOWER(username) = LOWER(?)' );
	$stmt->execute ( array (
			$username 
	) );
	$stmt->fetch ( PDO::FETCH_OBJ );
	return $stmt->rowCount () > 0;
}
function userExtraExists($pdo, $id) {
	$stmt = $pdo->prepare ( 'SELECT fk_user_id from user_extra WHERE fk_user_id = ?' );
	if (! ex2er ( $stmt, (array (
			$id 
	)) )) {
		return false;
	}
	$stmt->fetch ( PDO::FETCH_OBJ );
	return $stmt->rowCount () > 0;
}
function emailExists($pdo, $email) {
	$stmt = $pdo->prepare ( 'select email from user where LOWER(email) = LOWER(?)' );
	$stmt->execute ( array (
			$email 
	) );
	$stmt->fetch ( PDO::FETCH_OBJ );
	return $stmt->rowCount () > 0;
}
function registerUser($pdo, $username, $hashedpassword, $email) {
	$stmt = $pdo->prepare ( "insert into user (username, hashedpassword, email, status, register_date) VALUES (?,?,?,'registered',now())" );
	if (! ex2er ( $stmt, (array (
			$username,
			$hashedpassword,
			$email 
	)) )) {
		return false;
	}
	return $pdo->lastInsertId ();
}
function addUserExtra($pdo, $id) {
	$stmt = $pdo->prepare ( "insert into user_extra (fk_user_id) VALUES (?)" );
	if (! ex2er ( $stmt, (array (
			$id 
	)) )) {
		return false;
	}
	return $pdo->lastInsertId ();
}
function updateUserExtra($pdo, DBUserExtra $userextra) {
	$stmt = $pdo->prepare ( "update user_extra set realname = ?, birthdate = ? , address= ? , address_coord = PointFromText(?), mailing = ?, phone = ? where fk_user_id = ?" );
	$point = 'POINT(' . $userextra->address_lat . " " . $userextra->address_long . ')';
	$date = toDbmsDate ( $userextra->birtdate );
	if (! ex2er ( $stmt, array (
			$userextra->realname,
			$date,
			$userextra->address,
			$point,
			$userextra->mailing,
			$userextra->phone,
			$userextra->id 
	) )) {
		return false;
	}
	return true;
}
function getDBUserExtraById($pdo, $userid) {
	$stmt = $pdo->prepare ( 'select *,X(address_coord) as address_lat,Y(address_coord) as address_long from user_extra where user_extra.fk_user_id = ?' );
	$stmt->execute ( array (
			$userid 
	) );
	return getDBUserExtra ( $stmt->fetch ( PDO::FETCH_ASSOC ) );
}
function changePassword($pdo, $userid, $hashedpassword) {
	$stmt = $pdo->prepare ( "update user set hashedpassword = ? where id = ?" );
	if (! ex2er ( $stmt, (array (
			$hashedpassword,
			$userid 
	)) )) {
		return false;
	}
	return true;
}
function addActivationToken($pdo, $userid, $token) {
	$stmt = $pdo->prepare ( "insert into token (fk_user_id,token,action)  VALUES (?,?,'emailverification')" );
	return ex2er ( $stmt, (array (
			$userid,
			$token 
	)) );
}
function addPasswordresetToken($pdo, $userid, $token) {
	$stmt = $pdo->prepare ( "insert into token (fk_user_id,token,action)  VALUES (?,?,'passwordreset')" );
	return ex2er ( $stmt, (array (
			$userid,
			$token 
	)) );
}
function getUserByName($pdo, $username) {
	$stmt = $pdo->prepare ( "select id,username,email,register_date,modifydate from user where LOWER(username) = LOWER(?)" );
	$stmt->execute ( array (
			$username 
	) );
	return $stmt->fetch ( PDO::FETCH_ASSOC );
}
function getUserById($pdo, $userId) {
	$stmt = $pdo->prepare ( "select id,username,email,register_date,modifydate from user where id = ?" );
	$stmt->execute ( array (
			$userId 
	) );
	return $stmt->fetch ( PDO::FETCH_ASSOC );
}
function activate($pdo, $token) {
	$stmt = $pdo->prepare ( "update user left JOIN token ON user.id=token.fk_user_id set status='verified' where token.token = ? and token.action = 'emailverification'" );
	if (! ex2er ( $stmt, (array (
			$token 
	)) )) {
		return false;
	}
	$stmt = $pdo->prepare ( "delete from token where token.token = ?" );
	if (! ex2er ( $stmt, (array (
			$token 
	)) )) {
		return false;
	}
	return $stmt->rowCount () > 0;
}
function resetPassword($pdo, $token, $hashedpassword) {
	$stmt = $pdo->prepare ( "update user left JOIN token ON user.id=token.fk_user_id set hashedpassword=? where token.token = ? and token.action = 'passwordreset'" );
	if (! ex2er ( $stmt, (array (
			$hashedpassword,
			$token 
	)) )) {
		return false;
	}
	$stmt = $pdo->prepare ( "delete from token where token.token = ?" );
	if (! ex2er ( $stmt, (array (
			$token 
	)) )) {
		return false;
	}
	return $stmt->rowCount () > 0;
}
function getUserlist($pdo, $auth_id, $auth_lat, $auth_long) {
	$stmt = $pdo->prepare ( 'select id, fk_user_id, 111195 * ST_Distance(POINT(?,?), address_coord) as dist ' . //
' from user u' . //
' left join user_extra ue ON (ue.fk_user_id=u.id) ' . //
' WHERE status = "verified"' . //
' AND u.id != ?' . //
' ORDER BY lower(u.username) ASC' ); //
	ex2er ( $stmt, array (
			$auth_lat,
			$auth_long,
			$auth_id 
	) );
	return ($stmt->fetchAll ());
}

?>