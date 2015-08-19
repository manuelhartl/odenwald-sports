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
	
	/**
	 *
	 * @param Strng $sportsubname
	 * @return the shortcut for the sport subnmae
	 */
	public static function makeSportSubnameIconTag($sportsubname, $canceled) {
		// (1, 1, 'MTB'),
		// (3, 1, 'Rennrad'),
		// (4, 1, 'Crosser'),
		// (5, 2, 'Lauf'),
		// (6, 2, 'Trail-Running'),
		// (7, 3, 'Schwimmen'),
		// (8, 4, 'Triathlon'),
		// (9, 4, 'Duathlon'),
		// (10, 4, 'Swim & Bike'),
		// (11, 5, 'Langlauf');
		switch ($sportsubname) {
			case 'MTB' :
				$icon = "mtb";
				break;
			case 'Rennrad' :
				$icon = "rr";
				break;
			case 'Crosser' :
				$icon = "crosser";
				break;
			case 'Lauf' :
				$icon = "run";
				break;
			case 'Trail-Running' :
				$icon = "trailrun";
				break;
			case 'Schwimmen' :
				$icon = "swim";
				break;
			case 'Triathlon' :
				$icon = "3lon";
				break;
			case 'Duathlon' :
				$icon = "2lon";
				break;
			case 'Swim & Bike' :
				$icon = "edit";
				break;
			case 'Langlauf' :
				$icon = "ski";
				break;
			case 'delete' :
				$icon = "delete";
			default :
				$icon = "mtb";
		}
		if ($canceled) {
			$ret = '<div class="wrapIcon">' . PHP_EOL . //
'<img class="img-c" src="img/big/' . $icon . '.png" align="middle" border="0" height="40px" width="40px">' . PHP_EOL . //
'<img class="img-a" src="img/big/' . 'delete' . '.png" align="middle" border="0" height="40px" width="40px">' . PHP_EOL . //
'<div>';
		} else {
			$ret = "<img src='img/big/" . $icon . ".png' align='middle' border='0' height='40px' width='40px'>";
		}
		
		return ($ret);
	}
	
	/**
	 *
	 * @param string $text the text to clean
	 */
	public static function clearText4Mail($text) {
		return (Utilities::clearText ( $text ));
	}
	/**
	 *
	 * @param string $text the text to clean
	 */
	public static function clearText4Display($text) {
		return (Utilities::clearText ( $text ));
	}
	/**
	 *
	 * @param string $text the text to clean
	 */
	private static function clearText($text) {
		return (strip_tags ( $text, '<br><br/>' ));
	}
}
?>