<?php
require_once __DIR__ . '/../lib/global.php';
require_once __DIR__ . '/../lib/users.php';
require_once __DIR__ . '/restlib.php';

if (! hasAuth ()) {
	echo "not logged in";
	exit ();
}

if ($_SERVER ['REQUEST_METHOD'] != "GET") {
	exit ();
}
function getUserExtraById($pdo, $userid) {
	$stmt = $pdo->prepare ( 'select *,X(address_coord) as address_lat,Y(address_coord) as address_long from user_extra where user_extra.fk_user_id = ?' );
	$stmt->execute ( array (
			$userid 
	) );
	return $stmt->fetch ( PDO::FETCH_ASSOC );
}

$pdo = db_open ();

$stmt = getUserStmt($pdo);
$user_list = array ();
while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
	$user_list[]=row2user($pdo, $row);
}
$json ['users'] = $user_list;

header ( 'Content-type: application/json' );
echo json_encode ( $json );
