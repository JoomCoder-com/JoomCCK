<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
$cats_model = MModelBase::getInstance('Categories', 'JoomcckModel');
$cats_model->section = $this->section;
$cats_model->parent_id = 1;
$cats_model->order = $this->catsel_params->get('tmpl_params.cat_ordering', 'c.lft ASC');
$cats_model->levels = 1000;
$cats_model->all = 1;
$categories = $cats_model->getItems();
$options = array();
if($this->params->get('submission.first_category', 0))
{
	$options[] = JHtml::_('select.option', 0, $this->section->name, 'id', 'title');
}

$limit = $this->allow_multi ? $this->type->params->get('submission.multi_max_num', 1) : 1;

$options = array_merge($options, getCategoryOptions($categories, $this->type, $this, $this->params->get('submission.first_category', 0)));

if(!$options)
{
	echo JText::_('CSECTIONNOCATEGORIES');
	return;
}

$size = count($options) > $this->catsel_params->get('tmpl_params.multiple_catselect_size', 20) ? $this->catsel_params->get('tmpl_params.multiple_catselect_size', 20) : count($options);

if($this->allow_multi)
{
	$attr = ' size="'.$size.'" multiple="'.$this->allow_multi.'"';
}
else 
{
	$attr = ' size="'.$this->catsel_params->get('tmpl_params.single_catselect_size', 1).'"';
}
$attr .= ' required="true"';
$attr .= ' class="form-select required"';

if($limit > 1)
{
	echo '<div><small>'.JText::sprintf('CSELECTLIMIT', $limit).'</small></div>';
}	

if(!$this->default_categories) $this->default_categories = 0;

if($this->catsel_params->get('tmpl_params.first_element')) {
	array_unshift($options, JHtml::_('select.option', '', JText::_($this->catsel_params->get('tmpl_params.first_element')), 'id', 'title'));
}

echo  JHtml::_('select.genericlist', $options, 'jform[category][]', $attr, 'id', 'title', $this->default_categories);


function getCategoryOptions($categories, $type, $that, $fc = 0)
{
	$close = $level = $title = $options = array();

	foreach ($categories as $cat)
	{
		if(end($close) && (end($level) >= $cat->level))
		{
			$options[] = end($title);
			
			array_pop($close);
			array_pop($level);
			array_pop($title);
		}

		if(in_array((int)$cat->id, $that->not_allow_cats) && !$type->params->get('category_limit.show_restricted'))
		{
			continue;
		}

		if($cat->params->get('submission', 1))
		{
			$disabled = in_array($cat->id, $that->not_allow_cats) && $type->params->get('category_limit.show_restricted') ? true : false;
			$options[] = JHtml::_('select.option', $cat->id, str_repeat('|-- ', ($cat->level - 1 + $fc)).$cat->title, 'id', 'title', $disabled);
		}
		else	
		{
			$options[] = JHTMLJoomcck::optgroup(str_repeat('|-- ', ($cat->level - 1 + $fc)).$cat->title, 'id', 'title');
			
			$close[] = TRUE;
			$level[] = $cat->level;
			$title[] = JHTMLJoomcck::optgroup(str_repeat('|-- ', ($cat->level - 1 + $fc)).$cat->title, 'id', 'title');
		}
		if(isset($cat->children) && count($cat->children))
		{
			$opts = getCategoryOptions($cat->children, $type, $that, $fc);
			$options = array_merge($options, $opts);
		}
	}
	return $options;
}
