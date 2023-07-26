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
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckfield.php';

JHTML::_('bootstrap.tooltip', '*[rel^="tooltip"]');

class JFormFieldCGeo extends CFormField
{

	public $map_key;
	public $email;
	public $markers;
	public $section_id;


	public function getInput()
	{
		if(in_array($this->params->get('params.map_marker'), $this->user->getAuthorisedViewLevels()))
		{
			MapHelper::loadGoogleMapAPI();
			JFactory::getDocument()->addScript(JUri::root(TRUE) . '/components/com_joomcck/fields/geo/assets/geo.js?1=5');
		}

		$this->user    = JFactory::getUser();
		$this->map_key = JComponentHelper::getParams('com_joomcck')->get('map_key');

		return $this->_display_input();
	}

	public function onJSValidate()
	{
		$js = '';
		if(in_array($this->params->get('params.map_marker'), $this->user->getAuthorisedViewLevels()) && $this->params->get('params.map_require'))
		{
			MapHelper::loadGoogleMapAPI();

			$js .= sprintf("
			var lat%d = jQuery('#f%d_position_lat').val();
			var lng%d = jQuery('#f%d_position_lng').val();
			if(!lat%d || !lng%d){hfid.push(%d); isValid = false; errorText.push('%s');}", $this->id, $this->id, $this->id, $this->id, $this->id, $this->id, $this->id, JText::_('G_POSITIONREQUIRED'));
		}

		$links = self::getAditionalinks();
		foreach($links as $name => $val)
		{
			if($this->params->get("params.links.{$name}.show") && $this->params->get("params.links.{$name}.req"))
			{
				$js .= sprintf("\n\t\tif(!jQuery('#f%d_links_%s').val() || jQuery('#f%d_links_%s').val() == 'http://'){hfid.push(%d); isValid = false; errorText.push('%s');}", $this->id, $name, $this->id, $name, $this->id,
					htmlentities(sprintf(JText::_("CFIELDREQUIRED"), $links[$name]['label']), ENT_COMPAT, 'UTF-8'));
			}
		}
		$contacts = self::getAditionalFields();
		foreach($contacts as $name => $val)
		{
			if($this->params->get("params.contacts.{$name}.show") && $this->params->get("params.contacts.{$name}.req"))
			{
				$js .= sprintf("\n\t\tif(!jQuery('#f%d_contacts_%s').val()){hfid.push(%d); isValid = false; errorText.push('%s');}", $this->id, $name, $this->id, htmlentities(sprintf(JText::_("CFIELDREQUIRED"), $contacts[$name]['label']), ENT_COMPAT, 'UTF-8'));
			}
		}
		$address = self::getAddressFields();
		foreach($address as $name => $val)
		{
			if($this->params->get("params.address.{$name}.show") && $this->params->get("params.address.{$name}.req"))
			{
				$js .= sprintf("\n\t\tif(!jQuery('#f%d_address_%s').val()){hfid.push(%d); isValid = false; errorText.push('%s');}", $this->id, $name, $this->id, htmlentities(sprintf(JText::_("CFIELDREQUIRED"), $address[$name]['label']), ENT_COMPAT, 'UTF-8'));
			}
		}

		return $js;
	}

	public function validateField($value, $record, $type, $section)
	{
		$links    = self::getAditionalinks();
		$contacts = self::getAditionalFields();
		$address  = self::getAddressFields();

		$this->_validate_group($value, 'links', $links);
		$this->_validate_group($value, 'contacts', $contacts);
		$this->_validate_group($value, 'address', $address);

		// check URLS to be entered correctly.
		if(isset($value['links']))
		{
			foreach($value['links'] as $name => $link)
			{
				if(!trim($link))
				{
					continue;
				}
				if(trim($link) == 'http://')
				{
					continue;
				}
				if(!filter_var($link, FILTER_VALIDATE_URL))
				{
					$this->setError(JText::sprintf("G_URLINCORRECT", $links[$name]['label']));
				}
			}
		}

		if(isset($value['contacts']))
		{
			foreach($value['contacts'] as $name => $contact)
			{
				if(!trim($contact))
				{
					continue;
				}

				if(!empty($contacts[$name]['filter']) && !filter_var($contact, $contacts[$name]['filter']))
				{
					$this->setError(JText::sprintf("G_FORMATINCORRECT", $contacts[$name]['label']));
				}
				if(!empty($contacts[$name]['preg']) && !preg_match('/^' . $contacts[$name]['preg'] . '/', $contact))
				{
					$this->setError(JText::sprintf("G_FORMATINCORRECT", $contacts[$name]['label']));
				}
			}
		}

		if($this->params->get('params.map_marker'))
		{

		}

		parent::validateField($value, $record, $type, $section);
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		if(!empty($value['address']) && is_array($value['address']))
		{
			return implode(', ', $value['address']);
		}
	}

	public function isFilterActive()
	{
		if(!empty($this->value["radius"]["position"]["lat"]))
		{
			return TRUE;
		}
		if(!empty($this->value["country"]))
		{
			return TRUE;
		}
		if(!empty($this->value["marker"]["name"]))
		{
			return TRUE;
		}

		return FALSE;
	}

	public function onFilterWornLabel($section)
	{
		if(!$this->value)
		{
			return;
		}
		$value   = $this->value;
		$user    = JFactory::getUser();
		$out     = NULL;
		$default = new JRegistry($value);
		if(in_array($this->params->get('params.filter_distance', 0), $user->getAuthorisedViewLevels()))
		{
			if($default->get('bounds.sw_lat', 0) && $default->get('bounds.ne_lat', 0))
			{
				$out = sprintf(JText::_("G_DISTANCEWORNLABEL"), $value['radius'], $value['dist_type'], $default->get('position.address'));
			}
		}
		if(in_array($this->params->get('params.filter_address', 0), $user->getAuthorisedViewLevels()) && $this->params->get('params.address.country.show'))
		{
			$addr = array();

			if(!empty($value['country']))
			{
				$addr[] = $this->_getcountryname($value['country']);
			}
			if(!empty($value['state']))
			{
				$addr[] = $value['state'];
			}
			if(!empty($value['city']))
			{
				$addr[] = $value['city'];
			}

			if($addr)
			{
				$out = implode('/', $addr);
			}
		}
		if(in_array($this->params->get('params.filter_marker', 0), $user->getAuthorisedViewLevels()))
		{
			if($default->get('marker.name'))
			{
				$out = '<img align="absmiddle" src="' . JURI::root(TRUE) . '/components/com_joomcck/fields/geo/markers/' . $this->params->get('params.map_icon_src.dir', 'google') . '/' . $default->get('marker.name') . '" />';
				$out .= ' ' . $this->_getMarkerName($default->get('marker.name'));
			}
		}

		return $out;
	}

	public function _getMarkerName($name)
	{
		$ext   = JFile::getExt($name);
		$label = str_replace('.' . $ext, '', $name);
		$label = str_replace(array(
			'-',
			'_'
		), '_', $label);
		$label = JText::_(ucwords($label));

		return $label;
	}

	public function onFilterWhere($section, &$query)
	{
		$user = JFactory::getUser();
		if(!$this->value)
		{
			return;
		}

		$default = new JRegistry($this->value);

		if(in_array($this->params->get('params.filter_distance', 0), $user->getAuthorisedViewLevels()))
		{
			if($default->get('bounds.sw_lat', 0) && $default->get('bounds.ne_lat', 0))
			{
				$records = array();
				$sql     = "SELECT record_id FROM #__js_res_record_values WHERE value_index = 'lat' AND field_value BETWEEN " . $default->get('bounds.sw_lat', 0) . " AND " . $default->get('bounds.ne_lat', 0);
				$ids     = $this->getIds($sql);
				if($ids)
				{
					$sql     = "SELECT record_id FROM #__js_res_record_values WHERE value_index = 'lng' AND field_value BETWEEN " . $default->get('bounds.sw_lng', 0) . " AND " . $default->get('bounds.ne_lng', 0) . " AND record_id IN (" . implode(',', $ids) . ")";
					$records = $this->getIds($sql);
				}

				return $records;
			}
		}

		if(in_array($this->params->get('params.filter_address', 0), $user->getAuthorisedViewLevels()) && $this->params->get('params.address.country.show'))
		{
			if(!is_array($this->value))
			{
				return NULL;
			}
			$this->section_id = $section->id;

			if($default->get('city'))
			{
				$inner = $this->_getCity($default->get('country'), $default->get('state'), $default->get('city'));
			}
			elseif($default->get('state'))
			{
				$inner = $this->_getState($default->get('country'), $default->get('state'));
			}
			elseif($default->get('country'))
			{
				$inner = $this->_getCountry($default->get('country'));
			}

			if(!empty($inner))
			{
				$ids = $this->getIds($inner);

				return $ids;
			}
		}
		if(in_array($this->params->get('params.filter_marker', 0), $user->getAuthorisedViewLevels()))
		{
			if($default->get('marker.name'))
			{

				$sql = "SELECT record_id from #__js_res_record_values WHERE field_key = '{$this->key}' AND value_index = 'marker' AND field_value = '" . $default->get('marker.name') . "'";
				$ids = $this->getIds($sql);

				return $ids;
			}
		}

		return FALSE;
	}

	public function onRenderFilter($section, $module = FALSE)
	{
		$this->user    = JFactory::getUser();
		$this->map_key = JComponentHelper::getParams('com_joomcck')->get('map_key');
		$this->markers = array();

		if(in_array($this->params->get('params.filter_marker', 0), $this->user->getAuthorisedViewLevels()))
		{
			$db  = JFactory::getDbo();
			$sql = "SELECT DISTINCT field_value FROM #__js_res_record_values
					WHERE value_index = 'marker' AND field_key = '{$this->key}' AND section_id = {$section->id}";
			$db->setQuery($sql);
			$this->markers = $db->loadColumn();
		}

		return $this->_display_filter($section, $module);
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		return $value;
	}

	public function onStoreValues($validData, $record)
	{
		$out  = array();
		$lang = JFactory::getLanguage();

		if(!empty($this->value['address']['country']))
		{
			$out['country'] = $this->value['address']['country'];
		}
		if(!empty($this->value['address']['zip']))
		{
			$out['zip'] = $this->value['address']['zip'];
		}
		if(!empty($this->value['address']['city']))
		{
			$out['city'] = $this->value['address']['city'];
			//$out['city_trans'] = $lang->transliterate($this->value['address']['city']);
		}
		if(!empty($this->value['address']['state']))
		{
			$out['state'] = $this->value['address']['state'];
			//$out['state_trans'] = $lang->transliterate($this->value['address']['state']);
		}
		if(!empty($this->value['position']['lat']))
		{
			$out['lat'] = $this->value['position']['lat'];
		}
		if(!empty($this->value['position']['lng']))
		{
			$out['lng'] = $this->value['position']['lng'];
		}
		if(!empty($this->value['position']['marker']) && !empty($this->value['position']['lng']) && !empty($this->value['position']['lat']))
		{
			$out['marker'] = $this->value['position']['marker'];
		}

		return $out;
	}

	public function onRenderFull($record, $type, $section)
	{
		return $this->_render(2, $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		return $this->_render(1, $record, $type, $section);
	}

	private function _render($client, $record, $type, $section)
	{
		static $data = array();

		$mecard = array();
		$key    = $client . '-' . $record->id . '-' . $this->id;
		if(isset($data[$key]))
		{
			return $data[$key];
		}

		$this->user    = JFactory::getUser();
		$this->map_key = JComponentHelper::getParams('com_joomcck')->get('map_key');

		$email = NULL;
		if($this->params->get('params.qr_code_address'))
		{
			$db = JFactory::getDbo();
			$db->setQuery("SELECT field_value FROM #__js_res_record_values WHERE field_type = 'email' AND record_id = {$record->id}");
			$email = $db->loadResult();
		}
		$this->email = $email;

		if(in_array($this->params->get('params.map_client'),
				array(
					$client, 3
				)) && in_array($this->params->get('params.map_view'), $this->user->getAuthorisedViewLevels()) && !empty($this->value['position']['lat']) && !empty($this->value['position']['lng'])
		)
		{
			MapHelper::loadGoogleMapAPI();
		}

		$client = ($client == 1 ? 'list' : 'full');

		$data[$key] = $this->_display_output($client, $record, $type, $section);

		return $data[$key];
	}

	public function _title($name, $client)
	{
		if(!in_array($this->params->get("params.adr_title"),
			array(
				$client,
				3
			))
		)
		{
			return;
		}

		return '<h4>' . $name . '</h4>';
	}

	public function _input($group, $name, $type = 'text', $span = 12)
	{
		return sprintf('<input class="form-control col-md-%d" id="f%d_%s_%s" type="%s" name="jform[fields][%d][%s][%s]" value="%s" %s />',
			$span, $this->id, $group, $name, $type, $this->id, $group, $name,
			(isset($this->value[$group][$name]) ? $this->value[$group][$name] : ($group == 'links' ? 'http://' : '')),
			($this->params->get("params.{$group}.{$name}.req") ? ' class="required" required="required"' : NULL));
	}

	public function _input_f($group, $name, $type = 'text')
	{
		return sprintf('<input id="f%d_%s_%s" style="width:100%%" type="%s" name="filters[%s][%s][%s]" value="%s" %s />',
			$this->id, $group, $name, $type, $this->key, $group, $name, @$this->value[$group][$name], ($this->params->get("params.{$group}.{$name}.req") ? ' class="required" required="required"' : NULL));
	}

	public function _label($group, $name, $label)
	{
		if($this->params->get("params.{$group}.{$name}.req"))
		{
			$out[] = JHtml::image(JURI::root() . 'media/com_joomcck/icons/16/asterisk-small.png', 'Required', array(
				'rel'                 => 'tooltip',
				'data-bs-title' => JText::_('CREQUIRED')
			));
		}
		$out[] = JText::_($label);

		return implode(' ', $out);
	}

	private function _validate_group($value, $group, $array)
	{
		foreach($array as $name => $val)
		{
			if(!$this->params->get("params.{$group}.{$name}.show"))
			{
				continue;
			}

			if($this->params->get("params.{$group}.{$name}.req") && empty($value[$group][$name]))
			{
				$this->setError(JText::sprintf("CFIELDREQUIRED", $array[$name]['label']));
			}
		}
	}

	public static function getAddressFields()
	{
		return array(
			'company'  => array(
				'label' => JText::_('G_CONPANY')
			),
			'person'   => array(
				'label' => JText::_('G_CONTACTPERSON'),
				'icon'  => JURI::root() . 'components/com_joomcck/fields/geo/icons/person.png'
			),
			'address1' => array(
				'label' => JText::_('G_ADDRESS1')
			),
			'address2' => array(
				'label' => JText::_('G_ADDRESS2')
			),
			'city'     => array(
				'label' => JText::_('G_CITY')
			),
			'state'    => array(
				'label' => JText::_('G_STATE')
			),
			'zip'      => array(
				'label' => JText::_('G_ZIP')
			),
			'country'  => array(
				'label' => JText::_('G_COUNTRY')
			)
		);

	}

	public static function getAditionalFields()
	{
		$out = array(
			'tel'    => array(
				'label'  => JText::_('G_TEL'),
				'patern' => '[VALUE]',
				'icon'   => JURI::root() . 'components/com_joomcck/fields/geo/icons/phone.png',
				'preg'   => '[0-9\(\)\. \-\+\#]*'
			),
			'mob'    => array(
				'label'  => JText::_('G_MOBILE'),
				'patern' => '[VALUE]',
				'icon'   => JURI::root() . 'components/com_joomcck/fields/geo/icons/mobile.png',
				'preg'   => '[0-9\(\)\. \+\#]*'
			),
			'fax'    => array(
				'label'  => JText::_('G_FAX'),
				'patern' => '[VALUE]',
				'icon'   => JURI::root() . 'components/com_joomcck/fields/geo/icons/fax.png',
				'preg'   => '[0-9\(\)\. \-\+\#]*'
			),
			'skype'  => array(
				'label'  => JText::_('G_SKYPE'),
				'patern' => '<a href="skype:[VALUE]?call">[VALUE]</a>',
				'icon'   => 'http://mystatus.skype.com/smallicon/[VALUE]'
			)
		);

		if(JFile::exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'additional.php'))
		{
			include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'additional.php';
		}

		if(isset($contacts) && is_array($contacts))
		{
			$out = array_merge($out, $contacts);
		}

		return $out;

	}

	public static function getAditionalinks()
	{
		$out = array(
			'web'         => array(
				'label' => JText::_('G_SITE'),
				'icon'  => JURI::root() . 'components/com_joomcck/fields/geo/icons/web.png'
			),
			'facebook'    => array(
				'label' => JText::_('G_FACEBOOK'),
				'icon'  => JURI::root() . 'components/com_joomcck/fields/geo/icons/fb.png'
			),
			'twitter'     => array(
				'label' => JText::_('G_TWITTER'),
				'icon'  => JURI::root() . 'components/com_joomcck/fields/geo/icons/twitter.png'
			),
			'linkin'      => array(
				'label' => JText::_('G_LINKIN'),
				'icon'  => JURI::root() . 'components/com_joomcck/fields/geo/icons/linkedin.png'
			),
			'youtube'     => array(
				'label' => JText::_('G_YOUTUBE'),
				'icon'  => JURI::root() . 'components/com_joomcck/fields/geo/icons/youtube.png'
			),
			'odnoclasnik' => array(
				'label' => JText::_('G_ODNOKLASSNIKI'),
				'icon'  => JURI::root() . 'components/com_joomcck/fields/geo/icons/od.png'
			)
		);

		if(JFile::exists(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'additional.php'))
		{
			include dirname(__FILE__) . DIRECTORY_SEPARATOR . 'additional.php';
		}

		if(isset($links) && is_array($links))
		{
			$out = array_merge($out, $links);
		}

		return $out;

	}

	public function _getChilds($post)
	{
		settype($this->value, 'array');
		$default = new JRegistry($this->value);

		$index = $post['index'];
		$list  = $this->_getList($post);
		if($list)
		{
			$js = "onchange=\"getChilds(this.value, '{$post['child']}')\" class=\"select-geo\"";

			return JHtml::_('select.genericlist', $list, "filters[{$this->key}][{$post['index']}]", $js, 'value', 'text', $default->get($post['index'], NULL));
		}

		return NULL;
	}

	protected function _getList(&$post, $label = 'G_SELECTCOUNTRY')
	{
		$index            = $post['index'];
		$this->section_id = (int)$post['section_id'];
		$db               = JFactory::getDbo();

		$add = array();
		switch($index)
		{
			case 'country':
				$child   = 'state';
				$country = NULL;
				$add[]   = JHTML::_('select.option', 0, JText::_($label), 'value', 'text');
				$query   = $this->_getCountry();
				$db->setQuery($query);
				$list = $db->loadObjectList();
				$dir  = JPATH_ROOT . '/components/com_joomcck/fields/geo/countries/';
				$lang = strtolower(JFactory::getLanguage()->getTag()) . '.php';

				if(!JFile::exists($dir . $lang))
				{
					$lang = 'en-gb.php';
				}

				$countries = include $dir . $lang;

				foreach($list as &$row)
				{
					if(!empty($countries[$row->value]))
					{
						$row->text = $countries[$row->value];
					}
					else
					{
						$row->text = $row->value;
					}
				}
				break;

			case 'state':
				$child   = 'city';
				$country = $post['value'];
				$add[]   = JHTML::_('select.option', 0, JText::_('G_SELECTSTATE'), 'value', 'text');
				$add[]   = JHTML::_('select.option', "-1", JText::_('G_WITHOUTSTATE'), 'value', 'text');
				$query   = $this->_getState($country);
				$db->setQuery($query);
				$list = $db->loadObjectList();
				break;
			default:
				$child   = NULL;
				$state   = $post['value'];
				$country = $post['country'];
				$add[]   = JHTML::_('select.option', 0, JText::_('G_SELECTCITY'), 'value', 'text');
				$query   = $this->_getCity($country, $state);
				$db->setQuery($query);
				$list = $db->loadObjectList();
		}
		$post['child'] = $child;

		if(count($list) <= 1 && $child && @$post['donotgolower'] == 0)
		{
			$post['index']   = $child;
			$post['value']   = (isset($list[0]->value) ? $list[0]->value : NULL);
			$post['country'] = $country;
			$list            = $this->_getList($post);
			$add             = array();
		}

		if(count($list) == 0)
		{
			return array();
		}

		$list = array_merge($add, $list);

		return $list;
	}

	private function _getCountry($default = NULL)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(TRUE);

		$query->from('#__js_res_record_values');
		$query->where("field_key = '{$this->key}'");
		$query->where("value_index = 'country'");
		$query->where("section_id = {$this->section_id}");

		if(!$default)
		{
			$query->select('field_value as value');
			$query->order('value');
			$query->group('value');
		}
		else
		{
			$query->select('record_id');
			$query->where("field_value = '{$default}'");
		}

		return $query;
	}

