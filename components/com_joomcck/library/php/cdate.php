<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
class CDate extends JDate
{
	const DIR_DATE_TO_SFORMAT = 0;
	const DIR_SFORMAT_TO_DATE = 1;

	public function strtotime($date, $format)
	{
		$format = $this->convertFormat($format);
		$time = $this->formattotime($format, $date);
		if(!$time['month'] || !$time['day'] || !$time['year'])
		{
			return false;
		}
		if(!$time['hour']) $time['hour'] = 12;
		
		$time = mktime($time['hour'],$time['minute'],$time['second'],$time['month'],$time['day'],$time['year']);
		
		return $time;
	}

	function formattotime($format, $date)
	{
		if(function_exists('date_parse_from_format'))
		{
			return date_parse_from_format($format, $date);
		}
		
		$i = 0;
		$pos = 0;
		$output = array(
			'second' => 0, 
			'minute' => 0, 
			'hour' => 0, 
			'day' => 0, 
			'month' => 0, 
			'year' => 0);
		while($i < strlen($format))
		{
			$pat = substr($format, $i, 1);
			$i ++;
			echo $pat;
			switch($pat)
			{
				case 'd': //    Day of the month, 2 digits with leading zeros    01 to 31
					$output['day'] = substr($date, $pos, 2);
					$pos += 2;
				break;
				case 'D': // A textual representation of a day: three letters    Mon through Sun
				//TODO
				break;
				case 'j': //    Day of the month without leading zeros    1 to 31
					$output['day'] = substr($date, $pos, 2);
					if(! is_numeric($output['day']) || ($output['day'] > 31))
					{
						$output['day'] = substr($date, $pos, 1);
						$pos --;
					}
					$pos += 2;
				break;
				case 'm': //    Numeric representation of a month: with leading zeros    01 through 12
					$output['month'] = (int)substr($date, $pos, 2);
					$pos += 2;
				break;
				case 'n': //    Numeric representation of a month: without leading zeros    1 through 12
					$output['month'] = substr($date, $pos, 2);
					if(! is_numeric($output['month']) || ($output['month'] > 12))
					{
						$output['month'] = substr($date, $pos, 1);
						$pos --;
					}
					$pos += 2;
				break;
				case 'Y': //    A full numeric representation of a year: 4 digits    Examples: 1999 or 2003
					$output['year'] = (int)substr($date, $pos, 4);
					$pos += 4;
				break;
				case 'y': //    A two digit representation of a year    Examples: 99 or 03
					$output['year'] = (int)substr($date, $pos, 2);
					$pos += 2;
				break;
				case 'g': //    12-hour format of an hour without leading zeros    1 through 12
					$output['hour'] = substr($date, $pos, 2);
					if(! is_numeric($output['day']) || ($output['hour'] > 12))
					{
						$output['hour'] = substr($date, $pos, 1);
						$pos --;
					}
					$pos += 2;
				break;
				case 'G': //    24-hour format of an hour without leading zeros    0 through 23
					$output['hour'] = substr($date, $pos, 2);
					if(! is_numeric($output['day']) || ($output['hour'] > 23))
					{
						$output['hour'] = substr($date, $pos, 1);
						$pos --;
					}
					$pos += 2;
				break;
				case 'h': //    12-hour format of an hour with leading zeros    01 through 12
					$output['hour'] = (int)substr($date, $pos, 2);
					$pos += 2;
				break;
				case 'H': //    24-hour format of an hour with leading zeros    00 through 23
					$output['hour'] = (int)substr($date, $pos, 2);
					$pos += 2;
				break;
				case 'i': //    Minutes with leading zeros    00 to 59
					$output['minute'] = (int)substr($date, $pos, 2);
					$pos += 2;
				break;
				case 's': //    Seconds: with leading zeros    00 through 59
					$output['second'] = (int)substr($date, $pos, 2);
					$pos += 2;
				break;
				case 'l': // (lowercase 'L')    A full textual representation of the day of the week    Sunday through Saturday
				case 'N': //    ISO-8601 numeric representation of the day of the week (added in PHP 5.1.0)    1 (for Monday) through 7 (for Sunday)
				case 'S': //    English ordinal suffix for the day of the month: 2 characters    st: nd: rd or th. Works well with j
				case 'w': //    Numeric representation of the day of the week    0 (for Sunday) through 6 (for Saturday)
				case 'z': //    The day of the year (starting from 0)    0 through 365
				case 'W': //    ISO-8601 week number of year: weeks starting on Monday (added in PHP 4.1.0)    Example: 42 (the 42nd week in the year)
				case 'F': //    A full textual representation of a month: such as January or March    January through December
				case 'u': //    Microseconds (added in PHP 5.2.2)    Example: 654321
				case 't': //    Number of days in the given month    28 through 31
				case 'L': //    Whether it's a leap year    1 if it is a leap year: 0 otherwise.
				case 'o': //    ISO-8601 year number. This has the same value as Y: except that if the ISO week number (W) belongs to the previous or next year: that year is used instead. (added in PHP 5.1.0)    Examples: 1999 or 2003
				case 'e': //    Timezone identifier (added in PHP 5.1.0)    Examples: UTC: GMT: Atlantic/Azores
				case 'I': // (capital i)    Whether or not the date is in daylight saving time    1 if Daylight Saving Time: 0 otherwise.
				case 'O': //    Difference to Greenwich time (GMT) in hours    Example: +0200
				case 'P': //    Difference to Greenwich time (GMT) with colon between hours and minutes (added in PHP 5.1.3)    Example: +02:00
				case 'T': //    Timezone abbreviation    Examples: EST: MDT ...
				case 'Z': //    Timezone offset in seconds. The offset for timezones west of UTC is always negative: and for those east of UTC is always positive.    -43200 through 50400
				case 'a': //    Lowercase Ante meridiem and Post meridiem    am or pm
				case 'A': //    Uppercase Ante meridiem and Post meridiem    AM or PM
				case 'B': //    Swatch Internet time    000 through 999
				case 'M': //    A short textual representation of a month: three letters    Jan through Dec
				default:
					$pos ++;
			}
		}
		return $output;
	}

