<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
jimport('mint.mvc.model.list');

class JoomcckModelCategories extends MModelList
{
	public function getListQuery()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();

		$query->select("c.id, c.title,  c.*, CONCAT(repeat('-- ', (c.level - 1)), c.title) as opt ");
		$query->select("(SELECT count(id) FROM #__js_res_categories WHERE parent_id = c.id)  as childs_num ");
		$query->from('#__js_res_categories AS c ');
		$query->where('c.access IN ('.implode(',', $user->getAuthorisedViewLevels()).')');
		$query->where('c.section_id = ' . $this->section->id);
		$query->where('c.published = 1');

		// If no need to select subcategories.
		if(!$this->levels)
		{
			$query->where('c.parent_id = '. $this->parent_id);
		}
		// if subcategories
		else
		{
			$parent = MECAccess::_getlftrgt($this->parent_id);
			$query->where('c.level <= ' . ($parent->level + $this->levels + 1));
			$query->where("c.lft BETWEEN {$parent->lft} AND {$parent->rgt} ");
		}

		if(!empty($this->comments))
		{
			$sql = $db->getQuery(true);
			$sql->select('sum(r.comments)');
			$sql->from('#__js_res_record_category AS rc');
			$sql->where("rc.catid = c.id");

			$sql->leftJoin('#__js_res_record AS r ON rc.record_id = r.id');
			if(CStatistics::hasUnPublished($this->section->id))
			{
				$sql->where('r.published = 1');
			}
			$sql->where('r.hidden = 0');
			$sql->where('r.section_id = ' . JFactory::getApplication()->input->getInt('section_id'));

			if(! in_array($this->section->params->get('general.show_restrict'), $user->getAuthorisedViewLevels()))
			{
				$sql->where("(r.access IN(" . implode(',', $user->getAuthorisedViewLevels()) . ") OR r.user_id = ".$user->get('id').")");
			}

			if(!in_array($this->section->params->get('general.show_future_records'), $user->getAuthorisedViewLevels()))
			{
				$sql->where("r.ctime < ".$db->quote(JFactory::getDate()->toSql()));
			}

			if(!in_array($this->section->params->get('general.show_past_records'), $user->getAuthorisedViewLevels()))
			{
				$sql->where("(r.extime = '0000-00-00 00:00:00' OR r.extime > '".JFactory::getDate()->toSql()."')");
			}

			$query->select("({$sql}) AS comments_num");

			//$sql = $db->getQuery(true);
			//$sql->select("concat(user_id, ':::', ctime)");
			//$sql->from('#__js_res_comments AS c');
			//$sql->where("c.published = 1");
			//$sql->order('c.ctime DESC');
			//$sql->
		}