	public function onTypeaheadState($post)
	{
		$db   = JFactory::getDbo();
		$post = new JRegistry($post);

		$q = $post->get('q');
		$q = $db->escape($q);

		$query = $db->getQuery(TRUE);

		$query->select("field_value as value, CONCAT(field_value, ' <span class=badge>', count(record_id), '</span>') as label");
		$query->from('#__js_res_record_values');
		$query->where("field_type = 'geo'");
		$query->where("field_value LIKE '%{$q}%'");
		$query->where("value_index = 'state'");
		$query->group('field_value');

		if($post->get('country'))
		{
			$query->where('record_id IN (SELECT r.record_id FROM #__js_res_record_values AS r where r.value_index = "country" AND field_value = "' . $post->get('country') . '")');
		}

		$db->setQuery($query, 0, $post->get('limit', 10));
		$result = $db->loadObjectList();

		return $result;
	}

	public function onTypeaheadCity($post)
	{
		$db = JFactory::getDbo();

		$q = $this->request->get('q');
		$q = $db->escape($q);

		$query = $db->getQuery(TRUE);

		$query->select("field_value as value, CONCAT(field_value, ' <span class=badge>', count(record_id), '</span>') as label");
		$query->from('#__js_res_record_values');
		$query->where("field_type = 'geo'");
		$query->where("field_value LIKE '%{$q}%'");
		$query->where("value_index = 'city'");
		$query->group('field_value');
		$db->setQuery($query, 0, $this->request->get('limit', 10));

		if($this->request->get('country'))
		{
			$query->where('record_id IN (SELECT r.record_id FROM #__js_res_record_values AS r where r.value_index = "country" AND r.field_value = "' . $this->request->get('country') . '")');
		}
		if($this->request->get('state'))
		{
			$query->where('record_id IN (SELECT r.record_id FROM #__js_res_record_values AS r where r.value_index = "state" AND r.field_value LIKE "%' . $this->request->getString('state') . '%")');
		}

		$result = $db->loadObjectList();

		return $result;
	}

