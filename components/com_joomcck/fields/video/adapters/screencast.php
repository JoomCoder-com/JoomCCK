<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class CVideoAdapterScreencast extends CVideoAdapterAbstarct
{
	public function __construct($params)
	{
		$this->params = $params;
	}
	
	public function check($url)
	{
		//$i = preg_match('/.*screencast\.com\/(.*)/i', $url, $matches);
		//if($i)
		//{
			$this->key = $url;
			return true;
		//}
			
		//return false;
	}
	
	public function getHtml()
	{
		return $this->getObject($this->key);
	}
}