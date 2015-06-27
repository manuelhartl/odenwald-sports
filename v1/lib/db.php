<?php
require 'config/settings.php';
function db_open() {
	global $config;
	return new PDO ( 'mysql:host=' . $config ['dbHostname'] . ';dbname=' . $config ['dbDatabase'] . ';charset=utf8', $config ['dbUsername'], $config ['dbPassword'] );
}
function toDbmsDate($datetime) {
	return date_format ( $tour->tourDatetime, "Y-m-d H:i:s" );
}
function fromDbmsDate($datetimeString) {
	return DateTime::createFromFormat ( "Y-m-d H:i:s", $datetimeString );
}
function ex2er($stmt, $params) {
	global $config;
	$result = $stmt->execute ( $params );
	if (! $result) {
		if (array_key_exists ( 'debug', $config ) && ($config ['debug'])) {
			print_r ( $stmt->errorInfo () );
		}
	}
	return $result;
}
?>