<?php
require_once 'db.php';
function getUserObject($user) {
	$userObj = new User ();
	$userObj->id = $user ['id'];
	$userObj->userName = $user ['username'];
	$userObj->email = $user ['email'];
	return $userObj;
}
function checkAuth($pdo, $userName, $password) {
	$stmt = $pdo->prepare ( 'select hashedpassword from user where status=? and username=?' );
	$stmt->execute ( array (
			'verified',
			$userName 
	) );
	$user = $stmt->fetch ( PDO::FETCH_OBJ );
	if ($user) {
		return password_verify ( $password, $user->hashedpassword );
	} else {
		return false;
	}
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
function addActivationToken($pdo, $userid, $token) {
	$stmt = $pdo->prepare ( "insert into email_verification (fk_user_id,token)  VALUES (?,?)" );
	return ex2er ( $stmt, (array (
			$userid,
			$token 
	)) );
}
function getUser($pdo, $username) {
	$stmt = $pdo->prepare ( "select * from user where username = ?" );
	$stmt->execute ( array (
			$username 
	) );
	return $stmt->fetch ( PDO::FETCH_ASSOC );
}
function activate($pdo, $token) {
	$stmt = $pdo->prepare ( "update user left JOIN email_verification ON user.id=email_verification.fk_user_id set status='verified' where email_verification.token = ?" );
	if (! ex2er ( $stmt, (array (
			$token 
	)) )) {
		return false;
	}
	$stmt = $pdo->prepare ( "delete from email_verification where email_verification.token = ?" );
	if (! ex2er ( $stmt, (array (
			$token 
	)) )) {
		return false;
	}
	return $stmt->rowCount () > 0;
}

?>