	public function onTypeaheadZip($post)
	{
		$db = JFactory::getDbo();

		$q = $this->request->get('q');
		$q = $db->escape($q);

		$query = $db->getQuery(TRUE);

		$query->select("field_value as value, CONCAT(field_value, ' <span class=badge>', count(record_id), '</span>') as label");
		$query->from('#__js_res_record_values');
		$query->where("field_type = 'geo'");
		$query->where("field_value LIKE '%{$q}%'");
		$query->where("value_index = 'zip'");
		$query->group('field_value');
		$db->setQuery($query, 0, $this->request->get('limit', 10));

		if($this->request->get('country'))
		{
			$query->where('record_id IN (SELECT r.record_id FROM #__js_res_record_values AS r where r.value_index = "country" AND r.field_value = "' . $this->request->get('country') . '")');
		}
		if($this->request->get('state'))
		{
			$query->where('record_id IN (SELECT r.record_id FROM #__js_res_record_values AS r where r.value_index = "country" AND r.field_value LIKE "%' . $this->request->get('state') . '%")');
		}
		if($this->request->get('city'))
		{
			$query->where('record_id IN (SELECT r.record_id FROM #__js_res_record_values AS r where r.value_index = "city" AND r.field_value LIKE "%' . $this->request->get('city') . '%")');
		}

		$result = $db->loadObjectList();

		return $result;
	}

