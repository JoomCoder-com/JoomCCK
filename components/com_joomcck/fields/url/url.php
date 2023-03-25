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

jimport('joomla.mail.helper');
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckfield.php';

class JFormFieldCUrl extends CFormField
{

	public function getInput()
	{
		$document = JFactory::getDocument();
		$document->addScript(JURI::root(TRUE) . '/components/com_joomcck/fields/url/assets/url.js');
		$user   = JFactory::getUser();
		$params = $this->params;

		$labels = explode("\n", $params->get('params.default_labels', ''));

		ArrayHelper::clean_r($labels);
		ArrayHelper::trim_r($labels);

		$this->labels = $labels;

		return $this->_display_input();
	}

	public function onJSValidate()
	{
		$js = "\n\t\t" . 'var vals' . $this->id . ' = 0;
		jQuery.each(jQuery("#url-list' . $this->id . '").children("div.url-item"), function(key, val){
			if(jQuery(jQuery("input", val)[0]).val()) {
				vals' . $this->id . '++;
			}
		});';
		if($this->required)
		{
			$js .= "\n\t\tif(vals{$this->id} === 0){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(JText::sprintf('CFIELDREQUIRED', $this->label)) . "');}";
		}
		$limit = $this->params->get('params.limit');
		if($limit > 0)
		{
			$js .= "\n\t\tif(vals{$this->id} > {$limit}){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(JText::sprintf('U_REACHEDLIMIT', $limit)) . "');}";
		}

		return $js;
	}

	public function validateField($value, $record, $type, $section)
	{
		$vals = 0;
		if($value)
		{
			foreach($value as $i => $val)
			{
				if(!empty($val['url']))
				{
					$vals++;
				}
			}
		}
		if($this->required && !$vals)
		{
			$this->setError(JText::sprintf('CFIELDNOTENTERED', $this->label));

			return FALSE;
		}
		if($limit = $this->params->get('params.limit'))
		{
			if($limit > 0 && $vals > $limit)
			{
				$this->setError(JText::sprintf('U_REACHEDLIMIT', $this->label));

				return FALSE;
			}
		}

		return parent::validateField($value, $record, $type, $section);
	}

	public function onStoreValues($validData, $record)
	{
		$value = array();
		foreach($this->value as $val)
		{
			$value[] = $val['url'];
		}

		return $value;
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		if(!is_array($value))
		{
			return array();
		}

		foreach($value as $i => $val)
		{
			if(empty($val['url']) || $val['url'] == 'http://')
			{
				unset($value[$i]);
				continue;
			}
			//$value[$i]['url'] = str_replace('http://', '', $val['url']);
			if($this->params->get('params.link_redirect', 0) && !isset($val['hits']))
			{
				$value[$i]['hits'] = 0;
			}

		}

		if(!count($value))
		{
			return array();
		}


		foreach($value as $i => $val)
		{
			$array[] = $val;
		}

		return $array;
	}

	public function onFilterWornLabel($section)
	{
		return $this->value;
	}

	public function onFilterWhere($section, &$query)
	{
		$this->value = JFactory::getDbo()->escape($this->value);
		$ids         = $this->getIds("SELECT record_id FROM #__js_res_record_values WHERE field_value = '{$this->value}' AND section_id = {$section->id} AND field_key = '{$this->key}'");

		return $ids;
	}

	public function onRenderFilter($section, $module = FALSE)
	{
		return $this->_display_filter($section, $module);
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		if(!is_array($value))
		{
			return array();
		}

		$out = array();
		foreach($value AS $link)
		{
			$link  = new JRegistry($link);
			$out[] = $link->get('label', $link->get('url'));
		}

		return implode(', ', $out);
	}

	public function onRenderFull($record, $type, $section)
	{
		return $this->_render('full', $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		return $this->_render('list', $record, $type, $section);
	}

	private function _render($client, $record, $type, $section)
	{
		if(!count((array)$this->value))
		{
			return;
		}

		return $this->_display_output($client, $record, $type, $section);
	}

	public function _gettitle($post)
	{
		if(strpos($post['url'], 'https://') === FALSE)
		{
			$url  = str_replace('http://', '', $post['url']);
			$file = @fopen("http://$url", "r");
		}
		else
		{
			$url  = str_replace('https://', '', $post['url']);
			$file = @fopen("https://$url", "r");

		}
		if(!$file)
		{
			return 'Could not open URL';
		}

		$line = '';
		while(!feof($file))
		{
			$line .= fgets($file, 1024);
			/* this only works if the title and its tags are on one line */
			if(preg_match("/<title>(.*)<\/title>/iU", $line, $out))
			{
				$title = $out[1];
				break;
			}
		}
		fclose($file);

		return $title;
	}

	public function onFilterData($post)
	{
		$db = JFactory::getDbo();

		$q = $this->request->get('q');
		$q = $db->escape($q);

		$query = $db->getQuery(TRUE);

		$query->select("field_value as value, CONCAT(field_value, ' (', count(record_id), ')') as label");
		$query->from('#__js_res_record_values');
		$query->where("field_key = '{$this->key}'");
		$query->where("field_value LIKE '%{$q}%'");
		$query->group('field_value');
		$db->setQuery($query, 0, 10);
		$result = $db->loadObjectList();

		return $result;
	}

	public function onImport($value, $params, $record = NULL)
	{
		return $value;
	}

	public function onImportData($row, $params)
	{
		$return = array();
		if($row->get($params->get('field.' . $this->id . '.url')))
		{
			$return[0]['url'] = $row->get($params->get('field.' . $this->id . '.url'));
			if($row->get($params->get('field.' . $this->id . '.label')))
			{
				$return[0]['label'] = $row->get($params->get('field.' . $this->id . '.label'));
			}
		}

		return $return;
	}

	public function onImportForm($heads, $defaults)
	{
		$pattern = '<div><small>%s</small></div>%s';

		$out = sprintf($pattern, JText::_('Label'), $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id . '.label'), 'label'));
		$out .= sprintf($pattern, JText::_('URL'), $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id . '.url'), 'url'));

		return $out;
	}

	public function _redirect($post, $record, $field, $get)
	{
		$rfields = json_decode($record->fields, TRUE);

		if(!isset($rfields[$this->id]))
		{
			return;
		}
		$record_table = JTable::getInstance('Record', 'JoomcckTable');
		$record_table->load($record->id);


		foreach($rfields[$this->id] as $k => $value)
		{
			if(!isset($value['hits']))
			{
				$rfields[$this->id][$k]['hits'] = 0;
			}
			settype($rfields[$this->id][$k]['hits'], 'int');
			if($value['url'] == $get['url'])
			{
				$rfields[$this->id][$k]['hits']++;
			}
		}
		$record_table->fields = json_encode($rfields);
		$record_table->store();
		JFactory::getApplication()->redirect($get['url']);

	}

}
	
