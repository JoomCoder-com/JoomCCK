<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
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
		$default = \Joomla\Utilities\ArrayHelper::toInteger($default);
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
		$default = \Joomla\Utilities\ArrayHelper::toInteger($default);
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

		// prepare layout data
		$data = [
			'items' => $categories,
			'default' => $default,
			'display' => 'inline',
			'idPrefix' => 'ccat',
			'name' => 'filters[cats][]',
			'textProperty' => 'title',
			'countProperty' => 'records_num'
		];


		return Joomcck\Layout\Helpers\Layout::render('core.bootstrap.checkBoxes',$data);



	}

	public static function select($section, $default = array(), $params = array())
	{
		$db = JFactory::getDbo();

		if (! is_array($default) && ! empty($default))
		{
			$default = explode(',', $default);
		}
		$default = \Joomla\Utilities\ArrayHelper::toInteger($default);
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
		$attr['class'] = 'form-select';
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