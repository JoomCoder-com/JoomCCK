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

		return JHtml::_('select.genericlist', $list, 'filters[type]', NULL, 'id', 'name', $default);
	}

	public static function checkbox($list, $stypes, $default)
	{
		$out[] = '<div class="container-fluid">';
		foreach($list AS $type)
		{
			$out[] = '<div class="col-md-3"><label class="checkbox">';
			$out[] = '<input id="type-' . $type . '" type="checkbox" name="filters[type][]" value="' . $type . '" ' . $stypes[$type]->filter_checked . '>';
			$out[] = $stypes[$type]->name . '</label></div>';
		}
		$out[] = '</div>';

		return implode("\n", $out);
	}

	public static function toggle($list, $stype, $default)
	{
		if(!$list)
		{
			return;
		}

		ArrayHelper::clean_r($default);
		\Joomla\Utilities\ArrayHelper::toInteger($default);


		foreach($list as $id => &$type)
		{
			$value = (in_array($type, $default) ? $type : NULL);
			$out[] = '<li id="type-' . $type . '" ' . ($value ? 'class="active"' : NULL) . '><a href="javascript:void(0);" rel="' . $type . '">' . $stype[$type]->name . '<input type="hidden" name="filters[type][]" id="ftp-' . $type . '"
			value="' . $value . '"></a></li>';
		}

		$html = '<ul id="types-list-filters" class="nav nav-pills">' . implode(' ', $out) . '</ul>';

		$html .= "<script>
		(function($){
			$.each($('#types-list-filters').children('li'), function(k, v) {
				$(this).bind('click', function() {
					var a = $('a', this)
					var id = a.attr('rel');
					var hf = $('#ftp-' + id);
					if(hf.val()) {
						$(this).removeClass('active');
						hf.val('');
					}
					else {
						$(this).addClass('active');
						hf.val(id);
					}
				});
			});
		}(jQuery));
		</script>";


		return $html;
	}
}