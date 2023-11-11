<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
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

		$params = new \Joomla\CMS\Form\Form('params', array(
			'control' => 'params'
		));
		$params->loadFile(JPATH_COMPONENT. '/models/forms/params.type.xml');
		$this->params_form = $params;

		$this->params_groups = array(
			'properties' => \Joomla\CMS\Language\Text::_('FS_GENERAL'),
			'submission' => \Joomla\CMS\Language\Text::_('FS_SUBMISPARAMS'),
			'comments' => \Joomla\CMS\Language\Text::_('FS_COMMPARAMS'),
			'emerald' => \Joomla\CMS\Language\Text::_('FS_EMERALDINTEGRATE')
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
		\Joomla\CMS\Factory::getApplication()->input->set('hidemainmenu', true);

		$user = \Joomla\CMS\Factory::getUser();
		$isNew = ($this->item->id == 0);
		$checkedOut = ! ($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		JToolBarHelper::title(($isNew ? \Joomla\CMS\Language\Text::_('CNEWTYPE') : \Joomla\CMS\Language\Text::_('CEDITTYPE').': '.$this->item->name), ($isNew ? 'type_new.png' : 'type_edit.png'));

		if(! $checkedOut)
		{
			JToolBarHelper::apply('type.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('type.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::save2new('type.save2new');
			if(! $isNew) JToolBarHelper::save2copy('type.save2copy');
		}
		JToolBarHelper::cancel('type.cancel', 'JTOOLBAR_CANCEL');
		//MRToolBar::helpW('http://help.joomcoder.com/joomcck/index.html?filters.htm', 1000, 500);
		JToolBarHelper::divider();
	}
}
