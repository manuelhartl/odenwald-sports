<?php
require_once __DIR__ . '/../lib/global.php';

function getUserExtraById($pdo, $userid) {
	$stmt = $pdo->prepare ( 'select *,X(address_coord) as address_lat,Y(address_coord) as address_long from user_extra where user_extra.fk_user_id = ?' );
	$stmt->execute ( array (
			$userid
	) );
	return $stmt->fetch ( PDO::FETCH_ASSOC );
}

if ($_SERVER['REQUEST_METHOD'] != "GET"){
	http_response_code(405);
	exit;
}

header('Content-type: application/json');

$json['authenticated']=false;
if (!hasAuth()) {
	http_response_code(401);
} else {
	http_response_code(200);
	$json['authenticated']=true;
  $pdo = db_open();
	$user = array();
	$userextra = getUserExtraById ( $pdo, authUser()->id );
	$user['id']=authUser ()->id;
	$user['username']=authUser ()->username;
	$user['registerdate']= authUser()->register_date->format(DateTime::ISO8601);
	if (isset($userextra ['realname'])) {
		$user['realname']= $userextra ['realname'];
	}
	if (isset($userextra['birthdate'])) {
		$user['birthdate']= date_create ( $userextra ['birthdate'] )->format(DateTime::ISO8601);
	}
	if (isset($userextra['dist'])) {
		$user['distance']= $userextra ['dist'];
	}
	if (isset($userextra['address'])) {
		$user['address']= $userextra ['address'];
	}
	if (isset ( $userextra['address_lat'] )) {
		$user['address_gps']['lat'] = $userextra['address_lat'];
		$user['address_gps']['long'] = $userextra['address_long'];
	}
	if (isset($userextra['phone'])) {
		$user['phone']= $userextra ['phone'];
	}
	$json['user'] = $user;
}

echo json_encode($json);

?>
