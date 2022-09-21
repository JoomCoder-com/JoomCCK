<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
jimport('mint.mvc.controller.admin');
class JoomcckControllerField extends MControllerAdmin
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		if(!$this->input)
		{
			$this->input = JFactory::getApplication()->input;
		}
	}

	public function call()
	{
		$func  = $this->input->get('func');
		$field_id = $this->input->getInt('field_id');
		$record_id = $this->input->getInt('record_id');

		if(!$field_id)
		{
			JError::raiseError(500, JText::_('CERRNOFILEID'));
			return;
		}

		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . 'tables/field.php');
		$field_table = JTable::getInstance('Field', 'JoomcckTable');
		$field_table->load($field_id);

		if(!$field_table->id)
		{
			JError::raiseError(500, JText::_('CERRNOFILED'));
			return;
		}


		$field_path =  JPATH_ROOT . '/components/com_joomcck/fields' . DIRECTORY_SEPARATOR . $field_table->field_type . DIRECTORY_SEPARATOR . $field_table->field_type . '.php';
		if(!JFile::exists($field_path))
		{
			JError::raiseError(500, JText::_('CERRNOFILEHDD'));
			return;
		}

		if(!$func)
		{
			JError::raiseError(500, JText::_('AJAX_NOFUNCNAME'));
			return;
		}

		require_once  $field_path;


		$default = array();
		$record = NULL;
		if($record_id)
		{
			$record_model = MModelBase::getInstance('Record', 'JoomcckModel');
			$record = $record_model->getItem($record_id);
			$values = json_decode($record->fields, TRUE);
			$default = @$values[$field_id];
		}

		$classname = 'JFormFieldC' . ucfirst($field_table->field_type);
		if(!class_exists($classname))
		{
			JError::raiseError(500, JText::_('CCLASSNOTFOUND'));
			return ;
		}

		$fieldclass = new $classname($field_table, $default);

		if(!method_exists($fieldclass, $func))
		{
			JError::raiseError(500, JText::_('AJAX_METHODNOTFOUND'));
			return ;
		}
		$result = $fieldclass->$func($_POST, $record, $this, (count($_POST) ? null : $_GET) );

		if($fieldclass->getErrors())
		{
			JError::raiseError(500, $fieldclass->getError());
			return;
		}
	}
}