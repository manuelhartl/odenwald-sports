<?php
require_once __DIR__ . '/../config/settings.php';
function db_open() {
	global $config;
	return new PDO ( 'mysql:host=' . $config ['dbHostname'] . ';dbname=' . $config ['dbDatabase'] . ';charset=utf8', $config ['dbUsername'], $config ['dbPassword'] );
}
function toDbmsDate($datetime) {
	return date_format ( $datetime, "Y-m-d H:i:s" );
}
function fromDbmsDate($datetimeString) {
	return DateTime::createFromFormat ( "Y-m-d H:i:s", $datetimeString );
}
function getWeekDay($datetime) {
	switch ($datetime->format ( 'N' )) {
		case 1 :
			return "Mo";
		case 2 :
			return "Di";
		case 3 :
			return "Mi";
		case 4 :
			return "Do";
		case 5 :
			return "Fr";
		case 6 :
			return "Sa";
		case 7 :
			return "So";
	}
}
function ex2er($stmt, $params = null) {
	global $config;
	$result = $stmt->execute ( $params );
	if (! $result) {
		if (array_key_exists ( 'debug', $config ) && ($config ['debug'])) {
			echo "<pre>";
			print_r ( $stmt->errorInfo () );
			$stmt->debugDumpParams ();
			echo "</pre>";
		}
	}
	return $result;
}
?>