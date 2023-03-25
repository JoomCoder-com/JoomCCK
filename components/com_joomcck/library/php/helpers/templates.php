<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

class JoomcckTmplHelper
{
	function getTmplTypes()
	{
		return array('itemlist', 'filters', 'comments', 'article', 'rating', 'articleform', 'categoryselect', 'category', 'user_menu');
	}

	static function getTmplPath($type)
	{

		switch($type)
		{
			case 'user_menu':
			case 'itemlist':
			case 'markup':
			case 'filters':
			case 'category':
				$tmpl_path = JPATH_ROOT . '/components/com_joomcck/views/records/tmpl';
				break;
			case 'categoryselect':
				$tmpl_path = JPATH_ROOT . '/components/com_joomcck/views/form/tmpl';
				break;

			case 'rating':
				$tmpl_path = JPATH_ROOT . '/components/com_joomcck/views/rating_tmpls';
				break;

			case 'comments':
			case 'article':
				$tmpl_path = JPATH_ROOT . '/components/com_joomcck/views/record/tmpl';
				break;
			case 'articleform':
				$tmpl_path = JPATH_ROOT . '/components/com_joomcck/views/form/tmpl';
				break;

		}

		return $tmpl_path;

	}

	static public function getTmplImgSrc($type, $name)
	{
		$img_path = '';
		switch($type)
		{
			case 'markup':
				$view     = 'records';
				$name     = 'default_markup_' . $name . '.png';
				$img_path = JURI::root() . 'components/com_joomcck/views/' . $view . '/tmpl/' . $name;
				break;
			case 'user_menu':
				$view     = 'records';
				$name     = 'default_menu_' . $name . '.png';
				$img_path = JURI::root() . 'components/com_joomcck/views/' . $view . '/tmpl/' . $name;
				break;
			case 'itemlist':
				$view     = 'records';
				$name     = 'default_list_' . $name . '.png';
				$img_path = JURI::root() . 'components/com_joomcck/views/' . $view . '/tmpl/' . $name;
				break;
			case 'category':
				$view     = 'records';
				$name     = 'default_cindex_' . $name . '.png';
				$img_path = JURI::root() . 'components/com_joomcck/views/' . $view . '/tmpl/' . $name;
				break;
			case 'categoryselect':
				$view     = 'form';
				$name     = 'default_category_' . $name . '.png';
				$img_path = JURI::root() . 'components/com_joomcck/views/' . $view . '/tmpl/' . $name;
				break;
			case 'filters':
				$view     = 'records';
				$name     = 'default_filters_' . $name . '.png';
				$img_path = JURI::root() . 'components/com_joomcck/views/' . $view . '/tmpl/' . $name;
				break;
			case 'comments':
				$view     = 'record';
				$name     = 'default_comments_' . $name . '.png';
				$img_path = JURI::root() . 'components/com_joomcck/views/' . $view . '/tmpl/' . $name;
				break;
			case 'article':
				$view     = 'record';
				$name     = 'default_record_' . $name . '.png';
				$img_path = JURI::root() . 'components/com_joomcck/views/' . $view . '/tmpl/' . $name;
				break;
			case 'articleform':
				$view     = 'form';
				$name     = 'default_form_' . $name . '.png';
				$img_path = JURI::root() . 'components/com_joomcck/views/' . $view . '/tmpl/' . $name;
				break;
			case 'rating':
				$img_path = JURI::root() . 'components/com_joomcck/views/rating_tmpls/' . $name . '_img/';
				break;
		}

		return $img_path;
	}

	static public function getTmplFile($type, $name, $is_json = FALSE)
	{
		$layouts_path = JoomcckTmplHelper::getTmplPath($type);
		if($is_json)
		{
			$layouts_path = JPATH_ROOT . '/components/com_joomcck/configs';
		}
		$result = '';

		switch($type)
		{
			case 'markup':
				$result = $layouts_path . DIRECTORY_SEPARATOR . 'default_markup_' . $name;
				break;
			case 'user_menu':
				$result = $layouts_path . DIRECTORY_SEPARATOR . 'default_menu_' . $name;
				break;
			case 'category':
				$result = $layouts_path . DIRECTORY_SEPARATOR . 'default_cindex_' . $name;
				break;
			case 'categoryselect':
				$result = $layouts_path . DIRECTORY_SEPARATOR . 'default_category_' . $name;
				break;
			case 'itemlist':
				$result = $layouts_path . DIRECTORY_SEPARATOR . 'default_list_' . $name;
				break;
			case 'filters':
				$result = $layouts_path . DIRECTORY_SEPARATOR . 'default_filters_' . $name;
				break;
			case 'comments':
				$result = $layouts_path . DIRECTORY_SEPARATOR . 'default_comments_' . $name;
				break;
			case 'article':
				$result = $layouts_path . DIRECTORY_SEPARATOR . 'default_record_' . $name;
				break;
			case 'articleform':
				$result = $layouts_path . DIRECTORY_SEPARATOR . 'default_form_' . $name;
				break;
			case 'rating':
				$result = $layouts_path . DIRECTORY_SEPARATOR . 'rating_' . $name;
				break;
		}

		return $result;
	}

