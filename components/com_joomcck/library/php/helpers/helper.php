<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class MapHelper
{

	public static function loadGoogleMapAPI()
	{
		static $loaded = NULL;

		if($loaded == 1)
		{
			return;
		}

		$app     = JFactory::getApplication();
		$map_key = JComponentHelper::getParams('com_joomcck')->get('map_key');
		list($lang, $region) = explode('-', JFactory::getLanguage()->getTag());

		JFactory::getDocument()->addScript('//maps.googleapis.com/maps/api/js?language=' . $lang . '&region=' . $region . '&key=' . $map_key . '&sensor=true&libraries=weather,panoramio,visualization,places');

		$loaded = 1;
	}
}
class JoomcckCommentHelper
{

	public static function laded($id)
	{
		static $included = array();
		if(array_key_exists($id, $included)) {
			return TRUE;
		}
		$included[$id] = 1;

		return FALSE;
	}
}

class JoomcckFilter {
	static public function base64($string) {
		$b = base64_decode($string);
		$c = strip_tags($b);

		if(strlen($c) != strlen($b))
		{
			return '';
		}

		return $b;
	}

	static public function in($string)
	{
		$string = preg_replace("/[^0-9,]*/iU", "", $string);

		while(strpos($string, ',,') !== FALSE) {
			$string = str_replace(',,', ',', $string);
		}

		$string = preg_replace("/,$/iU", '', $string);
		$string = preg_replace("/^,/iU", '', $string);

		/*if($string == '') {
			$string = 0;
		}*/

		return $string;
	}
}

class CSubscriptionsHelper
{

	static public function auto_subscribe($record, $section_id)
	{
		$db = JFactory::getDbo();

		$db->setQuery('SELECT user_id FROM `#__js_res_user_options_autofollow` WHERE section_id = ' . $section_id);
		$user_list = $db->loadColumn();
		foreach($user_list as $user_id)
		{
			self::subscribe_record($record, $user_id);
		}
	}

	static public function unsubscribe_record($id)
	{
		$user = JFactory::getUser();

		if(!$user->get('id'))
		{
			return;
		}

		$record = JTable::getInstance('Record', 'JoomcckTable');
		$record->load($id);

		$data = array(
			'user_id'    => $user->get('id'),
			'type'       => 'record',
			'ref_id'     => $id,
			'section_id' => $record->section_id
		);

		$table = JTable::getInstance('Subscribe', 'JoomcckTable');
		$table->load($data);

		if($table->id)
		{
			$table->delete();
		}

		$record->onFollow();
	}

	static public function subscribe_record($id, $user_id = NULL)
	{
		$user_id = $user_id ? $user_id : NULL;
		$user    = JFactory::getUser($user_id);
		if(!$user->get('id'))
		{
			return;
		}

		$record = $id;
		if(!is_object($id))
		{
			$record = JTable::getInstance('Record', 'JoomcckTable');
			$record->load($id);
		}

		if(!in_array($record->access, $user->getAuthorisedViewLevels()) && !($record->user_id == $user->get('id')))
		{
			return;
		}

		$section = ItemsStore::getSection($record->section_id);
		if(!$section->params->get('events.subscribe_record'))
		{
			return;
		}

		$data = array(
			'user_id'    => $user->get('id'),
			'type'       => 'record',
			'ref_id'     => $record->id,
			'section_id' => $record->section_id
		);

		$table = JTable::getInstance('Subscribe', 'JoomcckTable');
		$table->load($data);

		if(!$table->id)
		{
			$table->save($data);
		}
		$table->reset();

		$record->onFollow();
	}
}

class AttachmentHelper
{

	/**
	 *
	 *
	 * Enter description here ...
	 *
	 * @param array $list
	 *            list of saved attachments
	 */
	public static function getAttachments($list, $show_hits = FALSE)
	{
		if(!$list)
		{
			return array();
		}
		if(is_string($list))
		{
			$list = json_decode($list);
		}

		ArrayHelper::clean_r($list);

		if($show_hits)
		{
			$in = array();
			foreach($list as $attach)
			{
				settype($attach, 'array');
				$in[] = $attach['id'];
			}

			if($in)
			{
				$files = JTable::getInstance('Files', 'JoomcckTable');
				$list  = $files->getFiles($in, 'id');
			}
		}
		foreach($list as &$file)
		{
			if(is_array($file))
			{
				$file = \Joomla\Utilities\ArrayHelper::toObject($file);
			}
			$file->url = JURI::root(TRUE) . '/index.php?option=com_joomcck&task=files.download_attach&tmpl=component&id=' . $file->id;
		}

		return $list;
	}
}

class FieldHelper
{

	public function __construct($keys, $fields)
	{
		$this->keys   = $keys;
		$this->fields = $fields;
	}

	public function isExists($key)
	{
		return isset($this->fields[$key]);
	}

	public function hasIcon($key)
	{
		return $this->fields[$key]->params->get('core.icon', 0);
	}

