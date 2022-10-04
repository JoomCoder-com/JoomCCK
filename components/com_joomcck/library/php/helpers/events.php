<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class CEventsHelper
{

	const _RECORD_NEW = 'record_new';
	const _RECORD_VIEW = 'record_view';
	const _RECORD_EXPIRED = 'record_expired';
	const _RECORD_FEATURED_EXPIRED = 'record_featured_expired';
	const _RECORD_TAGGED = 'record_tagged';
	const _RECORD_BOOKMARKED = 'record_bookmarked';
	const _RECORD_RATED = 'record_rated';
	const _RECORD_APPROVED = 'record_approved';
	const _RECORD_WAIT_APPROVE = 'record_wait_approve';
	const _RECORD_UNPUBLISHED = 'record_unpublished';
	const _RECORD_FEATURED = 'record_featured';
	const _RECORD_EXTENDED = 'record_extended';
	const _RECORD_DELETED = 'record_deleted';
	const _RECORD_EDITED = 'record_edited';
	const _RECORD_REPOSTED = 'record_reposted';
	const _RECORD_POSTED = 'record_posted';
	//const _RECORD_IMPORTED = 'record_imported';
	//const _RECORD_IMPORT_UPDATE = 'record_import_update';

	const _COMMENT_NEW = 'comment_new';
	const _COMMENT_RATED = 'comment_rated';
	const _COMMENT_DELETED = 'comment_deleted';
	const _COMMENT_APPROVED = 'comment_approved';
	const _COMMENT_REPLY = 'comment_reply';
	const _COMMENT_UNPUBLISHED = 'comment_unpublished';
	const _COMMENT_EDITED = 'comment_edited';

	const _FIELDS_STATUS_CHANGED = 'status_changed';
	const _FIELDS_PARENT_NEW = 'parent_new';
	const _FIELDS_CHILD_NEW = 'child_new';
	const _FIELDS_PARENT_ATTACHED = 'parent_attached';
	const _FIELDS_CHILD_ATTACHED = 'child_attached';
	const _FIELDS_PAY_STATUS_CHANGE = 'order_updated';
	const _FIELDS_PAY_NEW_SALE = 'new_sale';
	const _FIELDS_PAY_NEW_SALE_MANUAL = 'new_sale_manual';

	/*
	 * Add notifications to all subscribed users
	 *
	 * @param string $type
	 *        	record|category
	 * @param string $event
	 *        	event type. Use uniquie string.
	 * @param int $record_id
	 * @param int $section_id
	 * @param int $cat_id
	 * @param array $params
	 *        	additional data to be saved as JSON. Later passed to callback
	 *        	function and you can manage what to show.
	 * @param int $who
	 *        	Who can get notifyed as access level ID
	 */
	static public function notify($type, $event, $record_id, $section_id, $cat_id, $comment_id, $field_id, $params, $who = 2, $only_user = NULL)
	{
		$user_opt = CUsrHelper::getOptions();
		$section  = ItemsStore::getSection($section_id);
		$user     = JFactory::getUser();

		if($record_id)
		{
			$record = JTable::getInstance('Record', 'JoomcckTable');
			$record->load($record_id);

			$icats = json_decode($record->categories, TRUE);
			if(!$cat_id && $icats)
			{
				$array_keys = array_keys($icats);
				$cat_id     = array_shift($array_keys);
			}
		}

		if(is_object($params))
		{
			$params = get_object_vars($params);
			foreach($params AS $key => $p)
			{
				if(is_object($p) || is_array($p))
				{
					unset($params[$key]);
				}
			}
		}


		settype($params, 'array');

		unset($params['access_key'], $params['published'], $params['params'], $params['access'], $params['checked_out'], $params['checked_out_time'],
			$params['hits'], $params['ordering'], $params['meta_descr'], $params['meta_index'], $params['meta_key'],
			$params['alias'], $params['featured'], $params['archive'], $params['ucatid'], $params['ucatname'], $params['langs'],
			$params['asset_id'], $params['votes'], $params['favorite_num'], $params['hidden'], $params['votes_result'], $params['exalert'],
			$params['fieldsdata'], $params['fields'], $params['comments'], $params['tags'], $params['multirating'],
			$params['subscriptions_num'], $params['parent_id'], $params['parent'], $params['whorepost'], $params['repostedby'],
			$params['fields_by_id'], $params['fields_by_key'], $params['fields_by_groups'], $params['field_groups'], $params['ucatname_link'],
			$params['expired'], $params['nofollow'], $params['bookmarked'], $params['subscribed'], $params['rating'], $params['controls'], $params['controls_notitle']);

		$actor             = (in_array($event, array(self::_RECORD_NEW)) && isset($record->user_id) ? $record->user_id : $user->id);
		$params['section'] = $section;
		$params['by']      = $actor;
		$params['on']      = time();

		$array = array(
			'type'       => $event,
			'params'     => json_encode($params),
			'record_id'  => $record_id,
			'section_id' => $section_id,
			'cat_id'     => $cat_id,
			'comment_id' => $comment_id,
			'field_id'   => $field_id,
			'eventer'    => $actor
		);

		$user_id = @$params['user_id'];
		if(!empty($record->user_id))
		{
			$user_id = $record->user_id;
		}
		if($only_user)
		{
			$user_id = $only_user;
		}

		if($user->id && in_array($section->params->get('events.event.' . $event . '.activ'), JFactory::getUser()->getAuthorisedViewLevels()))
		{
			$array['new_vote'] = @$params['new_vote'];
			$array['status']   = @$params['status'];
			CCommunityHelper::avtivity($actor, $user_id, $array, $record);
		}
		CCommunityHelper::karma($actor, $user_id, $array, $record);

		if($type == 'record')
		{
			if(!$section->params->get('events.subscribe_' . $type, 1))
			{
				return;
			}
		}
		else
		{
			if(!$section->params->get('events.subscribe_section', 1)
				&& !$section->params->get('events.subscribe_category', 1)
				&& !$section->params->get('events.subscribe_user', 1)
			)
			{
				return;
			}
		}

		if($section->params->get('events.event.' . $event . '.notif', $who) == 0)
		{
			return;
		}

		if($only_user !== NULL)
		{
			if(empty($only_user))
			{
				return;
			}
			$list[] = $only_user;
		}
		else
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(TRUE);
			$query->select('user_id');
			$query->from('#__js_res_subscribe');

			if($type == 'category')
			{
				if(!empty($section_id))
				{
					$or['sec'] = "`type` = 'section' AND ref_id = {$section_id}";
				}

				if(!empty($record->user_id) && $section->params->get('events.subscribe_user') && $section->params->get('personalize.personalize'))
				{
					unset($or['sec']);
					// Select those users who follow author of this record
					$or['user'] = "user_id IN(SELECT user_id FROM #__js_res_subscribe_user WHERE u_id = {$record->user_id} AND section_id = {$section_id} AND `exclude` = 0)";
					// But do not notify those who not wollowing this user
					$query->where("user_id NOT IN(SELECT user_id FROM #__js_res_subscribe_user WHERE u_id = {$record->user_id} AND section_id = {$section_id} AND `exclude` = 1)");
				}

				if(!empty($cat_id) && $section->params->get('events.subscribe_category'))
				{
					$or['cat'] = "user_id IN(SELECT user_id FROM #__js_res_subscribe_cat WHERE cat_id = {$cat_id} AND section_id = {$section_id} AND `exclude` = 0)";
					$query->where("user_id NOT IN(SELECT user_id FROM #__js_res_subscribe_cat WHERE cat_id = {$cat_id} AND section_id = {$section_id} AND `exclude` = 1)");
				}
				$query->where("((" . implode(") OR (", $or) . "))");
			}
			else
			{
				$query->where("`type` = 'record' AND ref_id = {$record_id}");
			}

			$query->where('`user_id` != ' . $user->get('id'));

			$query->where('`user_id` NOT IN (SELECT user_id FROM #__js_res_notifications WHERE notified = 0 AND type= "' . $event . '" AND ref_1=' . $record_id . ' AND eventer = ' . $user->id . ' )');


			$db->setQuery($query);
			$list = $db->loadColumn();

			//JError::raiseWarning(100, $query);
		}

		$list = array_unique($list);
		ArrayHelper::clean_r($list);
		\Joomla\Utilities\ArrayHelper::toInteger($list);

		if(!$list)
		{
			return;
		}

		foreach($list as $uid)
		{
			$senduser = JFactory::getUser($uid);
			if(!in_array($section->params->get('events.event.' . $event . '.notif', $who), $senduser->getAuthorisedViewLevels()) && !($record->user_id && $senduser->get('id') == $record->user_id))
			{
				continue;
			}

			if(self::_RECORD_NEW == $event && !empty($record->access) && !in_array($record->access, $senduser->getAuthorisedViewLevels()))
			{
				continue;
			}

			if(!CUsrHelper::getOptions($senduser)->get('notification.' . $section->id . '.' . $event, ($event == 'record_view' ? 0 : 1)))
			{
				continue;
			}

			CCommunityHelper::notify($uid, $array);
		}
	}

	static public function notification($event, $filter)
	{
		$record  = ItemsStore::getRecord($event->ref_1);
		$section = ItemsStore::getSection($event->ref_2);
		$type    = ItemsStore::getType($record->type_id);
		$params  = json_decode($event->params);

		$event_author = CCommunityHelper::getName($event->eventer, $section);

		$msg = JText::_($section->params->get('events.event.' . $event->type . '.msg_pers', 'EVENT_' . strtoupper($event->type) . '_PERS'));

		$filter_pattern = ' <a onclick="Joomcck.setAndSubmit(\'filter_search\', \'%s:%s\')" href="javascript:void(0);"
			rel="tooltip" data-original-title="%s"><img align="absmiddle" src="' . JURI::root(TRUE) . '/media/mint/icons/16/funnel-small.png" /></a>';
		$record->link   = Url::record($record, $type, $section);
		$record_link    = JHtml::link(JRoute::_($record->link), $record->title);

		$section_link = JHtml::link(JRoute::_(Url::records($section)), $section->name);

		if($filter)
		{
			$record_link .= sprintf($filter_pattern, 'rid', $record->id, JText::_('CFILTERTIPRECORD'));
			$event_author .= sprintf($filter_pattern, 'uid', $event->eventer, JText::_('CFILTERTIPUSER'));
			$section_link .= ' <a onclick="Joomcck.setAndSubmit(\'section_id\', \'' . $section->id . '\');" href="javascript:void(0);"
			rel="tooltip" data-original-title="' . JText::_('CFILTERTIPSECTION') . '"><img align="absmiddle" src="' . JURI::root(TRUE) . '/media/mint/icons/16/funnel-small.png" /></a>';
		}

		$msg = str_replace(
			array(
				'[TYPE]',
				'[TITLE]',
				'[RECORD]',
				'[SECTION]',
				'[USER]',
				'[AUTHOR]',
				'[BY]',
				'[RATE]'

			),
			array(
				'<b>' . $type->name . '</b>',
				$record->title,
				$record_link,
				$section_link,
				(CCommunityHelper::getName($event->eventer, $section)),
				($record->user_id ? CCommunityHelper::getName($record->user_id, $section) : NULL),
				($record->user_id ? CCommunityHelper::getName($record->user_id, $section) : NULL),
				@$params->new_vote
			),
			$msg);

		$fields     = json_decode($record->fields, TRUE);
		$field_vals = new JRegistry($fields);

		settype($fields, 'array');

		foreach($fields as $id => $value)
		{
			if(strpos($msg, "[{$id}]") !== FALSE)
			{
				if(is_array($value))
				{
					$value = implode(',', $value);
				}
				$msg = str_replace("[{$id}]", $value, $msg);
			}


			if(preg_match_all("/\[{$id}::(.*)\]/iU", $msg, $matches))
			{
				foreach($matches[0] AS $key => $match)
				{
					$path = $id . "." . str_replace('::', '.', $matches[1][$key]);
					if($field_vals->get($path))
					{
						$msg = str_replace($match, $field_vals->get($path), $msg);
					}
					$msg = str_replace($match, '', $msg);
				}
			}
		}

		$categories = json_decode($record->categories, TRUE);
		$msg = str_replace("[CAT]", implode(', ', $categories), $msg);

		return $msg;
	}

	static public function get_notification($item, $filter = TRUE)
	{

		$html = self::notification($item, $filter);

		if($item->ref_5)
		{
			$db = JFactory::getDbo();
			include_once JPATH_ROOT . '/components/com_joomcck/tables/field.php';
			$field = new JoomcckTableField($db);
			$field->load($item->ref_5);
			$field->params = new JRegistry($field->params);

			include_once JPATH_ROOT . '/components/com_joomcck/fields' . DIRECTORY_SEPARATOR . $field->field_type . DIRECTORY_SEPARATOR . $field->field_type . '.php';

			$class_name = 'JFormFieldC' . ucfirst($field->field_type);
			$class      = new $class_name($field, '');
			$html       = $class->onNotification($html, $item);
		}
		
		$html = preg_replace('/\[[^\]]*\]/iU', '', $html);

		return $html;
	}

	static public function cleanRecord($id)
	{
		$db = Jfactory::getDbo();
		$db->setQuery("DELETE FROM #__js_res_notifications WHERE ref_1 = " . $id . ' AND user_id = ' . JFactory::getUser()->get('id') . ' AND ref_2 = ' . JFactory::getApplication()->input->getInt('section_id'));
		$db->execute();
	}

	static public function markReadRecord($record)
	{
		$db = Jfactory::getDbo();

		$db->setQuery("UPDATE #__js_res_notifications SET state_new = 0, notified = 1 WHERE ref_1 = " . $record->id . ' AND user_id = ' . JFactory::getUser()->get('id') . ' AND ref_2 = ' . $record->section_id);
		$db->execute();
	}

	static public function getNum($type, $id = 0, $key = 'num')
	{
		$user = JFactory::getUser();
		if(!$user->get('id'))
		{
			return;
		}

		static $events = NULL;

		if($events === NULL)
		{
			$events = array();

			$db  = JFactory::getDbo();
			$sql = "SELECT `ref_1`, `ref_2`, `ref_3`, `ref_4`, `ref_5`, `type`, `eventer`, `ctime`  FROM #__js_res_notifications WHERE state_new = 1 AND user_id = " . $user->get('id');
			$db->setQuery($sql);
			$list = $db->loadObjectList();

			if(!empty($list))
			{
				foreach($list as $event)
				{
					$section = ItemsStore::getSection($event->ref_2);
					$text    = str_replace(
						array(
							'[BY]',
							'[ON]'
						),
						array(
							CCommunityHelper::getName($event->eventer, NULL, array('nohtml' => 1)),
							JHtml::_('date', $event->ctime, 'd F Y')
						),
						JText::_($section->params->get('events.event.' . $event->type . '.msg', 'event_' . $event->type))
					);
					@$events['total'][0]['num']++;
					@$events['record'][$event->ref_1]['num']++;
					@$events['record'][$event->ref_1]['text'][] = $text;

					@$events['section'][$event->ref_2]['num']++;
					@$events['category'][$event->ref_3]['num']++;
					@$events['field'][$event->ref_5]['num']++;

					@$events['comment'][$event->ref_4]['num']++;
					@$events['comment'][$event->ref_4]['text'][$text] = $text;
				}
			}
		}
		$out = 0;

		if(!empty($events[$type][$id][$key]))
		{
			$out = $events[$type][$id][$key];
		}

		return $out;
	}

	static public function showNum($type, $id = 0, $url = FALSE)
	{
		$num = self::getNum($type, $id);
		if(!$num)
		{
			return;
		}

		$text = self::getNum($type, $id, 'text');
		$attr = '';

		if(is_array($text))
		{
			$attr = sprintf(' rel="tooltipright" data-original-title="%s"', htmlspecialchars(implode('<br>', $text), ENT_QUOTES, 'UTF-8'));
		}

		$out = '<span class="badgebg-darkbg-info"' . $attr . '>' . $num . '</span>';

		if($url)
		{
			$out = '<a href="' . JRoute::_(Url::user('events')) . '">' . $out . '</a>';
		}

		return $out;
	}

	static public function getEventsList()
	{
		$events                            = array();
		$events['record_new']              = JText::_('EVENT_TYPE_RECORD_NEW');
		$events['record_view']             = JText::_('EVENT_TYPE_RECORD_VIEW');
		$events['record_expired']          = JText::_('EVENT_TYPE_RECORD_EXPIRED');
		$events['record_featured_expired'] = JText::_('EVENT_TYPE_RECORD_FEATURED_EXPIRED');
		$events['record_tagged']           = JText::_('EVENT_TYPE_RECORD_TAGGED');
		$events['record_bookmarked']       = JText::_('EVENT_TYPE_RECORD_BOOKMARKED');
		$events['record_rated']            = JText::_('EVENT_TYPE_RECORD_RATED');
		$events['record_wait_approve']     = JText::_('EVENT_TYPE_RECORD_WAIT_APPROVE');
		$events['record_approved']         = JText::_('EVENT_TYPE_RECORD_APPROVED');
		$events['record_unpublished']      = JText::_('EVENT_TYPE_RECORD_UNPUBLISHED');
		$events['record_featured']         = JText::_('EVENT_TYPE_RECORD_FEATURED');
		$events['record_extended']         = JText::_('EVENT_TYPE_RECORD_EXTENDED');
		$events['record_deleted']          = JText::_('EVENT_TYPE_RECORD_DELETED');
		$events['record_edited']           = JText::_('EVENT_TYPE_RECORD_EDITED');
		//$events['record_imported']           = JText::_('EVENT_TYPE_RECORD_IMPORTED');
		//$events['record_import_update']           = JText::_('EVENT_TYPE_RECORD_IMPORT_UPDATE');

		$events['comment_new']         = JText::_('EVENT_TYPE_COMMENT_NEW');
		$events['comment_rated']       = JText::_('EVENT_TYPE_COMMENT_RATED');
		$events['comment_deleted']     = JText::_('EVENT_TYPE_COMMENT_DELETED');
		$events['comment_approved']    = JText::_('EVENT_TYPE_COMMENT_APPROVED');
		$events['comment_reply']       = JText::_('EVENT_TYPE_COMMENT_REPLY');
		$events['comment_unpublished'] = JText::_('EVENT_TYPE_COMMENT_UNPUBLISHED');
		$events['comment_edited']      = JText::_('EVENT_TYPE_COMMENT_EDITED');

		$events['status_changed']  = JText::_('EVENT_TYPE_STATUS_CHANGED');
		$events['parent_new']      = JText::_('EVENT_TYPE_PARENT_NEW');
		$events['child_new']       = JText::_('EVENT_TYPE_CHILD_NEW');
		$events['parent_attached'] = JText::_('EVENT_TYPE_PARENT_ATTACHED');
		$events['child_attached']  = JText::_('EVENT_TYPE_CHILD_ATTACHED');
		$events['order_updated']   = JText::_('EVENT_TYPE_ORDER_UPDATED');
		$events['new_sale']        = JText::_('EVENT_TYPE_NEW_SALE');
		$events['new_sale_manual'] = JText::_('EVENT_TYPE_NEW_SALE_MANUAL');

		return $events;
	}

	static public function _events_list()
	{
		return array(
			'record_new'              => array(1, 0),
			'record_view'             => array(1, 1),
			'record_wait_approve'     => array(0, 0),
			'record_approved'         => array(1, 1),
			'record_edited'           => array(1, 1),
			'record_deleted'          => array(1, 1),
			'record_rated'            => array(1, 1),
			'record_expired'          => array(0, 1),
			'record_featured_expired' => array(0, 1),
			'record_bookmarked'       => array(1, 1),
			'record_tagged'           => array(1, 1),
			'record_unpublished'      => array(1, 1),
			'record_featured'         => array(1, 1),
			'record_extended'         => array(1, 1),
			'record_reposted'         => array(1, 1),
			'record_posted'           => array(1, 0),
			//'record_imported'           => array(1, 0),
			//'record_import_update'           => array(1, 0),
			'comment_new'             => array(1, 1),
			'comment_edited'          => array(1, 1),
			'comment_rated'           => array(1, 1),
			'comment_deleted'         => array(1, 1),
			'comment_approved'        => array(1, 1),
			'comment_reply'           => array(1, 1),
			'comment_unpublished'     => array(1, 1),
			'status_changed'          => array(1, 1),
			'parent_new'              => array(1, 1),
			'child_new'               => array(1, 1),
			'parent_attached'         => array(1, 1),
			'child_attached'          => array(1, 1),
			'order_updated'           => array(1, 1),
			'new_sale'                => array(1, 1),
			'new_sale_manual'         => array(1, 1)
		);
	}
}