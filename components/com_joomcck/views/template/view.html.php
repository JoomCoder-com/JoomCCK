<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

class JoomcckViewTemplate extends MViewBase
{

	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		$this->form = $this->get('Form');
		$this->form->setFieldAttribute('source', 'syntax', JFactory::getApplication()->input->get('ext'));

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			throw new Exception( implode("\n", $errors),500);

		}

		parent::display($tpl);
	}
}
