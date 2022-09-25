<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Application\ApplicationHelper;

defined('_JEXEC') or die();

$component_params = JComponentHelper::getParams('com_joomcck');
define('COBS', $component_params->get('separator', ':'));

class Url
{

	static public function back()
	{
		$url = JUri::getInstance()->toString();
		$url = base64_encode($url);
		$url = urlencode($url);
		return $url;
	}

	static public function get_back($name, $default = NULL)
	{
		$url = JFactory::getApplication()->input->getString($name);
		$url = str_replace(' ', '+', $url);

		if($url)
		{
			$url = JoomcckFilter::base64($url);
		}

		if(!$url)
		{
			$url = $default;
		}

		if(!JUri::isInternal($url))
		{
			$url = JUri::root();
		}

		return $url;
	}

	static public function add($section, $type, $category)
	{

        if(!isset($type->id))
            return '#';


		$url = 'index.php?option=com_joomcck&view=form&section_id=' . $section->id;
		$url .= '&type_id=' . $type->id . COBS . ApplicationHelper::stringURLSafe($type->name);
		if(!empty($category->id))
		{
			$url .= '&cat_id=' . $category->id . COBS . ApplicationHelper::stringURLSafe($category->title);
		}

		$itemid = isset($type->params) ? $type->params->get('properties.item_itemid') : '';

		if(empty($itemid) && $section->params->get('general.category_itemid'))
		{
			$itemid = $section->params->get('general.category_itemid');
		}

		if(!empty($category->id) && $category->params->get('category_item_itemid', 0))
		{
			$itemid = $category->params->get('category_item_itemid');
		}

		if($itemid)
		{
			$url .= '&Itemid=' . $itemid;
		}


		$url .= '&return=' . self::back();

		return JRoute::_($url);
	}

	static public function edit($id, $return = NULL)
	{
		$url = 'index.php?option=com_joomcck&task=form.edit&id=' . $id . '&return=' . ($return ? $return : self::back());

		return JRoute::_($url, ($return ? FALSE : TRUE));
	}

	static public function task($task, $id, $return = FALSE)
	{
		$r = ($return ? $return : self::back());
		$url = 'index.php?option=com_joomcck&task=' . $task . '&id=' . $id . '&return=' . $r;

		return JRoute::_($url);
	}
	static public function taskCid($task, $id, $return = FALSE)
	{
		$url = 'index.php?option=com_joomcck&task=' . $task . '&cid[]=' . $id . '&return=' . ($return ? $return : self::back());

		return JRoute::_($url);
	}

	static public function view($view, $x = true)
	{
		$url = 'index.php?option=com_joomcck&view=' . $view ;

		return JRoute::_($url, $x);
	}
	static public function _($type, $array = array(), $ignore = array())
	{
		if($s = JFactory::getApplication()->input->getInt('section_id'))
		{
			$array['section_id'] = $s;
		}

		$array['view'] = $type;

		if($c = JFactory::getApplication()->input->getInt('cat_id'))
		{
			$array['cat_id'] = $c;
		}
		if($c = JFactory::getApplication()->input->getInt('ucat_id'))
		{
			$array['ucat_id'] = $c;
		}
		if($u = JFactory::getApplication()->input->getInt('user_id'))
		{
			$array['user_id'] = $u;
		}

		return self::build($array);
	}

	static public function user($view_what, $user_id = NULL, $section_id = NULL)
	{
		$user = JFactory::getUser($user_id);

		if(!$user->get('id'))
		{

			return;
		}


		if(!$section_id)
		{
			$section_id = JFactory::getApplication()->input->getInt('section_id');
		}

		if(!$section_id)
		{
			return NULL;
		}

		$section = ItemsStore::getSection((int)$section_id);
		if(!$section->id)
		{
			return NULL;
		}

		$array['view']       = 'records';
		$array['section_id'] = $section->id . COBS . $section->alias;
		$array['user_id']    = $user->get('id') . COBS . ApplicationHelper::stringURLSafe($user->get($section->params->get('personalize.author_mode')));
		$array['view_what']  = $view_what;
		if($section->params->get('general.category_itemid'))
		{
			$array['Itemid'] = $section->params->get('general.category_itemid');
		}

		if(JFactory::getApplication()->input->getInt('start'))
		{
			$array['start'] = JFactory::getApplication()->input->getInt('start');
		}

		return self::build($array);
	}

	static public function build($array)
	{
		$url = 'index.php?option=com_joomcck';

		foreach($array as $key => $val)
		{
			$url .= '&' . $key . '=' . urlencode($val);
		}

		return $url;
	}

