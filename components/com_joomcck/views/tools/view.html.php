<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.view');

/**
 * View information about joomcck.
 *
 * @package        Joomcck
 * @subpackage     com_joomcck
 * @since          6.0
 */
class JoomcckViewTools extends MViewBase
{
	public function display($tpl = NULL)
	{
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();

		if($app->input->getCmd('layout') == 'form')
		{
			$this->_tool();
		}
		else
		{
			$this->_list();
		}

		parent::display($tpl);
	}

	function _tool()
	{
		$uri = \Joomla\CMS\Uri\Uri::getInstance();

		$this->form   = $this->get('ToolForm');
		$this->tool   = $this->get('Tool');
	}

	function _list()
	{
		JHTML::_('bootstrap.tooltip');
		JHTML::_('bootstrap.modal');

		$this->tools = $this->get('Tools');
	}
}