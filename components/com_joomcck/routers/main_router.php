<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 3.1 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ROOT . '/components/com_joomcck/library/php/helpers/itemsstore.php';

$component_params = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');

define('S', $component_params->get('separator', ':'));


function category_route($cid)
{
	static $component_params = null;
	if (!$cid)
	{
		return;
	}

	$category       = ItemsStore::getCategory($cid);
	$category->path = str_replace("root/", '', $category->path);
	if ($pos = strrpos($category->path, '/'))
	{
		$path = substr_replace($category->path, $cid . '-', strrpos($category->path, '/') + 1, 0);
	}
	else
	{
		$path = $cid . '-' . $category->path;
	}

	return $path;
}

function my_unset($str, &$query)
{
	$vars = explode(',', $str);
	foreach ($vars as $var)
	{
		if (isset($query[$var]))
		{
			unset($query[$var]);
		}
	}
}

function is_view_what($vw)
{
	$view_whats = array(
		"favorited", "children", "rated", "visited", "created", "commented", "related", "expired", "unpublished",
		"hidden", "follow", "events", "featured", "show_all_children", "show_all_parents"
	);
	foreach ($view_whats as $alias)
	{
		if (\Joomla\CMS\Language\Text::_($alias) == urldecode($vw))
		{
			return $alias;
		}
		if (\Joomla\CMS\Language\Text::_('user_' . $alias) == urldecode($vw))
		{
			return 'user_' . $alias;
		}
	}

	return 0;
}

function cleanAlias($alias)
{
	$alias = str_replace(array(
		'/', '.', ',', '"', "'", '?', '(', ')', '[', ']', '{', '}', '\\', ';', ':', '~', '!', '@',
		'#', '$', '%' . '^', '&', '*', '+', '='
	), '-', $alias);

	while (strpos($alias, '--') !== false)
	{
		$alias = str_replace('--', '-', $alias);
	}

	return \Joomla\String\StringHelper::strtolower(\Joomla\CMS\Language\Text::_(\Joomla\String\StringHelper::strtoupper($alias)));
}

function explodeUrlParam($param)
{
	$s = S;

	if (preg_match('/^[0-9]*\:/', (string) $param))
	{
		$s = ':';
	}

	$array = explode($s, (string) $param);

	$out[0] = $array[0];

	if (count($array) > 1)
	{
		unset($array[0]);
		$out[1] = implode('-', $array);
	}

	return $out;
}

