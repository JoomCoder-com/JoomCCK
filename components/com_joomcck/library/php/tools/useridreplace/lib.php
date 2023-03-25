<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');

class METoolSetUserHelper
{
	public static function execute($params)
	{
		$db = JFactory::getDBO();

		$from = (int)$params->get('user_from');

		if(!$from)
		{

			Factory::getApplication()->enqueueMessage('From what user ID? Please set.','warning');

			return;
		}

		$action = $params->get('action');
		$to     = (int)$params->get('user_to');

		if($action == 2 && !$to)
		{
			Factory::getApplication()->enqueueMessage('To what user ID? Please set.','warning');

			return;
		}

		if($action == 1)
		{
			self::cleanFiles($from);
		}

		self::cleanTable($action, $from, $to, 'emerald_subscriptions');
		self::cleanTable($action, $from, $to, 'emerald_coupons_history');
		self::cleanTable($action, $from, $to, 'emerald_history');
		self::cleanTable($action, $from, $to, 'emerald_exp_alerts');
		self::cleanTable($action, $from, $to, 'emerald_url_history');

		self::cleanTable($action, $from, $to, 'js_res_category_user');
		self::cleanTable($action, $from, $to, 'js_res_hits');
		self::cleanTable($action, $from, $to, 'js_res_favorite');
		self::cleanTable($action, $from, $to, 'js_res_comments');
		self::cleanTable($action, $from, $to, 'js_res_files');
		self::cleanTable($action, $from, $to, 'js_res_polls');
		self::cleanTable($action, $from, $to, 'js_res_record');
		self::cleanTable($action, $from, $to, 'js_res_record_values');
		self::cleanTable($action, $from, $to, 'js_res_record_repost', 'host_id');
		self::cleanTable($action, $from, $to, 'js_res_sales', array('saler_id', 'user_id'));
		self::cleanTable($action, $from, $to, 'js_res_tags_history');
		self::cleanTable($action, $from, $to, 'js_res_vote');
		self::cleanTable($action, $from, $to, 'js_res_moderators');
		self::cleanTable($action, $from, $to, 'js_res_user_options');
		self::cleanTable($action, $from, $to, 'js_res_user_options_autofollow');
		self::cleanTable($action, $from, $to, 'js_res_user_post_map');
		self::cleanTable($action, $from, $to, 'js_res_subscribe');
		self::cleanTable($action, $from, $to, 'js_res_subscribe_cat');
		self::cleanTable($action, $from, $to, 'js_res_subscribe_user', array('user_id', 'u_id'));

		if($params->get('user_delete'))
		{
			$sql = "DELETE FROM #__users WHERE id = {$from}";
			$db->setQuery($sql);
			$db->execute();
		}
		JFactory::getApplication()->enqueueMessage(JText::_('Successfully'));
	}

	public static function cleanFiles($from)
	{
		$db = JFactory::getDBO();

		$sql = "SELECT * FROM #__js_res_files WHERE user_id = " . $from;
		$db->setQuery($sql);
		$files = $db->loadObjectList();

		foreach($files AS $file)
		{
			$subfolder = self::_getSubfolder($file->field_id);
			$joomcck_params = JComponentHelper::getParams('com_joomcck');

			$filetodel = JPATH_ROOT . DIRECTORY_SEPARATOR . $joomcck_params->get('general_upload') . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR . $files->fullpath;
			if(JFile::exists($filetodel))
			{
				JFile::delete($filetodel);
			}
		}
	}

	public static function cleanTable($action, $from, $to, $table, $tag = 'user_id')
	{

		if(!self::isTableExists($table)) return;
		if($action == 0) return;

		$db = JFactory::getDBO();

		if($action == 1)
		{
			return;
		}

		$db = JFactory::getDBO();

		settype($tag, 'array');
		foreach($tag AS $t)
		{
			if($action == 1)
			{
				$sql = "DELETE FROM #__{$table} WHERE {$t} = {$from}";
			}
			else
			{
				$sql = "UPDATE #__{$table} SET {$t} = {$to} WHERE {$t} = {$from}";
			}

			$db->setQuery($sql);
			$db->execute();
		}
	}

	public static function isTableExists($table_name)
	{
		$db  = JFactory::getDBO();
		$sql = "SHOW TABLES LIKE '%{$table_name}'";

		$db->setQuery($sql);
		$table = $db->loadResult();

		return $table;
	}

	private static function _getSubfolder($id)
	{
		static $params = array();
		static $defaults =array();

		if(!isset($params[$id]))
		{
			$db = JFactory::getDbo();
			$sql = "SELECT params, field_type FROM #__js_res_fields WHERE id = ".$id;
			$db->setQuery($sql);
			$result = $db->loadObject();
			$params[$id] = new JRegistry($result->params);
			$defaults[$id] = $result->field_type;
		}

		return $params[$id]->get('params.subfolder', $defaults[$id]);
	}
}