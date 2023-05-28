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

class JHTMLTypes
{
	public static function select($list, $default = array())
	{
		if(!$list)
			return;

		return JHtml::_('select.genericlist', $list, 'filters[type]', 'class="form-select"', 'id', 'name', $default);
	}

	public static function checkbox($list, $stypes, $default)
	{

		if(!$list)
		{
			return;
		}


		$out[] = '<div class="mb-3">';
		foreach($list AS $type)
		{
			$out[] = '<div class="form-check">';
			$out[] = '<input class="form-check-input" id="type-' . $type . '" type="checkbox" name="filters[type][]" value="' . $type . '" ' . $stypes[$type]->filter_checked . '>';
			$out[] = '<label class="form-check-label" for="type-' . $type . '">'.$stypes[$type]->name . '</label></div>';
		}
		$out[] = '</div>';

		return implode("\n", $out);
	}

	public static function toggle($list, $stypes, $default)
	{
		if(!$list)
		{
			return;
		}

		ArrayHelper::clean_r($default);
		$default = \Joomla\Utilities\ArrayHelper::toInteger($default);


		$out[] = '<div>';
		foreach($list AS $type)
		{
			$out[] = '<div class="mb-2"><input class="btn-check" id="type-' . $type . '" autocomplete="off" type="checkbox" name="filters[type][]" value="' . $type . '" ' . $stypes[$type]->filter_checked . '>';
			$out[] = '<label class="btn btn-outline-success" for="type-' . $type . '">'.$stypes[$type]->name . '</label></div>';
		}
		$out[] = '</div>';

		return implode("\n", $out);

		return implode(' ', $out);
	}
}