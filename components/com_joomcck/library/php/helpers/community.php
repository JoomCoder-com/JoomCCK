<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

jimport('mint.resizeimage');

JPluginHelper::importPlugin('mint');

class CCommunityHelper
{

	static public function karma($actor, $target, $options, $record)
	{
		$dispatcher = JEventDispatcher::getInstance();
		$dispatcher->trigger('onKarma', array($actor, $target, $options, $record));
	}

	static public function avtivity($actor, $target, $options, $record)
	{
		$dispatcher = JEventDispatcher::getInstance();
		$dispatcher->trigger('onActivity', array($actor, $target, $options, $record));
	}

	static public function notify($user_id, $options)
	{
		$dispatcher = JEventDispatcher::getInstance();
		$dispatcher->trigger('onNotification', array($user_id, $options));

		$array = array(
			'id'      => NULL,
			'ctime'   => JFactory::getDate()->toSql(),
			'type'    => $options['type'],
			'user_id' => $user_id,
			'params'  => $options['params'],
			'option'  => 'com_joomcck',
			'ref_1'   => $options['record_id'],
			'ref_2'   => $options['section_id'],
			'ref_3'   => $options['cat_id'],
			'ref_4'   => $options['comment_id'],
			'ref_5'   => $options['field_id'],
			'html'    => '',
			'eventer' => $options['eventer']
		);

		$table = JTable::getInstance('Notificat', 'JoomcckTable');
		$table->save($array);
	}

	static public function getAvatar($user_id, $width, $height)
	{
		static $files = array();

		$class = self::_getClass();

		if(empty($files[$user_id]))
		{
			$files[$user_id] = $class->getAvatar($user_id);
		}

		if(!$files[$user_id])
		{
			$default = $class->getDefaultAvatar();
			$email   = JFactory::getUser($user_id)->get('email');

			if($email && JComponentHelper::getParams('com_joomcck')->get('gravatar'))
			{
				$default = str_replace(JPATH_ROOT, JUri::root(), $class->getDefaultAvatar());
				$aurl = "http://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=" . $width;
				$aurl .= '&d=' . ($default ? urldecode($default) : 'identicon');
				$scheme = JUri::getInstance()->toString(array('scheme'));
				$aurl    = str_replace('http://', $scheme, $aurl);
			}
			else
			{
				if(!$default)
				{
					if($user_id)
					{
						$default = JPATH_ROOT . '/components/com_joomcck/images/avatar1.gif';
					}
					else
					{
						$default = JPATH_ROOT . '/components/com_joomcck/images/avatar0.gif';
					}
				}
				$aurl =  CImgHelper::getThumb($default, $width, $height, 'avatars');
			}
		}
		else
		{
			$aurl = CImgHelper::getThumb($files[$user_id], $width, $height, 'avatars', $user_id);
		}

		return $aurl;
	}

