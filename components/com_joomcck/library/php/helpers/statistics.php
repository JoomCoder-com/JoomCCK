<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
class CStatistics
{
	public static $records_num = array();
	public static $authors_num = array();
	public static $comments_num = array();
	public static $members_num = array();
	public static $views_num = array();

	/**
	 * How many records in the section
	 * @param int $section_id
	 * @return int:
	 */
	static public function records_num($section_id)
	{
		if (!isset(self::$records_num[$section_id]))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select("COUNT(id)");
			$query->from("#__js_res_record");
			$query->where("section_id = '$section_id'");
			if(CStatistics::hasUnPublished($section_id))
			{
				$query->where("published = '1'");
			}
			$query->where("ctime < NOW()");
			$query->where("(extime > NOW() OR extime = '0000-00-00 00:00' OR ISNULL(extime))");
			$db->setQuery($query);
			self::$records_num[$section_id] = $db->loadResult();
		}
		return self::$records_num[$section_id];
	}

	/**
	 * How many commentc in the section
	 * @param int $section_id
	 * @return int:
	 */
	static public function comments_num($section_id)
	{
		if (!isset(self::$comments_num[$section_id]))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select("COUNT(id)");
			$query->from("#__js_res_comments");
			$query->where("section_id = '$section_id'");
			$query->where("published = '1'");
			$db->setQuery($query);
			self::$comments_num[$section_id] = $db->loadResult();
		}
		return self::$comments_num[$section_id];
	}

	/**
	 * How many authors in the section
	 * @param int $section_id
	 * @return int:
	 */
	static public function authors_num($section_id)
	{
		if (!isset(self::$authors_num[$section_id]))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select("user_id");
			$query->from("#__js_res_record");
			$query->where("section_id = '$section_id'");
			if(CStatistics::hasUnPublished($section_id))
			{
				$query->where("published = '1'");
			}
			$query->where("ctime < NOW()");
			$query->where("(extime > NOW() OR extime = '0000-00-00 00:00' OR ISNULL(extime))");
			$db->setQuery($query);

			$ids = $db->loadColumn();
			$ids = array_unique($ids);

			self::$authors_num[$section_id] = count($ids);
		}
		return self::$authors_num[$section_id];
	}

	/**
	 * How many users in this section including authors, subscibes, readers.
	 * @param int $section_id
	 * @param \Joomla\Registry\Registry $params
	 * @return int:
	 */
	static public function members_num($section_id, $params)
	{
		if (!isset(self::$members_num[$section_id]))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select("DISTINCT user_id");
			$query->from("#__js_res_subscribe");
			$query->where("section_id = '$section_id'");
			$db->setQuery($query);
			$subscr = $db->loadColumn();

			$query = $db->getQuery(true);
			$query->select("DISTINCT user_id");
			$query->from("#__js_res_record");
			$query->where("section_id = '$section_id'");
			if(CStatistics::hasUnPublished($section_id))
			{
				$query->where("published = '1'");
			}
			$query->where("ctime < NOW()");
			$query->where("(extime > NOW() OR extime = '0000-00-00 00:00' OR ISNULL(extime) )");
			$db->setQuery($query);
			$authors = $db->loadColumn();

			$query = $db->getQuery(true);
			$query->select("DISTINCT user_id");
			$query->from("#__js_res_hits");
			$query->where("section_id = '$section_id'");
			$query->where("user_id != 0");
			$db->setQuery($query);
			$readers = $db->loadColumn();

			$ip = 0;
			if ($params->get('use_anonim', 1))
			{
				$sql = "SELECT COUNT(DISTINCT ip) FROM #__js_res_hits WHERE user_id = '0' AND section_id = '$section_id'";
				$db->setQuery($sql);
				$id = $db->loadResult();
			}

			settype($subscr, 'array');
			settype($authors, 'array');
			settype($readers, 'array');

			$members = array_merge($subscr, $authors, $readers);
			$members = count(array_unique($members)) + (int)$ip;

			self::$members_num[$section_id] = $members;
		}
		return self::$members_num[$section_id];
	}

	/**
	 * How many total views/hits in the section
	 * @param int $section_id
	 * @return int:
	 */
	static public function views_num($section_id)
	{
		if (!isset(self::$views_num[$section_id]))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select("COUNT(id)");
			$query->from("#__js_res_hits");
			$query->where("section_id = '$section_id'");
			$db->setQuery($query);
			self::$views_num[$section_id] = $db->loadResult();
		}
		return self::$views_num[$section_id];
	}

	public static $created = array();
	public static $expired = array();
	public static $hidden = array();
	public static $featured = array();
	public static $rated = array();
	public static $unpublished = array();
	public static $categories = array();
	public static $comments_left = array();
	public static $commented = array();
	public static $readers = array();
	public static $visited = array();
	public static $rating_average = array();
	public static $whofollow = array();
	public static $followed = array();
	public static $whofavorited = array();
	public static $favorited = array();
	public static $user_records = array();

	/**
	 * How many records this user created in the section
	 * @param int $user_id
	 * @param int $section_id
	 * @return int:
	 */
	static public function created($user_id, $section_id)
	{
		if (!isset(self::$created["$section_id-$user_id"]))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select("COUNT(id)");
			$query->from("#__js_res_record");
			$query->where("section_id = '$section_id'");
			if(CStatistics::hasUnPublished($section_id))
			{
				$query->where("published = '1'");
			}
			$query->where("user_id = '$user_id'");
			$query->where('hidden = 0');
			$query->where('archive = 0');
			self::_data_show($query, $section_id);
			//	$query->where("ctime < NOW()");
			//	$query->where("(extime > NOW() OR extime = '0000-00-00 00:00')");
			$db->setQuery($query);
			//echo $query;
			self::$created["$section_id-$user_id"] = $db->loadResult();
		}
		return self::$created["$section_id-$user_id"];
	}

	/**
	 * how many records of current user have expired in the section
	 * @param int $user_id
	 * @param int $section_id
	 * @return int:
	 */
	static public function expired($user_id, $section_id)
	{
		if (!isset(self::$expired["$section_id-$user_id"]))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(TRUE);
			$query->select('count(*)');
			$query->from('#__js_res_record');
			$query->where('user_id = ' . $user_id);
			if(CStatistics::hasUnPublished($section_id))
			{
				$query->where('published = 1');
			}
			$query->where('hidden = 0');
			$query->where('section_id = ' . $section_id);
			$query->where('(extime != ' . $db->quote('0000-00-00 00:00:00') . ' AND extime < \'' . \Joomla\CMS\Factory::getDate()->toSql() . '\')');
			$query->where('ctime < NOW()');
			$db->setQuery($query);
			self::$expired["$section_id-$user_id"] = $db->loadResult();
		}
		return self::$expired["$section_id-$user_id"];
	}

	/**
	 * How many hidden records of the currect user in the section
	 * @param int $user_id
	 * @param int $section_id
	 * @return int:
	 */
	static public function hidden($user_id, $section_id)
	{
		if (!isset(self::$hidden["$section_id-$user_id"]))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(TRUE);
			$query->select('count(*)');
			$query->from('#__js_res_record');
			$query->where('user_id = ' . $user_id);
			$query->where('section_id = ' . $section_id);
			if(CStatistics::hasUnPublished($section_id))
			{
				$query->where('published = 1');
			}
			$query->where('hidden = 1');
			$query->where('archive = 0');
			//self::_data_show($query, $section_id);
			$db->setQuery($query);
			self::$hidden["$section_id-$user_id"] = $db->loadResult();
		}
		return self::$hidden["$section_id-$user_id"];
	}

	/**
	 * How many feaured records of the current user  in the section
	 * @param int $user_id
	 * @param int $section_id
	 * @return int:
	 */
	static public function featured($user_id, $section_id)
	{
		if (!isset(self::$featured["$section_id-$user_id"]))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(TRUE);
			$query->select('count(*)');
			$query->from('#__js_res_record');
			$query->where('user_id = ' . $user_id);
			$query->where('section_id = ' . $section_id);
			if(CStatistics::hasUnPublished($section_id))
			{
				$query->where('published = 1');
			}
			$query->where('hidden = 0');
			$query->where('archive = 0');
			self::_data_show($query, $section_id);
			$query->where('featured = 1 AND ftime > NOW()');
			$db->setQuery($query);
			self::$featured["$section_id-$user_id"] = $db->loadResult();
		}
		return self::$featured["$section_id-$user_id"];
	}

	/**
	 * How many records this user have rated  in the section
	 * @param int $user_id
	 * @param int $section_id
	 * @return int:
	 */
	static public function rated($user_id, $section_id)
	{
		if (!isset(self::$rated["$section_id-$user_id"]))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select("ref_id");
			$query->from("#__js_res_vote");
			$query->where("section_id = '$section_id'");
			$query->where("user_id = $user_id");
			$query->where("ref_type = 'record'");
			$db->setQuery($query);
			$ids = $db->loadColumn();

			if ($ids)
			{
				$ids = self::_get_records_count($ids, $section_id);
			}
			self::$rated["$section_id-$user_id"] = (int)$ids;
		}
		return self::$rated["$section_id-$user_id"];
	}

	/**
	 * How many unpublished records of current user in the section
	 * @param int $user_id
	 * @param int $section_id
	 * @return int:
	 */
	static public function unpublished($user_id, $section_id)
	{
		if(!CStatistics::hasUnPublished($section_id)) {
			return self::$unpublished["$section_id-$user_id"] = 0;
		}

		if (!isset(self::$unpublished["$section_id-$user_id"]))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(TRUE);
			$query->select('count(*)');
			$query->from('#__js_res_record');
			$query->where('section_id = ' . $section_id);
			$query->where('published = 0');

			$dummy = new stdClass();
			$dummy->params = new \Joomla\Registry\Registry();
			$section = ItemsStore::getSection($section_id);

			if (!MECAccess::allowPublish(null, $dummy, $section))
			{
				$query->where('user_id = ' . $user_id);
			}

			self::_data_show($query, $section_id);
			$db->setQuery($query);
			self::$unpublished["$section_id-$user_id"] = $db->loadResult();
		}

		return self::$unpublished["$section_id-$user_id"];
	}

	/**
	 * How many comments have lbeen left for this user records in the section
	 * @param int $user_id
	 * @param int $section_id
	 * @return int:
	 */
	static public function comments_left($user_id, $section_id)
	{
		if (!isset(self::$comments_left["$section_id-$user_id"]))
		{
			self::$comments_left["$section_id-$user_id"] = 0;
			if ($user_rec = self::_user_records($user_id, $section_id))
			{
				$db = \Joomla\CMS\Factory::getDbo();
				$query = $db->getQuery(true);
				$query->select("COUNT(id)");
				$query->from("#__js_res_comments");
				$query->where("section_id = '$section_id'");
				$query->where("record_id IN (" . implode(', ', $user_rec) . ")");
				$db->setQuery($query);
				self::$comments_left["$section_id-$user_id"] = $db->loadResult();
			}
		}
		return self::$comments_left["$section_id-$user_id"];
	}

	/**
	 * How many records this user commented
	 * @param int $user_id
	 * @param int $section_id
	 * @return int:
	 */
	static public function commented($user_id, $section_id)
	{
		if (!isset(self::$commented["$section_id-$user_id"]))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select("record_id");
			$query->from("#__js_res_comments");
			$query->where("section_id = '$section_id'");
			$query->where("user_id = $user_id");
			$db->setQuery($query);
			$ids = $db->loadColumn();

			if ($ids)
			{
				$ids = self::_get_records_count($ids, $section_id);
			}
			self::$commented["$section_id-$user_id"] = (int)$ids;

		}
		return self::$commented["$section_id-$user_id"];
	}

	/**
	 * How many records this user visited
	 * @param int $user_id
	 * @param int $section_id
	 * @return int:
	 */
	static public function visited($user_id, $section_id)
	{
		if (!isset(self::$visited["$section_id-$user_id"]))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select("record_id");
			$query->from("#__js_res_hits");
			$query->where("section_id = '$section_id'");
			$query->where("user_id = $user_id");
			$db->setQuery($query);
			$ids = $db->loadColumn();

			if ($ids)
			{
				$ids = self::_get_records_count($ids, $section_id);
			}
			self::$visited["$section_id-$user_id"] = (int)$ids;
		}
		return self::$visited["$section_id-$user_id"];
	}

	/**
	 * How many personal categories user have  in the section
	 * @param int $user_id
	 * @param int $section_id
	 * @return int:
	 */
	static public function categories($user_id, $section_id)
	{
		if (!isset(self::$categories["$section_id-$user_id"]))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(TRUE);
			$query->select('count(*)');
			$query->from('#__js_res_category_user');
			$query->where('user_id = ' . $user_id);
			$query->where('section_id = ' . $section_id);
			$db->setQuery($query);
			self::$categories["$section_id-$user_id"] = $db->loadResult();
		}
		return self::$categories["$section_id-$user_id"];
	}

	/**
	 * How many readers this user have  in the section
	 * @param int $user_id
	 * @param int $section_id
	 * @return int:
	 */
	static public function readers($user_id, $section_id, $params)
	{
		if (!isset(self::$readers["$section_id-$user_id"]))
		{
			self::$readers["$section_id-$user_id"] = 0;
			if ($user_rec = self::_user_records($user_id, $section_id))
			{
				$db = \Joomla\CMS\Factory::getDbo();
				$query = $db->getQuery(true);
				$query->select("user_id");
				$query->from("#__js_res_hits");
				$query->where("section_id = '$section_id'");
				$query->where("record_id IN (" . implode(', ', $user_rec) . ")");
				$query->where("user_id != 0");
				$db->setQuery($query);
				if ($res = $db->loadColumn())
				{
					$res = count(array_unique($res));
				}

				$ip = 0;
				if ($params->get('use_anonim', 1))
				{
					$sql = "SELECT COUNT(DISTINCT ip) FROM #__js_res_hits WHERE user_id = '0' AND section_id = {$section_id}
						AND record_id IN (" . implode(', ', $user_rec) . ")";
					$db->setQuery($sql);
					$ip = $db->loadResult();
				}

				self::$readers["$section_id-$user_id"] = (int)$res;
				self::$readers["$section_id-$user_id"] += (int)$ip;
			}
		}
		return self::$readers["$section_id-$user_id"];
	}

	/**
	 * What is average rating of articles of this user in the section
	 * @param int $user_id
	 * @param int $section_id
	 * @return int:
	 */
	static public function rating_average($user_id, $section_id)
	{
		if (!isset(self::$rating_average["$section_id-$user_id"]))
		{
			self::$rating_average["$section_id-$user_id"] = 0;
			if ($user_rec = self::_user_records($user_id, $section_id))
			{
				$db = \Joomla\CMS\Factory::getDbo();
				$query = $db->getQuery(true);
				$query->select("AVG(vote)");
				$query->from("#__js_res_vote");
				$query->where("ref_type = 'record'");
				$query->where("section_id = '$section_id'");
				$query->where("ref_id IN (" . implode(', ', $user_rec) . ")");
				$db->setQuery($query);
				self::$rating_average["$section_id-$user_id"] = number_format($db->loadResult(), 2);
			}
		}
		return self::$rating_average["$section_id-$user_id"];
	}

	/**
	 * How many users follow this user
	 * @param int $user_id
	 * @param int $section_id
	 * @return int:
	 */
	static public function follow($user_id, $section_id)
	{
		if (!isset(self::$whofollow["$section_id-$user_id"]))
		{
			self::$whofollow["$section_id-$user_id"] = 0;
			if ($user_rec = self::_user_records($user_id, $section_id))
			{
				$db = \Joomla\CMS\Factory::getDbo();
				$query = $db->getQuery(true);
				$query->select("COUNT(id)");
				$query->from("#__js_res_subscribe_user");
				$query->where("section_id = '$section_id'");
				$query->where("user_id = $user_id");
				$query->where("exclude = 0");
				$db->setQuery($query);
				$ids = $db->loadResult();
				self::$whofollow["$section_id-$user_id"] = (int)$ids;
			}
		}
		return self::$whofollow["$section_id-$user_id"];
	}

	/**
	 * How many users this user follows
	 * @param int $user_id
	 * @param int $section_id
	 * @return int:
	 */
	static public function followed($user_id, $section_id)
	{
		if (!isset(self::$followed["$section_id-$user_id"]))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select("ref_id");
			$query->from("#__js_res_subscribe");
			$query->where("section_id = '$section_id'");
			$query->where("user_id = $user_id");
			$query->where("`type` = 'record'");
			$db->setQuery($query);
			$ids = $db->loadColumn();

			if ($ids)
			{
				$ids = (int)self::_get_records_count($ids, $section_id);
			}
			self::$followed["$section_id-$user_id"] = (int)$ids;

		}
		return self::$followed["$section_id-$user_id"];
	}

	/**
	 * How many users have added to bookmarks records of this user
	 * @param int $user_id
	 * @param int $section_id
	 * @return int:
	 */
	static public function whofavorited($user_id, $section_id)
	{
		if (!isset(self::$whofavorited["$section_id-$user_id"]))
		{
			self::$whofavorited["$section_id-$user_id"] = 0;
			if ($user_rec = self::_user_records($user_id, $section_id))
			{
				$db = \Joomla\CMS\Factory::getDbo();
				$query = $db->getQuery(true);
				$query->select("COUNT(DISTINCT user_id)");
				$query->from("#__js_res_favorite");
				$query->where("section_id = '$section_id'");
				$query->where("record_id IN (" . implode(', ', $user_rec) . ")");
				$db->setQuery($query);
				$ids = $db->loadResult();
				self::$whofavorited["$section_id-$user_id"] = (int)$ids;
			}
		}
		return self::$whofavorited["$section_id-$user_id"];
	}

	/**
	 * How many records this user have added to bookmarks
	 * @param int $user_id
	 * @param int $section_id
	 * @return int:
	 */
	static public function favorited($user_id, $section_id)
	{
		if (!isset(self::$favorited["$section_id-$user_id"]))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('record_id');
			$query->from('#__js_res_favorite');
			$query->where("section_id = '$section_id'");
			$query->where("user_id = $user_id");
			$db->setQuery($query);
			$ids = $db->loadColumn();

			if ($ids)
			{
				$ids = self::_get_records_count($ids, $section_id);
			}
			self::$favorited["$section_id-$user_id"] = (int)$ids;

		}
		return self::$favorited["$section_id-$user_id"];
	}

	static private function _user_records($user_id, $section_id)
	{
		if (!isset(self::$user_records["$section_id-$user_id"]))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(true);
			$query = $db->getQuery(true);
			$query->select("id");
			$query->from("#__js_res_record");
			$query->where("section_id = '$section_id'");
			if(CStatistics::hasUnPublished($section_id))
			{
				$query->where("published = '1'");
			}
			$query->where("hidden = '0'");
			$query->where("user_id = '$user_id'");
			$query->where("ctime < NOW()");
			$query->where("(extime > NOW() OR extime = '0000-00-00 00:00' OR ISNULL(extime) )");
			$db->setQuery($query);
			self::$user_records["$section_id-$user_id"] = $db->loadColumn();
		}
		return self::$user_records["$section_id-$user_id"];
	}

	static private function _data_show(&$query, $section_id)
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$section = ItemsStore::getSection($section_id);

		if (!in_array($section->params->get('general.show_restrict'), $user->getAuthorisedViewLevels()))
		{
			$query->where("access IN(" . implode(',', $user->getAuthorisedViewLevels()) . ")");
		}
		if (!in_array($section->params->get('general.show_future_records'), $user->getAuthorisedViewLevels()))
		{
			$query->where("ctime < " . \Joomla\CMS\Factory::getDbo()->quote(\Joomla\CMS\Factory::getDate()->toSql()));
		}
		if (!in_array($section->params->get('general.show_past_records'), $user->getAuthorisedViewLevels()))
		{
			$query->where("(extime = '0000-00-00 00:00:00' OR ISNULL(extime) OR extime > '" . \Joomla\CMS\Factory::getDate()->toSql() . "')");
		}
	}

	static private function _get_records_count($ids, $section_id)
	{
		$db = \Joomla\CMS\Factory::getDbo();
		$query = $db->getQuery(TRUE);
		$query->select('count(*)');
		$query->from('#__js_res_record AS r');
		if(CStatistics::hasUnPublished($section_id))
		{
			$query->where('r.published = 1');
		}
		$query->where('r.hidden = 0');
		$query->where('r.archive = 0');
		self::_data_show($query, $section_id);
		$query->where('r.id IN ('.implode(',', $ids).')');
		$db->setQuery($query);
		return $db->loadResult();
	}

	static public function hasUnPublished($section_id)
	{
		static $out = array();

		$section = ItemsStore::getSection($section_id);

		if($section->params->get('general.have_unpublished'))
		{
			return 1;
		}

		if(!array_key_exists($section_id, $out) && !is_null($section_id))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$db->setQuery("SELECT COUNT(*) FROM `#__js_res_record` WHERE section_id = {$section_id} AND published = 0");
			$out[$section_id] = $db->loadResult();
		}

		return $out[$section_id];

	}
}