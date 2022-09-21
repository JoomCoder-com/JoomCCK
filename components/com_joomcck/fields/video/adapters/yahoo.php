<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class CVideoAdapterYahoo extends CVideoAdapterAbstarct
{
	static private $tamplate = '<iframe frameborder="0" width="%s" height="%s" src="http://d.yimg.com/nl/omg/site/player.html#shareUrl=%s&startScreenCarouselUI=hide&vid=%s&browseCarouselUI=hide&repeat=0"></iframe>';

	public function __construct($params)
	{
		$this->params = $params;
	}
	
	public function check($url)
	{
		$i = preg_match('/.*(screen\.yahoo\.com\/.*\.html).*/iU', $url, $matches);
		if($i)
		{
			$this->key = urlencode('http://'.$matches[1]);
			$i = preg_match('/-([0-9]*)\.html/i', $this->key, $matches);
			
			if(!$i)
			{
				return false;
			}
			
			$this->key2 = $matches[1];
			
			return true;
		}
			
		return false;
	}
	
	public function getHtml()
	{
		return sprintf(self::$tamplate,
				$this->params->get('width', 350),
				$this->params->get('height', 200),
				$this->key,
				$this->key2
			);
	}
}