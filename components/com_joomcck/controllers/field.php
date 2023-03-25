<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\MVC\View\GenericDataException;

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

			throw new GenericDataException(JText::_('CERRNOFILEID'), 500);

			return;
		}

		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . 'tables/field.php');
		$field_table = JTable::getInstance('Field', 'JoomcckTable');
		$field_table->load($field_id);

		if(!$field_table->id)
		{
			throw new GenericDataException(JText::_('CERRNOFILEID'), 500);

			return;
		}


		$field_path =  JPATH_ROOT . '/components/com_joomcck/fields' . DIRECTORY_SEPARATOR . $field_table->field_type . DIRECTORY_SEPARATOR . $field_table->field_type . '.php';
		if(!JFile::exists($field_path))
		{
			throw new GenericDataException(JText::_('CERRNOFILEHDD'), 500);

			return;
		}

		if(!$func)
		{
			throw new GenericDataException(JText::_('AJAX_NOFUNCNAME'), 500);
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
			throw new GenericDataException(JText::_('CCLASSNOTFOUND'), 500);
			return ;
		}

		$fieldclass = new $classname($field_table, $default);

		if(!method_exists($fieldclass, $func))
		{
			throw new GenericDataException(JText::_('AJAX_METHODNOTFOUND'), 500);
			return ;
		}
		$result = $fieldclass->$func($_POST, $record, $this, (count($_POST) ? null : $_GET) );

		if($fieldclass->getErrors())
		{
			throw new GenericDataException($fieldclass->getError(), 500);

			return;
		}
	}
}