function JoomcckBuildRoute(&$query)
{
	static $component_params = null;

	$component_params = (!empty($component_params) ? $component_params : \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck'));

	// active menu
	$activeMenu = \Joomla\CMS\Factory::getApplication()->getMenu()->getActive();

	$segments = array();
	$unset    = false;
	switch (@$query['view'])
	{
		case 'records':
			$unset = true;

			if (isset($query['view_what']))
			{
				$user = explodeUrlParam(@$query['user_id']);

				if (isset($user[0]) && $user[0])
				{
					$segments[] = urlencode(\Joomla\CMS\Language\Text::_('SEF_USERITEM'));

					$segments[] = urlencode(\Joomla\CMS\Language\Text::_($query['view_what']));

					$section = explodeUrlParam($query['section_id']);
					if (count($section) < 2)
					{
						$section[1] = ItemsStore::getSection($section[0])->alias;
					}
					$segments[] = ($section[0] . '-' . cleanAlias($section[1]));

					if (count($user) < 2)
					{
						$user[1] = \Joomla\CMS\Filter\OutputFilter::stringURLSafe(\Joomla\CMS\Factory::getUser($user[0])->get(ItemsStore::getSection($section[0])->params->get('personalize.author_mode')));
					}

					$segments[] = ($user[0] . '-' . cleanAlias($user[1]));
				}
				else
				{
					$segments[] = urlencode(\Joomla\CMS\Language\Text::_('SEF_VWITEM'));

					$segments[] = urlencode(\Joomla\CMS\Language\Text::_($query['view_what']));

					$section = explodeUrlParam($query['section_id']);
					if (count($section) < 2)
					{
						$section[1] = ItemsStore::getSection($section[0])->alias;
					}
					$segments[] = ($section[0] . '-' . cleanAlias($section[1]));
				}
				break;
			}

			if (isset($query['ucat_id']))
			{
				$segments[] = urlencode(\Joomla\CMS\Language\Text::_('SEF_USERCATEGORY'));
				$section    = explodeUrlParam($query['section_id']);
				if (count($section) < 2)
				{
					$section[1] = ItemsStore::getSection($section[0])->alias;
				}
				$segments[] = ($section[0] . '-' . cleanAlias($section[1]));
				$ucat       = explodeUrlParam($query['ucat_id']);
				if (count($ucat) < 2)
				{
					$ucat[1] = ItemsStore::getUserCategory($ucat[0])->alias;
				}
				$segments[] = ($ucat[0] . '-' . cleanAlias($ucat[1]));
				$user       = explodeUrlParam($query['user_id']);
				if (count($user) < 2)
				{
					$user[1] = \Joomla\CMS\Application\ApplicationHelper::stringURLSafe(\Joomla\CMS\Factory::getUser($user[0])->get(ItemsStore::getSection($section[0])->params->get('personalize.author_mode')));
				}
				$segments[] = ($user[0] . '-' . cleanAlias($user[1]));
				break;
			}

			if (isset($query['cat_id']))
			{
				$segments[] = urlencode(\Joomla\CMS\Language\Text::_('SEF_CATITEMS'));
				$section    = explodeUrlParam($query['section_id']);
				if (count($section) < 2)
				{
					$section[1] = ItemsStore::getSection($section[0])->alias;
				}
				$segments[] = ($section[0] . '-' . cleanAlias($section[1]));
				$category   = explodeUrlParam($query['cat_id']);
				if ($component_params->get('sef_category', 0) == 'full')
				{
					$segments[] = category_route($category[0]);
				}
				else
				{
					if (count($category) < 2)
					{
						$category[1] = ItemsStore::getCategory($category[0])->alias;
					}
					$segments[] = ($category[0] . '-' . cleanAlias($category[1]));
				}
				break;

			}
			/*if(isset($query['user_id']))
			{
				$segments[] = urlencode(\Joomla\CMS\Language\Text::_('SEF_CREATED'));
				if(isset($query['section_id']))
				{
					$section = explodeUrlParam($query['section_id']);
					if(count($section) < 2)
					{
						$section[1] = ItemsStore::getSection($section[0])->alias;
					}
					$segments[] = ($section[0].'-'.cleanAlias($section[1]));
				}
				$user = explodeUrlParam($query['user_id']);
				if(count($user) < 2)
				{
					$user[1] = \Joomla\CMS\Application\ApplicationHelper::stringURLSafe(CCommunityHelper::getName($user[0], ItemsStore::getSection($section[0]), array('nohtml' => 1)));
				}
				$segments[] = ($user[0].'-'.cleanAlias($user[1]));
				break;
			}*/

			if (isset($query['section_id'])) // is section
			{

				// check if has an item id, if has it then use item id build
				if (isset($query['Itemid']))
				{
					unset($query['view']);
					unset($query['section_id']);

					return [];
				}

				$segments[] = urlencode(\Joomla\CMS\Language\Text::_('SEF_ITEMS'));
				if (isset($query['section_id']))
				{
					$section = explodeUrlParam($query['section_id']);
					if (count($section) < 2)
					{
						$section[1] = ItemsStore::getSection($section[0])->alias;
					}
					$segments[] = ($section[0] . '-' . cleanAlias($section[1]));
				}
				break;
			}
			break;

		case 'record':
			$unset = true;
			if (isset($query['user_id']))
			{
				$segments[] = urlencode(\Joomla\CMS\Language\Text::_('SEF_USER_ITEM'));
				$user       = explodeUrlParam($query['user_id']);
				if (!isset($user[1]))
				{
					$user[1] = \Joomla\CMS\Application\ApplicationHelper::stringURLSafe(\Joomla\CMS\Factory::getUser($user[0])->get('name'));
				}
				$segments[] = $user[0] . '-' . $user[1];
			}
			else
			{
				$segments[] = urlencode(\Joomla\CMS\Language\Text::_('SEF_ITEM'));
			}

			if (isset($query['cat_id']))
			{
				$category = explodeUrlParam($query['cat_id']);
				if ($component_params->get('sef_category', 0) == 'full')
				{
					$segments[] = category_route($category[0]);
				}
				else
				{
					if (count($category) < 2)
					{
						$category[1] = ItemsStore::getCategory($category[0])->alias;
					}
					$segments[] = $category[0] . '-' . cleanAlias($category[1]);
				}
			}

			$record     = explodeUrlParam($query['id']);
			$segments[] = $record[0] . (isset($record[1]) ? '-' . cleanAlias($record[1]) : '');

			break;

		case 'form':
			$unset = true;
			if (isset($query['id']))
			{
				$segments[] = urlencode(\Joomla\CMS\Language\Text::_('SEF_FORM_EDIT'));
				$record     = explodeUrlParam($query['id']);
				if (count($record) < 2)
				{
					$record[1] = ItemsStore::getRecord($record[0])->alias;
				}
				$segments[] = $record[0] . '-' . cleanAlias($record[1]);
			}
			else
			{
				$segments[] = urlencode(\Joomla\CMS\Language\Text::_('SEF_FORM_ADD'));

				if (isset($query['section_id']))
				{
					$section = explodeUrlParam($query['section_id']);
					if (count($section) < 2)
					{
						$section[1] = ItemsStore::getSection($section[0])->alias;
					}
					$segments[] = ($section[0] . '-' . cleanAlias($section[1]));
				}
				if (isset($query['type_id']))
				{
					$type       = explodeUrlParam($query['type_id']);
					$segments[] = $type[0] . (isset($type[1]) ? '-' . \Joomla\CMS\Application\ApplicationHelper::stringURLSafe($type[1]) : '');
				}
				if (isset($query['cat_id']))
				{
					$category = explodeUrlParam($query['cat_id']);
					if ($component_params->get('sef_category', 0) == 'full')
					{
						$segments[] = category_route($category[0]);
					}
					else
					{
						if (count($category) < 2)
						{
							$category[1] = ItemsStore::getCategory($category[0])->alias;
						}
						$segments[] = $category[0] . '-' . cleanAlias($category[1]);
					}
				}
			}
			break;
	}
	if ($unset)
	{
		my_unset('view,section_id,cat_id,view_what,user_id,ucat_id,type_id,id', $query);
	}

	return $segments;

}

function JoomcckParseRoute($segments)
{
	$vars         = array();
	$filter       = \Joomla\CMS\Filter\InputFilter::getInstance();
	$count        = count($segments);
	$last_segment = count($segments) - 1;

	switch (urldecode(str_replace(':', '-', $segments[0])))
	{
		case \Joomla\CMS\Language\Text::_('SEF_ITEMS'):
			$vars['view']       = 'records';
			$vars['section_id'] = $filter->clean($segments[1], 'INT');
			break;
		case \Joomla\CMS\Language\Text::_('SEF_CATITEMS'):
			$vars['view']       = 'records';
			$vars['section_id'] = $filter->clean($segments[1], 'INT');
			$vars['cat_id']     = $filter->clean($segments[$last_segment], 'INT');
			break;
		case \Joomla\CMS\Language\Text::_('SEF_USERCATEGORY'):
			$vars['view']       = 'records';
			$vars['section_id'] = $filter->clean($segments[1], 'INT');
			$vars['ucat_id']    = $filter->clean($segments[$last_segment - 1], 'INT');
			$vars['user_id']    = $filter->clean($segments[$last_segment], 'INT');
			break;
		case \Joomla\CMS\Language\Text::_('SEF_USERITEM'):
			$vars['view']       = 'records';
			$vw                 = is_view_what($filter->clean($segments[1]));
			$vars['view_what']  = $vw ? $vw : 'created';
			$vars['section_id'] = $filter->clean($segments[2], 'INT');
			$vars['user_id']    = ($last_segment > 2) ? $filter->clean($segments[$last_segment], 'INT') : 0;
			break;
		case \Joomla\CMS\Language\Text::_('SEF_VWITEM'):
			$vars['view']       = 'records';
			$vw                 = is_view_what($filter->clean($segments[1]));
			$vars['view_what']  = $vw ? $vw : 'created';
			$vars['section_id'] = $filter->clean($segments[2], 'INT');
			break;
		case \Joomla\CMS\Language\Text::_('SEF_FORM_ADD'):
			$vars['view']       = 'form';
			$vars['section_id'] = $filter->clean($segments[1], 'INT');
			$vars['type_id']    = $filter->clean($segments[2], 'INT');

			if ($count >= 4)
			{
				$vars['cat_id'] = $filter->clean($segments[$last_segment], 'INT');
			}
			break;
		case \Joomla\CMS\Language\Text::_('SEF_FORM_EDIT'):
			$vars['view'] = 'form';
			$vars['id']   = $filter->clean($segments[$last_segment], 'INT');
			break;
		case \Joomla\CMS\Language\Text::_('SEF_CREATED'):
			$vars['view']       = 'records';
			$vars['section_id'] = $filter->clean($segments[1], 'INT');
			$vars['user_id']    = $filter->clean($segments[$last_segment], 'INT');
			$vars['view_what']  = 'user_created';
			break;
		case \Joomla\CMS\Language\Text::_('SEF_USER_ITEM'):
			$vars['view']    = 'record';
			$vars['user_id'] = $filter->clean($segments[1], 'INT');
			$vars['id']      = $filter->clean($segments[$last_segment], 'INT');
			if ($count >= 4)
			{
				$vars['cat_id'] = $filter->clean($segments[$last_segment - 1], 'INT');
			}
			break;
		case \Joomla\CMS\Language\Text::_('SEF_ITEM'):
			$vars['view'] = 'record';
			$vars['id']   = $filter->clean($segments[$last_segment], 'INT');
			if ($count >= 3)
			{
				$vars['cat_id'] = $filter->clean($segments[$last_segment - 1], 'INT');
			}
			break;
	}

	//print_r( $vars);
	return $vars;
}