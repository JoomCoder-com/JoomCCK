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

class JFormFieldCCurrency extends CFormField
{
	protected $type = 'Currency';
	public $data;

	public function getInput()
	{
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
				$this->setError(\Joomla\CMS\Language\Text::sprintf('CURRENCY_MINMAX_ERROR', $this->label, $this->params->get('params.val_min', 0), $this->params->get('params.val_max', 0)));
				return false;
			}
		}
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		$filter = \Joomla\CMS\Filter\InputFilter::getInstance();
		// Remove currency symbols and formatting, keep only numbers and decimal point
		$value = preg_replace('/[^\d\.-]/', '', $value);
		return $filter->clean($value);
	}

	public function onFilterWornLabel($section)
	{
		$value = $this->value;
		settype($value, 'array');
		$currency_symbol = $this->params->get('params.currency_symbol', '$');
		return \Joomla\CMS\Language\Text::sprintf($this->params->get('params.filter_worn', 'Between %s and %s'),
			'<b>'.$currency_symbol.number_format($value['min'], $this->params->get('params.decimals_num', 2), $this->params->get('params.dseparator', '.'), $this->params->get('params.separator', ',')).'</b>',
			'<b>'.$currency_symbol.number_format($value['max'], $this->params->get('params.decimals_num', 2), $this->params->get('params.dseparator', '.'), $this->params->get('params.separator', ',')).'</b>');
	}

	public function onFilterWhere($section, &$query)
	{
		$value = $this->value;
		settype($value, 'array');
		\Joomla\Utilities\ArrayHelper::trim_r($value);

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

		$ids = $this->getIds("SELECT record_id FROM #__js_res_record_values
			WHERE (field_value + 0 BETWEEN {$value['min']} AND {$value['max']})
			  AND section_id = {$section->id}
			  AND field_key = '{$this->key}'");

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
			$query->where("section_id = {$section->id}");
			$query->where("`field_key` = '{$this->key}'");
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
		// Remove currency symbols and formatting for import
		$value = preg_replace('/[^\d\.-]/', '', $value);
		settype($value, 'float');
		$value = round($value, $this->params->get('params.decimals_num', 2));
		return $value;
	}

	public function onImportForm($heads, $defaults)
	{
		return $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id));
	}

	/**
	 * Get filter value from request
	 * @return mixed
	 */
	public function getFilterValue()
	{
		$app = \Joomla\CMS\Factory::getApplication();
		$filter_name = 'filter_' . $this->field->id;
		$value = $app->input->get($filter_name, null, 'array');
		
		if (is_array($value) && count($value) == 2) {
			// Clean and validate the values
			$min = !empty($value[0]) ? (float)$value[0] : null;
			$max = !empty($value[1]) ? (float)$value[1] : null;
			
			if ($min !== null || $max !== null) {
				return [$min, $max];
			}
		}
		
		return null;
	}

	/**
	 * Get formatted currency value
	 */
	public function getFormattedValue($value = null)
	{
		if($value === null) $value = $this->value;
		if($value === null || $value === '') return '';

		$currency_symbol = $this->params->get('params.currency_symbol', '$');
		$symbol_position = $this->params->get('params.symbol_position', 'before');
		$decimals = $this->params->get('params.decimals_num', 2);
		$decimal_separator = $this->params->get('params.dseparator', '.');
		$thousands_separator = $this->params->get('params.separator', ',');

		$formatted_value = number_format($value, $decimals, $decimal_separator, $thousands_separator);

		if($symbol_position === 'after') {
			return $formatted_value . $currency_symbol;
		} else {
			return $currency_symbol . $formatted_value;
		}
	}
}