	static public function record($record, $type = NULL, $section = NULL, $category = NULL)
	{
		if(!$record)
		{
			return;
		}

		$app = JFactory::getApplication();


		if(!is_object($record))
		{
			$record = ItemsStore::getRecord($record);
		}

		if(!$record)
		{
			return;
		}

		if(empty($category->id) && is_int($category))
		{
			$category = ItemsStore::getCategory($category);
		}

		if(!$type)
		{
			$type = ItemsStore::getType($record->type_id);
		}

		if(!$section)
		{
			$section = ItemsStore::getSection($record->section_id);
		}

		$itemid = $type->params->get('properties.item_itemid');

		if(empty($itemid) && $section->params->get('general.category_itemid'))
		{
			$itemid = $section->params->get('general.category_itemid');
		}

		if(!empty($category->id) && $category->params->get('category_item_itemid', 0))
		{
			$itemid = $category->params->get('category_item_itemid');
		}

		if(empty($itemid))
		{
			$itemid = $app->input->get('Itemid');
		}

		if($app->input->get('force_itemid'))
		{
			$itemid = $app->input->get('force_itemid');
		}

		$url = 'index.php?option=com_joomcck&view=record';

		if($section->params->get('personalize.personalize') && $record->user_id)
		{
			$url .= '&user_id=' . $record->user_id . COBS . ApplicationHelper::stringURLSafe(JFactory::getUser($record->user_id)->get($section->params->get('personalize.author_mode')));
		}
		if(!empty($category->id))
		{
			$url .= '&cat_id=' . $category->id . COBS . $category->alias;
		}
		$url .= '&id=' . $record->id . COBS . $record->alias;
		$url .= '&Itemid=' . $itemid;

		if($app->input->getInt('start'))
		{
			//$url .= '&start='.$app->input->getInt('start');
		}

		$view_what = $app->input->get('view_what');
		/*if($view_what)
		{
			$url .= '&view_what=' . $view_what;
		}*/
		if($view_what == 'show_children' || $view_what == 'show_parents')
		{
			$url .= '&access='.urldecode(base64_encode($app->input->getInt('_rfid').','.$app->input->getInt('_rrid')));
		}

		if($app->input->getInt('_rfaid'))
		{
			$url .= '&access='.urldecode(base64_encode($app->input->getInt('_rfaid')));
		}

		return $url;
	}

	static public function records($section, $category = NULL, $user_id = NULL, $vw = NULL, $additional = array())
	{
		if(!is_object($section))
		{
			$section = ItemsStore::getSection($section);
		}
		if(empty($category->id) && (!is_object($category) && (int)$category))
		{
			$category = ItemsStore::getCategory($category);
		}

		$url = 'index.php?option=com_joomcck&view=records&section_id=';
		$url .= $section->id . COBS . $section->alias;
		if(!empty($category->id))
		{
			$url .= '&cat_id=' . $category->id . COBS . $category->alias;
		}
		if($user_id)
		{
			$url .= '&user_id=' . $user_id;
		}

		if($vw)
		{
			$url .= '&view_what=' . $vw;
		}

		foreach($additional as $uk => $up)
		{
			if(empty($up))
			{
				continue;
			}
			$url .= "&{$uk}={$up}";
		}

		$itemid = $section->params->get('general.category_itemid');

		if($itemid && $url == 'index.php?option=com_joomcck&view=records&section_id='.$section->id . COBS . $section->alias)
		{
			return "index.php?Itemid=" . $itemid;
		}

		if(!empty($category->id) && $category->params->get('category_itemid', 0))
		{
			$itemid = $category->params->get('category_itemid');
		}
		if((int)$itemid == 0)
		{
			$app    = JFactory::getApplication();
			$itemid = $app->input->getInt('Itemid');
		}
		$url .= '&Itemid=' . $itemid;

		return $url;
	}

	static public function usercategory_records($user_id, $section, $ucategory_id = NULL)
	{
		$url = 'index.php?option=com_joomcck&view=records';
		$url .= '&section_id=' . $section->id . COBS . $section->alias;
		if($ucategory_id)
		{
			$url .= '&ucat_id=' . $ucategory_id;
		}
		$url .= '&user_id=' . $user_id . COBS . ApplicationHelper::stringURLSafe(JFactory::getUser($user_id)->get($section->params->get('personalize.author_mode')));
		$url .= '&Itemid=' . $section->params->get('general.category_itemid');

		return $url;
	}

}