<?php 
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class CUsrHelper
{

	public static function canPostIn($user_id, $section)
	{
		if(!$user_id)
		{
			return array();
		}
		
		if(!is_object($section))
		{
			$section = ItemsStore::getSection($section);
		}
		$db = JFactory::getDbo();

		$db->setQuery("SELECT * FROM `#__js_res_user_options`");
		$list = $db->loadObjectList();

		//var_dump($list);


		return array();	

	}
	public static function getFolowers($user_id, $section)
	{
		static $out = array();

		if(!isset($out[$user_id]))
		{
			$db = JFactory::getDbo();

			if($section->params->get('events.subscribe_user'))
			{
				// if section subscription is on then every section subscriber is 
				// follower but no explicitly unsubscribed
				if($section->params->get('events.subscribe_section'))
				{
					$db->setQuery("SELECT id, username, name 
						FROM `#__users` 
						WHERE (
							id IN (SELECT user_id 
								FROM `#__js_res_subscribe` 
								WHERE `type` = 'section' 
								AND section_id = {$section->id}
							)
							AND id NOT IN (
								SELECT user_id 
								FROM `#__js_res_subscribe_user` 
								WHERE u_id = {$user_id} 
								AND section_id = {$section->id} 
								AND exclude = 1
							)
						)
						OR id IN (SELECT user_id 
							FROM `#__js_res_subscribe_user` 
							WHERE u_id = {$user_id} 
							AND section_id = {$section->id} 
							AND exclude = 0
						)
					");

				}

				// And if there is not section subscription easy. Just those who subscribed.
				else
				{
					$db->setQuery("SELECT id, username, name 
						FROM `#__users` 
						WHERE id IN (SELECT user_id 
							FROM `#__js_res_subscribe_user` 
							WHERE u_id = {$user_id} 
							AND section_id = {$section->id} 
							AND exclude = 0
						)
					");

				}
				$out[$user_id] = $db->loadObjectList('id');
			}

			// if section subscription is on then every section subscriber is follower
			elseif($section->params->get('events.subscribe_section'))
			{
				$db->setQuery("SELECT user_id 
					FROM `#__js_res_subscribe` 
					WHERE `type` = 'section' 
					AND section_id = {$section->id}");

				$out[$user_id] = $db->loadObjectList('user_id');
			}
			else
			{
				$out[$user_id] = array();
			}
		}

		return $out[$user_id];

	}
	
	/**
	 *
	 * @return JRegistry
	 */
	public static function getOptions($user = null)
	{
		static $out = array();

		if(! $user)
		{
			$uid = JFactory::getUser()->get('id');
		}
		else
		{
			$uid = $user->get('id');
		}
		if(!isset($out[$uid]))
		{
			$table = JTable::getInstance('Useropt', 'JoomcckTable');
			$table->load(array(
				'user_id' => $uid
			));

			if(!$table->params) $table->params = '[]';

			$out[$uid] = new JRegistry($table->params);
		}

		return $out[$uid];
	}

	public static function getOption($user, $key, $default = NULL)
	{
		$params = self::getOptions($user);

		return $params->get($key, $default);
	}

	public static function is_follower($user_id, $who, $section)
	{
		static $data = array();

		if(! isset($data[$user_id]))
		{
			$data[$user_id] =  array();
			$users = self::getFolowers($user_id, $section);
			foreach ($users as $u) 
			{
				$data[$user_id][] = $u->id;				
			}
		}

		if(in_array($who, $data[$user_id]))
		{
			return TRUE;
		}

		return FALSE;
	}

}