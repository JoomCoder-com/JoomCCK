<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

include_once JPATH_ROOT.'/components/com_joomcck/library/php/helpers/events.php';
include_once JPATH_ROOT.'/components/com_joomcck/library/php/helpers/community.php';
include_once JPATH_ROOT.'/components/com_joomcck/library/php/helpers/html.php';

class modJoomcckNotificationsHelper
{
	
	static public function getList($params)
	{
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$query = $db->getQuery(true);
		$list = array();
		$query->select('*, TO_DAYS(CURRENT_DATE) - TO_DAYS(ctime) as days');
		$query->from('#__js_res_notifications');
		$query->where('notified = 0');
		$query->where('user_id = ' . $user->id);
		if($params->get('section_id', 0))
		{
			$sections = $params->get('section_id');
			if(!is_array($sections)) 
				settype($sections, 'array');
			$query->where('ref_2  IN ('.implode(',', $sections).')');
		}
		
		$query->order('ctime DESC'); 
		$db->setQuery($query);
		if($list = $db->loadObjectList())
		{
			foreach ($list as $i => $item)
			{
				$list[$i]->html = CEventsHelper::get_notification($item);
			}
		}
		return $list;
	}
	
}

?>