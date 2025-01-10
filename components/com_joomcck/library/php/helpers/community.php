<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

jimport('mint.resizeimage');

\Joomla\CMS\Plugin\PluginHelper::importPlugin('mint');

class CCommunityHelper
{

	static public function karma($actor, $target, $options, $record)
	{
		$dispatcher = \Joomla\CMS\Factory::getApplication();
		$dispatcher->triggerEvent('onKarma', array($actor, $target, $options, $record));
	}

	static public function avtivity($actor, $target, $options, $record)
	{
		$dispatcher = \Joomla\CMS\Factory::getApplication();
		$dispatcher->triggerEvent('onActivity', array($actor, $target, $options, $record));
	}

	static public function notify($user_id, $options)
	{
		$dispatcher = \Joomla\CMS\Factory::getApplication();
		$dispatcher->triggerEvent('onNotification', array($user_id, $options));

		$array = array(
			'id'      => NULL,
			'ctime'   => \Joomla\CMS\Factory::getDate()->toSql(),
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

		$table = \Joomla\CMS\Table\Table::getInstance('Notificat', 'JoomcckTable');
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
			$email   = \Joomla\CMS\Factory::getUser($user_id)->get('email');

			if($email && \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck')->get('gravatar'))
			{
				$default = str_replace(JPATH_ROOT, \Joomla\CMS\Uri\Uri::root(), (string) $class->getDefaultAvatar());
				$aurl = "http://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=" . $width;
				$aurl .= '&d=' . ($default ? urldecode($default) : 'identicon');
				$scheme = \Joomla\CMS\Uri\Uri::getInstance()->toString(array('scheme'));
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

		$options = new \Joomla\Registry\Registry($options);

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
			$name = \Joomla\CMS\Factory::getUser($id)->get($section->params->get('personalize.author_mode', 'username'), \Joomla\CMS\Language\Text::_('CGUEST'));
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

			if(is_dir(JPATH_ROOT . '/components/com_uddeim'))
			{
				$links = array_merge($links,
					array(
						100 => array(
							'url'   => \Joomla\CMS\Router\Route::_('index.php?option=com_uddeim&task=new&recip=' . $id),
							'label' => HTMLFormatHelper::icon('mail.png') . ' ' . \Joomla\CMS\Language\Text::_('CUSERMESSAGE')
						)
					)
				);
			}

			if($section->params->get('personalize.personalize', 0) && $id)
			{
				$links = array_merge($links,
					array(
						101 => array(
							'url'   => \Joomla\CMS\Router\Route::_(Url::user('created', $id, $section->id)),
							'label' => HTMLFormatHelper::icon($section->params->get('personalize.text_icon')) . ' ' . \Joomla\CMS\Language\Text::_($section->params->get('personalize.home_text', 'CALLRECORDSBY'))
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
					$links[0]['label'] = \Joomla\CMS\Factory::getUser($id)->get($section->params->get('personalize.author_mode', 'username'), \Joomla\CMS\Language\Text::_('CGUEST'));
				}
				$attr = array(
					'data-bs-original-title' => str_replace('"', "&quot;", $links[0]['label']),
					'rel'                 => 'tooltip'
				);
				if(isset($links[0]['attr']))
				{
					$attr = array_merge($attr, $links[0]['attr']);
				}
				$root = '';
				if($options->get('external'))
				{
					$uri  = \Joomla\CMS\Uri\Uri::getInstance();
					$root = $uri->toString(array('scheme', 'host', 'port'));
				}
				$out = \Joomla\CMS\HTML\HTMLHelper::link($root . $links[0]['url'], $name, $attr);
			}
			elseif(count($links) > 1)
			{
				$out = '<span class="dropdown"><a class="dropdown-toggle" data-bs-toggle="dropdown" href="#">' . $name . '</a>
				<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">';
				foreach($links as $link)
				{
					$out .= '<li>';
					$out .= \Joomla\CMS\HTML\HTMLHelper::link($link['url'], $link['label'], isset($link['attr']) ? $link['attr'] : array());
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
			$db = \Joomla\CMS\Factory::getDbo();

			$api = JPATH_ROOT.'/components/com_emerald/api.php';
			if(in_array($section->params->get('personalize.vip'), \Joomla\CMS\Factory::getApplication()->getIdentity()->getAuthorisedViewLevels()) &&
				!in_array($section->params->get('personalize.novip'), \Joomla\CMS\Factory::getUser($id)->getAuthorisedViewLevels()) && is_file($api))
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
					$name .= ' <img src="' . \Joomla\CMS\Uri\Uri::root(TRUE) . '/components/com_joomcck/images/vip/' . $icon . '" alt="VIP" />';
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
				$name .= ' <img src="' . \Joomla\CMS\Uri\Uri::root(TRUE) . '/components/com_joomcck/images/moderator/' . $icons[$section->id][$id]->icon . '" alt="" />';
			}
		}

		if($section->params->get('personalize.onlinestatus', 1) && !$options->get('noonlinestatus'))
		{
			$result = self::isOnline($id);

			$name = '<img src="' . \Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/icons/16/status' . ($result ? NULL : '-offline') . '.png" rel="tooltip" data-bs-original-title="' . ($result ? \Joomla\CMS\Language\Text::_('CONLINE') : \Joomla\CMS\Language\Text::_('COFFLINE')) . '" align="absmiddle">' . $name;
		}

		$cache[$key] = $name;

		return $cache[$key];
	}

	static private function _getClass($component = NULL)
	{
		static $class = array();

		if(!$component)
		{
			$params    = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');
			$component = str_replace('.php', '', basename($params->get('community', 'com_joomcck.php')));
		}

		if(empty($class[$component]))
		{
			if(!\Joomla\CMS\Component\ComponentHelper::isEnabled($component))
			{
				$component = 'com_joomcck';
			}
			$file = JPATH_ROOT . '/components/com_joomcck/library/php/community/' . $component . '/' . $component . '.php';

			include_once $file;
			$name = 'CCommunity' . ucfirst($component);
			if(!class_exists($name))
			{
				throw new Exception( \Joomla\CMS\Language\Text::sprintf('CERR_COMMUNITYCLASSNOTFOUND', $name),404);


			}
			$class[$component] = new $name();
		}

		return $class[$component];

	}

	static public function goToLogin()
	{
		$url = self::getLoginUrl() . '&return=' . Url::back();

		\Joomla\CMS\Factory::getApplication()->redirect(\Joomla\CMS\Router\Route::_($url, FALSE));
	}

	static public function getRegistrationLink($label)
	{
		return \Joomla\CMS\HTML\HTMLHelper::link(\Joomla\CMS\Router\Route::_(self::getRegistrationUrl()), $label);
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

		$db  = \Joomla\CMS\Factory::getDbo();
		$sql = "SELECT session_id  FROM #__session WHERE client_id = 0 AND userid = {$user_id}";
		$db->setQuery($sql);
		$out[$user_id] = (boolean)$db->loadResult();

		return $out[$user_id];
	}

}