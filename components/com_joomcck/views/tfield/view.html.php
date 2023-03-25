<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

JHTML::_('bootstrap.modal', 'a.cmodal');

jimport('joomla.application.component.view');
class JoomcckViewTField extends MViewBase
{

	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$type_id = $this->item->type_id ? $this->item->type_id  : $this->state->get('filter.type');

		$this->form = $this->get('Form');
		$this->user = JFactory::getUser();
		$params = new JForm('params', array('control' => 'params'));
		$params->loadFile(JPATH_COMPONENT. '/models/forms/params.field.xml');
		$this->params_form = $params;

		$this->params_groups = array('core' => JText::_('FS_GENERAL'),
		'emerald' => JText::_('FS_EMERALDINTEGRATE')
		);

		if($this->item->id)
		{

			$this->parameters = MModelBase::getInstance('TField', 'JoomcckModel')->getFieldForm($this->item->field_type, $this->item->params);
		}

		$app->input->set('type_id', $type_id);

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			throw new Exception( implode("\n", $errors),500);

		}

		//$this->addToolbar();
		parent::display($tpl);
	}
}
