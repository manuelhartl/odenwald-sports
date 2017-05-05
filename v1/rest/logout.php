<?php
require_once __DIR__ . '/../lib/global.php';


if ($_SERVER['REQUEST_METHOD'] != "POST"){
	http_response_code(405);
	exit;
}

session_destroy ();
unset ( $_SESSION );
header('Content-type: application/json');

?>
