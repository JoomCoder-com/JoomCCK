<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
jimport('mint.mvc.model.list');

class JoomcckModelRecords extends MModelList
{

	public $worns = array();

	public $total = 0;

	public $section = NULL;

	private $_showRecords = TRUE;

	public $_filtersWhere = TRUE;

	public $_navigation = TRUE;

	public $_ids = array();

	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function populateState($ordering = NULL, $direction = NULL)
	{
		//$app = JFactory::getApplication('administrator');
		$app    = JFactory::getApplication('site');
		$filter = JFilterInput::getInstance();
		$key    = FilterHelper::key();

		$section_id = $app->getUserStateFromRequest($this->context . $key . '.filter.section_id', 'section_id', '', 'int');
		$this->setState('records.section_id', $section_id);

		$search = $app->getUserStateFromRequest('com_joomcck.section' . $key . '.filter_search', 'filter_search', '', 'string');
		$this->setState('records.search', trim($search));

		$search2 = $app->getUserStateFromRequest('com_joomcck.section' . $key . '.filter_search2', 'filter_search2', '', 'string');
		$this->setState('records.search2', trim($search2));

		$alpha = $app->getUserStateFromRequest('com_joomcck.section' . $key . '.filter_alpha', 'filter_alpha', '', 'string');
		$this->setState('records.alpha', $alpha);

		$type = $app->getUserStateFromRequest('com_joomcck.section' . $key . '.filter_type', 'filter_type', '', 'array');
		if(isset($type[0]) && strpos($type[0], ','))
		{
			$type = explode(',', $type[0]);
		}
		ArrayHelper::clean_r($type);
		\Joomla\Utilities\ArrayHelper::toInteger($type);
		$this->setState('records.type', $type);

		$user = $app->getUserStateFromRequest('com_joomcck.section' . $key . '.filter_user', 'filter_user', '', 'array');
		ArrayHelper::clean_r($user);
		\Joomla\Utilities\ArrayHelper::toInteger($user);
		$this->setState('records.user', $user);

		$tag = $app->getUserStateFromRequest('com_joomcck.section' . $key . '.filter_tag', 'filter_tag', '', 'array');
		ArrayHelper::clean_r($tag);
		\Joomla\Utilities\ArrayHelper::toInteger($tag);
		$this->setState('records.tag', $tag);

		$cat = $app->getUserStateFromRequest('com_joomcck.section' . $key . '.filter_cat', 'filter_cat', '', 'array');
		settype($cat, 'array');
		ArrayHelper::clean_r($cat);
		\Joomla\Utilities\ArrayHelper::toInteger($cat);
		$this->setState('records.category', $cat);

		$config['filter_fields'] = array(
			'r.id',
			'r.ctime',
			'r.mtime',
			'r.extime',
			'r.title',
			'name',
			'username',
			'r.hits',
			'r.comments',
			'r.favorite_num',
			'r.votes_result',
			'field_value'
		);
		if($search)
		{
			$config['filter_fields'][] = 'searchresult';
		}

		$order = $app->input->getString('filter_order');
		if(substr($order, 0, 6) == 'field^')
		{
			$ordervals = explode('^', $order);
			if(count($ordervals) == 3)
			{
				$config['filter_fields'][] = $order;
				$app->setUserState($this->context . '.ordering.vals' . $section_id, $ordervals);
			}
		}
		elseif($order)
		{
			$app->setUserState($this->context . '.ordering.vals' . $section_id, NULL);
		}

		if(is_array($app->getUserState($this->context . '.ordering.vals' . $section_id)))
		{
			if(count($app->getUserState($this->context . '.ordering.vals' . $section_id)) == 3)
			{
				$config['filter_fields'][] = implode('^', $app->getUserState($this->context . '.ordering.vals' . $section_id));
			}
		}

		$this->setProperties($config);

		$context = $this->context;
		$this->context .= $app->input->getInt('section_id');
		$app->input->set('limitstart', $app->input->getInt('limitstart', 0));
		$orders = $this->section->params->get('general.orderby', 'r.ctime DESC');
		if($this->section->params->get('general.section_home_items') && !$app->input->getInt('cat_id', NULL) && $this->section->categories)
		{
			$orders = $this->section->params->get('general.section_home_orderby', $orders);
		}
		if($app->input->getCmd('format') == 'feed')
		{
			$orders = $this->section->params->get('more.orderby_rss', $orders);
			$app->input->set('filter_order', $order[0]);
			$app->input->set('filter_order_Dir', $order[1]);
		}
		$orders = explode(' ', $orders);

		parent::populateState($orders[0], @$orders[1]);

		$this->context = $context;
	}

	protected function getStoreId($id = '')
	{
		$id .= ':' . $this->getState('record.section_id');
		$id .= ':' . $this->getState('list.start');
		$id .= ':' . $this->getState('list.limit');
		$id .= ':' . $this->getState('list.ordering');
		$id .= ':' . $this->getState('list.direction');
		$id .= ':' . $this->getState('records.section_id');
		$id .= ':' . $this->getState('records.search');
		$id .= ':' . $this->getState('records.search2');
		$id .= ':' . $this->getState('records.alpha');
		$id .= ':' . implode(',', (array)$this->getState('records.type'));
		$id .= ':' . implode(',', (array)$this->getState('records.user'));
		$id .= ':' . implode(',', (array)$this->getState('records.tag'));
		$id .= ':' . implode(',', (array)$this->getState('records.category'));

		return parent::getStoreId($id);
	}