	public function label($key)
	{
		return $this->fields[$key]->label;
	}

	public function result($key)
	{
		return $this->fields[$key]->result;
	}

	public function icon($key)
	{
		return $this->fields[$key]->params->get('core.icon', 0);
	}

	public static function sortName($field)
	{
		return sprintf('field^%s^%s', $field->key, $field->params->get('params.ordering_mode', 'digits'));
	}
	static public function loadLang($field_name) {
		$lang = JFactory::getLanguage();
		$tag  = $lang->getTag();
		if($tag != 'en-GB')
		{
			if(!JFile::exists(JPATH_BASE . "/language/{$tag}/{$tag}.com_joomcck_field_{$field_name}.ini"))
			{
				$tag == 'en-GB';
			}
		}

		$lang->load('com_joomcck_field_' . $field_name, JPATH_ROOT, $tag, TRUE);
		if(JFile::exists(JPATH_ROOT.'/components/com_joomcck/fields/'.$field_name.'/language/'.$tag.'.com_joomcck_field_'.$field_name.'.ini')){
		} 
	}
}

class MetaHelper
{

	public static function setMeta($meta = array())
	{
		$doc = JFactory::getDocument();

		foreach($meta as $key => $value)
		{
			if($value)
			{
				$doc->setMetaData($key, Mint::_($value));
			}
		}
	}
}

class WornHelper
{

	public static function getItem($name, $label, $value, $text = NULL)
	{
		$o        = new stdClass();
		$o->name  = $name;
		$o->label = JText::_($label);
		$o->text  = $text ? $text : $value;
		$o->value = $value;

		return $o;
	}
}

// here was ItemsStore class

class AjaxHelper
{

	public static function error($msg)
	{
		$out = array(
			'success' => 0,
			'error'   => $msg
		);
		echo json_encode($out);
		JFactory::getApplication()->close();
	}

	public static function send($result, $key = 'result')
	{
		$out = array(
			'success' => 1,
			$key      => $result
		);
		echo json_encode($out);
		JFactory::getApplication()->close();
	}
}

class ArrayHelper
{

	public static function trim_r(&$array)
	{
		if(!is_array($array))
		{
			settype($array, 'array');
		}

		foreach($array as &$val)
		{
			if(is_array($val))
			{
				$val = self::trim_r($val);
			}
			elseif(is_string($val))
			{
				$val = trim($val);
			}
		}
	}

	public static function clean_r(&$array, $zero = FALSE)
	{
		if(!is_array($array))
		{
			settype($array, 'array');
		}

		foreach($array as $k => &$val)
		{
			if(is_array($val))
			{
				self::clean_r($val);
			}
			elseif(is_string($val))
			{
				$val = trim($val);
				$val = str_replace("\r", "", $val);
			}

			if(($val != "0" && empty($val)) || ($val == "0" && $zero))
			{
				unset($array[$k]);
				continue;
			}
		}
		if(!$array)
		{
			$array = array();
		}
	}

	public static function tolower_r(&$array)
	{
		if(!is_array($array))
		{
			settype($array, 'array');
		}
		foreach($array as $k => &$val)
		{
			$val = \Joomla\String\StringHelper::strtolower($val);
		}
		if(!$array)
		{
			$array = array();
		}
	}

	public static function separate_r(&$array, $separator = '^')
	{
		if(!is_array($array))
		{
			settype($array, 'array');
		}
		foreach($array as $k => &$val)
		{
			$val = explode($separator, $val);
			$val = $val[0];
		}
		if(!$array)
		{
			$array = array();
		}
	}
}

class CensorHelper
{

	private static $params;

	private static $bad_words;

	private static $replacer;

	public static function cleanText($str)
	{
		if(empty($str))
		{
			return $str;
		}
		if(!self::$params)
		{
			self::$params    = JComponentHelper::getParams('com_joomcck');
			self::$bad_words = explode(',', self::$params->get('censor_words'));
			ArrayHelper::trim_r(self::$bad_words);
			self::$replacer = JText::_(trim(self::$params->get('censor_replace')));
		}
		if(!self::$params->get('censor'))
		{
			return $str;
		}
		if(is_array($str))
		{
			foreach($str as $key => $value)
			{
				if(is_array($value))
				{
					self::cleanText($value);
				}
				$str[$key] = str_ireplace(self::$bad_words, self::$replacer, $value);
			}

			return $str;
		}

		return str_ireplace(self::$bad_words, self::$replacer, $str);
	}
}

function list_controls($ctrl)
{
	if(!$ctrl)
	{
		return;
	}

	$out = '';

	foreach($ctrl as $key => $link)
	{
		if(is_array($link))
		{
			$out .= '<li class="option-link dropdown-submenu">' . $key;
			$out .= '<ul class="dropdown-menu">';
			$out .= list_controls($link);
			$out .= "</ul></li>";
		}
		else
		{
			$out .= "<li class='option-link'>{$link}</li>";
		}
	}

	return $out;
}