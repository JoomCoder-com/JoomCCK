<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

include_once dirname(dirname(__FILE__)) . '/com_joomcck/com_joomcck.php';

class CCommunityCom_community extends CCommunityCom_joomcck
{
	function getName($id, $name, $section)
	{
		$app = JFactory::getApplication();

		if($app->input->get('option') == 'com_communiy' && $app->input->get('view') == 'profile')
		{
			//return;
		}
		$jspath = JPATH_ROOT . '/components/com_community';
		include_once($jspath . '/libraries/core.php');
		include_once($jspath . '/libraries/messaging.php');

		$out[0]['url']   = JRoute::_("index.php?option=com_community&view=profile&userid=" . $id);
		$out[0]['label'] = HTMLFormatHelper::icon('user-silhouette.png') . ' ' . JText::_('CUSERPOFILE');

		$out[1]['url']   = "javascript:" . CMessaging::getPopup($id);
		$out[1]['label'] = HTMLFormatHelper::icon('mail.png') . ' ' . JText::_('CUSERMESSAGE');

		return $out;
	}

	function getAvatar($id)
	{
		$db = JFactory::getDBO();

		$sql = "SELECT avatar FROM #__community_users WHERE userid = " . $id;
		$db->setQuery($sql);
		$fname = $db->loadResult();

		$file = JPATH_ROOT . DIRECTORY_SEPARATOR . $fname;

		if(JFile::exists($file))
		{
			return JPath::clean($file);
		}
	}

	public function getDefaultAvatar()
	{
		$path = JPATH_ROOT . '/components/com_community/assets/user.png';
		if(JFile::exists($path))
		{
			return $path;
		}
	}

	public function getRegistrationLink()
	{
		return 'index.php?option=com_community&view=register&task=register';

	}
}