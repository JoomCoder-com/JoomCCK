<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

class CVideoAdapterHelper
{

	static function getVideoCode($params, $url)
	{
		$adapters = $params->get('params.adapters', false);
		
		if(! $adapters)
		{
			return JText::_('F_ADAPTERNOTSELECTED');
		}
		
		settype($adapters, 'array');
		
		$adp = new CVideoAdapterAbstarct($params);
		
		foreach($adapters as $adapter_name)
		{
			$instance = $adp->getAdapter($adapter_name);
			if($instance->check($url))
			{
				return $instance->getHtml();
			}
		}
		
		return JText::sprintf('F_UNKNOWNLINK', JHtml::link($url, $url));
	}

	static public function constrain($object, $max_width)
	{
		preg_match("/width=(?:\"|&quot;|')?([0-9]*)p?x?(?:\"|&quot;|')?/i", $object, $match);
		$width = @$match[1];
		preg_match("/height=(?:\"|&quot;|')?([0-9]*)p?x?(?:\"|&quot;|')?/i", $object, $match);
		$height = @$match[1];

		if(($width && $height) && $width > $max_width)
		{
			$k = $width / $height;
			$new_width = $max_width;
			$new_height = round($max_width / $k);
			
			$what = array(
				"width=\"{$width}\"",
				"height=\"{$height}\"",
				"width=\"{$width}px\"",
				"height=\"{$height}px\"",
				"width='{$width}'",
				"height='{$height}'",
				"width='{$width}px'",
				"height='{$height}px'",
				"width:{$width}",
				"width: {$width}",
				"height:{$height}",
				"height: {$height}"
			);
			$for = array(
				"width=\"{$new_width}\"",
				"height=\"{$new_height}\"",
				"width=\"{$new_width}\"",
				"height=\"{$new_height}\"",
				"width=\"{$new_width}\"",
				"height=\"{$new_height}\"",
				"width=\"{$new_width}\"",
				"height=\"{$new_height}\"",
				"width:{$new_width}",
				"width: {$new_width}",
				"height:{$new_height}",
				"height: {$new_height}"
			);
			
			$object = str_replace($what, $for, $object);
		}
		
		return $object;
	}

	static public function getVideoEmbed($text)
	{
		if(empty($text))
		{
			return NULL;
		}
		
		if(strstr($text, '<object'))
		{
			preg_match('/<object.*<\/object>/isU', $text, $matches);
			if(isset($matches[0]))
			{
				return $matches[0];
			}
		}
		elseif (strstr($text, '<iframe'))
		{
			preg_match('/<iframe.*<\/iframe>/isU', $text, $matches);
			if(isset($matches[0]))
			{
				return $matches[0];
			}
		}
		elseif (strstr($text, '<embed'))
		{
			preg_match('/<embed.*<\/embed>/isU', $text, $matches);
			if(isset($matches[0])) 
			{
				return $matches[0];
			}
		}
		
		return NULL;
	}
}

class CVideoAdapterAbstarct
{

	public function __construct($params)
	{
		$this->params = $params;
	}

	public function getAdapter($name)
	{
		static $adapters = array();
		
		if(isset($adapters[$name]))
		{
			//return $adapters[$name];
		}
		
		require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'adapters' . DIRECTORY_SEPARATOR . $name . '.php';
		
		$class = "CVideoAdapter" . ucfirst($name);
		
		$adapters[$name] = new $class($this->params);
		
		return $adapters[$name];
	}

	protected function getObject($link)
	{
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $this->key);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		
		if(preg_match("/<object(.*)<\/object>/isU", $data, $matches))
		{
			$object = CVideoAdapterHelper::constrain($matches[0], $this->params->get('width'));
			
			return $object;
		}
		else
		{
			return JText::sprintf('Video not found by URL %s', $this->key);
		}
	}
}