		$orders = explode(' ', $this->order);
		$query->order(' '.$orders[0] . ' ' . $orders[1]);
		//echo str_replace('#_', 'jos', $query);
		return $query;
	}

	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();
		$user = JFactory::getUser();
		// Try to load the data from internal storage.
		if (! empty($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		if(!is_object($this->section))
		{
			$this->section = ItemsStore::getSection($this->section);
		}

		// Load the list items.
		$query = $this->_getListQuery();
		$items = $this->_getList($query);
		$byparent = $cats = array();

		if(!empty($this->nums))
		{
			$sql = $this->_db->getQuery(true);
			$sql->select('count(rc.catid) as num, rc.catid');
			$sql->from('#__js_res_record_category AS rc');
			$sql->where("rc.section_id = {$this->section->id}");
			$sql->group('rc.catid');
			
			if($this->section->params->get('general.cat_mode'))
			{
				$sql2 = $this->_db->getQuery(true);
				$sql2->select('r.id');
				$sql2->from('#__js_res_record AS r');
				if(CStatistics::hasUnPublished($this->section->id))
				{
					$sql2->where('r.published = 1');
				}
				$sql2->where('r.hidden = 0');
				$sql2->where('r.section_id = ' . $this->section->id);
	
				if(! in_array($this->section->params->get('general.show_restrict'), $user->getAuthorisedViewLevels())  && !MECAccess::allowRestricted($user, $this->section))
				{
					$sql2->where("(r.access IN(" . implode(',', $user->getAuthorisedViewLevels()) . ") OR r.user_id = ".$user->get('id').")");
				}
	
				if(!in_array($this->section->params->get('general.show_future_records'), $user->getAuthorisedViewLevels()))
				{
					$sql2->where("r.ctime < ".$this->_db->quote(JFactory::getDate()->toSql()));
				}
	
				if(!in_array($this->section->params->get('general.show_past_records'), $user->getAuthorisedViewLevels()))
				{
					$sql2->where("(r.extime = '0000-00-00 00:00:00' OR r.extime > '".JFactory::getDate()->toSql()."')");
				}
	
				if(!in_array($this->section->params->get('general.show_children'), $user->getAuthorisedViewLevels()))
				{
					$sql2->where("r.parent_id = 0");
				}

				if($this->section->params->get('general.lang_mode'))
				{
					$sql2->where('r.langs = ' . $this->_db->quote(JFactory::getLanguage()->getTag()));
				}
	
				$sql->where('rc.record_id IN ('.$sql2.')');
			}
			$this->_db->setQuery($sql);


			try
			{
				$nums = $this->_db->loadAssocList("catid","num");
			}catch(RuntimeException $e){
				\Joomla\CMS\Factory::getApplication()->enqueueMessage($e->getMessage(),'error');
				return false;
			}




		}

		foreach ( $items as $key => $item )
		{
			$params = new JRegistry();
			$params->loadString($item->params);
			
			if(@$this->hidesubmision && !$params->get('submission', 1))
			{
				continue;
			}

			$cats[$item->id] = $item;
			$cats[$item->id]->submission = $params->get('submission', 1);
			$cats[$item->id]->records_num = (int)@$nums[$item->id];
			$cats[$item->id]->num_current = (int)@$nums[$item->id];
			$cats[$item->id]->params = $params;
			$cats[$item->id]->link = Url::records($this->section, $cats[$item->id]);

			$descr = JText::_($item->description);
			$descr = JHtml::_('content.prepare', $descr);
			$descr = preg_split('#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i', $descr, 2);
			$cats[$item->id]->descr_before = @$descr[0];
			$cats[$item->id]->descr_after = @$descr[1];
			$cats[$item->id]->descr_full = implode($descr);
			$cats[$item->id]->title = JText::_($item->title);

			$byparent[$item->parent_id][] = $cats[$item->id];

		}
		if($this->all)
		{
			$out = $cats;
		}
		else
		{
			$out = $this->sort($byparent, $this->parent_id);
		}



		// Add the items to the internal cache.
		$this->cache[$store] = $out;

		return $this->cache[$store];
	}

	private function sort($array, $parent_id = 1)
	{
		$out = array();
		if (isset($array[$parent_id]) && count($array[$parent_id]))
		{
			foreach ( $array[$parent_id] as $item )
			{
				$item->children = array();
				if (isset($array[$item->id]))
				{
					$item->children = $this->sort($array, $item->id);
				}
				$out[] = $item;
			}
		}
		return $out;
	}

	protected function _getListQuery()
	{

		// Capture the last store id used.
		static $lastStoreId;

		// Compute the current store id.
		$currentStoreId = $this->getStoreId();

		// If the last store id is different from the current, refresh the query.
		if ($lastStoreId != $currentStoreId || empty($this->query))
		{
			$lastStoreId = $currentStoreId;
			$this->query = $this->getListQuery();
		}

		return $this->query;
	}

	public function getChilds($parent, $items)
	{
		$childs = null;
		foreach ( $items as $item )
		{
			if ($item->parent_id == $parent->id)
			{
				$childs[] = $item;
			}
		}
		return $childs;
	}

	public function getCategoriesById($ids)
	{
		ArrayHelper::clean_r($ids);
		
		if(!$ids) return array();
		
		$query = $this->_db->getQuery(true);

		$query->select('c.*');
		$query->from('#__js_res_categories AS c');
		$query->where('c.id IN (' . implode(',', $ids) . ')');
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		
		return $result;
	}

	public function getParentsByChild($id)
	{
		$query = $this->_db->getQuery(true);

		$query->select('parent.id');
		$query->from('#__js_res_categories AS c, #__js_res_categories AS parent');
		$query->where('c.lft BETWEEN parent.lft AND parent.rgt');
		$query->where('c.id = ' . $id);
		$query->where('parent.section_id = c.section_id');
		$query->order('c.lft ASC');
		$this->_db->setQuery($query);
		$result = $this->_db->loadColumn();
		settype($result, 'array');
		return $result;
	}

	public function getParentsObjectsByChild($id)
	{
		$query = $this->_db->getQuery(true);

		$query->select('parent.*');
		$query->from('#__js_res_categories AS c, #__js_res_categories AS parent');
		$query->where('c.lft >= parent.lft AND c.lft <= parent.rgt');
		$query->where('c.id = ' . $id);
		$query->where('parent.section_id = c.section_id');
		$query->order('parent.lft ASC');
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList('id');
		if(!empty($result))
		{
			foreach ($result as $key => $cat) {
				$cat->params = new JRegistry($cat->params);
				$cat->path = str_replace('root/', '', $cat->path);
				ItemsStore::$categories[$key] = $cat;
			}
		}
		return $result;
	}

	public function getCategories1LevelBySection($section_id)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select("c.*");
		$query->select("(SELECT count(id) FROM #__js_res_categories WHERE parent_id = c.id)  as childs_num");
		$query->from('#__js_res_categories AS c');
		$query->where('c.level = 1');
		$query->where('c.section_id = ' . $section_id);
		$query->where('c.id <> 1');
		$query->order('c.title');
		$query->group('id');
		$db->setQuery($query);
		$items = $db->loadObjectList('id');
		$cats = $byparent = array();

		if($items)
		{
			foreach ( $items as $key => $item )
			{
				$params = new JRegistry();
				$params->loadString($item->params);

				$cats[$item->id] = $item;
				$cats[$item->id]->params = $params;
			}
		}
		else
		{
			$items = array();
		}

		return $items;
	}
}
