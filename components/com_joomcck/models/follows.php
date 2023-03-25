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

class JoomcckModelFollows extends MModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'r.title'
			);
		}

		parent::__construct($config);
	}

	public function getCats($section)
	{
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$db->setQuery("SELECT cat.id, cat.title, cat.alias, cat.params, cat.path FROM #__js_res_subscribe_cat AS c
		LEFT JOIN #__js_res_categories AS cat ON cat.id = c.cat_id
		WHERE c.section_id = {$section->id} AND c.exclude = {$section->follow} AND c.user_id = {$user->id}");

		$list = $db->loadObjectList();
		foreach ($list AS &$cat)
		{
			$cat->params = new JRegistry($cat->params);
		}

		return $list;
	}
	public function getUsers($section)
	{
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$db->setQuery("SELECT u_id as id FROM #__js_res_subscribe_user WHERE section_id = {$section->id} AND exclude = ".(int)$section->follow." AND user_id = {$user->id}");

		$list = $db->loadObjectList();
		foreach ($list AS &$cat)
		{
			//$cat->params = new JRegistry($cat->params);
		}

		return $list;
	}

	public function getTotalRecords($section_id)
	{
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$db->setQuery("SELECT count(*) FROM #__js_res_record WHERE section_id = {$section_id} AND (user_id = " . $user->get('id') . " OR access IN(".implode(',', $user->getAuthorisedViewLevels())."))");
		return (int)$db->loadResult();
	}
	public function getSubRecords($section_id)
	{
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$db->setQuery("SELECT count(*) FROM #__js_res_subscribe WHERE `type` = 'record' AND section_id = {$section_id} AND user_id = {$user->id}");
		return (int)$db->loadResult();
	}
	public function getListQuery()
	{
		$user = JFactory::getUser();

		//var_dump( MECAccess::allowSales($user));

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query = $db->getQuery(true);
		$query->select('r.id, r.title, r.alias, r.section_id, 1 as subscribed, r.access');
		$query->from('#__js_res_subscribe AS s');
		$query->leftJoin('#__js_res_record AS r ON r.id = s.ref_id');
		$query->where("`type` = 'record'");
		$query->where('s.user_id = '.$user->id);
		$query->where('s.section_id = '.JFactory::getApplication()->input->getInt('section_id', 0));


		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		//echo $query;
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

		$status = $app->getUserStateFromRequest($this->context . '.filter.status', 'filter_status', '', 'string');
		$this->setState('filter.status', $status);

		$section = $app->getUserStateFromRequest($this->context . '.filter.section', 'filter_section', '', 'string');
		$this->setState('filter.section', $section);

		$buyer = $app->getUserStateFromRequest($this->context . '.filter.buyer', 'filter_buyer', '', 'int');
		$this->setState('filter.buyer', $buyer);

		$saler = $app->getUserStateFromRequest($this->context . '.filter.saler', 'filter_saler', '', 'int');
		$this->setState('filter.saler', $saler);

		parent::populateState('r.ctime', 'desc');
	}

}
