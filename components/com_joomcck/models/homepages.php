<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
jimport('mint.mvc.model.list');

class JoomcckModelHomepages extends MModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'u.username',
				'u.registerDate',
				'u.lastvisitDate',
			);
		}

		parent::__construct($config);
	}

	public function _getauthor()
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		
		$id = $app->input->getInt('record_id');

		if(!$id)
		{
			return $user->get('id');
		}

		$record = ItemsStore::getRecord($id);

		return $record->user_id;
		
	}
	public function getAll()
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		
		$author = $this->_getauthor();

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('u.id');
		$query->from('#__users AS u');
		
		$query->select('o.params');
		$query->leftJoin('#__js_res_user_options AS o ON o.user_id = u.id');

		$query->where('u.id IN(
			SELECT user_id 
			FROM `#__js_res_user_post_map` 
			WHERE user_id NOT IN(
				SELECT u_id 
				FROM `#__js_res_subscribe_user` 
				WHERE section_id = '.$app->input->getInt('section_id').' 
				AND user_id = '.$author.' AND exclude = 0) 
			AND section_id = '.$app->input->getInt('section_id').' 
			AND whopost = 2)');

		//echo $query;

		$search = $db->escape($this->getState('filter.search', 0));
		if($search)
		{
			$intsearch = (int)$search;

			$query->where("(u.name LIKE '%{$search}%' OR u.username LIKE '%{$search}%' OR u.id = $intsearch)");
		}

		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		$db->setQuery($query);

		return $db->loadObjectList();

		
	}
	public function getListQuery()
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$author = $this->_getauthor();

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('u.id');
		$query->from('#__users AS u');
		
		$query->select('o.params');
		$query->leftJoin('#__js_res_user_options AS o ON o.user_id = u.id');

		$query->where('u.id IN(
			SELECT user_id 
			FROM `#__js_res_user_post_map` 
			WHERE user_id IN(
				SELECT u_id 
				FROM `#__js_res_subscribe_user` 
				WHERE section_id = '.$app->input->getInt('section_id').' 
				AND user_id = '.$author.' AND exclude = 0)
			AND section_id = '.$app->input->getInt('section_id').' 
			AND whopost > 0)');

		//echo $query;

		$search = $db->escape($this->getState('filter.search', 0));
		if($search)
		{
			$intsearch = (int)$search;

			$query->where("(u.name LIKE '%{$search}%' OR u.username LIKE '%{$search}%' OR u.id = $intsearch)");
		}

		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		// 		echo $query;
		return $query;
	}

	public function getStoreId($id = NULL)
	{
		$id .= ':' . $this->getState('list.start');
		$id .= ':' . $this->getState('list.limit');
		$id .= ':' . $this->getState('list.ordering');
		$id .= ':' . $this->getState('list.direction');

		return md5($this->context . ':' . $id);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		parent::populateState('u.name', 'ASC');
	}

}
