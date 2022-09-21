<?php

/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 3.1 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('mint.mvc.model.base');
jimport('mint.mvc.controller.base');
jimport('mint.mvc.view.base');
jimport('joomla.database.table');

JTable::addIncludePath(JPATH_ROOT . '/components/com_joomcck/tables', 'JoomcckTable');
MModelBase::addIncludePath(JPATH_ROOT . '/components/com_joomcck/models', 'JoomcckModel');
include_once dirname(__FILE__) . '/url.php';

class ItemsStore
{

	public static $categories = array();

	public static $sections = array();

	public static $records = array();

	public static $types = array();

	public static $usercategories = null;

	public static $record_ids = null;

	static public function getSection($section_id)
	{
		if(! isset(self::$sections[$section_id]))
		{
			$section_model = MModelBase::getInstance('Section', 'JoomcckModel');
			self::$sections[$section_id] = $section_model->getItem($section_id);
		}
		return self::$sections[$section_id];
	}

	static public function getRecord($record_id)
	{
		if(! isset(self::$records[$record_id]))
		{
			$rec_mod = MModelBase::getInstance('Record', 'JoomcckModel');
			self::$records[$record_id] = $rec_mod->getItem($record_id);
		}
		return self::$records[$record_id];
	}

	static public function getType($type_id)
	{
		if(! isset(self::$types[$type_id]))
		{
			self::$types[$type_id] = MModelBase::getInstance('Form', 'JoomcckModel')->getRecordType($type_id);
		}
		return self::$types[$type_id];
	}

	static public function getUserCategory($ucategory_id)
	{
		if(! isset(self::$usercategories[$ucategory_id]))
		{
			$usercategory_model = MModelBase::getInstance('Usercategory', 'JoomcckModel');
			self::$usercategories[$ucategory_id] = $usercategory_model->getItem($ucategory_id);
		}
		return self::$usercategories[$ucategory_id];
	}

	static public function getCategory($category_id)
	{
		if(array_key_exists($category_id, self::$categories))
		{
			return self::$categories[$category_id];
		}

		require_once JPATH_ROOT . '/components/com_joomcck/models/category.php';
		$model       = new JoomcckModelCategory();
		self::$categories[$category_id] = $model->getItem($category_id);

		/*$db = JFactory::getDbo();
		 if(! empty(self::$record_ids))
		 {
		$sql = "SELECT id,alias,params,path,access,published FROM #__js_res_categories WHERE id IN (SELECT catid FROM #__js_res_record_category WHERE record_id IN (" . implode(',', self::$record_ids) . ")) OR id = " . (int)$category_id;
		self::$record_ids = array();
		}
		else
		{
		$sql = "SELECT id,alias,params,path,title,published, access FROM #__js_res_categories WHERE id = " . (int)$category_id;

		}
		$db->setQuery($sql);
		$result = $db->loadObjectList('id');

		foreach($result as $key => $cat)
		{
		$cat->params = new JRegistry($cat->params);
		$cat->path = str_replace('root/', '', $cat->path);
		self::$categories[$key] = $cat;
		}*/

		return @self::$categories[$category_id];
	}
}