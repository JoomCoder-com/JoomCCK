<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT. '/components/com_joomcck/library/php/fields/joomcckfield.php';


class JFormFieldCDigits extends CFormField
{
	protected $type = 'Digits';
	public $data;

	public function getInput()
	{
		$document = \Joomla\CMS\Factory::getDocument();
		$document->addScript(\Joomla\CMS\Uri\Uri::root(TRUE).'/components/com_joomcck/fields/digits/assets/digits.js');

		return $this->_display_input();
	}

	public function onJSValidate()
	{
		$text = htmlentities(\Joomla\CMS\Language\Text::sprintf('F_FORMATINCORRECT', $this->label), ENT_QUOTES, 'UTF-8');
		$js = '';
		if ($this->required)
		{
			$js .= "\n\t\tif(jQuery('#field_{$this->id}').val() == ''){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(\Joomla\CMS\Language\Text::sprintf('CFIELDREQUIRED', $this->label)) . "');}";
		}

		return $js .= "\n\t\tif(!jQuery('#field_{$this->id}').val().match(/^[-\+]?[\d\.]*$/)){isValid = false; errorText.push('{$text}'); hfid.push({$this->id});}";
	}
	public function validateField($value, $record, $type, $section)
	{
		if($this->params->get('params.val_max', false) && $this->params->get('params.val_min', false))
		{
			if(is_numeric($value) && ($value > $this->params->get('params.val_max', 0) || $value < $this->params->get('params.val_min', 0)))
			{
				$this->setError(\Joomla\CMS\Language\Text::sprintf('D_MINMAX_ERROR', $this->label, $this->params->get('params.val_min', 0), $this->params->get('params.val_max', 0)));
				return false;
			}
		}
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		$filter = \Joomla\CMS\Filter\InputFilter::getInstance();
		return $filter->clean($value);
	}

	public function onFilterWornLabel($section)
	{
		$value = $this->value;
		settype($value, 'array');
		return  \Joomla\CMS\Language\Text::sprintf($this->params->get('params.filter_worn', 'P_BETWEEN'),
			'<b>'.$this->params->get('params.prepend').number_format($value['min'], $this->params->get('params.decimals_num', 0), $this->params->get('params.dseparator', '.'), $this->params->get('params.separator', ',')).$this->params->get('params.append').'</b>',
			'<b>'.$this->params->get('params.prepend').number_format($value['max'], $this->params->get('params.decimals_num', 0), $this->params->get('params.dseparator', '.'), $this->params->get('params.separator', ',')).$this->params->get('params.append').'</b>');
	}

	public function onFilterWhere($section, &$query)
	{
		$value = $this->value;
		settype($value, 'array');
		ArrayHelper::trim_r($value);

		if(empty($value['min']) || empty($value['max']))
		{
			return NULL;
		}

		if($value['min'] == $value['max'])
		{
			return NULL;
		}

		$data = $this->_get_min_max($section);

		if($value['min'] == $data->min && $value['max'] == $data->max)
		{
			return null;
		}

		$db = \Joomla\CMS\Factory::getDbo();
		$ids = $this->getIds("SELECT record_id FROM #__js_res_record_values
			WHERE (field_value + 0 BETWEEN " . (float)$value['min'] . " AND " . (float)$value['max'] . ")
			  AND section_id = " . (int)$section->id . "
			  AND field_key = " . $db->quote($this->key));

		return $ids;
	}

	private function _get_min_max($section) {

		static $out = array();

		if(!array_key_exists($this->id, $out))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(TRUE);
			$query->select('MAX(field_value + 0) as max, MIN(field_value + 0) as min');
			$query->from('#__js_res_record_values');
			$query->where("section_id = " . (int)$section->id);
			$query->where("`field_key` = " . $db->quote($this->key));
			$db->setQuery($query);

			$out[$this->id] = $db->loadObject();
		}

		return $out[$this->id];
	}


	public function onRenderFilter($section, $module = false)
	{

		$this->value = new \Joomla\Registry\Registry($this->value);

		$this->data = $this->_get_min_max($section);

		return $this->_display_filter($section, $module);
	}


	public function onRenderFull($record, $type, $section)
	{
		return $this->_render('full', $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		return $this->_render('list', $record, $type, $section);
	}

	private function _render($client, $record, $type, $section )
	{
		if($this->value === null || $this->value === '') return NULL;

		return $this->_display_output($client, $record, $type, $section);
	}
	public function isFilterActive()
	{
		if(!$this->value) return 0;

		$value = $this->value;
		if(is_array($value))
		{
			$value = new \Joomla\Registry\Registry($value);
		}

		$min = $value->get('min');
		$max = $value->get('max');
		if(!empty($min) && !empty($max))
		{
			return 1;
		}
		return 0;
	}
	public function onImport($value, $params, $record = null)
	{
		settype($value, 'float');
		$value = round($value, $this->params->get('params.decimals_num', 0));
		return $value;
	}
	public function onImportForm($heads, $defaults)
	{
		return $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id));
	}
}
