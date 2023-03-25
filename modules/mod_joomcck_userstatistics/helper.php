<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class modJoomcckUserStatisticsHelper
{

	static public function getData($params, $section_id)
	{
		$list = array();
		$user = JFactory::getUser();
		
		if($params->get('created', 1))
		{
			$list['mod_ustat_created'] = CStatistics::created($user->id, $section_id); 
		}
		if($params->get('comments_left', 1))
		{
			$list['mod_ustat_comments_left'] = CStatistics::comments_left($user->id, $section_id); 
		}
		if($params->get('commented', 1))
		{
			$list['mod_ustat_commented'] = CStatistics::commented($user->id, $section_id); 
		}
		if($params->get('readers', 1))
		{
			$list['mod_ustat_readers'] = CStatistics::readers($user->id, $section_id, $params); 
		}
		if($params->get('visited', 1))
		{
			$list['mod_ustat_visited'] = CStatistics::visited($user->id, $section_id); 
		}
		if($params->get('rating_average', 1))
		{
			$list['mod_ustat_rating_average'] = CStatistics::rating_average($user->id, $section_id); 
		}
		if($params->get('whofollow', 1))
		{
			$list['mod_ustat_whofollow'] = CStatistics::follow($user->id, $section_id);
		}
		if($params->get('followed', 1))
		{
			$list['mod_ustat_followed'] = CStatistics::followed($user->id, $section_id); 
		}
		if($params->get('whofavorited', 1))
		{
			$list['mod_ustat_whofavorited'] = CStatistics::whofavorited($user->id, $section_id); 
		}
		if($params->get('favorited', 1))
		{
			$list['mod_ustat_favorited'] = CStatistics::favorited($user->id, $section_id); 
		}
		
		return $list;
	}

	
}

?>