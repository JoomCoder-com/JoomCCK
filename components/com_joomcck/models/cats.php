<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die ();
jimport('mint.mvc.model.list');

class JoomcckModelCats extends MModelList
{
	/**
	 * Constructor.
	 *
	 * @param    array    An optional associative array of configuration settings.
	 *
	 * @see        MController
	 * @since      1.6
	 */
	public function __construct($config = array())
	{
		if(empty ($config ['filter_fields']))
		{
			$config ['filter_fields'] = array(
				'id', 'a.id', 'title', 'a.title', 'alias', 'a.alias', 'published', 'a.published', 'access', 'a.access', 'access_level', 'language', 'a.language', 'checked_out', 'a.checked_out', 'checked_out_time', 'a.checked_out_time',
				'created_time',
				'a.created_time', 'created_user_id', 'a.created_user_id',
				'lft', 'a.lft', 'rgt', 'a.rgt',
				'level', 'a.level', 'path', 'a.path',
				'section_id', 'a.section_id'
			);
		}
		$this->option = 'com_joomcck';
		parent::__construct($config);
		$this->all = 1;
	}


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param    string    An optional ordering field.
	 * @param    string    An optional direction (asc|desc).
	 *
	 * @return    void
	 * @since    1.6
	 */
	protected function populateState($ordering = NULL, $direction = NULL)
	{
		// Initialise variables.
		$app     = \Joomla\CMS\Factory::getApplication();
		$context = $this->context;

		$section = $app->getUserStateFromRequest('com_joomcck.cats.filter.section', 'section_id', 0);

		$this->setState('filter.section', $section);
		$parts = explode('.', $section);

		// extract the component name
		$this->setState('filter.component', $parts [0]);

		// extract the optional section name
		$this->setState('filter.section', (count($parts) > 1) ? $parts [1] : NULL);

		$search = $app->getUserStateFromRequest($context . '.search', 'filter_search');
		$this->setState('filter.search', $search);

		$section = $app->getUserStateFromRequest($context . '.filter.section', 'filter_section', 0, 'int');
		$this->setState('filter.section', $section);

		$level = $app->getUserStateFromRequest($context . '.filter.level', 'filter_level', 0, 'int');
		$this->setState('filter.level', $level);

		$access = $app->getUserStateFromRequest($context . '.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);

		$published = $app->getUserStateFromRequest($context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$language = $app->getUserStateFromRequest($context . '.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// List state information.
		parent::populateState('a.lft', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param    string $id A prefix for the store id.
	 *
	 * @return    string        A store id.
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.section');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * @return    string
	 * @since    1.6
	 */
	function getListQuery()
	{
		// Create a new query object.
		$db      = $this->getDbo();
		$query   = $db->getQuery(TRUE);
		$user    = \Joomla\CMS\Factory::getUser();
		$section = \Joomla\CMS\Factory::getApplication()->input->getInt('section_id');

		// Select the required fields from the table.
		$query->select($this->getState('list.select', 'a.id, a.title, a.alias, a.note, a.published, a.access' . ', a.checked_out, a.checked_out_time, a.created_user_id' . ', a.path, a.parent_id, a.level, a.lft, a.rgt' . ', a.language' . ', a.params'));
		// 		$query->select("(SELECT count(id) FROM #__js_res_categories WHERE parent_id = a.id)  as childs_num");
		$query->from('#__js_res_categories AS a');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Join over the users for the author.
		$query->select('ua.name AS author_name');
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_user_id');

		// Filter by section
		if($section1 = $this->getState('filter.section'))
		{
			$query->where('a.section_id = ' . $section1);
		}
		else
		{
			$query->where('a.section_id = ' . $section);
		}

		// Filter on the level.
		if($level = $this->getState('filter.level'))
		{
			$query->where('a.level <= ' . ( int )$level);
		}

		// Filter by access level.
		if($access = $this->getState('filter.access'))
		{
			$query->where('a.access = ' . ( int )$access);
		}

		// Implement View Level Access
		if(!$user->authorise('core.admin'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if(is_numeric($published))
		{
			$query->where('a.published = ' . ( int )$published);
		}
		else if($published === '')
		{
			$query->where('(a.published IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if(!empty ($search))
		{
			if(stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . ( int )substr($search, 3));
			}
			else if(stripos($search, 'author:') === 0)
			{
				$search = $db->Quote('%' . $db->escape(substr($search, 7), TRUE) . '%');
				$query->where('(ua.name LIKE ' . $search . ' OR ua.username LIKE ' . $search . ')');
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, TRUE) . '%');
				$query->where('(a.title LIKE ' . $search . ' OR a.alias LIKE ' . $search . ' OR a.note LIKE ' . $search . ')');
			}
		}

		// Filter on the language.
		if($language = $this->getState('filter.language'))
		{
			$query->where('a.language = ' . $db->quote($language));
		}

		// Add the list ordering clause.
		$query->order($db->escape($this->getState('list.ordering', 'a.title')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

		//echo nl2br(str_replace('#__','jos_',$query)); exit;
		return $query;
	}

	public function getItems()
	{
		// Get a storage key.

		// Try to load the data from internal storage.

		// Load the list items.
		$query = $this->_getListQuery();
		$items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
		$cats  = $byparent = array();
		foreach($items as $key => $item)
		{
			$params = new \Joomla\Registry\Registry();
			$params->loadString((string)$item->params);

			$cats[$item->id]         = $item;
			$cats[$item->id]->params = $params;

			$cats[$item->id]->submission = $params->get('submission', 1);

			$byparent[$item->parent_id][] = $cats[$item->id];
		}

		if($this->all)
		{
			$out = $items;
		}
		else
		{
			$out = $this->sort($byparent);
		}


		// Add the items to the internal cache.
		return $out;
	}

	private function sort($array, $parent_id = 1)
	{
		$out = array();
		if(isset($array[$parent_id]) && count($array[$parent_id]))
		{
			foreach($array[$parent_id] as $item)
			{
				$item->children = array();
				if(isset($array[$item->id]))
				{
					$item->children = $this->sort($array, $item->id);
				}
				$out[] = $item;
			}
		}

		return $out;
	}

	function getSectionsWithCategoryNum()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(TRUE);

		$query->select('s.name as text, s.id as value, s.id, (SELECT count(id) FROM #__js_res_categories WHERE section_id = s.id)  as childs_num');
		$query->from('#__js_res_sections AS s');
		$query->where('s.published IN (0, 1)');
		$query->order(' s.id ASC');
		$db->setQuery($query);

		return $db->loadObjectList('id');
	}

	function getSections()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(TRUE);

		$query->select('name as text, id as value, id');
		$query->from('#__js_res_sections');
		$query->where('published IN (0, 1)');
		$query->order(' id ASC');
		$db->setQuery($query);

		return $db->loadObjectList('id');
	}

	public function getCategoriesWithSections()
	{
		$sections = $this->getSectionsWithCategoryNum();
		if(empty($sections))
		{
			return array(array(), array());
		}
		foreach($sections AS &$value)
		{
			$value->section = 1;
		}
		$db    = $this->getDbo();
		$query = $db->getQuery(TRUE);

		$query->select("c.*");
		$query->select("(SELECT count(id) FROM #__js_res_categories WHERE parent_id = c.id)  as childs_num");
		$query->from('#__js_res_categories AS c, #__js_res_categories AS parent');
		$query->where('c.lft BETWEEN parent.lft AND parent.rgt');
		$query->where('c.section_id IN ( ' . implode(',', array_keys($sections)) . ' )');
		$query->where('c.id <> 1');
		$query->group('id');
		$db->setQuery($query);
		$items = $db->loadObjectList('id');
		$cats  = $byparent = array();

		if($items)
		{
			foreach($items as $key => $item)
			{
				$params = new \Joomla\Registry\Registry();
				$params->loadString((string)$item->params);

				$cats[$item->id]         = $item;
				$cats[$item->id]->params = $params;

				$cats[$item->id]->submission = $params->get('submission', 1);

				$byparent[$item->parent_id][] = $cats[$item->id];
			}
			$out = $this->sort($byparent);
			foreach($out as $key => $item)
			{
				$sections[$item->section_id]->children[] = $item;
			}
		}
		else
		{
			$items = array();
		}

		return array($sections, $items);
	}
}
