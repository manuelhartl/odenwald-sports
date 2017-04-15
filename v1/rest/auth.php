<?php
require_once __DIR__ . '/../lib/global.php';


if ($_SERVER['REQUEST_METHOD'] != "POST"){
	http_response_code(405);
	exit;
}

$pdo = db_open();

$username = $_POST ['username'];
$password = $_POST ['password'];
if (checkAuth ( $pdo, $username, $password )) {
	login ( $pdo, $username );
	http_response_code(200);
} else {
	http_response_code(401);
}
header('Content-type: application/json');

?>
