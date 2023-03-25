<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT. '/components/com_joomcck/library/php/fields/joomcckrelate.php';


require_once JPATH_ROOT. '/components/com_joomcck/api.php';

class JFormFieldCParent extends CFormFieldRelate
{
	public function getInput()
	{
		$name = "jform[fields][$this->id]";

		ArrayHelper::clean_r($this->value);
		if($this->request->getInt('fand') && $this->request->getInt('field_id') == $this->id)
		{
			$this->value[] = $this->request->getInt('fand');
			$this->params->set('params.input_mode', 10);
		}

		if($this->params->get('params.child_section',0) && $this->params->get('params.child_field',0)){
			$this->inputvalue = $this->_render_input(
				$this->params->get('params.input_mode',2),
				$name,
				$this->params->get('params.child_section',0),
				MModelBase::getInstance('Fields', 'JoomcckModel')->getFieldTypeId($this->params->get('params.child_field',0))
			);
		}




		return $this->_display_input();
	}

	public function onStoreValues($validData, $record)
	{
		$db = JFactory::getDbo();
		$db->setQuery('DELETE FROM `#__js_res_record_values` WHERE record_id = '.$validData['id'].' AND field_id = '.$this->id);
		$db->execute();

		$table = JTable::getInstance('Record_values', 'JoomcckTable');

		$field = JTable::getInstance('Field', 'JoomcckTable');
		$field->load($this->params->get('params.child_field'));

		$save['field_type'] = $field->field_type;
		$save['field_label'] = $field->label;
		$save['field_key'] = 'k'.md5($field->label.'-'.$field->field_type);
		$save['field_id'] = $field->id;
		$save['section_id'] = $this->params->get('params.child_section');
		//$save['category_id'] = 0;
		$save['params'] = '';
		$save['type_id'] = $field->type_id;
		$save['field_value'] = trim($validData['id']);
		$save['user_id'] = JFactory::getUser()->get('id');

		$db->setQuery('DELETE FROM `#__js_res_record_values` WHERE field_value = '.$validData['id'].' AND field_id = '.$field->id);
		$db->execute();

		settype($this->value, 'array');

		foreach ($this->value AS $value)
		{
			if(!$value) continue;

			$save['record_id'] = trim($value);
			$table->load($save);
			if($table->id)
			{
				//var_dump($table->id);
			}
			else
			{
				$table->save($save);
			}
			$table->reset();
			$table->id = NULL;
		}
	}


	public function onRenderFilter($section, $module = false)
	{
		$this->isFilter = TRUE;

		if($this->params->set('params.filter_user_strict', 0) == 0)
		{
			$this->user_strict = FALSE;
		}

		$name = "filters[{$this->key}]";
		ArrayHelper::clean_r($this->value);

		$limit = $this->params->get('params.multi_limit', 0);
		$this->params->set('params.multi_limit', 100);

		$this->filter = $this->_render_input($this->params->get('params.filter_style'),  $name,
			$this->params->get('params.child_section'),
			MModelBase::getInstance('TFields', 'JoomcckModel')->getFieldTypeId($this->params->get('params.child_field')));

		return $this->_display_filter($section, $module);
	}

	public function onFilterWhere($section, &$query)
	{
		ArrayHelper::clean_r($this->value);
		$field_id = $this->params->get('params.child_field');

		$ids = $this->getIds("SELECT field_value FROM #__js_res_record_values WHERE record_id IN(".implode(',', $this->value).") AND field_id = {$field_id}");
		return $ids;
	}

	public function onFilterWornLabel($section)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(TRUE);
		$query->from('#__js_res_record');
		$query->select('title');
		$query->where('id IN ('.implode(',', $this->value).')');
		$db->setQuery($query);
		$array = $db->loadColumn();
		return implode(', ', $array);
	}


	public function onRenderFull($record, $type, $section)
	{
		return $this->_render('full', $record, $this->params->get('params.child_field'),
			MModelBase::getInstance('Fields', 'JoomcckModel')->getFieldTypeId((int)$this->params->get('params.child_field', 0)),
			$this->params->get('params.child_section'), 'show_children');
	}
	public function onRenderList($record, $type, $section)
	{
		return $this->_render('list', $record, $this->params->get('params.child_field'),
			MModelBase::getInstance('Fields', 'JoomcckModel')->getFieldTypeId((int)$this->params->get('params.child_field', 0)),
			$this->params->get('params.child_section'), 'show_children');
	}

	public function onGetList($params)
	{
		$db = JFactory::getDbo();
		$user = JFactory::getUser();

		$name = "jform[fields][$this->id]";
		$attribs = $html = '';

		$query = $db->getQuery(TRUE);
		$query->select('id, title, null, title');
		$query->from('#__js_res_record');
		if(CStatistics::hasUnPublished($this->params->get('params.child_section')))
		{
			$query->where('published = 1');
		}
		$query->where('hidden = 0');
		$query->where('section_id = '.$this->params->get('params.child_section'));
		$query->where('type_id IN (SELECT type_id FROM #__js_res_fields WHERE `id` = '.(int)$this->params->get('params.child_field').')');
		if(!in_array($this->params->get('params.strict_to_user'), $user->getAuthorisedViewLevels()))
		{
			if($this->params->get('params.strict_to_user_mode') > 1)
			{
				$record = JTable::getInstance('Record', 'JoomcckTable');
				$record->load($params['record_id']);
				$user_id = $record->user_id;
				if(!$user_id && $this->params->get('params.strict_to_user_mode') == 3)
				{
					$user_id = $user->get('id');
				}
			}
			else
			{
				$user_id = $user->get('id');
			}
			$user_id = $user_id ? $user_id : 1;

			$isme = ((int)$user_id === (int)$user->get('id', null));

			$section = ItemsStore::getSection($this->params->get('params.child_section'));
			if($section->params->get('personalize.post_anywhere'))
			{
				if($section->params->get('personalize.records_mode') == 1 || $isme)
				{
					// Show all records posted on user home and all records posted by this user on homes of others.
					$query->where("(id IN (SELECT record_id FROM `#__js_res_record_repost` WHERE host_id = {$user_id}) OR user_id = {$user_id})");
				}
				else
				{
					// Show only records posted on this user home
					$query->where("id IN (SELECT record_id FROM `#__js_res_record_repost` WHERE host_id = {$user_id})");
				}
			}
			else
			{
				$query->where('user_id = ' . $user_id);
			}
		}


		$db->setQuery($query);
		return $db->loadRowList();
	}
}
