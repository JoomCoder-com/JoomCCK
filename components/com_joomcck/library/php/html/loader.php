<?php
/**
 * Joomcck by JoomBoost
* a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
* Author Website: https://www.joomBoost.com/
* @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
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
		JFactory::getDocument()->addScript(JUri::root(TRUE).'/media/mint/js/bootstrap/clickover/clickover.js');
		JFactory::getDocument()->addScriptDeclaration("jQuery(document).ready(function(){
			jQuery('*[rel=\"{$rel}\"]').clickover({$options});
		});");
	}
	public static function modal()
	{
		JFactory::getDocument()->addScript(JUri::root(TRUE).'/media/mint/vendors/jquery-modal/jquery.modal.js');
		JFactory::getDocument()->addStyleSheet(JUri::root(TRUE).'/media/mint/vendors/jquery-modal/jquery.modal.css');
	}
}
