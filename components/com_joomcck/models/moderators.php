<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
jimport('mint.mvc.model.list');

class JoomcckModelModerators extends MModelList
{

	public function __construct($config = array())
	{
		if(empty ($config ['filter_fields']))
		{
			$config ['filter_fields'] =
				array('u.username', 'm.published', 'm.id', 'm.ctime', 's.name');
		}

		parent::__construct($config);
	}

	public function getListQuery()
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		$db    = $this->getDbo();
		$query = $db->getQuery(TRUE);

		$query->select('m.*');
		$query->from('#__js_res_moderators AS m');
		$query->select('u.name, u.username');
		$query->leftJoin('#__users AS u ON m.user_id = u.id');
		$query->select('s.name AS section');
		$query->leftJoin('#__js_res_sections AS s ON m.section_id = s.id');
		$query->where('m.user_id != ' . $user->get('id'));
		// 		$query->where('cu.section_id = '.$section_id);

		$search = $this->getState('filter.search', 0);
		if($search)
		{
			$search = $db->Quote('%' . $db->escape($search, TRUE) . '%');
			$query->where('(u.name LIKE ' . $search . ' OR u.username LIKE ' . $search . ')');
		}

		$published = $this->getState('filter.state');
		if(is_numeric($published))
		{
			$query->where('m.published = ' . ( int )$published);
		}
		else if($published === '')
		{
			$query->where('(m.published IN (0, 1))');
		}

		$section = $this->getState('filter.section');
		if(is_numeric($section))
		{
			$query->where('m.section_id = ' . ( int )$section);
		}

		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		//		$query->order($db->escape('cu.ordering ASC'));
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

	public function getSections()
	{
		$db = $this->getDbo();

		$sql = "SELECT s.name AS text, s.id AS value FROM #__js_res_sections AS s
				WHERE s.published = 1";

		$db->setQuery($sql);
		$sections = $db->loadObjectList();
		$user_id  = \Joomla\CMS\Factory::getApplication()->getIdentity()->get('id');
		foreach($sections as $key => $value)
		{
			if(!MECAccess::isModerator($user_id, $value->value))
			{
				//echo 123;
				unset($sections[$key]);
			}
		}

		return $sections;
	}

	public function getStoreId($id = NULL)
	{
		$id .= ':' . $this->getState('list.start');
		$id .= ':' . $this->getState('list.limit');
		$id .= ':' . $this->getState('list.ordering');
		$id .= ':' . $this->getState('list.direction');

		return md5($this->context . ':' . $id);
	}

	protected function populateState($ordering = NULL, $direction = NULL)
	{
		parent::populateState('m.ctime', 'desc');

		$app = \Joomla\CMS\Factory::getApplication();

		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'string');
		$this->setState('filter.state', $published);

		$section = $app->getUserStateFromRequest($this->context . '.filter.section', 'filter_section', '', 'string');
		$this->setState('filter.section', $section);

		$return = $app->getUserStateFromRequest($this->context . '.moderators.return', 'return', NULL, 'string');
		$this->setState('moderators.return', $return);

		$value = $app->input->get('limitstart', 0, 'uint');
		$this->setState('list.start', $value);

	}

}