	private function _getState($country = NULL, $default = NULL)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(TRUE);

		$query->from('#__js_res_record_values');
		$query->where("field_key = '{$this->key}'");
		$query->where("value_index = 'state'");
		$query->where("section_id = {$this->section_id}");

		if($country)
		{
			$query->where("record_id IN (" . $this->_getCountry($country) . ")");
		}

		if(!$default)
		{
			$query->select('field_value as value, field_value as text');
			$query->order('value');
			$query->group('value');
		}
		else
		{
			$query->select('record_id');
			$query->where("field_value = '{$default}'");
		}

		return $query;
	}

	private function _getCity($country, $state, $default = NULL)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(TRUE);

		$query->from('#__js_res_record_values');
		$query->where("field_key = '{$this->key}'");
		$query->where("value_index = 'city'");
		$query->where("section_id = {$this->section_id}");

		if($state)
		{
			$query->where("record_id IN (" . $this->_getState($country, $state) . ")");
		}

		if($country)
		{
			$query->where("record_id IN (" . $this->_getCountry($country) . ")");
		}

		if(!$default)
		{
			$query->select('field_value as value, field_value as text');
			$query->order('value');
			$query->group('value');
		}
		else
		{
			$query->select('record_id');
			$query->where("field_value = '{$default}'");
		}

		return $query;
	}

	public function getMarker()
	{
		$icon_url = '/components/com_joomcck/fields/geo/markers/grouped/other/marker-big-red.png';

		if(!empty($this->value['position']['marker']))
		{
			$icon_url = '/components/com_joomcck/fields/geo/markers/' . $this->params->get('params.map_icon_src.dir', 'custom') . '/' . $this->value['position']['marker'];
		}

		$size = getimagesize(JPATH_ROOT . $icon_url);

		$out = array(
			(int)$size[0],
			(int)$size[1],
			JURI::root(TRUE) . $icon_url
		);

		return $out;
	}

	public function onInfoWindow($post, $record)
	{
		$model                  = MModelBase::getInstance('Record', 'JoomcckModel');
		$record                 = $model->_prepareItem($record, 'list');
		$section                = ItemsStore::getSection($record->section_id);
		$type                   = ItemsStore::getType($record->type_id);
		$this->field_keys_by_id = MModelBase::getInstance('Records', 'JoomcckModel')->getKeys($section);

		$exclude = $this->params->get('params.field_id_exclude');
		settype($exclude, 'array');

		ob_start();
		$template = JFactory::getApplication()->getTemplate();
		$tpath    = JPATH_THEMES . '/' . $template . '/html/com_joomcck/fields/' . $this->type . '/window/' . $this->params->get('params.template_window', 'default.php');
		if(!JFile::exists($tpath))
		{
			$tpath = JPATH_ROOT . '/components/com_joomcck/fields/' . $this->type . '/tmpl/window/' . $this->params->get('params.template_window', 'default.php');
		}
		include $tpath;
		$out = ob_get_contents();
		ob_end_clean();

		return $out;
	}

	public function onGetMarkersList($post)
	{
		return include __DIR__ . '/tmpl/markers_list/' . $this->params->get('params.template_marker', 'default.php');
	}

	public function countries()
	{
		$dir  = JPATH_ROOT . '/components/com_joomcck/fields/geo/countries/';
		$lang = strtolower(JFactory::getLanguage()->getTag()) . '.php';

		if(!JFile::exists($dir . $lang))
		{
			$lang = 'en-gb.php';
		}

		$countries = include $dir . $lang;

		$options[''] = JText::_('CCELECTCOUNTRY');

		$db = JFactory::getDbo();
		$db->setQuery('SELECT id, name FROM #__js_res_country');
		$list = $db->loadObjectList();

		foreach($list as $k => &$country)
		{
			if($this->params->get('params.country_limit') && !in_array($country->id, $this->params->get('params.country_limit')))
			{
				continue;
			}

			$out[$country->id] = empty($countries[$country->id]) ? $country->name : $countries[$country->id];
		}
		asort($out);

		if(count($out) == 1)
		{
			$name   = implode('', array_values($out));
			$key    = implode('', array_keys($out));
			$html[] = sprintf('%s<input type="hidden" value="%s" name="jform[fields][%d][address][country]" id="f%d_address_country">', $name, $key, $this->id, $this->id);

			return implode("\n", $html);
		}

		if($out)
		{
			$out = array_merge($options, $out);
		}

		$html[] = '<select class="col-md-12" id="f' . $this->id . '_address_country" type="text" name="jform[fields][' . $this->id .
			'][address][country]">';
		foreach($out as $id => $name)
		{
			$html[] = sprintf('<option value="%s"%s>%s</option>', $id, (@$this->value['address']['country'] == $id ? ' selected="selected"' : NULL), $name);
		}
		$html[] = '</select>';

		return implode("\n", $html);

	}

	protected function _listmarkers($dir, &$defaultmarker, $subfolder = NULL)
	{
		if(\Joomla\CMS\Filesystem\Folder::exists($dir))
		{
			$w      = 32;
			$h      = 37;
			$format = '<img src="%s" rel="tooltip" data-original-title="%s" class="img-marker" data-marker-file="%s" data-marker-width="%d" data-marker-height="%d" data-field-id="%d">';
			$path   = '/components/com_joomcck/fields/geo/markers/' . $this->params->get('params.map_icon_src.dir', 'custom') . '/';
			if($dh = opendir($dir))
			{
				while(($file = readdir($dh)) !== FALSE)
				{
					$ext = strtolower(substr($file, strrpos($file, '.') + 1));
					if($ext == 'png' || $ext == 'gif')
					{
						if(JFile::exists(JPATH_ROOT . $path . $subfolder . $file))
						{
							$msize = getimagesize(JPATH_ROOT . $path . $subfolder . $file);
							$w     = $msize[0];
							$h     = $msize[1];
						}

						echo sprintf($format, JURI::root(TRUE) . $path . $subfolder . $file, $this->_getMarkerName($file), $subfolder . $file, $w, $h, $this->id);

						//$img = JHTML::image(JURI::root() . $path . $subfolder . $file, JText::_('CICONCLICKINSERT'), array('align' => 'absmiddle'));
						if(!$defaultmarker)
						{
							$defaultmarker = $subfolder . $file;
						}
					}

				}
				closedir($dh);
			}
		}

	}

	public function _getcountryname($code)
	{
		$dir  = JPATH_ROOT . '/components/com_joomcck/fields/geo/countries/';
		$lang = strtolower(JFactory::getLanguage()->getTag()) . '.php';

		if(!JFile::exists($dir . $lang))
		{
			$lang = 'en-gb.php';
		}

		$countries = include $dir . $lang;

		return (!empty($countries[$code]) ? $countries[$code] : $code);
	}

	public function G_LAT($row, $params)
	{
		$return = array();

		if($row->get($params->get('field.' . $this->id . '.lat')) && $row->get($params->get('field.' . $this->id . '.lng')))
		{
			$return['position'] = array(
				'lat'    => $row->get($params->get('field.' . $this->id . '.lat')),
				'lng'    => $row->get($params->get('field.' . $this->id . '.lng')),
				'marker' => $this->params->get('params.map_icon_src.icon')
			);
		}

		return $return;
	}

	public function onImport($value, $params, $record = NULL)
	{
		return $value;
	}

	public function onImportData($row, $params)
	{
		return array(
			'position' => array(
				'lat' => $row->get($params->get('field.' . $this->id . '.lat')),
				'lng' => $row->get($params->get('field.' . $this->id . '.lng'))
			)
		);
	}

	public function onImportForm($heads, $defaults)
	{
		$pattern = '<div><small>%s</small></div>%s';

		$out = sprintf($pattern, JText::_('G_LAT'), $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id . '.lat'), 'lat'));
		$out .= sprintf($pattern, JText::_('G_LNG'), $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id . '.lng'), 'lng'));

		return $out;
	}
}
