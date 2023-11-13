<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.view');
class JoomcckViewComm extends MViewBase
{
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');
		
		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			throw new Exception( implode("\n", $errors),500);

		}
		
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		\Joomla\CMS\Factory::getApplication()->input->set('hidemainmenu', true);
		
		$user		= \Joomla\CMS\Factory::getApplication()->getIdentity();
		$isNew		= ($this->item->id == 0);
		
		\Joomla\CMS\Toolbar\ToolbarHelper::title(\Joomla\CMS\Language\Text::_('CEDITCOMMENT'), 'comments.png');
		
		\Joomla\CMS\Toolbar\ToolbarHelper::apply('comment.apply', 'JTOOLBAR_APPLY');
		\Joomla\CMS\Toolbar\ToolbarHelper::save('comment.save', 'JTOOLBAR_SAVE');
		
		\Joomla\CMS\Toolbar\ToolbarHelper::cancel('comment.cancel', 'JTOOLBAR_CANCEL');
		//MRToolBar::helpW('http://help.joomcoder.com/joomcck/index.html?filters.htm', 1000, 500);
		\Joomla\CMS\Toolbar\ToolbarHelper::divider();
	}
}