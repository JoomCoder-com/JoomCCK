<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckfield.php';

class CFormFieldSelectable extends CFormField
{

	public function onJSValidate()
	{
    }
    
    public function _getPillValue($v) {
        return [
            "id" => $v,
            "text" => JText::_($v)
        ];
    }

	public function _getPillObject($v) {

		$object = new stdClass();

		$object->id = $v;
		$object->text = JText::_($v);

		return $object;
	}

    public function _getPillValues($v,$asObjects = false) {
        $out = [];
        if(empty($v)){
            return $out;
        } 
        foreach($v AS $val){

			if($asObjects)
				$out[] = $this->_getPillObject($val);
			else
                $out[] = $this->_getPillValue($val);
        }
        return $out;
    }

	public function validateField($value, $record, $type, $section)
	{

		return parent::validateField($value, $record, $type, $section);
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		ArrayHelper::clean_r($value);
		ArrayHelper::separate_r($value, $this->params->get('params.color_separator', '^'));

		foreach($value AS &$val)
		{
			$val = JText::_($val);
		}


		return implode(', ', $value);
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		if(!$value)
		{
			return;
		}
		if($this->params->get('params.sql_source'))
		{
			$values = $this->_getSqlValues(TRUE);

			settype($value, 'array');
			foreach($value as &$v)
			{
				$out[$v] = $values[$v];
			}
			$value = $out;
		}
		else
		{
			ArrayHelper::clean_r($value);
			if($this->params->get('params.total_limit'))
			{
				$value = array_chunk($value, $this->params->get('params.total_limit'));
				$value = $value[0];
				ArrayHelper::clean_r($value);
			}
		}

		return $value;
	}

	public function onStoreValues($validData, $record)
	{
		if($this->params->get('params.save_new', 1) && isset($this->value))
		{
			$list = explode("\n", str_replace("\r", "", $this->params->get('params.values', '')));
			$list = $list2 = array_values($list);
			ArrayHelper::clean_r($list);
			ArrayHelper::tolower_r($list2);
			ArrayHelper::separate_r($list2, $this->params->get('params.color_separator', '^'));
			ArrayHelper::clean_r($this->value);

			$save = FALSE;
			foreach($this->value as $value)
			{
				$val = explode($this->params->get('params.color_separator', '^'), $value);
				if(!in_array(\Joomla\String\StringHelper::strtolower($val[0]), $list2))
				{
					$list[] = $value;
					$save = TRUE;
				}
			}
			ArrayHelper::clean_r($list);

			if($save)
			{
				$this->params->set('params.values', implode("\n", $list));
				$params = $this->params->toString();

				$table = JTable::getInstance('Field', 'Joomccktable');
				$table->load($this->id);
				$table->params = $params;
				$table->store();
			}
		}

		return $this->value;
	}

	public function onRenderFilter($section, $module = FALSE)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(TRUE);

		$query->select('field_value');
		$query->from('#__js_res_record_values');
		$query->where("section_id = {$section->id}");
		$query->where("`field_key` = '{$this->key}'");
		$query->group('field_value');

		if($this->params->get('params.filter_show_number', 1))
		{
			$query->select('count(record_id) as num');
		}
		if($this->params->get('params.sort') == 2)
		{
			$query->order('field_value ASC');
		}
		if($this->params->get('params.sort') == 3)
		{
			$query->order('field_value DESC');
		}

		if(CStatistics::hasUnPublished($section->id))
		{
			$query->where("record_id IN(SELECT r.id FROM #__js_res_record AS r WHERE r.section_id = {$section->id} AND r.published = 1 AND r.hidden = 0)");
		}

		$db->setQuery($query);

		$this->values = $db->loadObjectList();
		if(!$this->values)
		{
			return;
		}


		if($this->params->get('params.sort') == 1)
		{
			$this->values = $this->sortArrayByArray($this->values, explode("\n", $this->params->get('params.values')));
		}


		$this->default = $this->_getvalue();

