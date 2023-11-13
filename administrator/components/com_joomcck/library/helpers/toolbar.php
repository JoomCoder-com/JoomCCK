<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\Helpers\Sidebar;

defined('_JEXEC') or die;

/**
 * Toolbar helper. Helps to build toolbars and subnmenu bars
 *
 * @author Sergey
 * @package		Joomcck
 * @subpackage	com_joomcck
 *
 */
class MRToolBar extends \Joomla\CMS\Toolbar\ToolbarHelper
{
	public static function addSubmenu($vName = 'records')
	{
		Sidebar::addEntry(
			'<img src="'.\Joomla\CMS\Uri\Uri::root(TRUE).'/media/com_joomcck/icons/16/gear.png" align="absmiddle"> '.
			\Joomla\CMS\Language\Text::_('XML_SUBMENU_CONFIGURATION'),
			'index.php?option=com_config&view=component&component=com_joomcck&return='.base64_encode(\Joomla\CMS\Uri\Uri::getInstance()),
			$vName == 'config'
		);
		Sidebar::addEntry(
			'<img src="'.\Joomla\CMS\Uri\Uri::root(TRUE).'/media/com_joomcck/icons/16/information.png" align="absmiddle"> '.
			\Joomla\CMS\Language\Text::_('XML_SUBMENU_ABOUT'),
			'index.php?option=com_joomcck&view=about',
			$vName == 'about'
		);
		Sidebar::addEntry(
			'<img src="'.\Joomla\CMS\Uri\Uri::root(TRUE).'/media/com_joomcck/icons/16/lifebuoy.png" align="absmiddle"> '.
			\Joomla\CMS\Language\Text::_('XML_SUBMENU_SUPPORT'),
			'https://www.joomcoder.com/support/community-forum/category-items/6-community-forum/48-joomcck-8.html',
			$vName == 'html'
		);
	}
}