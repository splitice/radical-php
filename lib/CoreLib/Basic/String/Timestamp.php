<?php
namespace Basic\String;

class Timestamp extends \Core\Object {
	public $string;
	public $timezone;
	public $timestamp;
	
	/**
	 * Pre-defined formatting styles
	 *
	 * @var array
	 */
	static private $formats = array();
	
	/**
	 * A callback to process all formatting strings through
	 *
	 * @var callback
	 */
	static private $format_callback = NULL;
	
	/**
	 * A callback to parse all date string to allow for locale-specific parsing
	 *
	 * @var callback
	 */
	static private $unformat_callback = NULL;
	
	
	/**
	 * If a format callback is defined, call it
	 *
	 * @internal
	 *
	 * @param string $formatted_string The formatted date/time/timestamp string to be (possibly) modified
	 * @return string The (possibly) modified formatted string
	 */
	static public function callFormatCallback($formatted_string)
	{
		if (self::$format_callback) {
			return call_user_func(self::$format_callback, $formatted_string);
		}
		return $formatted_string;
	}
	
	
	/**
	 * If an unformat callback is defined, call it
	 *
	 * @internal
	 *
	 * @param string $date_time_string A raw date/time/timestamp string to be (possibly) parsed/modified
	 * @return string The (possibly) parsed or modified date/time/timestamp
	 */
	static public function callUnformatCallback($date_time_string)
	{
		if (self::$unformat_callback) {
			return call_user_func(self::$unformat_callback, $date_time_string);
		}
		return $date_time_string;
	}
	
	
	/**
	 * Creates a reusable format for formatting fDate, fTime, and fTimestamp objects
	 *
	 * @param string $name The name of the format
	 * @param string $formatting_string The format string compatible with the [http://php.net/date date()] function
	 * @return void
	 */
	static public function defineFormat($name, $formatting_string)
	{
		self::$formats[$name] = $formatting_string;
	}
	
	
	/**
	 * Fixes an ISO week format into `'Y-m-d'` so [http://php.net/strtotime strtotime()] will accept it
	 *
	 * @internal
	 *
	 * @param string $date The date to fix
	 * @return string The fixed date
	 */
	static public function fixISOWeek($date)
	{
		if (preg_match('#^(.*)(\d{4})-W(5[0-3]|[1-4][0-9]|0?[1-9])-([1-7])(.*)$#D', $date, $matches)) {
			$before = $matches[1];
			$year = $matches[2];
			$week = $matches[3];
			$day = $matches[4];
			$after = $matches[5];
	
			$first_of_year = strtotime($year . '-01-01');
			$first_thursday = strtotime('thursday', $first_of_year);
			$iso_year_start = strtotime('last monday', $first_thursday);
	
			$ymd = date('Y-m-d', strtotime('+' . ($week-1) . ' weeks +' . ($day-1) . ' days', $iso_year_start));
	
			$date = $before . $ymd . $after;
		}
		return $date;
	}
	
	
	/**
	 * Provides a consistent interface to getting the default timezone. Wraps the [http://php.net/date_default_timezone_get date_default_timezone_get()] function.
	 *
	 * @return string The default timezone used for all date/time calculations
	 */
	static public function getDefaultTimezone()
	{
		return date_default_timezone_get();
	}
	
	function __construct($datetime = null,$timezone = null){
		$default_tz = date_default_timezone_get();
		
		if ($timezone) {
			if (!self::isValidTimezone($timezone)) {
				throw new \Exceptions\FormattedException (
						'The timezone specified, %s, is not a valid timezone',
						$timezone
				);
			}
		
		} elseif ($datetime instanceof Timestamp) {
			$timezone = $datetime->timezone;
		
		} else {
			$timezone = $default_tz;
		}
		
		$this->timezone = $timezone;
		
		if ($datetime === NULL) {
			$timestamp = time();
			$this->string = (string)$timestamp;
		} elseif (is_numeric($datetime) && preg_match('#^-?\d+$#D', $datetime)) {
			$timestamp = (int) $datetime;
			$this->string = (string)$timestamp;
		} elseif (is_string($datetime) && in_array(strtoupper($datetime), array('CURRENT_TIMESTAMP', 'CURRENT_TIME'))) {
			$timestamp = time();
			$this->string = (string)$datetime;
		} elseif (is_string($datetime) && strtoupper($datetime) == 'CURRENT_DATE') {
			$timestamp = strtotime(date('Y-m-d'));
			$this->string = (string)$datetime;
		} else {
			if (is_object($datetime) && is_callable(array($datetime, '__toString'))) {
				$datetime = $datetime->__toString();
			} elseif (is_numeric($datetime) || is_object($datetime)) {
				$datetime = (string) $datetime;
			}
		
			$datetime = self::callUnformatCallback($datetime);
		
			if ($timezone != $default_tz) {
				date_default_timezone_set($timezone);
			}
			$timestamp = strtotime(self::fixISOWeek($datetime));
			if ($timezone != $default_tz) {
				date_default_timezone_set($default_tz);
			}
			
			$this->string = (string)$datetime;
		}
		
		if ($timestamp === FALSE) {
			throw new \Exceptions\FormattedException(
					'The date/time specified, %s, does not appear to be a valid date/time',
					$datetime
			);
		}
		
		$this->timestamp = $timestamp;
	}
	
