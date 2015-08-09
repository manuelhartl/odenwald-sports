<?php
class Utilities {
	
	/**
	 *
	 * @param Object[T] $value
	 * @param Object[T) $defaultValue
	 * @return if value is set return value, otherwise default
	 */
	public static function getVal($value, $defaultValue) {
		return (isset ( $value ) ? $value : $defaultValue);
	}
	/**
	 *
	 * @param DateTime $value
	 * @param DateTime $defaultValue
	 * @return if value is set return value,otherwise default
	 */
	public static function isValidDate($value, $defaultValue) {
		if (is_a ( $value, 'DateTime' )) {
			return ($value);
		}
		return (new DateTime ( $defaultValue ));
	}
	
	/**
	 *
	 * @param mixed $value
	 * @return \DateTime
	 */
	static function getValidatedDate($value) {
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
	 * @param String $message
	 */
	public static function alert(string $message) {
		if (isset ( $message )) {
			echo "<script language=\"Javascript\">";
			echo "alert('" . $message . "');";
			echo "</script>";
		}
	}
	/**
	 *
	 * @param DateTime $datetime
	 * @return string
	 */
	public static function getWeekDay(DateTime $datetime) {
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
}
?>