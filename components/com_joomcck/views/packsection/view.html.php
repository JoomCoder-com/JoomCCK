<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

class JoomcckViewPacksection extends MViewBase
{
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item = $this->get('Item');

		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::root(TRUE) . '/libraries/mint/forms/style.css');

		$this->form = $this->get('Form');

		if($this->item->id)
		{
			$this->parameters = MModelBase::getInstance('Packsection', 'JoomcckModel')->getSectionForm($this->item->section_id, $this->item->params);
		}

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
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
// 		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		JToolBarHelper::title(($isNew ? JText::_('CNEWPACKSECTION') : JText::_('CEDITPACKSECTION')), 'sections');

// 		if (!$checkedOut)
		{
			JToolBarHelper::apply('packsection.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('packsection.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('packsection.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}
		JToolBarHelper::cancel('packsection.cancel', 'JTOOLBAR_CANCEL');
		//MRToolBar::helpW('http://help.joomcoder.com/joomcck/index.html?filters.htm', 1000, 500);
	}
}
