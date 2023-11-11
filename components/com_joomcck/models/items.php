<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('mint.mvc.model.list');

class JoomcckModelItems extends MModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'a.id',
				'a.title',
				'a.published',
				'a.hits',
				'a.featured',
				'a.ctime', 'ctime',
				'a.extime',
				'a.mtime',
				'a.access',
				'a.favorite_num',
				'a.votes',
				'a.comments',
				'u.username',
				's.name',
				't.name'
			);
		}
		$this->option = 'com_joomcck';
		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		$app = \Joomla\CMS\Factory::getApplication('administrator');

		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $published);

		$section = $app->getUserStateFromRequest($this->context . '.filter.section', 'filter_section', '', 'INT');
		$this->setState('filter.section', $section);

		$category = $app->getUserStateFromRequest($this->context . '.filter.category', 'filter_category', '', 'INT');
		$this->setState('filter.category', $category);

		$type = $app->getUserStateFromRequest($this->context . '.filter.type', 'filter_type', '', 'INT');
		$this->setState('filter.type', $type);

		parent::populateState('a.ctime', 'asc');
	}

	protected function getStoreId($id = '')
	{
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');
		$id .= ':' . $this->getState('filter.access');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('a.*');
		$query->from('#__js_res_record AS a');

		$query->select('t.name AS type_name');
		$query->leftJoin('#__js_res_types AS t ON a.type_id = t.id');

		$query->select('s.name AS section_name');
		$query->leftJoin('#__js_res_sections AS s ON a.section_id = s.id');

		$query->select('u.username AS userlogin');
		$query->select('u.name AS username');
		$query->leftJoin('#__users AS u ON a.user_id = u.id');

		$query->select('vl.title AS access_title');
		$query->leftJoin('#__viewlevels AS vl ON a.access = vl.id');

		$search = $this->getState('filter.search');
		if($search)
		{
			if(substr($search, 0, 7) == 'record:')
			{
				$query->where("a.id = " . (int)str_replace('record:', '', $search));
			}
			elseif(substr($search, 0, 5) == 'user:')
			{
				$query->where('(u.id = ' . (int)str_replace('user:', '', $search) . ')');
			}
			elseif(substr($search, 0, 3) == 'ip:')
			{
				$query->where('(a.ip = \'' . str_replace('ip:', '', $search) . '\')');
			}
			else
			{
				$w[] = "a.title    LIKE '%" . $search . "%'";
				$w[] = "u.username LIKE '%" . $search . "%'";
				$w[] = "a.ip LIKE '%" . $search . "%'";
				$w[] = "u.email    LIKE '%" . $search . "%'";
				$w[] = "a.id = '" . $search . "'";
				$query->where('(' . implode(' OR ', $w) . ')');
			}
		}

		$section = $this->getState('filter.section');
		if($section)
		{
			$query->where('(a.section_id = ' . $section . ')');
		}

		$category = $this->getState('filter.category');
		if($category)
		{
			$query->join('LEFT', '#__js_res_record_category AS rc  ON rc.record_id = a.id');
			$query->where('(rc.catid = ' . $category . ')');
		}

		$type = $this->getState('filter.type');
		if($type)
		{
			$query->where('(a.type_id = ' . $type . ')');
		}

		$published = $this->getState('filter.state');
		if(is_numeric($published))
		{
			$query->where('a.published = ' . (int)$published);
		}
		else
			if($published === '')
			{
				$query->where('(a.published IN (0, 1))');
			}

		$orderCol = $this->state->get('list.ordering', 'a.ctime');
		$orderDirn = $this->state->get('list.direction', 'desc');
		$query->order($db->escape($orderCol.' '.$orderDirn));
		//$query->group('a.id');


		return $query;
	}

    
	public function getTypesLinks()
	{
        $types = $this->getTypes();
		$sections = $this->getSections();
        
		foreach($sections AS $section)
		{
            $params = new \Joomla\Registry\Registry($section->params);
            
		}
	}
    public function getSections()
    {
        $sql = 'SELECT name AS text, id AS value, params FROM #__js_res_sections';
        $this->_db->setQuery($sql);
        return $this->_db->loadObjectList();
    }
	public function getTypes()
	{
		$sql = 'SELECT name AS text, id AS value, params FROM #__js_res_types';
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	}
}
