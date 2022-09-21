<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die ();

JForm::addFieldPath(JPATH_ROOT . '/administrator/components/com_menus/models/fields');
class JoomcckViewSection extends MViewBase
{
	public function display($tpl = NULL)
	{
		if($this->getLayout() == 'fast') {
			$this->form  = $this->get('FormQs');
		} else {
			$this->form  = $this->get('Form');
		}

		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->canDo = 1;

		$params = new JForm('params', array('control' => 'params'));
		$params->loadFile(JPATH_COMPONENT. '/models/forms/params.section.xml');
		$this->params_form   = $params;
		$this->params_groups = array(
			'general'     => JText::_('FS_GENERAL'),
			'events'      => JText::_('FS_EVENTPARAMS'),
			'personalize' => JText::_('FS_PERSPARAMS'),
			'more'        => JText::_('FS_OTHERPARAMS'),
		);

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return FALSE;
		}


		parent::display($tpl);
	}
}
