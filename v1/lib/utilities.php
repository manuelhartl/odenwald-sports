<?php

/**
 * @param mixed $value
 * @return \DateTime
 */
function getValidatedDate($value) {
	$returnValue;
	if ($value instanceof \DateTime) {
		$returnValue = $value;
	} elseif (is_string ( $value )) {
		$returnValue = new \DateTime ( $value );
	} elseif (is_int ( $value )) {
		$returnValue = new \DateTime ( date ( DATE_ATOM, $value ) );
	}
	return ($returnValue);
}

/**
 *
 * @param object $value        	
 * @param object $defaultValue        	
 * @return not isset value retrun defaultValue
 */
function getValue($value, $defaultValue) {
	return (isset ( $value ) ? $value : $defaultValue);
}