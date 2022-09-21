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
if(JFile::exists(JPATH_ROOT . '/administrator/components/com_easysocial/includes/easysocial.php'))
{
	require_once JPATH_ROOT . '/administrator/components/com_easysocial/includes/easysocial.php';
}

require_once(JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php');

class CCommunityCom_easysocial extends CCommunityCom_joomcck
{
	function getName($id, $name, $section)
	{
		if(method_exists(
			'ES',
			'initialize'
		))
		{
			ES::initialize();
		}
		else
		{
			Foundry::page()->processScripts();
		}

		$user            = Foundry::user($id);
		$out[0]['url']   = $user->getPermalink();
		$out[0]['label'] = HTMLFormatHelper::icon('user-silhouette.png') . ' ' . JText::_('CUSERPOFILE');

		if(Foundry::config()->get('conversations.enabled'))
		{
			Foundry::document()->init();
			$out[1]['url']   = "javascript:void(0);";
			$out[1]['label'] = HTMLFormatHelper::icon('mail.png') . ' ' . JText::_('CUSERMESSAGE');
			$out[1]['attr']  = array(
				"data-es-conversations-compose" => 1,
				"data-es-conversations-id"      => $id
			);
		}

		return $out;
	}

	function getAvatar($id)
	{
		$user   = Foundry::user($id);
		$config = Foundry::config();

		// @TODO: Configurable storage path.
		$avatarLocation = Foundry::cleanPath($config->get('avatars.storage.container'));
		$typesLocation  = Foundry::cleanPath($config->get('avatars.storage.' . SOCIAL_TYPE_USER));

		// Build absolute path to the file.
		$path = JPATH_ROOT . '/' . $avatarLocation . '/' . $typesLocation . '/' . $id . '/' . $user->avatars[SOCIAL_AVATAR_LARGE];

		if(JFile::exists($path))
		{
			return $path;
		}
	}

	public function getDefaultAvatar()
	{
		$config = Foundry::config();
		$path   = JPATH_ROOT . '/' . $config->get('avatars.default.user.large');

		if(JFile::exists($path))
		{
			return $path;
		}
	}
}
