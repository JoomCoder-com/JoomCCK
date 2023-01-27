<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
class JHTMLCategories
{
	public static function labels($cats)
	{
		$db = JFactory::getDbo();
		$sql = "SELECT title, id FROM #__js_res_categories WHERE id IN(".implode(',', $cats).")";
		$db->setQuery($sql);
		$cats = $db->loadAssocList('id', 'title');

		return implode(', ', $cats);
	}

	public static function form($section, $default = array(), $params = array())
	{
		if(!is_object($section))
		{
			$section = ItemsStore::getSection($section);
		}

		$reg = new JRegistry();
		$reg->loadArray($params);

		ArrayHelper::clean_r($default);
		\Joomla\Utilities\ArrayHelper::toInteger($default);
		if($default)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id, title as text');
			$query->from('#__js_res_categories');
			$query->where("id IN(".implode(',', $default).")");

			$db->setQuery($query);
			$default = $db->loadObjectList();
		}

		$options['only_suggestions'] = 1;
        $options['can_add'] = 1;
        $options['can_delete'] = 1;
		$options['suggestion_limit'] = 10;
		$options['suggestion_url'] = 'index.php?option=com_joomcck&task=ajax.category_filter&section_id='.$section->id.'&tmpl=component';

		return JHtml::_('mrelements.pills', 'filters[cats]', "categs", $default, array(), $options);
	}

	public static function checkboxes($section, $default = array(), $params = array())
	{
		$db = JFactory::getDbo();

		if (! is_array($default) && ! empty($default))
		{
			$default = explode(',', $default);
		}
		\Joomla\Utilities\ArrayHelper::toInteger($default);
		ArrayHelper::clean_r($default);

		$reg = new JRegistry();
		$reg->loadArray($params);
		$columns = $reg->get('columns', 3);

		$cats_model = MModelBase::getInstance('Categories', 'JoomcckModel');
		$cats_model->section = $section;
		$cats_model->parent_id = 1;
		$cats_model->order = 'c.lft ASC';
		$cats_model->levels = 1000;
		$cats_model->all = 1;
		$cats_model->nums = 1;
		$categories = $cats_model->getItems();
		if(!$reg->get('empty_cats', 1))
		{
			foreach ($categories as $k => $cat)
			{
				if (!$cat->records_num)
				{
					unset($categories[$k]);
				}
			}
		}

		$key = 0;
		$li = array();

		foreach ($categories AS $cat)
		{
			$chekced = (in_array($cat->id, $default) ? ' checked="checked"' : NULL);
			if($key % 3 == 0) $li[] = '<div class="row">';
			$li[] = sprintf('<div class="col-md-4"><label class="checkbox"><input type="checkbox" id="ccat-%d" class="form-control" name="filters[cats][]" value="%d"%s />
			 	<label for="ccat-%d">%s <span class="label">%d<span></label></label></div>',
				$cat->id, $cat->id, $chekced, $cat->id, $cat->title, $cat->records_num);
			if($key % 3 == 2) $li[] = '</div>';
			$key++;
		}
		if($key % 3 != 0) $li[] = '</div>';

		return '<div class="container-fluid">'.implode(' ', $li).'</div>';


		$list = array();
		$out = '';
		$j = 0;
		if ($categories)
		{
			$out = '<table class="category_filter_checkboxes"><tr>';
			foreach ($categories as $i => $cat)
			{
				if($j == $columns)
				{
					$j = 0;
					$out .= '</tr><tr>';
				}
				$out .= '<td><input name="filters[cats][]" type="checkbox" value="'.$cat->id.'" '.(in_array($cat->id, $default) ? 'checked="checked"' : '').' />'.$cat->title.'</td>';
				$j++;
			}
			$out .= '</tr></table>';
		}

		return $out;
	}

	public static function select($section, $default = array(), $params = array())
	{
		$db = JFactory::getDbo();

		if (! is_array($default) && ! empty($default))
		{
			$default = explode(',', $default);
		}
		\Joomla\Utilities\ArrayHelper::toInteger($default);
		ArrayHelper::clean_r($default);

		$reg = new JRegistry();
		$reg->loadArray($params);

		$multiple = $reg->get('multiple', 0);

		MModelBase::addIncludePath(JPATH_ROOT . '/components/com_joomcck/models');
		$cats_model = MModelBase::getInstance('Categories', 'JoomcckModel');
		$cats_model->section = $section;
		$cats_model->parent_id = 1;
		$cats_model->order = 'c.lft ASC';
		$cats_model->levels = 1000;
		$cats_model->all = 1;
		if(!$reg->get('empty_cats', 1))
		{
			$cats_model->nums = 1;
		}
		$categories = $cats_model->getItems();
		if(!$reg->get('empty_cats', 1))
		{
			foreach ($categories as $k => $cat)
			{
				if (!$cat->records_num)
				{
					unset($categories[$k]);
				}
			}
		}
		$out = '';
		$attr = array();
		if($multiple)
		{
			$attr['multiple'] = 'multiple';
			$attr['size'] = count($categories) > $reg->get('size', 25) ? $reg->get('size', 25) : count($categories);
		}
		if ($categories)
		{
			$df = new stdClass();
			$df->id = '';
			$df->opt = ' - '.JText::_('CPLEASESELECTCAT').' - ';
			array_unshift($categories, $df);
			$out = JHtml::_('select.genericlist', $categories, 'filters[cats]'.($multiple ? '[]' : ''), $attr, 'id', 'opt', $default);
		}
		return $out;
	}
}