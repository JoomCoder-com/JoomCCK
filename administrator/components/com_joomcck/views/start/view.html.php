<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;

defined('_JEXEC') || die();
jimport('joomla.application.component.view');
/**
 * View information about joomcck.
 *
 * @package        Joomcck
 * @subpackage    com_joomcck
 * @since        6.0
 */
class JoomcckViewStart extends MViewBase
{

    public function display($tpl = null)
    {
		$this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        \Joomla\CMS\Toolbar\ToolbarHelper::title(\Joomla\CMS\Language\Text::_('Start'));
    }

	public function checkAdminDashboardMenuItem(){

		\Joomla\CMS\Table\Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_menus/tables');
		$menu_table = \Joomla\CMS\Table\Table::getInstance('Menu', 'JTable', []);

		$result = $menu_table->load([
			"link" => 'index.php?option=com_joomcck&view=cpanel',
			"type" => 'component'
		]);


		return $result;


	}


	public function getAdminDashboardLink(){

		\Joomla\CMS\Table\Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_menus/tables');
		$menu_table = \Joomla\CMS\Table\Table::getInstance('Menu', 'JTable', []);

		$menu_table->load([
			"link" => 'index.php?option=com_joomcck&view=cpanel',
			"type" => 'component'
		]);

		$live_site    = substr(\Joomla\CMS\Uri\Uri::root(), 0, -1);
		$url          = $live_site . '/index.php?option=com_joomcck&view=cpanel&Itemid=' . $menu_table->id;

		return $url;


	}

}



