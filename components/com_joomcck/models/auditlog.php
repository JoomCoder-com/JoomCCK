<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die ();
jimport('mint.mvc.model.list');

class JoomcckModelAuditlog extends MModelList
{

	public function __construct($config = array())
	{

		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'al.ctime',
				'u.username',
				'r.title'
			);
		}
		parent::__construct($config);
	}

	protected function populateState($ordering = NULL, $direction = NULL)
	{
		$app    = JFactory::getApplication();
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$section = $app->getUserStateFromRequest($this->context . '.section_id', 'filter_section', $app->input->get('section_id', array(), 'array'), 'array');
		$this->setState('auditlog.section_id', $section);

		$type = $app->getUserStateFromRequest($this->context . '.type_id', 'filter_type', $app->input->get('filter_type', array(), 'array'), 'array');
		$this->setState('auditlog.type_id', $type);

		$event = $app->getUserStateFromRequest($this->context . '.event_id', 'filter_event', $app->input->get('filter_event', array(), 'array'), 'array');
		$this->setState('auditlog.event_id', $event);

		$users = $app->getUserStateFromRequest($this->context . '.user_id', 'filter_user', $app->input->get('filter_user', array(), 'array'), 'array');
		$this->setState('auditlog.user_id', $users);

		$start = $app->getUserStateFromRequest($this->context . '.fcs', 'filter_cal_start', $app->input->get('filter_cal_start'), 'string');
		$this->setState('auditlog.fcs', $start);

		$end = $app->getUserStateFromRequest($this->context . '.fce', 'filter_cal_end', $app->input->get('filter_cal_end'), 'string');
		$this->setState('auditlog.fce', $end);

		parent::populateState('al.ctime', 'desc');
	}

	protected function getStoreId($id = '')
	{
		$id .= ':' . $this->getState('list.start');
		$id .= ':' . $this->getState('list.limit');
		$id .= ':' . $this->getState('list.ordering');
		$id .= ':' . $this->getState('list.direction');
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('auditlog.fcs');
		$id .= ':' . $this->getState('auditlog.fce');
		$id .= ':' . implode(',', (array)$this->getState('auditlog.section_id'));
		$id .= ':' . implode(',', (array)$this->getState('auditlog.type_id'));
		$id .= ':' . implode(',', (array)$this->getState('auditlog.event_id'));
		$id .= ':' . implode(',', (array)$this->getState('auditlog.user_id'));

		return parent::getStoreId($id);
	}


	public function getTable($type = 'Audit_log', $prefix = 'JoomcckTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getListQuery()
	{
		$user  = JFactory::getUser();
		$db    = $this->getDbo();
		$query = $db->getQuery(TRUE);

		$query->select('al.*');
		$query->from('#__js_res_audit_log al');

		$query->select('r.title, IF(r.id,1,0) as isrecord');
		$query->leftJoin('#__js_res_record AS r ON r.id = al.record_id');

		$query->select('u.username');
		$query->leftJoin('#__users AS u ON u.id = al.user_id');


		$fcs = $this->state->get('auditlog.fcs');
		$fce = $this->state->get('auditlog.fce');

		if($fce && $fcs)
		{
			$query->where("al.ctime BETWEEN '{$fcs}' AND '{$fce}'");
		}

		$event_id = $this->state->get('auditlog.event_id');
		if($event_id)
		{
			$query->where('al.event IN (' . implode(',', $event_id) . ')');
		}

		$user_id = $this->state->get('auditlog.user_id');
		if($user_id)
		{
			$query->where('al.user_id IN (' . implode(',', $user_id) . ')');
		}

		$type_id = $this->state->get('auditlog.type_id');
		if($type_id)
		{
			$query->where('al.type_id IN (' . implode(',', $type_id) . ')');
		}
		else
		{
			$types = $this->getTypes();
			$keys  = array_keys($types);
			$query->where('al.type_id IN (' . implode(',', $keys) . ')');
		}

		$section_id = $this->state->get('auditlog.section_id');
		if($section_id)
		{
			$query->where('al.section_id IN (' . implode(',', $section_id) . ')');
		}
		else
		{
			$sections = $this->getSections();
			$keys     = array_keys($sections);
			$query->where('al.section_id IN (' . implode(',', $keys) . ')');
		}
		$search = $this->getState('filter.search', 0);
		if($search)
		{
			switch(substr($search, 0, 4))
			{
				case 'rid:':
					$where = 'al.record_id = ' . (int)str_replace('rid:', '', $search);
					break;

				case 'uid:':
					$where = 'al.user_id = ' . (int)str_replace('uid:', '', $search);
					break;

				case 'log:':
					$where = 'al.event = ' . (int)str_replace('log:', '', $search);
					break;

				default:
					$s1 = $db->Quote('%' . $db->escape($search, TRUE) . '%');
					$s  = $db->Quote($db->escape($search, TRUE));

					$w[] = 'r.title LIKE ' . $s1;
					$w[] = 'u.username LIKE ' . $s1;

					$where = "(" . implode(' OR ', $w) . ")";
			}

			$query->where($where);
		}

		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		//echo str_replace('#_', 'jos', $query);

		return $query;
	}

	public function getEvents()
	{
		static $events = NULL;

		if($events === NULL)
		{
			$events = array();

			$db    = $this->getDbo();
			$query = $db->getQuery(TRUE);
			$query->select("a.event, count(a.event) as total, a.type_id");
			$query->from('#__js_res_audit_log AS a');
			$query->group('a.event');

			$type_id = $this->state->get('auditlog.type_id');
			if($type_id)
			{
				$query->where('a.type_id IN (' . implode(',', $type_id) . ')');
			}
			else
			{
				$types = $this->getTypes();
				$keys  = array_keys($types);
				$query->where('a.type_id IN (' . implode(',', $keys) . ')');
			}

			$section_id = $this->state->get('auditlog.section_id');
			ArrayHelper::clean_r($section_id, TRUE);
			\Joomla\Utilities\ArrayHelper::toInteger($section_id);
			$section_id = implode(',', $section_id);
			if(!empty($section_id))
			{
				$query->where('a.section_id IN (' . $section_id . ')');
			}
			else
			{
				$sections = $this->getSections();
				$keys     = array_keys($sections);
				$query->where('a.section_id IN (' . implode(',', $keys) . ')');
			}

			$db->setQuery($query);

			$list = $db->loadObjectList();


			foreach($list as $key => $event)
			{
				$type     = ItemsStore::getType($event->type_id);
				$events[] = JHtml::_('select.option', $event->event, JText::_($type->params->get('audit.al' . $event->event . '.msg')) . ' <span class="badge bg-light text-muted border">' . $event->total . '</span>');
			}

			ArrayHelper::clean_r($events);
		}

		return $events;
	}

	public function getUsers()
	{
		static $users = NULL;

		if($users === NULL)
		{
			$users = array();

			$db    = $this->getDbo();
			$query = $db->getQuery(TRUE);
			$query->select("a.user_id, count(a.user_id) as total");
			$query->from('#__js_res_audit_log AS a');

			$query->select("u.name, u.username");
			$query->leftJoin('#__users AS u ON u.id = a.user_id');

			$query->group('a.user_id');

			$type_id = $this->state->get('auditlog.type_id');
			if($type_id)
			{
				$query->where('a.type_id IN (' . implode(',', $type_id) . ')');
			}
			else
			{
				$types = $this->getTypes();
				$keys  = array_keys($types);
				$query->where('a.type_id IN (' . implode(',', $keys) . ')');
			}

			$section_id = $this->state->get('auditlog.section_id');
			\Joomla\Utilities\ArrayHelper::toInteger($section_id);
			if($section_id)
			{
				$query->where('a.section_id IN (' . implode(',', $section_id) . ')');
			}
			else
			{
				$sections = $this->getSections();
				$keys     = array_keys($sections);
				$query->where('a.section_id IN (' . implode(',', $keys) . ')');
			}

			$db->setQuery($query);

			$list = $db->loadObjectList();

			foreach($list as $key => $user)
			{
				$users[] = JHtml::_('select.option', $user->user_id, sprintf('%s (%s) <span class="badge bg-light text-muted border">%d</span>', $user->name, $user->username ? $user->username : JText::_('CGUEST'), $user->total));
			}

			ArrayHelper::clean_r($users);
		}

		return $users;
	}

	public function getSections()
	{
		static $sections = NULL;

		if($sections === NULL)
		{
			$sections = array();

			$db    = $this->getDbo();
			$query = $db->getQuery(TRUE);
			$query->select("a.section_id AS id, count(a.section_id) as total, s.params, s.name");
			$query->leftJoin('#__js_res_sections AS s ON s.id = a.section_id');
			$query->from('#__js_res_audit_log AS a');
			$query->group('a.section_id');

			$db->setQuery($query);

			$list = $db->loadObjectList();

			foreach($list as $key => $section)
			{
				$section->params = new JRegistry($section->params);
				if(MECAccess::allowAuditLog($section))
				{
					$sections[$section->id] = $section;
				}
			}

			ArrayHelper::clean_r($sections);
			$sections[0] = new stdClass();
		}

		return $sections;
	}

	public function getTypes()
	{
		static $types = NULL;

		if($types === NULL)
		{
			$types = array();

			$db    = $this->getDbo();
			$query = $db->getQuery(TRUE);
			$query->select("a.type_id AS id, count(a.type_id) as total, t.name, t.params");
			$query->from('#__js_res_audit_log AS a');
			$query->leftJoin('#__js_res_types AS t ON t.id = a.type_id');
			$query->group('a.type_id');

			$db->setQuery($query);

			$list = $db->loadObjectList();

			foreach($list as $key => $type)
			{
				$type->params = new JRegistry($type->params);
				if($type->params->get('audit.audit_log'))
				{
					$types[$type->id] = $type;
				}
			}

			ArrayHelper::clean_r($types);
			$types[0] = new stdClass();
		}

		return $types;
	}
}