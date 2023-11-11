<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die ('Restricted access');
jimport('mint.mvc.model.list');

class JoomcckModelCTypes extends MModelList
{

	public function __construct($config = array())
	{
		if(empty ($config ['filter_fields']))
		{
			$config ['filter_fields'] = array('a.id', 'a.name', 'a.published', 'a.language');
		}
		$this->option = 'com_joomcck';
		parent::__construct($config);
	}

	protected function populateState($ordering = NULL, $direction = NULL)
	{
		$app = \Joomla\CMS\Factory::getApplication('administrator');

		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $published);

		parent::populateState('a.name', 'asc');
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
		$db    = $this->getDbo();
		$query = $db->getQuery(TRUE);

		$query->select('a.*');
		$query->from('#__js_res_types AS a');

		$query->select('count(f.id) AS fieldnum');
		$query->join('LEFT', '#__js_res_fields AS f  ON f.type_id = a.id');

		$search = $this->getState('filter.search');
		if($search = $this->getState('filter.search'))
		{
			$search = $db->Quote('%' . $db->escape($search, TRUE) . '%');
			$query->where('(a.name LIKE ' . $search . ')');
		}

		$published = $this->getState('filter.state');
		if(is_numeric($published))
		{
			$query->where('a.published = ' . ( int )$published);
		}
		else if($published === '')
		{
			$query->where('(a.published IN (0, 1))');
		}

		$orderCol  = $this->state->get('list.ordering', 'a.name');
		$orderDirn = $this->state->get('list.direction', 'ASC');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		$query->group('a.id');


		//echo nl2br(str_replace('#__','jos_',$query));
		//exit;
		return $query;
	}
}