	public function getListQuery()
	{
		$app       = JFactory::getApplication();
		$db        = $this->getDbo();
		$user      = JFactory::getUser();
		$total     = $this->total;
		$view_what = $app->input->get('view_what');

		$section = $this->section;
		$query   = $db->getQuery(TRUE);
		$query->from('#__js_res_record AS r');

		if(!$total)
		{
			$query->select('r.*');

			if($user->get('id'))
			{
				if(in_array($section->params->get('events.subscribe_record'), $user->getAuthorisedViewLevels()))
				{
					$query->select("(SELECT id FROM #__js_res_subscribe
						WHERE ref_id = r.id
						AND user_id = " . $user->get('id') . " AND `type` = 'record' AND section_id = {$section->id} LIMIT 1) as subscribed");
				}

				if(!empty($this->types))
				{
					foreach($this->types AS $type)
					{
						if(in_array($type->params->get('properties.item_can_favorite'), $user->getAuthorisedViewLevels()))
						{
							$query->select('(SELECT id FROM #__js_res_favorite WHERE record_id = r.id AND user_id = ' . $user->get('id') . ' LIMIT 1) as bookmarked');
							break;
						}
					}
				}
				else
				{
					$query->select('(SELECT id FROM #__js_res_favorite WHERE record_id = r.id AND user_id = ' . $user->get('id') . ' LIMIT 1) as bookmarked');
				}

			}

			if($section->params->get('general.marknew'))
			{
				if($user->get('id'))
				{
					$query->select("(SELECT id FROM #__js_res_hits
						WHERE record_id = r.id AND user_id = " . $user->get('id') . " LIMIT 1) AS `new`");
				}
				else
				{
					$query->select("(SELECT id FROM #__js_res_hits
						WHERE record_id = r.id AND ip = '" . $_SERVER['REMOTE_ADDR'] . "' LIMIT 1) AS `new`");
				}
			}
			else
			{
				$query->select('0 as `new`');
			}

			if($section->params->get('personalize.personalize') && $section->params->get('personalize.pcat_submit'))
			{
				$query->select('uc.name AS ucatname, uc.alias AS ucatalias');
				$query->join('LEFT', '#__js_res_category_user AS uc on uc.id = r.ucatid');
			}

			$orderCol  = $this->state->get('list.ordering', 'r.ctime');
			$orderDirn = $this->state->get('list.direction', 'DESC');

			if($section->params->get('general.featured_first') || ($this->section->params->get('general.home_featured_first') && !$app->input->getInt('cat_id', NULL)))
			{
				$query->order('r.featured DESC, r.ftime DESC');
			}
			else
			{
				//$query->where('r.featured IN (0,1)');
			}

			$field_orders = JFactory::getApplication()->getUserState($this->context . '.ordering.vals' . $this->section->id);

			if($this->state->get('records.ordering'))
			{
				$query->order($db->escape($this->state->get('records.ordering') . ' ' . $this->state->get('records.direction')));
			}
			elseif($field_orders)
			{
				$query->select("(SELECT `field_value` FROM #__js_res_record_values WHERE record_id = r.id AND field_key = '{$field_orders[1]}' LIMIT 1) AS field_value");
				if($field_orders[2] == 'digits')
				{
					$query->order("field_value + 0 " . $orderDirn);
				}
				$query->order("field_value " . $orderDirn);
			}
			else
			{
				$query->order($db->escape($orderCol . ' ' . $orderDirn));
			}

			if(($orderCol == 'name' || $orderCol == 'username'))
			{
				$query->select('(SELECT `' . $orderCol . '` FROM  #__users WHERE id = r.user_id) AS ' . $orderCol);
			}
		}
		else
		{
			$query->select('r.id');
		}

		$query = $this->where($query);

		if($this->_showRecords)
		{
			$this->add_where($query);
		}

		if(!$this->_showRecords())
		{
			$query = "SELECT * FROM `#__js_res_record` WHERE FALSE";
		}

		if(!$total)
		{
			//echo nl2br(str_replace('#__', 'jos_', $query)) . "<br><br>";
		}

		//exit;
		return $query;
	}

	public function add_where(&$query)
	{
		$wheres = $this->wherefilter($query);
		$cats   = $this->wherecategory($query);


		if(!empty($wheres) && !empty($cats))
		{
			$ids = array_intersect($wheres, $cats);
		}
		else if(is_array($wheres) && is_array($cats))
		{
			$ids = $wheres + $cats;
		}
		else if(is_array($wheres))
		{
			$ids = $wheres;
		}
		else if(is_array($cats))
		{
			$ids = $cats;
		}

		if(!empty($ids))
		{
			$query->where("r.id IN(" . implode(',', $ids) . ")");
		}
	}

