<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die ();

\Joomla\CMS\Form\Form::addFieldPath(JPATH_ROOT . '/administrator/components/com_menus/models/fields');
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
			'general'     => \Joomla\CMS\Language\Text::_('FS_GENERAL'),
			'events'      => \Joomla\CMS\Language\Text::_('FS_EVENTPARAMS'),
			'personalize' => \Joomla\CMS\Language\Text::_('FS_PERSPARAMS'),
			'more'        => \Joomla\CMS\Language\Text::_('FS_OTHERPARAMS'),
		);

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			throw new Exception( implode("\n", $errors),500);


		}


		parent::display($tpl);
	}
}
