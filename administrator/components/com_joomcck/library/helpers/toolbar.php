<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

/**
 * Toolbar helper. Helps to build toolbars and subnmenu bars
 *
 * @author Sergey
 * @package		Joomcck
 * @subpackage	com_joomcck
 *
 */
class MRToolBar extends JToolBarHelper
{
	public static function addSubmenu($vName = 'records')
	{
		JSubMenuHelper::addEntry(
			'<img src="'.JUri::root(TRUE).'/media/mint/icons/16/gear.png" align="absmiddle"> '.
			JText::_('XML_SUBMENU_CONFIGURATION'),
			'index.php?option=com_config&view=component&component=com_joomcck&return='.base64_encode(JFactory::getURI()),
			$vName == 'config'
		);
		JSubMenuHelper::addEntry(
			'<img src="'.JUri::root(TRUE).'/media/mint/icons/16/information.png" align="absmiddle"> '.
			JText::_('XML_SUBMENU_ABOUT'),
			'index.php?option=com_joomcck&view=about',
			$vName == 'about'
		);
		JSubMenuHelper::addEntry(
			'<img src="'.JUri::root(TRUE).'/media/mint/icons/16/lifebuoy.png" align="absmiddle"> '.
			JText::_('XML_SUBMENU_SUPPORT'),
			'https://www.joomBoost.com/support/community-forum/category-items/6-community-forum/48-joomcck-8.html',
			$vName == 'html'
		);
	}
}