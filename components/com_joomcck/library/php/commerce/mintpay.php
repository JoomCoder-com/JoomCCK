<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

require_once JPATH_ROOT . '/components/com_joomcck/library/php/commerce/mintpayabstract.php';
class MintPay
{

	/**
	 * 
	 * @param string $provider
	 * @return MintPayAbstract object
	 */
	public static function getInstance($field_id, $provider, $log)
	{
		static $instances = array();

		$key = "$field_id-$provider";
		
		if (isset($instances[$key]))
		{
			return $instances[$key];
		}
		$file = JPATH_ROOT . "/components/com_joomcck/gateways/{$provider}/{$provider}.php";
		if (!JFile::exists($file))
		{
			throw new Exception( JText::sprintf('CGATEWAYNOTFOUND', $provider),500);

		}
		require_once $file;
		
		$class_name = 'MintPay' . ucfirst($provider);
		
		if (!class_exists($class_name))
		{
			throw new Exception( JText::sprintf('CGATEWAYNOTFOUND', $provider),500);

		}	
		
		$lang = JFactory::getLanguage();
		$tag = $lang->getTag();
		if (!JFile::exists(JPATH_BASE . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $tag . DIRECTORY_SEPARATOR . $tag . '.com_joomcck_field_' . $provider . '.ini'))
		{
			$tag == 'en-GB';
		}		
		$lang->load('com_joomcck_gateway_' . $provider, JPATH_BASE, $tag);
		
		$instances[$key] = new $class_name();
		$instances[$key]->log = $log;
		$instances[$key]->provider = strtolower($provider);
		
		return $instances[$key];
	}
}