	static public function getTmplMask($type, $name = '.*')
	{

		$result = Array();

		switch($type)
		{
			case 'markup':
				$result['index_file'] = '^default_markup_' . $name . '\.xml$';
				$result['ident']      = array("/^(default_markup_)/", "/(\..{2,3})$/");
				$result['folder']     = '^default_markup_' . $name;
				break;
			case 'user_menu':
				$result['index_file'] = '^default_menu_' . $name . '\.xml$';
				$result['ident']      = array("/^(default_menu_)/", "/(\..{2,3})$/");
				$result['folder']     = '^default_menu_' . $name;
				break;
			case 'itemlist':
				$result['index_file'] = '^default_list_' . $name . '\.xml$';
				$result['ident']      = array("/^(default_list_)/", "/(\..{2,3})$/");
				$result['folder']     = '^default_list_' . $name;
				break;
			case 'category':
				$result['index_file'] = '^default_cindex_' . $name . '\.xml$';
				$result['ident']      = array("/^(default_cindex_)/", "/(\..{2,3})$/");
				$result['folder']     = '^default_cindex_' . $name;
				break;
			case 'categoryselect':
				$result['index_file'] = '^default_category_' . $name . '\.xml$';
				$result['ident']      = array("/^(default_category_)/", "/(\..{2,3})$/");
				$result['folder']     = '^default_category_' . $name;
				break;
			case 'filters':
				$result['index_file'] = '^default_filters_' . $name . '\.xml$';
				$result['ident']      = array("/^(default_filters_)/", "/(\..{2,3})$/");
				$result['folder']     = '^default_filters_' . $name;
				break;
			case 'comments':
				$result['index_file'] = '^default_comments_' . $name . '\.xml$';
				$result['ident']      = array("/^(default_comments_)/", "/(\..{2,3})$/");
				$result['folder']     = '^default_comments_' . $name;
				break;
			case 'article':
				$result['index_file'] = '^default_record_' . $name . '\.xml$';
				$result['ident']      = array("/^(default_record_)/", "/(\..{2,3})$/");
				$result['folder']     = '^default_record_' . $name;
				break;
			case 'articleform':
				$result['index_file'] = '^default_form_' . $name . '\.xml$';
				$result['ident']      = array("/^(default_form_)/", "/(\..{2,3})$/");
				$result['folder']     = '^default_form_' . $name;
				break;
			case 'rating':
				$result['index_file'] = '^rating_' . $name . '\.xml$';
				$result['folder']     = "^" . $name . "_img$";
				$result['ident']      = array("/^(rating_)/", "/(\..{2,3})$/");
				break;
		}


		return $result;
	}

	static public function getTmplFullMask($type, $name = '.*')
	{

		$result = Array();

		switch($type)
		{
			case 'markup':
				$result['index_file'] = '^default_markup_' . $name . '\..{2,3}$';
				$result['title']      = array("/^(default_markup_)/", "/(\..{2,3})$/");
				$result['folder']     = '^default_markup_' . $name;
				break;
			case 'user_menu':
				$result['index_file'] = '^default_menu_' . $name . '\..{2,3}$';
				$result['title']      = array("/^(default_menu_)/", "/(\..{2,3})$/");
				$result['folder']     = '^default_menu_' . $name;
				break;
			case 'itemlist':
				$result['index_file'] = '^default_list_' . $name . '\..{2,3}$';
				$result['title']      = array("/^(default_list_)/", "/(\..{2,3})$/");
				$result['folder']     = '^default_list_' . $name;
				break;
			case 'category':
				$result['index_file'] = '^default_cindex_' . $name . '\..{2,3}$';
				$result['title']      = array("/^(default_cindex_)/", "/(\..{2,3})$/");
				$result['folder']     = '^default_cindex_' . $name;
				break;
			case 'categoryselect':
				$result['index_file'] = '^default_category_' . $name . '\..{2,3}$';
				$result['title']      = array("/^(default_category_)/", "/(\..{2,3})$/");
				$result['folder']     = '^default_category_' . $name;
				break;
			case 'filters':
				$result['index_file'] = '^default_filters_' . $name . '\..{2,3}$';
				$result['title']      = array("/^(default_filters_)/", "/(\..{2,3})$/");
				$result['folder']     = '^default_filters_' . $name;
				break;
			case 'comments':
				$result['index_file'] = '^default_comments_' . $name . '\..{2,3}$';
				$result['title']      = array("/^(default_comments_)/", "/(\..{2,3})$/");
				$result['folder']     = '^default_comments_' . $name;
				break;
			case 'article':
				$result['index_file'] = '^default_record_' . $name . '\..{2,3}$';
				$result['title']      = array("/^(default_record_)/", "/(\..{2,3})$/");
				$result['folder']     = '^default_record_' . $name;
				break;
			case 'articleform':
				$result['index_file'] = '^default_form_' . $name . '\..{2,3}$';
				$result['title']      = array("/^(default_form_)/", "/(\..{2,3})$/");
				$result['folder']     = '^default_form_' . $name;
				break;
			case 'rating':
				$result['index_file'] = '^rating_' . $name . '\..{2,3}$';
				$result['folder']     = "^" . $name . "_img$";
				$result['title']      = array("/^(rating_)/", "/(\..{2,3})$/");
				break;
		}


		return $result;
	}

	static public function partName($name)
	{
		$res = NULL;
		if(preg_match("/\[([^]]+)\],\[([^]]+)\]*/i", $name, $found))
		{
			$res['name'] = $found[1];
			switch($found[2])
			{
				case 'markup':
					$res['type'] = 'default_markup_';
					break;
				case 'user_menu':
					$res['type'] = 'default_menu_';
					break;
				case 'category':
					$res['type'] = 'default_cindex_';
					break;
				case 'categoryselect':
					$res['type'] = 'default_category_';
					break;
				case 'itemlist':
					$res['type'] = 'default_list_';
					break;
				case 'filters':
					$res['type'] = 'default_filters_';
					break;
				case 'comments':
					$res['type'] = 'default_comments_';
					break;
				case 'article':
					$res['type'] = 'default_record_';
					break;
				case 'articleform':
					$res['type'] = 'default_form_';
					break;
				case 'rating':
					$res['type'] = 'rating_';
					break;
			}
		}

		return $res;
	}
}

?>