<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class CVideoAdapterVimeo extends CVideoAdapterAbstarct
{
	static private $tamplate = '<iframe src="//player.vimeo.com/video/%s?portrait=0&amp;color=c9ff23"
			width="%s" height="%s" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

	public function __construct($params)
	{
		$this->params = $params;
	}
	
	public function check($url)
	{
		$i = preg_match('/.*vimeo\.com\/(.*)/i', $url, $matches);
		if($i)
		{
			$this->key = $matches[1];
			return true;
		}
			
		return false;
	}
	
	public function getHtml()
	{
		return sprintf(self::$tamplate,
				$this->key,
				$this->params->get('width', 350),
				$this->params->get('height', 200),
				$this->key
			);
	}
}