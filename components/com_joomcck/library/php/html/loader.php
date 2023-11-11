<?php
/**
 * Joomcck by joomcoder
* a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
* Author Website: https://www.joomcoder.com/
* @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die();
require_once JPATH_ROOT. '/libraries/joomla/form/fields/groupedlist.php';

class JHTMLLoader
{
	public static function clickover($rel, $attr = array())
	{
		ArrayHelper::clean_r($attr);
		
		$options = json_encode($attr);
		\Joomla\CMS\Factory::getDocument()->addScript(\Joomla\CMS\Uri\Uri::root(TRUE).'/media/com_joomcck/js/bootstrap/clickover/clickover.js');
		\Joomla\CMS\Factory::getDocument()->addScriptDeclaration("jQuery(document).ready(function(){
			jQuery('*[rel=\"{$rel}\"]').clickover({$options});
		});");
	}
	public static function modal()
	{
		\Joomla\CMS\Factory::getDocument()->addScript(\Joomla\CMS\Uri\Uri::root(TRUE).'/media/com_joomcck/vendors/jquery-modal/jquery.modal.js');
		\Joomla\CMS\Factory::getDocument()->addStyleSheet(\Joomla\CMS\Uri\Uri::root(TRUE).'/media/com_joomcck/vendors/jquery-modal/jquery.modal.css');
	}
}