	public function getItems()
	{
		if(!$this->_showRecords())
		{
			return array();
		}


		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the list items.
		$query = $this->_getListQuery();

		if(!$this->_showRecords())
		{
			return array();
		}

		try
		{
			$items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		$items = $this->cache[$store];

		if(!$items)
		{
			return array();
		}

		$ids = array();
		foreach($items as $key => $item)
		{
			$ids[] = $item->id;
		}
		ItemsStore::$record_ids = $ids;

		return $items;
	}

	private function _showRecords()
	{
		$show = FALSE;
		$app  = JFactory::getApplication();

		if(!$app->input->getInt('cat_id'))
		{
			if($this->section->categories == 0)
			{
				$show = TRUE;
			}
			if($this->section->params->get('general.section_home_items'))
			{
				$show = TRUE;
			}
			if($app->input->get('view_what'))
			{
				$show = TRUE;
			}
			if($this->section->params->get('general.filter_mode') == 0 && $this->worns)
			{
				$show = TRUE;
			}
			if($this->section->params->get('general.filter_mode') == 1 && $this->section->params->get('general.records_mode') == 1 && $this->worns)
			{
				$show = TRUE;
			}
			if($this->section->params->get('general.filter_mode') == 1 && $this->section->params->get('general.records_mode') == 0 && $this->section->params->get('general.section_home_items') == 0 && $this->worns)
			{
				$show = TRUE;
			}
		}
		else
		{
			$show = TRUE;
		}
		if($this->_showRecords == FALSE)
		{
			$show = FALSE;
		}

		return $show;
	}

	private function getrecordids($sql, $ids = array())
	{
		static $out = array();

		$key = md5($sql);

		if(isset($out[$key]))
		{
			return $out[$key];
		}

		if(!$ids)
		{
			$db = JFactory::getDbo();
			$db->setQuery($sql);
			$ids = $db->loadColumn();
			$ids = array_unique($ids);
		}

		if($ids)
		{
			$cat_sql = "SELECT record_id FROM #__js_res_record_category WHERE catid IN(" . implode(',', $ids) . ")";
			$db->setQuery($cat_sql);
			$record_ids = $db->loadColumn();
		}
		$record_ids[] = 0;

		$out[$key] = $record_ids;

		return $out[$key];
	}

	public function wherecategory(&$query)
	{
		$app       = JFactory::getApplication();
		$cat       = $app->input->getInt('cat_id', NULL);
		$view_what = $app->input->get('view_what');
		$user      = JFactory::getUser();
		$db        = JFactory::getDbo();

		if(!empty($this->worns['cats']))
		{
			$cat = NULL;
		}
		if($view_what)
		{
			$cat = NULL;
		}
		if($this->section->params->get('general.filter_mode', 0) == 0 && !empty($this->worns))
		{
			$cat = NULL;
		}
		if($this->section->params->get('personalize.personalize') && $app->input->getInt('user_id'))
		{
			$cat = NULL;
		}
		if(!empty($this->_ids))
		{
			$cat = NULL;
		}

		$cat = $app->input->getString('force_cat_id', $cat);

		if($cat)
		{
			$cat_sql = 'SELECT c.id FROM `#__js_res_categories` AS c WHERE c.id IN (' . $cat . ')
				AND c.access IN (' . implode(',', $user->getAuthorisedViewLevels()) . ') AND c.published = 1';

			if($this->section->params->get('general.records_mode'))
			{
				settype($cat, 'integer');
				$sql = "SELECT lft, rgt FROM `#__js_res_categories` WHERE id = {$cat}";
				$db->setQuery($sql);
				$res = $db->loadObject();

				if(!$res)
				{
					throw new Exception( JText::sprintf('CERR_NOCATEGORY', $cat),100);
				}

				$cat_sql = "SELECT id FROM `#__js_res_categories`
					WHERE lft >= " . (int)$res->lft . " AND rgt <= " . (int)$res->rgt . "
					AND section_id = {$this->section->id}
					AND published = 1
					AND access IN (" . implode(',', $user->getAuthorisedViewLevels()) . ")";
			}
			$record_ids = $this->getrecordids($cat_sql);

			return $record_ids;
			//$query->where("r.id IN(" . implode(',', $record_ids) . ")");
		}

		if($this->section->params->get('general.section_home_items') == 1 && !$app->input->getInt('cat_id', NULL))
		{
			$section_root = TRUE;
			if(!empty($this->worns))
			{
				$section_root = FALSE;
			}
			if($this->section->params->get('personalize.personalize') && $app->input->getInt('user_id'))
			{
				$section_root = FALSE;
			}
			if($this->section->params->get('general.filter_mode', 0) == 1 && !empty($this->worns))
			{
				$section_root = TRUE;
			}
			if($view_what)
			{
				$section_root = FALSE;
			}

			if($section_root)
			{
				$query->where("r.id NOT IN(SELECT record_id FROM #__js_res_record_category WHERE section_id = {$this->section->id})");
			}
		}

		//return $query;
	}

	public function wherefilter(&$query)
	{
		if($this->_filtersWhere == FALSE)
		{
			return;
		}

		$app = JFactory::getApplication();

		$filters = $this->getFilters();
		$ids     = array();

		foreach($filters as $fkey => $filter)
		{
			$state = $app->getUserState('com_joomcck.section' . FilterHelper::key() . '.filter_' . $fkey);
			if($state)
			{
				$condition = $filter->onFilterWhere($this->section, $query);
				if($condition)
				{
					if(is_array($condition))
					{
						if(empty($ids))
						{
							$ids = $condition;
						}
						else
						{
							$ids = array_intersect($ids, $condition);
						}
					}
					$text               = $filter->onFilterWornLabel($this->section);
					$this->worns[$fkey] = WornHelper::getItem('filter_' . $fkey, $filter->label, $filter->value, $text);
				}
			}
		}

		return $ids;

		if($ids)
		{
			$sql = implode(',', $ids);
			$query->where("r.id IN ({$sql})");
		}

		return $query;
	}

	public function where($query)
	{
		$app          = JFactory::getApplication();
		$view_what    = $app->input->get('view_what');
		$total        = $this->total;
		$db           = JFactory::getDbo();
		$user         = JFactory::getUser();
		$user_id      = $app->input->getInt('user_id', NULL);
		$isme         = ((int)$user_id === (int)$user->get('id', NULL) && $user_id);
		$filters      = TRUE;
		$include_lang = TRUE;

		if($app->getUserState('com_joomcck.skip_record'))
		{
			$query->where("r.id != " . $app->getUserState('com_joomcck.skip_record'));
		}

		$query->where('r.section_id = ' . $this->getState('records.section_id'));

		if($this->section->params->get('personalize.personalize') && $this->section->params->get('personalize.pcat_submit') > 0)
		{
			if($app->input->getInt('ucat_id'))
			{
				$query->where("r.ucatid = " . $app->input->getInt('ucat_id'));
			}
		}

		// Show list of attached children or parents to article owner.
		$apply_access = TRUE;
		if($view_what == 'show_children' || $view_what == 'show_parents' || $view_what == 'show_all_children' || $view_what == 'show_all_parents')
		{
			$field_id = $app->input->getInt('_rfid', $app->input->getInt('_rfaid'));
			include_once 'components/com_joomcck/library/php/fields/joomcckrelate.php';
			$params   = CFormFieldRelate::getFieldParams($field_id);

			if($params->get('params.show_relate'))
			{
				$apply_access = FALSE;

				$record_id = $app->input->getInt('_rrid');
				if($record_id)
				{
					$sql = "SELECT user_id FROM #__js_res_record WHERE id = " . $record_id;
					$db->setQuery($sql);
					$parent_user = $db->loadResult();

				}
				else
				{
					$parent_user = $user->get('id');
				}

				if(!($parent_user && $parent_user == $user->get('id')))
				{
					$apply_access = TRUE;
				}
			}
		}


		if(
			!in_array($this->section->params->get('general.show_restrict'), $user->getAuthorisedViewLevels()) &&
			!MECAccess::allowRestricted($user, $this->section) &&
			!($view_what == 'children' && $user->get('id') && $user->get('id') == $app->input->getInt('parent_user_id')) &&
			$apply_access
		)
		{
			$access[] = "r.access IN(" . implode(',', $user->getAuthorisedViewLevels()) . ")";

			if($user->get('id'))
			{
				$access[] = "r.user_id = " . $user->get('id');
			}

			if($app->input->getInt('parent_see_special'))
			{
				$parent_id = $app->input->getInt('parent_id');
				$parent    = $app->input->get('parent', 'com_joomcck');
				$access[]  = "r.access = 3 AND r.parent_id = {$parent_id} AND r.parent = '{$parent}'";
			}


			$query->where("(" . implode(" OR ", $access) . ")");

			if($this->section->params->get('general.section_home_items') == 2 && $this->section->categories)
			{
				$query->where("IF(r.categories != '[]',(SELECT id FROM #__js_res_record_category WHERE record_id = r.id AND published = 1 AND access IN(" . implode(',', $user->getAuthorisedViewLevels()) . ") LIMIT 1), 1)");
			}
		}

		if($this->section->params->get('general.can_display') > 0)
		{
			$query->where("r.user_id IN (SELECT user_id FROM #__user_usergroup_map WHERE group_id = " . $this->section->params->get('general.can_display') . ")");
		}

		if($view_what == 'only_future')
		{
			$query->where("r.ctime > " . $db->quote(JFactory::getDate()->toSql()));
		}
		elseif(!in_array($this->section->params->get('general.show_future_records'), $user->getAuthorisedViewLevels()) || $view_what == 'only_expired')
		{
			$query->where("r.ctime < " . $db->quote(JFactory::getDate()->toSql()));
		}

		if($view_what == 'only_expired')
		{
			$query->where("(r.extime != '0000-00-00 00:00:00' AND r.extime < '" . JFactory::getDate()->toSql() . "')");
		}
		elseif($view_what == 'expired')
		{
			$query->where("(r.extime != '0000-00-00 00:00:00' AND r.extime < '" . JFactory::getDate()->toSql() . "')");
			$user_id = $user->get('id');
		}
		elseif(!in_array($this->section->params->get('general.show_past_records'), $user->getAuthorisedViewLevels()) || $view_what == 'exclude_expired')
		{
			$query->where("(r.extime = '0000-00-00 00:00:00' OR r.extime > '" . JFactory::getDate()->toSql() . "')");
		}

		if($view_what == 'children')
		{
			$parent_id = $app->input->getInt('parent_id', 0);
			$parent    = $app->input->get('parent', 'com_joomcck');
			$query->where("r.parent_id = {$parent_id}");
			$query->where("r.parent = '{$parent}'");
		}
		else
		{
			if(!in_array($this->section->params->get('general.show_children'), $user->getAuthorisedViewLevels()) && !$isme)
			{
				$query->where("r.parent_id = 0");
			}
		}

		if(is_array($this->_ids) && !empty($this->_ids))
		{
			\Joomla\Utilities\ArrayHelper::toInteger($this->_ids);
			$query->where('r.id IN(' . implode(',', $this->_ids) . ')');
		}

		if(!empty($this->_id_limit) && is_array($this->_id_limit))
		{
			\Joomla\Utilities\ArrayHelper::toInteger($this->_id_limit);
			$query->where('r.id IN(' . implode(',', $this->_id_limit) . ')');
		}


		//$query->where('r.archive = 0');

		if($view_what == 'unpublished')
		{
			$query->where('r.published = 0');

			$dummy         = new stdClass();
			$dummy->params = new JRegistry();

			if(MECAccess::allowPublish(NULL, $dummy, $this->section))
			{
				$user_id      = NULL;
				$include_lang = FALSE;
			}
		}
		else
		{
			if(CStatistics::hasUnPublished($this->section->id))
			{
				$query->where('r.published = 1');
			}
		}

		if($this->section->params->get('general.lang_mode') && $include_lang)
		{
			$lang = JFactory::getLanguage();
			$query->where('r.langs = ' . $db->quote($lang->getTag()));
		}

		$hide = 0;
		foreach($this->getAllTypes() AS $tp)
		{
			$hide = $tp->params->get('properties.allow_hide') ? $tp->params->get('properties.allow_hide') : $hide;
		}
		if($hide)
		{
			if($view_what == 'hidden')
			{
				$query->where('r.hidden = 1');
				$user_id = $user->get('id');
			}
			else
			{
				$query->where('r.hidden = 0');
			}
		}

		if($view_what == 'featured')
		{
			$query->where('r.featured = 1');
			$user_id = $user->get('id');
		}

		if($view_what == 'events')
		{
			$query->where('r.id IN(SELECT ref_1 FROM #__js_res_notifications WHERE state_new = 1 AND user_id = ' . $user->get('id') . ' AND ref_2 = ' . $this->section->id . ')');
			$user_id = NULL;
		}

		if($view_what == 'follow')
		{
			$query->where('r.id IN(SELECT ref_id FROM #__js_res_subscribe WHERE user_id = ' . JFactory::getUser($app->input->getInt('user_id'))->get('id') . ' AND section_id = ' . $this->section->id . ' AND `type` = \'record\')');
			$user_id = NULL;
		}

		if($view_what == 'visited')
		{
			$query->where('r.id IN(SELECT record_id FROM #__js_res_hits WHERE user_id = ' . JFactory::getUser($app->input->getInt('user_id'))->get('id') . ' AND section_id = ' . $this->section->id . ')');
			$user_id = NULL;
		}

		if($view_what == 'commented')
		{
			$query->where('r.id IN(SELECT record_id FROM #__js_res_comments WHERE user_id = ' . JFactory::getUser($app->input->getInt('user_id'))->get('id') . ' AND section_id = ' . $this->section->id . ')');
			$user_id = NULL;
		}
		if($view_what == 'rated')
		{
			$query->where('r.id IN(SELECT ref_id FROM #__js_res_vote WHERE user_id = ' . JFactory::getUser($app->input->getInt('user_id'))->get('id') . ' AND ref_type = \'record\' AND section_id = ' . $this->section->id . ')');
			$user_id = NULL;
		}
		if($view_what == 'favorited')
		{
			$query->where('r.id IN(SELECT record_id FROM #__js_res_favorite WHERE user_id = ' . JFactory::getUser($app->input->getInt('user_id'))->get('id') . ' AND section_id = ' . $this->section->id . ')');
			$user_id = NULL;
		}

		if($view_what == 'only_featured')
		{
			$query->where('r.featured = 1');
			$user_id = NULL;
		}
		if($view_what == 'exclude_featured')
		{
			$query->where('r.featured = 0 OR (r.featured = 1 AND r.ftime < NOW())');
			$user_id = NULL;
		}

		if($view_what == 'who_comment')
		{
			$comments = 'SELECT user_id FROM #__js_res_comments WHERE user_id > 0 AND record_id = ' . $app->input->getInt('id');
			$sql      = "SELECT record_id FROM #__js_res_comments WHERE user_id IN ({$comments}) AND section_id = " . $this->section->id;
			$query->where("r.id IN({$sql})");

			$query->select("(SELECT COUNT(*) FROM #__js_res_comments WHERE user_id IN({$comments}) AND record_id = r.id) AS comments_nums");

			$this->state->set('list.ordering', 'comments_nums');
			$this->state->set('list.direction', 'DESC');
			$user_id = NULL;
			$filters = FALSE;
		}
		if($view_what == 'who_rate')
		{
			$rates = "SELECT user_id FROM #__js_res_vote WHERE user_id > 0 AND ref_type = 'record' AND ref_id = " . $app->input->getInt('id');
			$sql   = "SELECT ref_id FROM #__js_res_vote WHERE user_id IN ({$rates}) AND section_id = " . $this->section->id;
			$query->where("r.id IN({$sql})");

			$query->select("(SELECT COUNT(*) FROM #__js_res_vote WHERE user_id IN({$rates}) AND ref_type = 'record' AND ref_id = r.id) AS vote_nums");

			$this->state->set('list.ordering', 'vote_nums');
			$this->state->set('list.direction', 'DESC');
			$user_id = NULL;
			$filters = FALSE;
		}

		if($view_what == 'who_visit')
		{
			$visit_users = 'SELECT user_id FROM #__js_res_hits WHERE  user_id > 0 AND record_id = ' . $app->input->getInt('id');
			$sql         = "SELECT record_id FROM #__js_res_hits WHERE user_id IN ({$visit_users}) AND section_id = " . $this->section->id;
			$query->where("r.id IN({$sql})");

			$query->select("(SELECT COUNT(*) FROM #__js_res_hits WHERE user_id IN({$visit_users}) AND record_id = r.id) AS visited_num");

			$this->state->set('list.ordering', 'visited_num');
			$this->state->set('list.direction', 'DESC');
			$user_id = NULL;
			$filters = FALSE;
		}

		if($view_what == 'who_favorite')
		{
			$visit_users = 'SELECT user_id FROM #__js_res_favorite WHERE  user_id > 0 AND record_id = ' . $app->input->getInt('id');
			$sql         = "SELECT record_id FROM #__js_res_favorite WHERE user_id IN ({$visit_users}) AND section_id = " . $this->section->id;
			$query->where("r.id IN({$sql})");

			$query->select("(SELECT COUNT(*) FROM #__js_res_favorite WHERE user_id IN({$visit_users}) AND record_id = r.id) AS fav_num");

			$this->state->set('list.ordering', 'fav_num');
			$this->state->set('list.direction', 'DESC');
			$user_id = NULL;
			$filters = FALSE;
		}

		if($view_what == 'author_tag_related' || $view_what == 'tag_related')
		{
			$ids  = array();
			$tags = 'SELECT tag_id FROM #__js_res_tags_history WHERE  record_id = ' . $app->input->getInt('id');
			$db->setQuery($tags);
			$ids1 = $db->loadColumn();
			if($ids1)
			{
				$sql = "SELECT record_id FROM #__js_res_tags_history WHERE tag_id IN (" . implode(',', $ids1) . ") AND section_id = " . $this->section->id;
				$ids = $this->getIds($sql);
			}

			if(empty($ids))
			{
				$this->_showRecords = FALSE;

				return $query;
			}

			$query->where("r.id IN(" . implode(',', $ids) . ")");
			$query->select("(SELECT COUNT(*) FROM #__js_res_tags_history WHERE tag_id IN({$tags}) AND record_id = r.id) AS count_tags");

			$this->state->set('list.ordering', 'count_tags');
			$this->state->set('list.direction', 'DESC');

			if($view_what == 'tag_related')
			{
				$user_id = NULL;
			}
			$filters = FALSE;
		}

		if($view_what == 'field_data')
		{
			$from_key  = JFactory::getApplication()->getUserState('com_joomcck.field.from');
			$in_key    = JFactory::getApplication()->getUserState('com_joomcck.field.in');
			$record_id = $app->input->getInt('id', $app->input->getInt('_rrid'));

			if($in_key && $from_key && $record_id)
			{
				$from_values = "SELECT fv1.field_value FROM #__js_res_record_values fv1 WHERE fv1.field_key = '{$from_key}' AND  fv1.record_id = " . $record_id;
				$sql         = "SELECT fv2.record_id FROM #__js_res_record_values AS fv2 WHERE fv2.section_id = {$this->section->id} AND fv2.field_key = '{$in_key}' AND fv2.field_value IN ({$from_values})";
				$ids         = $this->getIds($sql);
				if(empty($ids))
				{
					$this->_showRecords = FALSE;

					return $query;
				}
				$ids[] = 0;

				$query->where("r.id IN(" . implode(',', $ids) . ")");

				$query->select("(SELECT COUNT(*) FROM #__js_res_record_values AS fv3 WHERE fv3.field_value IN({$from_values}) AND fv3.record_id = r.id) AS count_value");

				$this->state->set('list.ordering', 'count_value');
				$this->state->set('list.direction', 'DESC');
				$user_id = NULL;
			}
			else
			{
				$this->_showRecords = FALSE;
			}
			$filters = FALSE;
		}

		if($view_what == 'user_field_data')
		{
			$from_key = JFactory::getApplication()->getUserState('com_joomcck.field.from');
			$in_key   = JFactory::getApplication()->getUserState('com_joomcck.field.in');

			if($in_key && $from_key)
			{
				$from_values = "SELECT field_value FROM #__js_res_record_values WHERE field_key = '{$from_key}' AND  record_id = " . $app->input->getInt('id');
				$sql         = "SELECT record_id FROM #__js_res_record_values WHERE section_id = {$this->section->id}
					AND field_key = '{$in_key}' AND field_value IN ({$from_values})";
				$ids         = $this->getIds($sql);
				if(empty($ids))
				{
					$this->_showRecords = FALSE;

					return $query;
				}

				$ids[] = 0;
				$query->where("r.id IN(" . implode(',', $ids) . ")");
				$query->select("(SELECT COUNT(*) FROM #__js_res_record_values WHERE field_value IN({$from_values}) AND record_id = r.id) AS count_value");

				$this->state->set('list.ordering', 'count_value');
				$this->state->set('list.direction', 'DESC');
			}
			else
			{
				$this->_showRecords = FALSE;
			}
			$filters = FALSE;
		}

		if($view_what == 'distance')
		{
			$record_id = $app->input->getInt('_rrid');
			$field_id  = $app->input->getInt('_rfid');
			$dist      = $app->input->getInt('_rdist');

			if(!$record_id || !$field_id || !$dist)
			{
				$this->_showRecords = FALSE;

				return $query;
			}

			$dist = (((1 / 115.1666667) * $dist) / 2);

			$sql = "SELECT field_value, value_index FROM #__js_res_record_values WHERE value_index IN('lat','lng') AND record_id = $record_id AND field_id = $field_id";
			$db->setQuery($sql);
			$data = $db->loadAssocList('value_index', 'field_value');

			if(empty($data['lat']) || empty($data['lng']))
			{
				$this->_showRecords = FALSE;

				return $query;
			}

			$p_sw_lat = $data['lat'] - $dist;
			$p_sw_lng = $data['lng'] - $dist;
			$p_ne_lat = $data['lat'] + $dist;
			$p_ne_lng = $data['lng'] + $dist;


			$db  = JFactory::getDbo();
			$sql = "SELECT record_id FROM #__js_res_record_values WHERE value_index = 'lat' AND field_value < " . $p_ne_lat . " AND field_value > " . $p_sw_lat;
			$db->setQuery($sql);
			$ids = $db->loadColumn();

			if(!empty($ids))
			{
				$sql = "SELECT record_id FROM #__js_res_record_values WHERE value_index = 'lng' AND field_value < " . $p_ne_lng . " AND field_value > " . $p_sw_lng . " AND record_id IN (" . implode(',', $ids) . ")";

				$db->setQuery($sql);
				$ids = $db->loadColumn();
			}

			if(empty($ids))
			{
				$this->_showRecords = FALSE;

				return $query;
			}

			$query->where("r.id IN(" . implode(',', $ids) . ")");
			$filters = FALSE;
		}
		if($view_what == 'show_parents')
		{
			$record_id = $app->input->getInt('_rrid');
			$field_id  = $app->input->getInt('_rfid');
			if($record_id && $field_id)
			{
				$sql = "SELECT field_value FROM #__js_res_record_values WHERE record_id = {$record_id} AND field_id = {$field_id}";
				$ids = $this->getIds($sql);
			}
			if(empty($ids))
			{
				$this->_showRecords = FALSE;

				return $query;
			}

			$ids[] = 0;
			$this->_set_skiper($ids);

			$query->where("r.id IN(" . implode(',', $ids) . ")");
			$filters = FALSE;
		}

		if($view_what == 'show_all_parents')
		{
			$field_id   = $app->input->getInt('_rfaid');
			$section_id = $app->input->getString('_rsid');

			if($section_id && $field_id)
			{
				$sql = "SELECT field_value FROM #__js_res_record_values
					WHERE record_id IN (SELECT id FROM #__js_res_record as r WHERE r.user_id = '{$user->id}' AND r.section_id IN ({$section_id}))
					AND field_id = {$field_id}";
				$ids = $this->getIds($sql);
			}
			if(empty($ids))
			{
				$this->_showRecords = FALSE;

				return $query;
			}

			$ids[] = 0;

			$query->where("r.id IN(" . implode(',', $ids) . ")");
			$filters = FALSE;
		}

		if($view_what == 'show_children')
		{
			$record_id = $app->input->getInt('_rrid');
			$field_id  = $app->input->getInt('_rfid');
			if($record_id && $field_id)
			{
				$sql = "SELECT record_id FROM #__js_res_record_values WHERE field_value = '{$record_id}' AND field_id = {$field_id}";
				$ids = $this->getIds($sql);
			}
			if(empty($ids))
			{
				$this->_showRecords = FALSE;

				return $query;
			}

			$ids[] = 0;
			$this->_set_skiper($ids);

			$query->where("r.id IN(" . implode(',', $ids) . ")");
			$filters = FALSE;
		}

		if($view_what == 'show_all_children')
		{
			$field_id   = $app->input->getInt('_rfaid');
			$section_id = $app->input->getString('_rsid');

			if($section_id && $field_id)
			{
				$sql = "SELECT record_id FROM #__js_res_record_values
					WHERE field_value IN (SELECT id FROM #__js_res_record as r WHERE r.user_id = '{$user->id}' AND r.section_id IN ({$section_id}))
					AND field_id = {$field_id}";
				$ids = $this->getIds($sql);
			}
			if(empty($ids))
			{
				$this->_showRecords = FALSE;

				return $query;
			}

			$ids[] = 0;

			$query->where("r.id IN(" . implode(',', $ids) . ")");
			$filters = FALSE;
		}

		if($view_what == 'show_related')
		{
			$record_id = $app->input->getInt('_rmrid');
			$field_id  = $app->input->getInt('_rmfid');
			$strict    = $app->input->getInt('_rmstrict');
			$ids       = $ids2 = array();

			if(!$record_id || !$field_id)
			{
				$this->_showRecords = FALSE;

				return $query;
			}

			$sql = "SELECT field_value FROM #__js_res_record_values WHERE record_id = '{$record_id}' AND field_id = {$field_id}";
			$ids = $this->getIds($sql);

			if($strict)
			{
				$sql  = "SELECT record_id FROM #__js_res_record_values WHERE field_value = '{$record_id}' AND field_id = {$field_id}";
				$ids2 = $this->getIds($sql);
			}

			$ids = array_merge($ids, $ids2);
			$ids = array_unique($ids);

			if(empty($ids))
			{
				$this->_showRecords = FALSE;

				return $query;
			}
			$query->where("(r.id IN(" . implode(',', $ids) . "))");
			$filters = FALSE;
		}

		if($view_what == 'compare')
		{
			$user_id = NULL;
			$filters = FALSE;
			$list    = $app->getUserState("compare.set{$this->section->id}");
			ArrayHelper::clean_r($list);
			\Joomla\Utilities\ArrayHelper::toInteger($list);
			$list[] = 0;
			$query->where('r.id IN (' . implode(',', $list) . ')');
		}

		$excludes = $app->input->getString('excludes');
		$excludes = JoomcckFilter::in($excludes);

		if($excludes)
		{
			$query->where('r.id NOT IN (' . $excludes . ')');
		}

		if($user_id)
		{

			if($this->section->params->get('personalize.personalize') && $this->section->params->get('personalize.post_anywhere'))
			{
				if($this->section->params->get('personalize.records_mode') == 1 || $isme)
				{
					// Show all records posted on user home and all records posted by this user on homes of others.
					$query->where("(r.id IN (SELECT record_id FROM `#__js_res_record_repost` WHERE host_id = {$user_id}) OR r.user_id = {$user_id})");
				}
				else
				{
					// Show only records posted on this user home
					$query->where("r.id IN (SELECT record_id FROM `#__js_res_record_repost` WHERE host_id = {$user_id})");
				}
			}
			else
			{
				$query->where('r.user_id = ' . $user_id);
			}
		}

		$type = $this->getState('records.type');
		ArrayHelper::clean_r($type, TRUE);
		if($type)
		{
			if(!$total)
			{
				settype($type, 'array');
				$typelabels = array();
				$types      = $this->getAllTypes();
				foreach($type as $t)
				{
					if(isset($types[$t]))
					{
						$typelabels[] = $types[$t]->name;
					}
				}
				$this->worns['type'] = WornHelper::getItem('filter_type', JText::_('CTYPEOF'), $type, implode(', ', $typelabels));
			}
			$query->where("r.type_id IN (" . implode(',', $type) . ")");
		}

		if($this->_filtersWhere == FALSE)
		{
			$filters = FALSE;
		}

		if($filters === FALSE)
		{
			return $query;
		}

		$search2 = $this->getState('records.search2');
		if($search2)
		{
			$scount = explode(" ", $search2);
			ArrayHelper::clean_r($scount);

			if(count($scount) == 1)
			{
				$string = "r.fieldsdata LIKE '%" . $db->escape($search2) . "%'";
				$query->where($string);
			}
			else if(count($scount) > 2)
			{
				$search_mode = ' IN NATURAL LANGUAGE MODE';
				foreach($scount as $word)
				{
					if(in_array(
						substr($word, 0, 1),
						array(
							'+',
							'-'
						))
					)
					{
						$search_mode = ' IN BOOLEAN MODE';
						break;
					}
				}

				$search2 = $db->quote($db->escape($search2));
				$query->where("MATCH (fieldsdata) AGAINST ({$search2}{$search_mode})");
				if(!$total)
				{
					$query->select("MATCH (fieldsdata) AGAINST ({$search2}{$search_mode}) AS searchresult");
				}
			}

		}

		$search = $this->getState('records.search');
		if($search)
		{
			$this->worns['search'] = WornHelper::getItem('filter_search', JText::_('CSEARCHTEXT'), $search);

			$search_mode = NULL;
			$scount      = explode(" ", $search);
			ArrayHelper::clean_r($scount);

			if($this->section->params->get('more.search_mode') == 1 || ($this->section->params->get('more.search_mode') == 3 && count($scount) == 1))
			{
				$words = explode(" ", $search);
				if(count($words) == 1)
				{
					$string = "r.fieldsdata LIKE '%" . $db->escape($words[0]) . "%'";
				}
				else
				{
					foreach($words AS $w)
					{
						$string[] = "r.fieldsdata LIKE '%" . $db->escape($w) . "%'";
					}

					$string = "(".implode(' AND ', $string).")";
				}

				$query->where($string);
				if(!$total)
				{
					//$query->select("1 AS searchresult");
				}
			}
			else
			{
				if(count($scount) > 2)
				{
					$search_mode = ' IN NATURAL LANGUAGE MODE';
				}
				elseif(count($scount) == 1)
				{
					$search_mode = ' IN BOOLEAN MODE';
				}

				foreach($scount as $word)
				{
					if(in_array(
						substr($word, 0, 1),
						array(
							'+',
							'-'
						))
					)
					{
						$search_mode = ' IN BOOLEAN MODE';
						break;
					}
				}

				$search = $db->quote($db->escape($search));
				$query->where("MATCH (fieldsdata) AGAINST ({$search}{$search_mode})");
				if(!$total)
				{
					$query->select("MATCH (fieldsdata) AGAINST ({$search}{$search_mode}) AS searchresult");
				}
			}
		}

		$alpha = $this->getState('records.alpha');
		if($alpha)
		{
			$this->worns['alpha'] = WornHelper::getItem('filter_alpha', JText::_('CSTARTWITH'), $alpha);
			$query->where("r.title like '{$alpha}%'");
		}

		$tag = $this->getState('records.tag');
		if($tag)
		{
			if(!$total)
			{
				foreach($tag as $t)
				{
					$taglabels[] = JHtml::_('tags.name', $t);
				}
				$this->worns['tags'] = WornHelper::getItem('filter_tag', JText::_('CTAGS'), $tag, implode(', ', $taglabels));
			}

			$sql = "SELECT record_id from #__js_res_tags_history WHERE tag_id IN(" . implode(',', $tag) . ") AND section_id = {$this->section->id}";
			$db->setQuery($sql);
			$ids   = $db->loadColumn();
			$ids[] = 0;
			$query->where("r.id IN(" . implode(', ', $ids) . ")");
		}

		$users = $this->getState('records.user');
		if($users)
		{
			if(!$total)
			{
				foreach($users as $usr)
				{
					$userlabels[] = CCommunityHelper::getName($usr, $this->section, array(
						'nohtml' => 1
					));
				}
				$this->worns['users'] = WornHelper::getItem('filter_user', JText::_('CBYAUTHOR'), $users, implode(', ', $userlabels));
			}
			$query->where("r.user_id IN (" . implode(',', $users) . ")");
			//$query->where("r.user_id = ".(int)$user);
		}

		$cats = $this->getState('records.category');
		if($cats)
		{
			if(!$total)
			{
				$catslabels          = JHtml::_('categories.labels', $cats);
				$this->worns['cats'] = WornHelper::getItem('filter_cat', JText::_('CCATEGORIES'), $cats, $catslabels);
			}

			$sql = "SELECT record_id FROM #__js_res_record_category WHERE catid IN(" . implode(',', $cats) . ")";
			$db->setQuery($sql);
			$ids   = $db->loadColumn();
			$ids[] = 0;
			$ids   = array_unique($ids);

			$query->where("r.id IN(" . implode(',', $ids) . ")");
		}

		return $query;
	}

	public function getIds($sql)
	{
		$app = JFactory::getApplication();
		$db  = JFactory::getDbo();
		$db->setQuery($sql);
		$ids = $db->loadColumn();
		\Joomla\Utilities\ArrayHelper::toInteger($ids);
		$ids = array_unique($ids);

		if(in_array((int)$app->getUserState('com_joomcck.skip_record'), $ids))
		{
			unset($ids[array_search((int)$app->getUserState('com_joomcck.skip_record'), $ids)]);
		}
		ArrayHelper::clean_r($ids);

		return $ids;
	}

	public function getResetFilters()
	{
		$types = $this->section->params->get('general.type');
		settype($types, 'array');
		ArrayHelper::clean_r($types);
		\Joomla\Utilities\ArrayHelper::toInteger($types);

		$query = $this->_db->getQuery(TRUE);
		$query->select('f.*');
		$query->from('#__js_res_fields f');
		$query->where('f.filter = 1');
		$query->where('f.published = 1');
		$query->where('f.type_id IN(' . implode(',', $types) . ')');
		$this->_db->setQuery($query);

		$filters = $this->_db->loadObjectList();

		return $filters;
	}

	public function getFilters()
	{
		static $cache = array();

		$types = $this->getFilterTypes();

		if(!$types)
		{
			return array();
		}

		$query = $this->_db->getQuery(TRUE);

		$query->select('f.*');
		$query->from('#__js_res_fields f');
		$query->where('f.filter = 1');
		$query->where('f.published = 1');
		$query->where('f.type_id IN(' . implode(',', $types) . ')');
		$query->select('g.ordering');
		$query->leftJoin('#__js_res_fields_group AS g ON g.id = f.group_id');

		$query->order('g.ordering ASC, f.ordering ASC');

		if(count($types) > 1)
		{
			$query->select('count(f.id) as nums');
			$query->having('nums > 1');
			$query->group('f.`key`');
		}
		$key = md5((string)$query);

		if(isset($cache[$key]))
		{
			return $cache[$key];
		}

		$this->_db->setQuery($query);
		$filters = $this->_db->loadObjectList();

		$out = array();
		foreach($filters as $filter)
		{
			require_once JPATH_ROOT . '/components/com_joomcck/fields/' . $filter->field_type . '/' . $filter->field_type . '.php';

			$default           = JFactory::getApplication()->getUserState('com_joomcck.section' . FilterHelper::key() . '.filter_' . $filter->key);
			$name              = 'JFormFieldC' . ucfirst($filter->field_type);
			$out[$filter->key] = new $name($filter, $default);
		}

		$cache[$key] = $out;

		return $cache[$key];
	}

	/**
	 * get field id to key conversion.
	 */
	public function getKeys($section)
	{
		static $out = array();

		if(isset($out[$section->id]))
		{
			return $out[$section->id];
		}

		$this->section = $section;

		$types = array_keys($this->getAllTypes());
		if(empty($types))
		{
			return array();
		}

		$query = $this->_db->getQuery(TRUE);
		$query->select('`key`, `id`');
		$query->from('#__js_res_fields');
		$query->where('type_id IN(' . implode(',', $types) . ')');

		$this->_db->setQuery($query);
		$fields = $this->_db->loadObjectList();

		$o = array();
		foreach($fields as $field)
		{
			$o[$field->id] = $field->key;
		}

		$out[$section->id] = $o;

		return $out[$section->id];
	}

	public function getAllTypes()
	{
		static $out = array();

		if(array_key_exists($this->section->id, $out))
		{
			return $out[$this->section->id];
		}

		$types = $this->section->params->get('general.type');
		settype($types, 'array');

		ArrayHelper::clean_r($types);
		\Joomla\Utilities\ArrayHelper::toInteger($types);
		if(empty($types))
		{
			JError::raiseNotice(100, JText::_('CERRNOTYPESELECTED'));
		}
		$types[] = 0;

		$query = $this->_db->getQuery(TRUE);
		$query->select('t.id, t.name, t.params');
		$query->from('#__js_res_types AS t');
		$query->where('t.id IN(' . implode(',', $types) . ')');
		$query->where('t.published = 1');

		$this->_db->setQuery($query);
		$types = $this->_db->loadObjectList();

		$out[$this->section->id] = array();
		$key                     = FilterHelper::key();
		$filter_types            = (array)JFactory::getApplication()->getUserState('com_joomcck.section' . $key . '.filter_type', array());
		foreach($types as $type)
		{
			$type->params         = new JRegistry($type->params);
			$type->filter_checked = NULL;
			if(in_array($type->id, $filter_types))
			{
				$type->filter_checked = ' checked="checked"';
			}
			$out[$this->section->id][$type->id] = $type;
		}

		return $out[$this->section->id];
	}

	/*
	 * Get number of types in current search
	 */
	public function getFilterTypes()
	{
		static $cache = array();

		if(isset($cache[$this->section->id]))
		{
			return $cache[$this->section->id];
		}

		$types = $this->section->params->get('general.type');
		settype($types, 'array');

		ArrayHelper::clean_r($types);
		\Joomla\Utilities\ArrayHelper::toInteger($types);

		if(count($types) == 1)
		{
			$cache[$this->section->id] = $types;
		}
		elseif(isset($this->worns['type']->value) && count($this->worns['type']->value) == 1)
		{
			$cache[$this->section->id] = $this->worns['type']->value;
		}
		else
		{
			$cache[$this->section->id] = $this->getCategoryTypes();
		}

		ArrayHelper::clean_r($cache[$this->section->id]);
		\Joomla\Utilities\ArrayHelper::toInteger($cache[$this->section->id]);

		$cache[$this->section->id] = array_unique($cache[$this->section->id]);

		return $cache[$this->section->id];
	}

	public function getCategoryTypes()
	{
		static $types = array();

		if(array_key_exists($this->section->id, $types))
		{
			return $types[$this->section->id];
		}

		$db = JFactory::getDbo();

		$query = $db->getQuery(TRUE);
		$query->select('DISTINCT r.type_id');
		$query->from('#__js_res_record r');
		$query = $this->where($query);
		$ids   = $this->wherecategory($query);
		if(!empty($ids))
		{
			$query->where("r.id IN(" . implode(',', $ids) . ")");
		}
		$db->setQuery($query);

		$types[$this->section->id] = $db->loadColumn();

		return $types[$this->section->id];
	}

	public function getAlphas()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(TRUE);

		$query->select('SUBSTRING(UCASE(r.title),1,1) AS letter');
		$query->from('#__js_res_record AS r');
		$query->where('r.section_id = ' . $this->getState('records.section_id'));
		$query = $this->where($query);

		$this->add_where($query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;

	}

	public function getWorns()
	{
		return $this->worns;
	}

	public function getTotal()
	{
		if($this->_navigation == FALSE)
		{
			return 0;
		}
		// Get a storage key.
		$store = $this->getStoreId('getTotal');

		// Try to load the data from internal storage.
		if(!empty($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		$this->total = 1;
		// Load the total.
		$query       = $this->_getListQuery();
		$total       = (int)$this->_getListCount($query);
		$this->total = 0;


		// Add the total to the internal cache.
		$this->cache[$store] = $total;

		return $this->cache[$store];
	}

	private function _set_skiper($ids)
	{
		$app = JFactory::getApplication();

		$skipers = $app->getUserState('skipers.all', array());
		settype($this->value, 'array');
		//var_dump($this->value);


		foreach($ids as $id)
		{
			$skipers[] = $id;
		}

		$skipers = array_unique($skipers);
		ArrayHelper::clean_r($skipers);

		$app->setUserState('skipers.all', $skipers);
	}

	protected function _getListQuery()
	{
		// Capture the last store id used.
		static $lastStoreId;

		// Compute the current store id.
		$currentStoreId = $this->getStoreId();

		// If the last store id is different from the current, refresh the query.
		if($lastStoreId != $currentStoreId || empty($this->query) || $this->total)
		{
			$lastStoreId = $currentStoreId;
			$this->query = $this->getListQuery();
		}

		return $this->query;
	}
}