	static public function getName($id, $section, $options = array())
	{
		static $icons = NULL;
		static $cache = array();

		if($options === TRUE)
		{
			$options = array(
				'nohtml' => 1
			);
		}

		$options = new JRegistry($options);

		if(!is_object($section))
		{
			$section = ItemsStore::getSection($section);
		}
		$key = md5($id . '-' . $section->id . '-' . http_build_query($options->toArray()));

		if(array_key_exists($key, $cache))
		{
			return $cache[$key];
		}

		$class = self::_getClass();

		$name = $options->get('label');
		if(!$name)
		{
			$name = JFactory::getUser($id)->get($section->params->get('personalize.author_mode', 'username'), JText::_('CGUEST'));
		}

		if($options->get('nohtml') && !(strpos($name, '@') === FALSE))
		{
			$_name = explode('@', $name);
			$name = $_name[0];
		}

		if($options->get('nohtml') || !$id)
		{
			$cache[$key] = $name;

			return $cache[$key];
		}

		if($options->get('nametag'))
		{

			$name = sprintf("<%s>%s</%s>", $options->get('nametag'), $name, $options->get('nametag'));
		}

		if(!$options->get('nolinks'))
		{
			$links = (array)$class->getName($id, $name, $section);

			if(JFolder::exists(JPATH_ROOT . '/components/com_uddeim'))
			{
				$links = array_merge($links,
					array(
						100 => array(
							'url'   => JRoute::_('index.php?option=com_uddeim&task=new&recip=' . $id),
							'label' => HTMLFormatHelper::icon('mail.png') . ' ' . JText::_('CUSERMESSAGE')
						)
					)
				);
			}

			if($section->params->get('personalize.personalize', 0) && $id)
			{
				$links = array_merge($links,
					array(
						101 => array(
							'url'   => JRoute::_(Url::user('created', $id, $section->id)),
							'label' => HTMLFormatHelper::icon($section->params->get('personalize.text_icon')) . ' ' . JText::_($section->params->get('personalize.home_text', 'CALLRECORDSBY'))
						)
					)
				);
			}

			if(count($links) > 0 && $options->get('onelink'))
			{
				$link[] = array_shift($links);
				$links  = $link;
			}
			if(count($links) == 0)
			{
				$out = $name;
			}
			elseif(count($links) == 1)
			{
				if($options->get('tooltip') == 'name')
				{
					$links[0]['label'] = JFactory::getUser($id)->get($section->params->get('personalize.author_mode', 'username'), JText::_('CGUEST'));
				}
				$attr = array(
					'data-original-title' => str_replace('"', "&quot;", $links[0]['label']),
					'rel'                 => 'tooltip'
				);
				if(isset($links[0]['attr']))
				{
					$attr = array_merge($attr, $links[0]['attr']);
				}
				$root = '';
				if($options->get('external'))
				{
					$uri  = JUri::getInstance();
					$root = $uri->toString(array('scheme', 'host', 'port'));
				}
				$out = JHtml::link($root . $links[0]['url'], $name, $attr);
			}
			elseif(count($links) > 1)
			{
				$out = '<span class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">' . $name . '</a>
				<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">';
				foreach($links as $link)
				{
					$out .= '<li>';
					$out .= JHtml::link($link['url'], $link['label'], isset($link['attr']) ? $link['attr'] : array());
					$out .= '</li>';
				}
				$out .= '</ul></span>';
			}
			$name = $out;
		}

		if(!$section->id)
		{
			$cache[$key] = $name;

			return $cache[$key];
		}

		if(!$options->get('nobadge'))
		{
			$db = JFactory::getDbo();

			$api = JPATH_ROOT.'/components/com_emerald/api.php';
			if(in_array($section->params->get('personalize.vip'), JFactory::getUser()->getAuthorisedViewLevels()) &&
				!in_array($section->params->get('personalize.novip'), JFactory::getUser($id)->getAuthorisedViewLevels()) && JFile::exists($api))
			{
				include_once $api;
				if(EmeraldHelper::userPurchasedTotal($id) >= $section->params->get('personalize.glod_amount'))
				{
					$icon = $section->params->get('personalize.vip_gold');
				}
				elseif(EmeraldHelper::userActiveSubscriptions($id))
				{
					$icon = $section->params->get('personalize.vip_silver');
				}
				elseif(EmeraldHelper::userInactiveSubscriptions($id))
				{
					$icon = $section->params->get('personalize.vip_gray');
				}

				if(!empty($icon))
				{
					$name .= ' <img src="' . JURI::root(TRUE) . '/components/com_joomcck/images/vip/' . $icon . '" alt="VIP" />';
				}
			}


			if(!isset($icons[$section->id]))
			{
				$sql = "SELECT user_id, icon FROM #__js_res_moderators WHERE published = 1 AND section_id = {$section->id}";
				$db->setQuery($sql);
				$icons[$section->id] = $db->loadObjectList('user_id');
			}

			if(isset($icons[$section->id][$id]) && $icons[$section->id][$id]->icon != -1)
			{
				$name .= ' <img src="' . JURI::root(TRUE) . '/components/com_joomcck/images/moderator/' . $icons[$section->id][$id]->icon . '" alt="" />';
			}
		}

		if($section->params->get('personalize.onlinestatus', 1) && !$options->get('noonlinestatus'))
		{
			$result = self::isOnline($id);

			$name = '<img src="' . JURI::root(TRUE) . '/media/mint/icons/16/status' . ($result ? NULL : '-offline') . '.png" rel="tooltip" data-original-title="' . ($result ? JText::_('CONLINE') : JText::_('COFFLINE')) . '" align="absmiddle">' . $name;
		}

		$cache[$key] = $name;

		return $cache[$key];
	}

	static private function _getClass($component = NULL)
	{
		static $class = array();

		if(!$component)
		{
			$params    = JComponentHelper::getParams('com_joomcck');
			$component = str_replace('.php', '', basename($params->get('community', 'com_joomcck.php')));
		}

		if(empty($class[$component]))
		{
			if(!JComponentHelper::isEnabled($component))
			{
				$component = 'com_joomcck';
			}
			$file = JPATH_ROOT . '/components/com_joomcck/library/php/community/' . $component . '/' . $component . '.php';

			include_once $file;
			$name = 'CCommunity' . ucfirst($component);
			if(!class_exists($name))
			{
				JError::raiseError(404, JText::sprintf('CERR_COMMUNITYCLASSNOTFOUND', $name));

				return FALSE;
			}
			$class[$component] = new $name();
		}

		return $class[$component];

	}

	static public function goToLogin()
	{
		$url = self::getLoginUrl() . '&return=' . Url::back();

		JFactory::getApplication()->redirect(JRoute::_($url, FALSE));
	}

	static public function getRegistrationLink($label)
	{
		return JHTML::link(JRoute::_(self::getRegistrationUrl()), $label);
	}

	static public function getRegistrationUrl()
	{
		$class = self::_getClass();

		return $class->getRegistrationLink();
	}

	static public function getLoginUrl()
	{
		$class = self::_getClass();

		return $class->getLoginLink();
	}

	static public function isOnline($user_id)
	{
		static $out = array();

		if(array_key_exists($user_id, $out))
		{
			return $out[$user_id];
		}

		$db  = JFactory::getDbo();
		$sql = "SELECT session_id  FROM #__session WHERE client_id = 0 AND userid = {$user_id}";
		$db->setQuery($sql);
		$out[$user_id] = (boolean)$db->loadResult();

		return $out[$user_id];
	}

}