<?php
require_once __DIR__ . '/../lib/global.php';
require_once __DIR__ . '/../lib/users.php';
require_once __DIR__ . '/../lib/db_tours.php'; //for dbgps...

if (!hasAuth()) {
	echo "not logged in";
	exit;
}

if ($_SERVER['REQUEST_METHOD'] != "GET"){
	exit;
}

function getUserExtraById($pdo, $userid) {
	$stmt = $pdo->prepare ( 'select *,X(address_coord) as address_lat,Y(address_coord) as address_long from user_extra where user_extra.fk_user_id = ?' );
	$stmt->execute ( array (
			$userid
	) );
	return $stmt->fetch ( PDO::FETCH_ASSOC );
}

$pdo = db_open();

$reference = new DBGps ();
$hasAdress = false;
$userextra = getDBUserExtraById ( $pdo, authUser ()->id );
if (isset ( $userextra->address_lat )) {
	$hasAdress = true;
	$reference->lat = $userextra->address_lat;
	$reference->long = $userextra->address_long;
}

$stmt = $pdo->prepare ( 'select username,id,register_date,realname,birthdate,fk_user_id,111195 * ST_Distance(POINT(?,?), address_coord) as dist, address, mailing, phone ' . //
' from user u' . //
' left join user_extra ue ON (ue.fk_user_id=u.id) ' . //
' WHERE status = "verified"' . //
' AND u.id != ?' . //
' ORDER BY lower(u.username) ASC' ); //
ex2er ( $stmt, array (
		$reference->lat,
		$reference->long,
		authUser ()->id 
) );


$user_list = array();
while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
	unset($user);

	$user['id']=$row ['id'];
	$user['username']=$row ['username'];
	$user['registerdate']= date_create ( $row ['register_date'] )->format(DateTime::ISO8601);
	if (isset($row ['realname'])) {
		$user['realname']= $row ['realname'];
	}
	if (isset($row['birthdate'])) {
		$user['birthdate']= date_create ( $row ['birthdate'] )->format(DateTime::ISO8601);
	}
	if (isset($row['dist'])) {
		$user['distance']= $row ['dist'];
	}
	if (isset($row['address'])) {
		$user['address']= $row ['address'];
	}
	if (isset($row['phone'])) {
		$user['phone']= $row ['phone'];
	}	
	
	// push to array
	$user_list[]=$user;
}
$json['users']=$user_list;

header('Content-type: application/json');
echo json_encode($json);
