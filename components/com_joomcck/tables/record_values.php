<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.table.table');

/**
 * @package JCommerce
 */
class JoomcckTableRecord_values extends JTable
{

	public function __construct(&$_db)
	{
		parent::__construct('#__js_res_record_values', 'id', $_db);
	}

	public function bind($array, $ignore = '')
	{
		$params = \Joomla\CMS\Factory::getApplication()->input->post->get('params', array(), 'array');
		if($params)
		{
			$registry = new JRegistry();
			$registry->loadArray($params);
			$array['params'] = (string)$registry;
		}

		//$this->key = 'k'.md5($array['label'].'-'.$array['field_type']);

		return parent::bind($array, $ignore);
	}

	public function check()
	{
		if(trim($this->field_label) == '')
		{
			$this->setError(JText::_('CNOLABEL'));

			return FALSE;
		}

		if(!$this->ip)
		{
			$this->ip = $_SERVER['REMOTE_ADDR'];
		}

		if($this->ctime == '' || $this->ctime == '0000-00-00 00:00:00')
		{
			$this->ctime = JFactory::getDate()->toSql();
		}

		return TRUE;
	}

	public function clean($record_id, $ids)
	{
		$query = $this->_db->getQuery(TRUE);
		$query->delete();
		$query->from($this->_tbl);
		$query->where('record_id = ' . (int)$record_id);
		if($ids)
		{
			$query->where('field_id IN (' . implode(',', $ids) . ')');
		}
		//$query->where("field_type NOT IN ('parent', 'child')");
		$this->_db->setQuery($query);
		$this->_db->query();

		return TRUE;
	}

	public function store_value($value, $key, $item, $field)
	{
		$save = array(
			'record_id'   => $item->id,
			'user_id'     => $item->user_id,
			'type_id'     => $item->type_id,
			'section_id'  => $item->section_id,
			'category_id' => 0,
			'params'      => '',
			'ip'          => $_SERVER['REMOTE_ADDR'],
			'ctime'       => JFactory::getDate()->toSql(),
			'field_type'  => $field->field_type ?: $field->type, 
			'field_label' => @$field->label_orig ?: $field->label,
			'field_key'   => @$field->key ?: 'k' . md5(@$field->label_orig ?: $field->label . '-' . $field->field_type ?: $field->type),
			'value_index' => $key,
			'field_id'    => $field->id
		);

		if(is_array($value) || is_object($value))
		{
			$value = json_encode($value);
		}
		$save['field_value'] = trim(CensorHelper::cleanText($value));

		if(!$save['field_value'])
		{
			return;
		}

		$this->bind($save);
		$this->store();
	}
}
