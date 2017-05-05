<?php
require_once __DIR__ . '/restlib.php';

if ($_SERVER ['REQUEST_METHOD'] != "GET") {
	exit ();
}

$pdo = db_open ();
$sports = getSports ( $pdo );
$sport_list = array ();
foreach ( $sports as $dbsport ) {
	$sport = array ();
	$sport ['id'] = $dbsport->sportsubid;
	$sport ['name'] = $dbsport->sportname;
	$sport ['subname'] = $dbsport->sportsubname;
	$sport_list [] = $sport;
}
$json ['sports'] = $sport_list;
$json ['authenticated'] = hasAuth ();
header ( 'Content-type: application/json' );
echo json_encode ( $json );
