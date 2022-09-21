<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.view');
class JoomcckViewGroup extends MViewBase
{

	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$this->state = $this->get('State');
		$this->item = $this->get('Item');

		$app->input->set('type_id', $this->state->get('groups.type'));
		$app->input->set('return', $this->state->get('groups.return'));

		$this->form = $this->get('Form');

		if(count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		parent::display($tpl);
	}

	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		JToolBarHelper::title(($isNew ? JText::_('CNEWGROUP') : JText::_('CEDITGROUP')), ($isNew ? 'field_new.png' : 'field_edit.png'));

		if (!$checkedOut){
			JToolBarHelper::apply('group.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('group.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('group.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			if(!$isNew) JToolBarHelper::custom('group.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if($isNew)
		{
			$bar = JToolBar::getInstance();
			$bar->appendButton('Custom', '<a class="btn btn-small" href="'.JRoute::_('index.php?option=com_joomcck&view=groups').'"><i class="icon-cancel "></i> '.JText::_('JTOOLBAR_CANCEL').'</a>', 'cancel');
		}
		else
		{
			JToolBarHelper::cancel('group.cancel', 'JTOOLBAR_CANCEL');
		}
		JToolBarHelper::divider();
	}
}
