<?php
require_once __DIR__ . '/../lib/global.php';

if ($_SERVER['REQUEST_METHOD'] != "GET"){
	http_response_code(405);
	exit;
}

$json['servertime']=(new DateTime())->format(DateTime::ISO8601);

header('Content-type: application/json');
echo json_encode($json);

?>
