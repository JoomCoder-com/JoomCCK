<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class FilterHelper
{

	public static function access($field)
	{

	}

	public static function filterLink($name, $value, $text, $type, $tip, $section)
	{
		$url = 'task=records.filter';
		if($type && count($section->params->get('general.type')) > 1)
		{
			$url .= '&filter_name[1]=filter_type';
			$url .= '&filter_val[1]=' . $type;
		}
		$url .= '&filter_name[0]=' . $name;
		$url .= '&filter_val[0]=' . urlencode($value);

		$url = self::url($url, $section);

		if(JComponentHelper::getParams('com_joomcck')->get('filter_nofollow', 1))
		{
			$nofollow = 'rel="tooltip nofollow"';
		}
		else
		{
			$nofollow = 'rel="tooltip"';
		}

		$patern = '<a class="filter-link" %s href="%s" ' . $nofollow . '>%s</a>';

		return sprintf($patern, ($tip ? ' data-original-title="' . htmlspecialchars($tip, ENT_COMPAT, 'UTF-8') . '"' : NULL), JRoute::_($url), $text);
	}

	public static function filterButton($name, $value, $type, $tip, $section, $icon = 'funnel-small.png')
	{
		$img = JHtml::image(JURI::root() . 'media/mint/icons/16/' . $icon, strip_tags($tip), array('border' => 0, 'align' => 'absmiddle'));

		return self::filterLink($name, $value, $img, $type, $tip, $section);
	}

	public static function key()
	{
		static $section = NULL;

		$app = JFactory::getApplication();
		if(!$section)
		{
			$section = ItemsStore::getSection($app->input->getInt('section_id'));
		}

		$key = array();
		if($s = $app->input->getInt('section_id'))
		{
			$key[] = $s;
		}

		if($section->params->get('general.filter_mode', 0) == 1)
		{
			if($c = $app->input->getInt('cat_id'))
			{
				$key['c'] = $c;
			}
		}

		if($c = $app->input->getInt('ucat_id'))
		{
			$key[] = $c;
		}

		if($u = $app->input->getInt('user_id') && empty($key['c']))
		{
			$key[] = $u;
		}
		if($v = $app->input->get('view_what'))
		{
			$key[] = $v;
		}

		return implode('_', $key);
	}

	public static function url($url, $section = NULL)
	{
		$url = 'index.php?option=com_joomcck&' . $url;
		$url .= '&section_id=' . $section->id;

		$vw       = JFactory::getApplication()->input->getCmd('view_what');
		$exeption = array('show_children', 'compare', 'show_all_children', 'show_parents', 'show_all_parents');

		if($section->id == JFactory::getApplication()->input->getInt('section_id'))
		{
			if($c = JFactory::getApplication()->input->getInt('cat_id'))
			{
				$url .= '&cat_id=' . $c;
			}
			if($c = JFactory::getApplication()->input->getInt('ucat_id'))
			{
				$url .= '&ucat_id=' . $c;
			}
			if($u = JFactory::getApplication()->input->getInt('user_id'))
			{
				$url .= '&user_id=' . $u;
			}
			if($vw && !in_array($vw, $exeption))
			{
				$url .= '&view_what=' . $vw;
			}
		}

		if($section->params->get('general.category_itemid'))
		{
			$url .= '&Itemid=' . $section->params->get('general.category_itemid');
		}

		return $url;
	}
}