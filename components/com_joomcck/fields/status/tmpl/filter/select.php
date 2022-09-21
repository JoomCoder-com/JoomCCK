<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
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

array_unshift($list, JHtml::_('select.option', '', '- ' . JText::sprintf('ST_SELECT', $this->label) . ' -'));

echo JHtml::_('select.genericlist', $list, "filters[{$this->key}]", NULL, 'value', 'text', $this->value);
