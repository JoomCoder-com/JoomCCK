<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomla\CMS\Layout\LayoutHelper;

defined('_JEXEC') or die();

class JHTMLMrelements
{
	public static function sort($title, $order, $direction = 'asc', $selected = 0, $task = NULL, $new_direction = 'asc', $tip = '')
	{
		$direction = strtolower($direction);
		$icon      = array('arrow-down', 'arrow-up');
		$index     = (int)($direction == 'desc');

		if($order != $selected)
		{
			$direction = $new_direction;
		}
		else
		{
			$direction = ($direction == 'desc') ? 'asc' : 'desc';
		}

		$html = '<a class="dropdown-item" href="javascript:void(0);" onclick="Joomla.tableOrdering(\'' . $order . '\',\'' . $direction . '\',\'' . $task . '\');">';
		$html .= \Joomla\CMS\Language\Text::_($title);

		if($order == $selected)
		{
			$html .= ' <i class="icon-' . $icon[$index] . '"></i>';
		}

		$html .= '</a>';

		return $html;
	}

	/**
	 * AJAX Category selector
	 *
	 * @param string $name    form element name
	 * @param mixed  $section 0 start from al sections, array - start from sections in array
	 * @param array  $default deafult
	 * @param int    $limit   limit selector.
	 * @param array  $ignore  IDs of categories to ignore.
	 */
	public static function catselector($name, $section, $default, $limit = 0, $ignore = array())
	{

		$lang = \Joomla\CMS\Factory::getLanguage();
		$lang->load('com_joomcck', JPATH_ROOT);

		$db = \Joomla\CMS\Factory::getDbo();

		if(!$section)
		{
			$db->setQuery("SELECT id, name, categories FROM #__js_res_sections WHERE published = 1 AND categories > 0");
			$sections = $db->loadObjectList();
		}
		else
		{
			settype($section, 'array');

			$db->setQuery("SELECT c.id, c.title, c.path, CONCAT(s.name, '/', c.path), c.params, c.section_id, s.name AS section_name,
				(SELECT count(id) FROM #__js_res_categories WHERE parent_id = c.id)  as children
    			FROM #__js_res_categories AS c
				LEFT JOIN #__js_res_sections AS s ON s.id = c.section_id
				WHERE c.published = 1 AND c.section_id IN (" . implode(',', $section) . ") AND c.parent_id = 1  ORDER BY c.lft ASC");
			$categories = $db->loadObjectList();
			foreach($categories as &$category)
			{
				$category->params = new \Joomla\Registry\Registry($category->params);
				$category->title  = htmlentities($category->title, ENT_QUOTES, 'UTF-8');
				$category->path   = htmlentities($category->path, ENT_QUOTES, 'UTF-8');
			}
		}

		ArrayHelper::clean_r($default);
		$default = \Joomla\Utilities\ArrayHelper::toInteger($default);

		if($default)
		{
			$db->setQuery("SELECT c.id, c.title, CONCAT(s.name, '/', c.path) AS path
			FROM #__js_res_categories AS c
			LEFT JOIN #__js_res_sections AS s ON s.id = c.section_id WHERE c.id IN (" . implode(',', $default) . ")");
			$defaults = $db->loadObjectList();
		}

		settype($ignore, 'array');

		ob_start();
		include_once 'mrelements/catselector.php';
		$out = ob_get_contents();
		ob_end_clean();
        
		$document = \Joomla\CMS\Factory::getDocument();
		$document->addStyleSheet(\Joomla\CMS\Uri\Uri::root(TRUE) . '/components/com_joomcck/library/php/html/mrelements/catselector.css');
        
		return $out;
	}
	
	public static function flow($name = 'filecontrol', $files = NULL, $options = array(), $field = NULL)
    {
        $app = \Joomla\CMS\Factory::getApplication();
        $doc = \Joomla\CMS\Factory::getDocument();
		$doc->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/vendors/flow/flow.js');
		$doc->addStyleSheet(\Joomla\CMS\Uri\Uri::root(TRUE) . '/components/com_joomcck/library/php/html/mrelements/flow.css');

        $upload_url = \Joomla\CMS\Router\Route::_("index.php?option=com_joomcck&task=files.upload&tmpl=component&section_id=" . $app->input->getInt('section_id') . "&record_id=" . $app->input->getInt('id') . "&type_id=" . $app->input->getInt('type_id') . "&field_id={$field->id}&key=" . md5($name) ."&iscomment=".(int)@$field->iscomment, false);

        ob_start();
		include 'mrelements/flow.php';
		$out = ob_get_contents();
		ob_end_clean();

		return $out;
    }


	public static function autocompleteitem($html, $id = NULL)
	{
		$o = new stdClass();

		$o->id    = ($id ? $id : strip_tags($html));
		$o->html  = $html;
		$o->plain = strip_tags($html);

		return $o;
	}

	public static function pills($name, $id, $default = array(), $list = array(), $options = array(),$params = null)
	{

		if(is_null($params))
			$params = new \Joomla\Registry\Registry();
		// max items user can add
		if(isset($options['limit']))
			$options['maxItems'] = $options['limit'];
		else
			$options['maxItems'] = $params->get('params.max_items',5);

		// suggestion limit
		if(isset($options['limit']))
			$options['maxOptions'] = $options['suggestion_limit'];
		else
			$options['maxOptions'] = $params->get('params.max_result',10);


		// allow user to add
		if(isset($options['can_add']))
			$options['canAdd'] = $options['can_add'];
		else
			$options['canAdd'] = $params->get('params.only_values',0) ? 'false' : 'true';

		// allow user to delete
		$options['canDelete'] = isset($options['can_delete']) ? $options['can_delete'] : 'false';


		// if list not used use default as list (to display selected items in dropdown
		if(empty($list)){

			foreach ($default as $iKey => $iValue){
				$iValue = (array) $iValue;
				$list[] = $iValue['id'];
			}
		}

		// prepare data
		$data = [
			'options' => $options,
			'params' => $params,
			'default' => $default,
			'list' => $list,
			'name' => $name,
			'id' => $id
		];

		//use new layout tomSelect
		return \Joomcck\Layout\Helpers\Layout::render('core.fields.tomSelect',$data);

	}
	public static function listautocomplete($name, $id, $default = array(), $list = array(), $options = array())
	{
		$params = new \Joomla\Registry\Registry();
		$params->loadArray($options);

		settype($default, 'array');

		if($params->get('only_values', 0) == 1 && !$list && !$params->get('ajax_url'))
		{
			return '<input type="hidden" name="' . $name . '" id="' . $id . '" value="" />';
		}

		$doc = \Joomla\CMS\Factory::getDocument();
		$doc->addStyleSheet(\Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/js/autocomplete/style.css');
		$doc->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/js/autocomplete/GrowingInput.js');
		$doc->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/js/autocomplete/TextboxList.js');
		$doc->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/js/autocomplete/TextboxList.Autocomplete.js');
		$doc->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/js/autocomplete/TextboxList.Autocomplete.Binary.js');

		$el     = $add = $skip = $a = array();
		$script = NULL;
		$patern = '["%s", "%s", "%s"]';

		foreach($default as $key => &$def)
		{
			if(!is_object($def))
			{
				$def = self::autocompleteitem($def, $key);
			}

			if(!$def->id)
				continue;

			$add[]  = sprintf('add("%s", "%s", "%s")', str_replace('"', '\\"', stripslashes($def->plain)), str_replace('"', '\\"', stripslashes($def->id)), str_replace('"', '\\"', stripslashes($def->html)));
			$skip[] = $def->id;
		}

		settype($list, 'array');

		foreach($list as &$item)
		{
			if(!is_object($item))
			{
				$item = self::autocompleteitem($item);
			}

			if(in_array($item->id, $skip))
				continue;
			if(!trim($item->id))
				continue;

			$el[] = sprintf($patern, str_replace('"', '\\"', stripslashes($item->id)), str_replace('"', '\\"', stripslashes($item->plain)), str_replace('"', '\\"', stripslashes($item->html)));
		}

		$a[] = "\nplaceholder: '" . \Joomla\CMS\Language\Text::_('CTYPETOSUGGEST') . "'";
		$a[] = "\nremote:{ emptyResultPlaceholder:'" . \Joomla\CMS\Language\Text::_('CNOSUGGEST') . "', loadPlaceholder:'" . \Joomla\CMS\Language\Text::_('CPLSWAIT') . "'}";
		$a[] = "\nwidth: '" . $params->get('min_width', 300) . "'";
		$a[] = "\nminLength: " . $params->get('min_length', 1);
		$a[] = "\nmaxResults: " . $params->get('max_result', 10);
		if($params->get('only_values', 0) == 1)
		{
			$a[] = "\nonlyFromValues: 1";
		}
		if($params->get('case_sensitive', 0))
		{
			$a[] = "\ninsensitive: false";
		}
		if($params->get('highlight', 0) == 0)
		{
			$a[] = "\nhighlight: false";
		}

		$additional[] = "\nplugins: {autocomplete: {" . implode(',', $a) . "}}";

		if($params->get('coma_separate', 0)) // && !count($el))
		{
			$additional[] = "\nbitsOptions : { editable : {addKeys:188}}";
		}
		if($params->get('max_items', 0))
		{
			$additional[] = "\nmax : " . $params->get('max_items', 0);
		}
		if($params->get('unique', 0))
		{
			$additional[] = "\nunique: true ";
		}
		if($params->get('separateby', 0))
		{
			$additional[] = "\n" . 'decode: function(o) {
				return o.split(\'' . $params->get('separateby') . '\');
			},
			encode: function(o) {
					return o.map(function(v) {
					v = ($chk(v[0]) ? v[0] : v[1]);
					return $chk(v) ? v : null;
				}).clean().join(\'' . $params->get('separateby') . '\');
			}';
		}

		$additional[] = "\ntexts:{ limit : '" . \Joomla\CMS\Language\Text::_('C_JSLIMITOPTIONS') . "'	}";

		$uniq    = substr(md5(time() . '-' . rand(0, 1000)), 0, 5);
		$options = '{' . implode(',', $additional) . '}';

		$html[] = '<input type="text" name="' . $name . '" id="' . $id . '" value="" />';
		$html[] = "<script type=\"text/javascript\">";
		$html[] = "var default{$uniq} = ['" . (count($skip) ? implode("','", $skip) : '') . "'];\n";
		$html[] = "var t{$uniq} = new jQuery.TextboxList('#{$id}', {$options});\n";


		$html[] = "t{$uniq}.addEvent('bitBoxRemove', function(box) {";
		if($params->get('max_items', 0))
		{
			//$html[] = "if($('#hidden-{$uniq}')) $('#hidden-{$uniq}').show();";
		}

		if($params->get('onRemove', 0))
		{
			$html[] = "
				jQuery(box).css('background-image', 'url(\"" . \Joomla\CMS\Uri\Uri::root(TRUE) . "/media/com_joomcck/img/loading.gif\")');

				jQuery.ajax({
					url:'" . \Joomla\CMS\Router\Route::_($params->get('onRemove'), FALSE) . "',
					type:'POST',
					dataType:'json',
					data:{
						rid: " . \Joomla\CMS\Factory::getApplication()->input->getInt('id') . ",
						tid: box.value[0]
					}
				}).done(function(json) {
					jQuery(box).css('background-image', '');
					if(json.success)
					{
						jQuery.each(default{$uniq}, function(key, item){
							if(box.value[0] == item)
								default{$uniq}.splice(key);
						});
						//t{$uniq}.update();
					}
					else
					{
						alert(json.error);
					}
				});";
		}
		$html[] = "});";


		$html[] = "t{$uniq}.addEvent('bitBoxAdd', function(box){
				if(box.value[0])
				{
					if(default{$uniq}.contains(box.value[0].toString()))
					{
						return;
					}
				}
			";
		if($params->get('max_items', 0))
		{
			$html[] = "var parent = $(box).parents('ul.textboxlist-bits');
			if(parent.children('li.textboxlist-bit.textboxlist-bit-box').length >= " . $params->get('max_items', 0) . ")
			{
				//parent.children('li').last().hide().attr('id', 'hidden-{$uniq}');

			}";
		}

		if($params->get('onAdd', 0))
		{
			$html[] = ($params->get('max_items', 0) ? "if(default{$uniq}.length > " . $params->get('max_items', 0) . "){ alert('" . \Joomla\CMS\Language\Text::_('CTAGLIMITREACHED') . "'); return;}" : "") . "

				jQuery(box).css('background-image', 'url(\"" . \Joomla\CMS\Uri\Uri::root(TRUE) . "/media/com_joomcck/img/loading.gif\")');

				jQuery.ajax({
					url:'" . \Joomla\CMS\Router\Route::_($params->get('onAdd'), FALSE) . "',
					type:'POST',
					dataType:'json',
					data:{
						rid: " . $params->get('record_id', 0) . ",
						val: box.value,
						max: " . $params->get('max_items', 0) . "
					}
				}).done(function(json) {
					jQuery(box).css('background-image', '');
					if(!json)
					{
						return;
					}
					if(json.success)
					{
						box.value = json.result;
						default{$uniq}.push(json.result[0]);
						//t{$uniq}.update();
					}
					else
					{
						alert(json.error);
					}
			    });";
		}
		$html[] = "});";


		if($add)
		{
			$html[] = "t{$uniq}." . implode(".", $add) . ";\n";
		}
		if($el)
		{
			$html[] = "var r{$uniq} = [" . implode(",", $el) . "];\n";
			$html[] = "t{$uniq}.plugins['autocomplete'].setValues(r{$uniq});\n";
		}
		if($params->get('ajax_url'))
		{
			//$html[] = "t{$uniq}.container.addClass('textboxlist-loading');\n";
			$html[] = "jQuery.ajax({
				url:'" . \Joomla\CMS\Router\Route::_($params->get('ajax_url'), FALSE) . "',
				type:'POST',
				dataType:'json',
				data:{" . $params->get('ajax_data') . "}
			}).done(function(json) {
				if(!json.success)
				{
					alert(json.error);
					return;
				}
				if(json.result)
				{
					//t{$uniq}.container.removeClass('textboxlist-loading');
					t{$uniq}.plugins['autocomplete'].setValues(json.result);
				}
		    });";
		}

		$html[] = "</script>\n";

		return implode("\n", $html);
	}
}