		return $this->_display_filter($section, $module);

	}

	function sortArrayByArray(Array $array, Array $orderArray)
	{
		$ordered = array();
		foreach($orderArray as $key)
		{
			if(array_key_exists($key, $array))
			{
				$ordered[$key] = $array[$key];
				unset($array[$key]);
			}
		}

		return $ordered + $array;
	}

	public function onFilterWornLabel($section)
	{
		$value = $this->_getvalue();
		foreach($value as $val)
		{
			$label[] = $this->_getVal($c);
		}
		$value = implode(JText::_('CFILTERWORNSEPARATOR'), $label);

		return $value;
	}

	private function _getvalue()
	{
		$value = $this->value;

		if(is_array($value))
		{
			unset($value['by']);
			if(array_key_exists('value', $value))
			{
				$value = $value['value'];
			}
		}

		if($this->params->get('params.template_filter') == 'autocomplete.php' && !is_array($value))
		{
			$value = explode(',', $value);
		}

		if($this->type == 'listautocomplete' && !is_array($value))
		{
			$value = explode(',', $value);
		}

		ArrayHelper::clean_r($value);

		return $value;
	}

	public function onFilterWhere($section, &$query)
	{
		$value = $this->_getvalue();

		if(!$value)
		{
			return NULL;
		}

		if(isset($this->value['by']) && $this->value['by'] == 'all')
		{
			$pattern = "SELECT record_id FROM #__js_res_record_values WHERE section_id = {$section->id} AND field_key = '{$this->key}' AND ";
			$list = array();
			foreach($value as $text)
			{
				$sql = $pattern . $this->_fieldtypecondition($text);
				$list[] = $this->getIds($sql);
			}
			$ids = $list[0];
			foreach($list as $id)
			{
				$ids = array_intersect($id, $ids);
			}
		}
		else
		{
			foreach($value as $text)
			{
				$sql[] = $this->_fieldtypecondition($text);
			}

			$ids = $this->getIds("SELECT record_id FROM `#__js_res_record_values` WHERE (" . implode(' OR ', $sql) . ") AND section_id = {$section->id} AND field_key = '{$this->key}'");
		}

		return $ids;
	}

	private function _fieldtypecondition($text)
	{
		if($this->type == 'text')
		{
			return " field_value LIKE '%" . JFactory::getDbo()->escape($text) . "%' ";
		}

		return " field_value = '" . JFactory::getDbo()->escape($text) . "' ";
	}

	public function onRenderFull($record, $type, $section)
	{
		$this->record = $record;

		return $this->_getvalues(0, $record, $type, $section, 'full');
	}

	public function onRenderList($record, $type, $section)
	{
		$this->record = $record;

		return $this->_getvalues($this->params->get('params.list_limit', 5), $record, $type, $section, 'list');
	}

	protected function _getSqlValues($assoc = FALSE)
	{
		return _getSQLValues($this, $assoc);
	}

	private function _getvalues($limit, $record, $type, $section, $client)
	{
		$user = JFactory::getUser();
		if($this->type == 'listautocomplete')
		{
			if(is_string($this->value))
			{
				$this->value = explode(',', $this->value);
			}
		}
		ArrayHelper::clean_r($this->value);
		$values = $i = NULL;


		foreach($this->value as $k => $value)
		{
			if($limit > 0 && ($i >= $limit))
			{
				break;
			}
			$i++;

			$text = $this->_getVal($value);

			if($this->params->get('params.sql_link') && $this->params->get('params.sql_source'))
			{
				$attributes = ($this->params->get('params.sql_link_target') ? array('target' => '_blank') : array());
				$process = str_replace(array('[ID]', '[USER_ID]', '[AUTHOR_ID]'), array($k, $user->get('id', 0), $this->record->user_id), $this->params->get('params.sql_link'));
				if(substr($process, 0, 9) == 'index.php')
				{
					$process = JRoute::_($process);
				}
				$text = JHtml::link($process, $text, $attributes);
				$this->params->set('params.filter_linkage', 2);
			}

			if($this->params->get('params.filter_enable'))
			{
				$tip = ($this->params->get('params.filter_tip') ? JText::sprintf($this->params->get('params.filter_tip'), '<b>' . JText::_($this->label) . '</b>', $text) : NULL);
				switch($this->params->get('params.filter_linkage'))
				{
					case 1 :
						$text = FilterHelper::filterLink('filter_' . $this->id, $value, $text, $this->type_id, $tip, $section);
						break;

					case 2 :
						$text = $text . ' ' . FilterHelper::filterButton('filter_' . $this->id, $value, $this->type_id, $tip, $section, $this->params->get('params.filter_icon', 'funnel-small.png'));
						break;
				}
			}
			$values[] = $text;
		}
		if(!$values)
		{
			return NULL;
		}
		$this->values = $values;

		return $this->_display_output($client, $record, $type, $section);
	}

	/*protected function _custom_value($type)
	{
		$out = array();
		JFactory::getDocument()->addScript(JURI::root(TRUE) . '/media/mint/js/GrowingInput.js');
		$out[] = '<div style="clear:both"></div>';
		$out[] = sprintf('<div class="variant-container" id="variant_%d">
			<a class="small" id="show_variant_link_%d" rel="{field_type:\'%s\', id:%d, inputtype:\'%s\', width:%d, limit:%d, max_size:%d}"
				href="javascript:void(0)" onclick="Joomcck.showAddForm(this)">%s</a></div>', $this->id, $this->id, $this->type, $this->id, $type, $this->params->get('params.width', '220'), $this->params->get('params.total_limit', 0), $this->params->get('params.size', 0), JText::_($this->params->get('params.user_value_label', 'Your variant')));
		$out[] = '<div style="clear:both"></div>';

		return implode("\n", $out);
	}*/

	private function _input_user_value()
	{

	}

	public function onFilterGetValues($post)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(TRUE);

		$section = ItemsStore::getSection($post['section_id']);

		$query->select('field_value as text');
		$query->from('#__js_res_record_values');
		$query->where("section_id = {$section->id}");
		$query->where("`field_key` = '{$this->key}'");
		$query->group('field_value');
		$query->where("record_id NOT IN(SELECT r.id FROM #__js_res_record AS r WHERE r.section_id = {$post['section_id']} AND (r.published = 0 OR r.hidden = 1 OR r.archive = 1))");

		if($this->params->get('params.filter_show_number', 1))
		{
			$query->select('count(record_id) as num');
		}
		$db->setQuery($query);

		$list = $db->loadObjectList();
		foreach($list as $k => $item)
		{
			$c = explode('^', $item->text);
			ArrayHelper::clean_r($c);
			$label = (isset($c[1]) ? "<SPAN style=\"color:{$c[1]}\">" . JText::_($c[0]) . "</SPAN>" : JText::_($c[0]));
			$label_num = $label;
			if($this->params->get('params.filter_show_number', 1))
			{
				$label_num .= '<span class="badge bg-light text-muted border">' . $item->num . '</span>';
			}
			$out[] = $item->text;
		}

		return $out;
	}

	public function isFilterActive()
	{
		return !empty($this->value['value']);
	}

	public function onImport($value, $params, $record = NULL)
	{
		$value = explode(',', $value);
		ArrayHelper::clean_r($value);

		return $value;
	}

	public function onImportForm($heads, $defaults)
	{
		return $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id));
	}
}

