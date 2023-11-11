<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.modal', 'a.cmodal');

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
		$app = \Joomla\CMS\Factory::getApplication();
		$this->state = $this->get('State');
		$this->item = $this->get('Item');
		$type_id = $this->item->type_id ? $this->item->type_id  : $this->state->get('filter.type');

		$this->form = $this->get('Form');
		$this->user = \Joomla\CMS\Factory::getUser();
		$params = new \Joomla\CMS\Form\Form('params', array('control' => 'params'));
		$params->loadFile(JPATH_COMPONENT. '/models/forms/params.field.xml');
		$this->params_form = $params;

		$this->params_groups = array('core' => \Joomla\CMS\Language\Text::_('FS_GENERAL'),
		'emerald' => \Joomla\CMS\Language\Text::_('FS_EMERALDINTEGRATE')
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
