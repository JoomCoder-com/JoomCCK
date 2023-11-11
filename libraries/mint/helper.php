<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') or die;

class Mint
{
	static function _($text, $default = NULL, $jsSafe = FALSE, $interpretBackSlashes = TRUE, $script = FALSE)
	{
		// no need to continue if empty
		if(empty($text))
			return '';

		$text = \Joomla\CMS\Filter\InputFilter::getInstance([], [], 1, 1)->clean(strip_tags($text), 'string');

		$key   = strtoupper($text);
		$trans = \Joomla\CMS\Language\Text::_($key, $jsSafe, $interpretBackSlashes, $script);

		if($key === $trans)
		{
			return $default ? $default : $text;
		}

		return $trans;
	}

	static function markdown2($val, $extra = FALSE)
	{
		if(!class_exists('MarkdownInterface'))
		{
			include_once __DIR__ . '/Markdown/Markdown.inc.php';
		}

		if($extra)
		{
			include_once __DIR__ . '/Markdown/MarkdownExtra.inc.php';

			$val = \Michelf\MarkdownExtra::defaultTransform($val);
		}
		else
		{
			$val = \Michelf\Markdown::defaultTransform($val);
		}

		return $val;
	}
	static function markdown($val, $extra = FALSE)
	{
		if(!class_exists('MarkdownInterface'))
		{
			include_once __DIR__ . '/Markdown/MarkdownExtra.inc.php';
		}

		$val = \Michelf\MarkdownExtra::defaultTransform($val);

		return $val;
	}

	static public function loadLayout($name, $path)
	{
		$options = array();
		$params  = \Joomla\CMS\Component\ComponentHelper::getParams('com_emerald');

		if($params->get('tmpl_prefix'))
		{
			$options['suffixes'] = [$params->get('tmpl_prefix')];
		}

		return new \Joomla\CMS\Layout\FileLayout($name, $path, $options);
	}
}

class MintArrayHelper
{

	public static function trim_r(&$array)
	{
		if(!is_array($array))
		{
			settype($array, 'array');
		}

		foreach($array as &$val)
		{
			if(is_array($val))
			{
				$val = self::trim_r($val);
			}
			elseif(is_string($val))
			{
				$val = trim($val);
			}
		}
	}

	public static function clean_r(&$array, $zero = FALSE)
	{
		if(!is_array($array))
		{
			settype($array, 'array');
		}

		foreach($array as $k => &$val)
		{
			if(is_array($val))
			{
				self::clean_r($val);
			}
			elseif(is_string($val))
			{
				$val = trim($val);
				$val = str_replace("\r", "", $val);
			}

			if(($val != "0" && empty($val)) || ($val == "0" && $zero))
			{
				unset($array[$k]);
				continue;
			}
		}
		if(!$array)
		{
			$array = array();
		}
	}

	public static function tolower_r(&$array)
	{
		if(!is_array($array))
		{
			settype($array, 'array');
		}
		foreach($array as $k => &$val)
		{
			$val = \Joomla\String\StringHelper::strtolower($val);
		}
		if(!$array)
		{
			$array = array();
		}
	}

	public static function separate_r(&$array, $separator = '^')
	{
		if(!is_array($array))
		{
			settype($array, 'array');
		}
		foreach($array as $k => &$val)
		{
			$val = explode($separator, $val);
			$val = $val[0];
		}
		if(!$array)
		{
			$array = array();
		}
	}
}