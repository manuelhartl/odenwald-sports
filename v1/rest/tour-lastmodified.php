<?php
require_once __DIR__ . '/restlib.php';

if ($_SERVER ['REQUEST_METHOD'] != 'GET') {
	http_response_code ( 500 );
	exit ();
}

header ( 'Content-type: application/json' );
$pdo = db_open ();

$tourlastmodifieddate = getTourLastModified ( $pdo );

$result = array ();
$result ['tours'] ['lastmodified'] = date_create ( $tourlastmodifieddate )->format ( DateTime::ISO8601 );
echo json_encode ( $result );

?>