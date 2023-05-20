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

class JoomcckModelOrders extends MModelList
{

	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'o.name',
				'section',
				'o.ctime',
				'o.status'
			);
		}

		parent::__construct($config);
	}

	public function getListQuery()
	{
		$user = JFactory::getUser();

		//var_dump( MECAccess::allowSales($user));

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('o.*');
		$query->from('#__js_res_sales AS o');

		$query->select('s.name AS section');
		$query->leftJoin('#__js_res_sections AS s ON o.section_id = s.id');

		$query->select('r.title AS rtitle');
		$query->leftJoin('#__js_res_record AS r ON o.record_id = r.id');

		$query->select('u.username');
		$query->leftJoin('#__users AS u ON o.user_id = u.id');

		$query->where('o.record_id IN (SELECT id FROM #__js_res_record)');

		$search = $this->getState('filter.search', 0);
		if($search)
		{
			switch(substr($search, 0, 4))
			{
				case 'pid:':
					$where = 'o.record_id = '.(int)str_replace('pid:', '', $search);
					break;

				case 'bid:':
					$where = 'o.user_id = '.(int)str_replace('bid:', '', $search);
					break;

				case 'sid:':
					$where = 'o.saler_id = '.(int)str_replace('sid:', '', $search);
					break;

				default:
					$s1 = $db->Quote('%' . $db->escape($search, true) . '%');
					$s = $db->Quote($db->escape($search, true));

					$w[] = "o.gateway_id = {$s}";
					$w[] = 'o.name LIKE ' . $s1;
					$w[] = "o.user_id = " . (int)$s;
					$w[] = "u.username = {$s}";

					$where = "(" . implode(' OR ', $w) . ")";
			}

			$query->where($where);
		}

		$buyer = $this->getState('filter.buyer', 0);
		if($buyer)
		{
			$query->where('o.user_id = ' . $buyer);
		}

		$saler = $this->getState('filter.saler', 0);
		if($saler && !$this->isSuperUser())
		{
			if($secmod = MECAccess::allowViewSales($user))
			{
				$query->where('(o.saler_id = ' . $saler.' OR o.section_id IN('.implode(',', $secmod).'))');
			}
			else
			{
				$query->where('o.saler_id = ' . $saler);
			}
		}

		$status = $this->getState('filter.status');
		if($status)
		{
			$query->where('o.status = ' . (int)$status);
		}

		$section = $this->getState('filter.section');
		if($section)
		{
			$query->where('o.section_id = ' . (int)$section);
		}

		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
				//echo $query;
		return $query;
	}

	public function getSections()
	{
		$db = $this->getDbo();

		$sql = "SELECT DISTINCT section_id FROM #__js_res_sales";

		if($this->getState('filter.saler', 0) && ! $this->isSuperUser())
		{
			//$sql .= " WHERE saler_id = " . $this->getState('filter.saler', 0);
		}
		elseif($this->getState('filter.buyer', 0))
		{
			//$sql .= " WHERE user_id = " . $this->getState('filter.buyer', 0);
		}

		$query = $db->getQuery(true);
		$query->select('s.name AS text, s.id AS value');
		$query->from('#__js_res_sections AS s');
		$query->where('s.published = 1');

		if(!$this->isSuperUser())
		{
			$user = JFactory::getUser();
			$w[] = 's.id IN (SELECT section_id FROM #__js_res_sales WHERE saler_id = '.$user->get('id').')';

			if($secmod = MECAccess::allowViewSales($user))
			{
				$w[] = 's.id IN ('.implode(',', $secmod).')';
			}
			$query->where("(".implode(' OR ', $w).")");
		}

		$db->setQuery($query);
		$sections = $db->loadObjectList();
		return $sections;
	}

	public function isSuperUser($uid = NULL)
	{
		$app = JFactory::getApplication();
		$menu = $app->getMenu();
		$active = $menu->getActive();

		if(!$uid)
		{
			$uid = JFactory::getUser()->get('id');
		}

		if(!@$active->getParams() instanceof JRegistry)
		{
			return false;
		}

		$allow_users = $active->getParams()->get('allow_users', false);
		$user_ids = explode(',', $allow_users);

		$user_ids = \Joomla\Utilities\ArrayHelper::toInteger($user_ids);
		ArrayHelper::clean_r($user_ids);

		return (in_array($uid, $user_ids));
	}

	public function getSection()
	{
		$section = $this->getState('filter.section');
		if($section)
		{
			return ItemsStore::getSection($this->getState('filter.section'));
		}

		return null;
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

		parent::populateState('o.ctime', 'desc');
	}

}
