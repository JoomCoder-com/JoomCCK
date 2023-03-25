<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

define('SPR', $this->params->get('delimiter'));
define('ENCL', $this->params->get('enclosed'));

foreach($view->items as $item)
{
	$line['title']     = $item->title;
	$line['created']   = $item->ctime;
	$line['modified']  = $item->mtime;
	$line['expired']   = $item->extime;
	$line['access']    = $item->access;
	$line['published'] = $item->published;
	$line['rating']    = $item->votes_result;
	$line['hits']      = $item->hits;

	$cats = $ids = array();
	foreach($item->categories AS $id => $cat)
	{
		$cats[] = $cat;
		$ids[]  = $id;
	}

	if($ids)
	{
		$line['categories'] = implode(',', $cats);
		$line['cat_ids']    = implode(',', $ids);

	}

	foreach($item->fields_by_id as $field)
	{
		$key = $field->params->get('core.xml_tag_name', $field->label);

		$value = $this->_getVal($field->value, $field->params->get('params.separator', $this->params->get('field_glue', ', ')));
		$value = str_replace("\n", "\\n", $value);

		if(ENCL == '"')
		{
			$value = str_replace('"', '""', $value);
		}

		$line[$key] = $this->params->get('strip_html') ? strip_tags($value) : $value;
	}


	$lines[] = ENCL . implode(ENCL . SPR . ENCL, $line) . ENCL;
}

$map = function ($value)
{
	return ucfirst(JText::_($value));
};

echo ENCL . implode(ENCL . SPR . ENCL, array_map($map, array_keys($line))) . ENCL . "\r\n";
echo implode("\r\n", $lines);
