<?php

require_once __DIR__ . '/restlib.php';

if ($_SERVER['REQUEST_METHOD'] != "GET"){
	exit;
}

$pdo = db_open();

$showold = isset ($_GET ["showold"]) ? $_GET ["showold"] : "false";
$showcanceled = isset ( $_GET ["showcanceled"] ) ? $_GET ["showcanceled"] : "false";


$stmt = getTourStmt($pdo);
$tour_list = array();
while ( $row = $stmt->fetch ( PDO::FETCH_ASSOC ) ) {
	$tour_list[]=row2tour($pdo, $row);
}
$json['tours']=$tour_list;
$json['authenticated']=hasAuth();
header('Content-type: application/json');
echo json_encode($json);