	function __toString(){
		return $this->string;
	}
	
	/**
	 * Returns the approximate difference in time, discarding any unit of measure but the least specific.
	 *
	 * The output will read like:
	 *
	 * - "This timestamp is `{return value}` the provided one" when a timestamp it passed
	 * - "This timestamp is `{return value}`" when no timestamp is passed and comparing with the current timestamp
	 *
	 * Examples of output for a timestamp passed might be:
	 *
	 * - `'5 minutes after'`
	 * - `'2 hours before'`
	 * - `'2 days after'`
	 * - `'at the same time'`
	 *
	 * Examples of output for no timestamp passed might be:
	 *
	 * - `'5 minutes ago'`
	 * - `'2 hours ago'`
	 * - `'2 days from now'`
	 * - `'1 year ago'`
	 * - `'right now'`
	 *
	 * You would never get the following output since it includes more than one unit of time measurement:
	 *
	 * - `'5 minutes and 28 seconds'`
	 * - `'3 weeks, 1 day and 4 hours'`
	 *
	 * Values that are close to the next largest unit of measure will be rounded up:
	 *
	 * - `'55 minutes'` would be represented as `'1 hour'`, however `'45 minutes'` would not
	 * - `'29 days'` would be represented as `'1 month'`, but `'21 days'` would be shown as `'3 weeks'`
	 *
	 * @param fTimestamp|object|string|integer $other_timestamp The timestamp to create the difference with, `NULL` is interpreted as now
	 * @param boolean $simple When `TRUE`, the returned value will only include the difference in the two timestamps, but not `from now`, `ago`, `after` or `before`
	 * @param boolean |$simple
	 * @return string The fuzzy difference in time between the this timestamp and the one provided
	 */
	public function getFuzzyDifference($other_timestamp=NULL, $simple=FALSE)
	{
		if (is_bool($other_timestamp)) {
			$simple = $other_timestamp;
			$other_timestamp = NULL;
		}
	
		$relative_to_now = FALSE;
		if ($other_timestamp === NULL) {
			$relative_to_now = TRUE;
		}
		$other_timestamp = new Timestamp($other_timestamp);
	
		$diff = $this->timestamp - $other_timestamp->timestamp;
	
		if (abs($diff) < 10) {
			if ($relative_to_now) {
				return self::compose('right now');
			}
			return 'at the same time';
		}
	
		$break_points = array(
		/* 45 seconds */
				45 => array(1, 'second', 'seconds'),
				/* 45 minutes */
				2700 => array(60, 'minute', 'minutes'),
				/* 18 hours */
				64800 => array(3600, 'hour', 'hours'),
				/* 5 days */
				432000 => array(86400, 'day', 'days'),
				/* 3 weeks */
				1814400 => array(604800, 'week', 'weeks'),
				/* 9 months */
				23328000 => array(2592000, 'month', 'months'),
				/* largest int */
				2147483647 => array(31536000, 'year', 'years')
		);
	
		foreach ($break_points as $break_point => $unit_info) {
			if (abs($diff) > $break_point) {
				continue;
			}
	
			$unit_diff = round(abs($diff)/$unit_info[0]);
			$units = Grammar::inflectOnQuantity($unit_diff, $unit_info[1], $unit_info[2]);
			break;
		}
	
		if ($simple) {
			return sprintf('%1$s %2$s', $unit_diff, $units);
		}
	
		if ($relative_to_now) {
			if ($diff > 0) {
				return sprintf('%1$s %2$s from now', $unit_diff, $units);
			}
	
			return sprintf('%1$s %2$s ago', $unit_diff, $units);
		}
	
		if ($diff > 0) {
			return sprintf('%1$s %2$s after', $unit_diff, $units);
		}
	
		return sprintf('%1$s %2$s before', $unit_diff, $units);
	}
	
	static public function isValidTimezone($timezone){
		$timezones = \DateTimeZone::listIdentifiers();
		return (array_search($timezone,$timezones) !== false);
	}
}