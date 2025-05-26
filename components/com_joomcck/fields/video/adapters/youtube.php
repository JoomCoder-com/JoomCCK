<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;

defined('_JEXEC') or die();

class CVideoAdapterYoutube extends CVideoAdapterAbstarct
{

	public $key;
	public $width;

	static private $tamplate = '<iframe  src="https://www.youtube.com/embed/%s" frameborder="0" allowfullscreen></iframe>';

	public function __construct($params)
	{
		$this->params = $params;
		$this->width = $this->params->get('params.default_width','100%');


	}

	public function check($url)
	{
		$i = preg_match('/.*youtu\.be\/(.*)/i', $url, $matches);
		if($i)
		{
			$this->key = $matches[1];
			return true;
		}

		$url = \Joomla\CMS\Uri\Uri::getInstance($url);
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

		return Layout::render('output.type.embedPlayers.youtube', [
			'key' => $this->key,
			'width' => $this->width
		],JPATH_ROOT . '/components/com_joomcck/fields/video/layouts');

	}
}