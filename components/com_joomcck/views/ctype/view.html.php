<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

class JoomcckViewCType extends MViewBase
{

	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$this->form = $this->get('Form');

		$params = new JForm('params', array(
			'control' => 'params'
		));
		$params->loadFile(JPATH_COMPONENT. '/models/forms/params.type.xml');
		$this->params_form = $params;

		$this->params_groups = array(
			'properties' => JText::_('FS_GENERAL'),
			'submission' => JText::_('FS_SUBMISPARAMS'),
			'comments' => JText::_('FS_COMMPARAMS'),
			'emerald' => JText::_('FS_EMERALDINTEGRATE')
		);

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

		$user = JFactory::getUser();
		$isNew = ($this->item->id == 0);
		$checkedOut = ! ($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		JToolBarHelper::title(($isNew ? JText::_('CNEWTYPE') : JText::_('CEDITTYPE').': '.$this->item->name), ($isNew ? 'type_new.png' : 'type_edit.png'));

		if(! $checkedOut)
		{
			JToolBarHelper::apply('type.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('type.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::save2new('type.save2new');
			if(! $isNew) JToolBarHelper::save2copy('type.save2copy');
		}
		JToolBarHelper::cancel('type.cancel', 'JTOOLBAR_CANCEL');
		//MRToolBar::helpW('http://help.JoomBoost.com/joomcck/index.html?filters.htm', 1000, 500);
		JToolBarHelper::divider();
	}
}
