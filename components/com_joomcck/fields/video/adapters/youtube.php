<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

class CVideoAdapterYoutube extends CVideoAdapterAbstarct
{
	static private $tamplate = '<iframe width="%s" height="%s" src="%s://www.youtube.com/embed/%s" frameborder="0" allowfullscreen></iframe>';

	public function __construct($params)
	{
		$this->params = $params;
	}

	public function check($url)
	{
		$i = preg_match('/.*youtu\.be\/(.*)/i', $url, $matches);
		if($i)
		{
			$this->key = $matches[1];
			return true;
		}

		$url = JUri::getInstance($url);
		$v = $url->getVar('v');
		if($v)
		{
			$this->key = $v;
			return true;
		}
		return false;
	}

	public function getHtml()
	{
		return sprintf(self::$tamplate,
				$this->params->get('width', 350),
				$this->params->get('height', 200),
				JUri::getInstance()->getScheme(),
				$this->key
			);
	}
}