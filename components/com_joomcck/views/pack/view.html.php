<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

class JoomcckViewPack extends MViewBase
{
	public function display($tpl = NULL)
	{
		$this->state = $this->get('State');
		$this->item  = $this->get('Item');

		$this->form = $this->get('Form');

		$params = new \Joomla\CMS\Form\Form('params', array('control' => 'params'));
		$params->loadFile(JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'xml/pack.xml');
		$this->params_form   = $params;
		$this->params_groups = array(
			'general' => 'General'
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
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		\Joomla\CMS\Factory::getApplication()->input->set('hidemainmenu', TRUE);
		$user       = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$isNew      = ($this->item->id == 0);
		$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));

		\Joomla\CMS\Toolbar\ToolbarHelper::title(($isNew ? \Joomla\CMS\Language\Text::_('CNEWPACK') : \Joomla\CMS\Language\Text::_('CEDITPACK')), 'packs');

		if(!$checkedOut)
		{
			\Joomla\CMS\Toolbar\ToolbarHelper::apply('pack.apply', 'JTOOLBAR_APPLY');
			\Joomla\CMS\Toolbar\ToolbarHelper::save('pack.save', 'JTOOLBAR_SAVE');
			\Joomla\CMS\Toolbar\ToolbarHelper::custom('pack.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', FALSE);
			if(!$isNew)
			{
				\Joomla\CMS\Toolbar\ToolbarHelper::custom('pack.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', FALSE);
			}
		}
		\Joomla\CMS\Toolbar\ToolbarHelper::cancel('pack.cancel', 'JTOOLBAR_CANCEL');
		//MRToolBar::helpW('http://help.joomcoder.com/joomcck/index.html?filters.htm', 1000, 500);
	}
}