function _getSQLValues($obj, $assoc)
{
	static $out = array();

	$key = md5($obj->params->get('params.sql') . '-' . (int)$assoc);

	if(array_key_exists($key, $out))
	{
		return $out[$key];
	}

	$db = JFactory::getDbo();
	$user = JFactory::getUser();

	if($obj->params->get('params.sql_ext_db'))
	{
		$option = array();

		$option['driver'] = $obj->params->get('params.sql_ext_driver', 'mysql');
		$option['host'] = $obj->params->get('params.sql_db_host') . ($obj->params->get('params.sql_db_port') ? ':' . $obj->params->get('params.sql_db_port') : NULL);
		$option['user'] = $obj->params->get('params.sql_db_user');
		$option['password'] = $obj->params->get('params.sql_db_pass');
		$option['database'] = $obj->params->get('params.sql_db_name');
		$option['prefix'] = '';

		$db = JDatabaseDriver::getInstance($option);
	}

	$sql = $obj->params->get('params.sql', "SELECT 1 AS id, 'No sql query entered' AS text");
	$sql = str_replace('[USER_ID]', $user->get('id', 0), $sql);
	$db->setQuery($sql);

	if($assoc)
	{
		$out[$key] = $db->loadAssocList('id', 'text');
	}
	else
	{
		$out[$key] = $db->loadObjectList();
	}


	return $out[$key];

}