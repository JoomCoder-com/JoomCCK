<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

if(is_array($this->value))
{
	$this->value = $this->value[0];
}

$list = array();
foreach($this->values as $k => $value)
{
	if(!$value->field_value)
	{
		continue;
	}

	$label = $this->_getVal($value->field_value);

	$list[$k]       = new stdClass();
	$list[$k]->text = $label;
	if($this->params->get('params.filter_show_number', 1))
	{
		$list[$k]->text .= " ({$value->num})";
	}
	$list[$k]->value = $value->field_value;
}

array_unshift($list, \Joomla\CMS\HTML\HTMLHelper::_('select.option', '', '- ' . \Joomla\CMS\Language\Text::sprintf('ST_SELECT', $this->label) . ' -'));

echo \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $list, "filters[{$this->key}]", NULL, 'value', 'text', $this->value);
