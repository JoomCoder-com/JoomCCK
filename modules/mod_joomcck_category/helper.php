<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class modJoomcckCategoriesHelper
{

	static public function getList($params, $cat_id = null)
	{
		require_once JPATH_ROOT . '/components/com_joomcck/models/categories.php';
		$model = new JoomcckModelCategories();
		$model->all = ($params->get('mode', 0) == 1 ? 0 : 1);

		switch($params->get('cat_style', 'default'))
		{
			case 'moomenu':
				$level = '1000';
			break;
			case 'default':
				$level = 1;
				if($cat_id)
				{
					$category = ItemsStore::getCategory($cat_id);
					$level = $category->level + 1;
				}
				$model->all = 0;
			break;
			default:
				$level = 100;
		}

		// Parent ROOT is ID 1
		if(!$cat_id) $cat_id = 1;

		$model->section = ItemsStore::getSection($params->get('section_id'));
		$model->parent_id = $cat_id;
		$model->order = $params->get('order') == 1 ? 'c.lft ASC' : 'c.title ASC';
		$model->levels = $level;
		$model->nums = $params->get('cat_nums', 'current');
		$list = $model->getItems();

		return $list;
	}


	static public function getParentsList($cat_id)
	{
		if(is_null($cat_id)) return array();
		$model = JModelLegacy::getInstance('Categories', 'JoomcckModel');
		$parents = $model->getParentsByChild($cat_id);
		return $parents;
	}

	static public function getRecordsNum($section, $cat_id)
	{
		$db = \Joomla\CMS\Factory::getDbo();
		$user = \Joomla\CMS\Factory::getUser();

		$sql = $db->getQuery(true);
		$sql->select('count(*)');
		$sql->from('#__js_res_record');
		$sql->where('published = 1');
		$sql->where('hidden = 0');
		$sql->where('section_id = ' . $section->id);
		$sql->where("categories LIKE '%" . $cat_id . "%' ");

		if(! in_array($section->params->get('general.show_future_records'), $user->getAuthorisedViewLevels()))
		{
			$sql->where("ctime < " . $db->quote(\Joomla\CMS\Factory::getDate()->toSql()));
		}

		if(! in_array($section->params->get('general.show_past_records'), $user->getAuthorisedViewLevels()))
		{
			$sql->where("(extime = '0000-00-00 00:00:00' OR ISNULL(extime) OR extime > '" . \Joomla\CMS\Factory::getDate()->toSql() . "')");
		}
		$db->setQuery($sql);
		return $db->loadResult();
	}

	static public function getCatRecords($cat_id, $params)
	{
		$db = \Joomla\CMS\Factory::getDbo();

		$sql = $db->getQuery(true);

		$sql->select('*');
		$sql->from('#__js_res_record');
		$sql->where('published = 1');
		$sql->where('hidden = 0');
		$sql->where("ctime < " . $db->quote(\Joomla\CMS\Factory::getDate()->toSql()));
		$sql->where("(extime = '0000-00-00 00:00:00' OR ISNULL(extime) OR extime > '" . \Joomla\CMS\Factory::getDate()->toSql() . "')");
		$sql->where("id IN (SELECT record_id FROM #__js_res_record_category WHERE catid = '{$cat_id}')");
		if($params->get('orderby'))
		{
			$sql->order($params->get('orderby'));
		}
		/*$sql->select('record_id');
			$sql->from('#__js_res_record_category');
			$sql->where('catid = '.$cat_id);*/
		$db->setQuery($sql, 0, ($params->get('records_limit') ? $params->get('records_limit') + 1 : 0));
		$items = array();
		if($recs = $db->loadColumn())
		{
			foreach($recs as $rid)
			{
				$record = ItemsStore::getRecord($rid);
				$record->url = Url::record($record, null, null, $cat_id);
				$items[] = $record;
			}
		}

		return $items;
	}

	static public function getSectionRecords($params)
	{
		$db = \Joomla\CMS\Factory::getDbo();
		$items = null;
		$sql = $db->getQuery(true);
		$sql->select('id');
		$sql->from('#__js_res_record');
		$sql->where('published = 1');
		$sql->where('hidden = 0');
		$sql->where('section_id = ' . $params->get('section_id'));
		$sql->where("ctime < " . $db->quote(\Joomla\CMS\Factory::getDate()->toSql()));
		$sql->where("(extime = '0000-00-00 00:00:00' OR ISNULL(extime)  OR extime > '" . \Joomla\CMS\Factory::getDate()->toSql() . "')");
		$sql->where("id NOT IN (SELECT record_id FROM #__js_res_record_category WHERE section_id = '" . $params->get('section_id') . "')");
		if($params->get('orderby'))
		{
			$sql->order($params->get('orderby'));
		}
		$db->setQuery($sql, 0, ($params->get('records_limit') ? $params->get('records_limit') + 1 : 0));


		if($recs = $db->loadColumn())
		{
			$items = array();
			foreach($recs as $rid)
			{
				$record = ItemsStore::getRecord($rid);
				$record->url = Url::record($record);
				$items[] = $record;
			}
		}
		return $items;
	}
}