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
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckselectable.php';

class JFormFieldCMultiselect extends CFormFieldSelectable
{

	public function getInput()
	{
		$params     = $this->params;
		$doc        = \Joomla\CMS\Factory::getDocument();
		$this->user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		$values = array();
		if($params->get('params.sql_source'))
		{
			$values = $this->_getSqlValues();
		}
		else
		{
			$values = explode("\n", $params->get('params.values'));
			ArrayHelper::clean_r($values);

			settype($this->value, 'array');
			$diff = array_diff($this->value, $values);
			if(count($diff))
			{
				$values = array_merge($values, $diff);
			}
			ArrayHelper::clean_r($values);

			if($params->get('params.sort') == 2)
				asort($values);
			if($params->get('params.sort') == 3)
				rsort($values);
		}
		$this->values = $values;

		if($this->isnew && $this->params->get('params.default_val'))
		{
			$this->value[] = $this->params->get('params.default_val');
		}

		return $this->_display_input();
	}

	public function onJSValidate()
	{
		$js = "\n\t\tvar ms{$this->id} = jQuery('[name^=\"jform\\\\[fields\\\\]\\\\[{$this->id}\\\\]\"]').val();";
		$js .= "\n\t\tif(ms{$this->id}) {ms{$this->id} = ms{$this->id}.length};";
		if($this->required)
		{
			$js .= "\n\t\tif(!ms{$this->id}){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(\Joomla\CMS\Language\Text::sprintf("CFIELDREQUIRED", $this->label)) . "');}";
		}
		if($this->params->get('params.total_limit'))
		{
			$js .= "\n\t\tif(ms{$this->id} > " . $this->params->get('params.total_limit') . ") {hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(\Joomla\CMS\Language\Text::sprintf("F_OPTIONSLIMIT", $this->params->get('params.total_limit'))) . "');}";
		}

		return $js;
	}

	public function validateField($value, $record, $type, $section)
	{
		if($this->params->get('params.total_limit'))
		{
			if(count($value) > $this->params->get('params.total_limit'))
			{
				$this->setError(\Joomla\CMS\Language\Text::sprintf("F_VALUESLIMIT", $this->params->get('params.total_limit'), $this->label));
			}
		}

		return parent::validateField($value, $record, $type, $section);
	}

	public function onImportData($row, $params)
	{
		return $row->get($params->get('field.' . $this->id . '.fname'));
	}

	public function onImport($value, $params, $record = NULL)
	{
		$values = explode($params->get('field.' . $this->id . '.separator', ','), $value);
		ArrayHelper::clean_r($values);

		return $values;
	}

	public function onImportForm($heads, $defaults)
	{
		$out = $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id . '.fname'), 'fname');
		$out .= sprintf('<div><small>%s</small></div><input type="text" name="import[field][%d][separator]" value="%s" class="col-md-2" >',
			\Joomla\CMS\Language\Text::_('CMULTIVALFIELDSEPARATOR'), $this->id, $defaults->get('field.' . $this->id . '.separator', ','));

		return $out;
	}

}