	public function convertFormat($format, $direction = self::DIR_SFORMAT_TO_DATE)
	{
		$conversion = array(
			'%%' => '%', 
			'%d' => 'd', 
			'%D' => 'm/d/y', 
			'%F' => 'Y-m-d', 
			'%n' => "\n", 
			'%t' => "\t", 
			'%x' => 'm/d/y', 
			'%s' => 'U', 
			'%c' => 'D M j H:i:s Y', 
			'%X' => 'H:i:s', 
			'%T' => 'H:i:s', 
			'%S' => 's', 
			'%R' => 'H:i', 
			'%r' => 'h:i:s A', 
			'%P' => 'a', 
			'%p' => 'A', 
			'%M' => 'i', 
			'%l' => 'g', 
			'%I' => 'h', 
			'%H' => 'H', 
			'%Y' => 'Y', 
			'%y' => 'y', 
			'%C' => '00', 
			'%g' => 'y', 
			'%G' => 'Y', 
			'%b' => 'M', 
			'%B' => 'F', 
			'%h' => 'M', 
			'%m' => 'm', 
			'%V' => 'W', 
			'%U' => '00', 
			'%W' => '00', 
			'%a' => 'D', 
			'%A' => 'l', 
			'%e' => 'd', 
			'%j' => 'z', 
			'%u' => 'N', 
			'%w' => 'w');
		
		if($direction == self::DIR_SFORMAT_TO_DATE)
		{
			$from = array_keys($conversion);
			$to = $conversion;
		}
		else
		{
			$from = $conversion;
			$to = array_keys($conversion);
		}
		
		return str_replace($from, $to, $format);
	}
}

