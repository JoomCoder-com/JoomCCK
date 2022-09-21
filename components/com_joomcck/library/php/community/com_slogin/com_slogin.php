<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

include_once dirname(dirname(__FILE__)) . '/com_joomcck/com_joomcck.php';

class CCommunityCom_slogin extends CCommunityCom_joomcck
{

	public function getRegistrationLink()
	{
		return 'index.php?option=com_users&view=registration';
	}

	public function getLoginLink()
	{
		return 'index.php?option=com_users&view=login';
	}

	public function getName($id, $name, $section)
	{
		return array();
	}


	public function getAvatar($user_id)
	{
		static $users = array();
        
        if(array_key_exists($user_id, $users)) 
        {
            return $users[$user_id];
        }
        
        $db = JFactory::getDbo();
        $db->setQuery("SELECT * FROM #__slogin_users WHERE user_id = {$user_id}");
        $user = $db->loadObject();
        
        
        $users[$user_id] = '';

        $file = JPATH_ROOT.'/images/avatar/'.$user->provider.'_'.$user->slogin_id.'.jpg';

		if(JFile::exists($file))
		{
            $users[$user_id] = $file;
		}

        return $users[$user_id];
	}
}