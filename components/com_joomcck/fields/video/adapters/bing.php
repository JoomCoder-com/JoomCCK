<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class CVideoAdapterBing extends CVideoAdapterAbstarct
{
	static private $tamplate = '<iframe width="%s" height="%s" frameborder="0" marginwidth="0" marginheight="0" scrolling="no" src="http://hub.video.msn.com/embed/%s/?vars=c3luZGljYXRpb249dGFnJmxpbmtiYWNrPWh0dHAlM0ElMkYlMkZ3d3cuYmluZy5jb20lMkZ2aWRlb3MlMkZicm93c2UmY29uZmlnQ3NpZD1NU05WaWRlbyZmcj1zaGFyZWVtYmVkLXN5bmRpY2F0aW9uJmxpbmtvdmVycmlkZTI9aHR0cCUzQSUyRiUyRnd3dy5iaW5nLmNvbSUyRnZpZGVvcyUyRmJyb3dzZSUzRm1rdCUzRGVuLXVzJTI2dmlkJTNEJTdCMCU3RCUyNmZyb20lM0QmbWt0PWVuLXVzJmJyYW5kPXY1JTVFNTQ0eDMwNiZjb25maWdOYW1lPXN5bmRpY2F0aW9ucGxheWVy"></iframe>';

	public function __construct($params)
	{
		$this->params = $params;
	}
	
	public function check($url)
	{
		$i = preg_match('/.*bing.com\/videos\/browse.*&vid=(.*)&.*/iU', $url, $matches);
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
				$this->params->get('width', 350),
				$this->params->get('height', 200),
				$this->key
			);
	}
}