<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldFieldstype extends \Joomla\CMS\Form\Field\ListField
{
	
	protected $type = 'Fieldstype';

	public function getOptions()
	{


		$fieldsType = $this->buildfieldTypesForSql();
		$typeId = $this->getTypeIdOfCurrentField();

		// get fields list
		$db = \Joomla\CMS\Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select("id AS value, label AS text")
			->from("#__js_res_fields")
			->where("type_id = $typeId AND field_type IN $fieldsType");


		$db->setQuery($query);

		$list = $db->loadObjectList();

		
		$opt = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '', \Joomla\CMS\Language\Text::_('CSELECTFIELD'));
		
		array_unshift($list, $opt);
		
		return $list;

	}

	private function buildfieldTypesForSql(){

		$fieldsType = $this->getAttribute('allowedFieldsType');
		$fieldsType = explode(',', $fieldsType);

		$db = \Joomla\CMS\Factory::getDbo();

		foreach ($fieldsType as &$field){

			$field = $db->quote($field);

		}

		return "(" . implode(',', $fieldsType) . ")";



	}


	public function getTypeIdOfCurrentField(){

		$currentFieldId = \Joomla\CMS\Factory::getApplication()->input->getInt('id',0);

		$db = \Joomla\CMS\Factory::getDbo();
		$query = 'SELECT type_id' .
			' FROM #__js_res_fields' .
			' WHERE id = ' . $currentFieldId;
		$db->setQuery($query);

		return $db->loadResult();

	}


}