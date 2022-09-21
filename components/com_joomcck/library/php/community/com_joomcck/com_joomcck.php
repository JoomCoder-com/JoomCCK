<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class CCommunityCom_joomcck
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

	public function getDefaultAvatar()
	{
		$file = JPATH_ROOT . '/components/com_joomcck/images' . DIRECTORY_SEPARATOR;
		$file .= 'avatar0.gif';

		//return JPath::clean($file);
	}

	public function getAvatar($user_id)
	{
		$file = JPATH_ROOT . '/components/com_joomcck/images' . DIRECTORY_SEPARATOR;
		if($user_id)
		{
			$file .= 'avatar1.gif';

			//return JPath::clean($file);
		}
	}
}