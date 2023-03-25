<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckrelate.php';
require_once JPATH_ROOT . '/components/com_joomcck/api.php';

class JFormFieldCChild extends CFormFieldRelate
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

		if($this->params->get('params.parent_section',0) && $this->params->get('params.parent_type',0)){
			$this->inputvalue = $this->_render_input(
				$this->params->get('params.input_mode',2),
				$name,
				$this->params->get('params.parent_section',0),
				$this->params->get('params.parent_type',0),
				$this->params->get('params.multi_parent',0)
			);
		}


		return $this->_display_input();
	}

	public function onFilterWhere($section, &$query)
	{
		ArrayHelper::clean_r($this->value);

		if(!$this->value)
		{
			return;
		}

		$ids = $this->getIds("SELECT record_id FROM #__js_res_record_values WHERE field_value IN(" . implode(',', $this->value) . ") AND field_id = {$this->id}");

		return $ids;

		//$query->where("r.id IN(".implode(',', $ids).")");
		//return TRUE;
	}

	public function onRenderFilter($section, $module = FALSE)
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

		$this->filter = $this->_render_input($this->params->get('params.filter_style'), $name,
			$this->params->get('params.parent_section'), $this->params->get('params.parent_type'));


		return $this->_display_filter($section, $module);

		$this->params->set('params.multi_limit', $limit);
	}


	public function onFilterWornLabel($section)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(TRUE);
		$query->from('#__js_res_record');
		$query->select('title');
		$query->where('id IN (' . implode(',', $this->value) . ')');
		$db->setQuery($query);
		$array = $db->loadColumn();

		return implode(', ', $array);
	}

	public function onRenderFull($record, $type, $section)
	{
		return $this->_render('full', $record, $this->id, $this->params->get('params.parent_type'),
			$this->params->get('params.parent_section'), 'show_parents');
	}

	public function onRenderList($record, $type, $section)
	{
		return $this->_render('list', $record, $this->id, $this->params->get('params.parent_type'),
			$this->params->get('params.parent_section'), 'show_parents');
	}

	public function onGetList($params)
	{
		$db   = JFactory::getDbo();
		$user = JFactory::getUser();

		$query = $db->getQuery(TRUE);
		$query->select('id, title, null, title');
		$query->from('#__js_res_record');
		if(CStatistics::hasUnPublished($this->params->get('params.parent_section')))
		{
			$query->where('published = 1');
		}
		$query->where('hidden = 0');
		$query->where('section_id = ' . $this->params->get('params.parent_section'));
		$query->where('type_id = ' . $this->params->get('params.parent_type'));

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

			$isme = ((int)$user_id === (int)$user->get('id', NULL));

			$section = ItemsStore::getSection($this->params->get('params.parent_section'));
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
