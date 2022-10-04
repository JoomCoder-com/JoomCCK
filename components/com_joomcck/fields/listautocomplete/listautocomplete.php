<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckselectable.php';

class JFormFieldCListautocomplete extends CFormFieldSelectable
{

	public function getInput()
	{
        $options['only_suggestions'] = $this->params->get('params.only_values', 0);
        $options['can_add'] = 1;
        $options['can_delete'] = 1;
        $options['suggestion_limit'] = $this->params->get('params.max_result', 10);
        $options['limit'] = $this->params->get('params.max_items', 10);

        $out = [];
        if($this->params->get('params.sql_source')){
            $options['suggestion_url'] =  "index.php?option=com_joomcck&task=ajax.field_call&tmpl=component&field_id={$this->id}&func=onGetSqlValues&field=listautocomplete";
        } else {
            $list = explode("\n", str_replace("\r", "", $this->params->get('params.values', '')));
            $list = array_values($list);
            $out = $this->_getPillValues($list);
        }

		if($this->isnew && $this->params->get('params.default_val'))
		{
			$this->value[] = $this->params->get('params.default_val');
        }

		$default = $this->_getPillValues($this->value);

		$this->inputvalue = JHtml::_('mrelements.pills', "jform[fields][{$this->id}]", "field_" . $this->id, $default, $out, $options);
		return $this->_display_input();
	}

	public function onJSValidate()
	{
		$js = "\n\t\tvar lac{$this->id} = jQuery('[name^=\"jform\\\\[fields\\\\]\\\\[$this->id\\\\]\"]').val();";
		if($this->required)
		{
			$js .= "\n\t\tif(!lac{$this->id}){hfid.push({$this->id}); isValid = false; errorText.push('".addslashes(JText::sprintf("CFIELDREQUIRED", $this->label))."');}";
		}
		return $js;
	}

	public function validateField($value, $record, $type, $section)
	{
		if ($this->params->get('params.max_items', 0) && (is_array($value) && count($value) > $this->params->get('params.max_items')))
		{
			$this->setError(JText::sprintf("L_ITEMSLIMITMSG", $this->label));
			return FALSE;
		}

		return parent::validateField($value, $record, $type, $section);
	}

	public function onGetSqlValues($post)
	{
		if ($this->params->get('params.sql_source'))
		{
			$db = JFactory::getDbo();
			$user = JFactory::getUser();
			$sql = $this->params->get('params.sql', "SELECT 1 AS id, 'No sql query entered' AS text");
			$sql = str_replace('[USER_ID]', $user->get('id', 0), $sql);
			$db->setQuery($sql);
			$out = $db->loadObjectList();
		}
		else
		{
			$list = explode("\n", str_replace("\r", "", $this->params->get('params.values', '')));
            $list = array_values($list);
            $out = $this->_getPillValues($list);
		}

		return $out;
	}

	public function onImportData($row, $params)
	{
		return $row->get($params->get('field.' . $this->id . '.fname'));
	}

	public function onImport($value, $params, $record = null)
	{
		$values = explode($params->get('field.' . $this->id . '.separator', ','), $value);
		ArrayHelper::clean_r($values);
		return $values;
	}

	public function onImportForm($heads, $defaults, $record = null)
	{
		$out = $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id . '.fname'), 'fname');
		$out .= sprintf('<div><small>%s</small></div><input type="text" name="import[field][%d][separator]" value="%s" class="span2" >',
			JText::_('CMULTIVALFIELDSEPARATOR'), $this->id, $defaults->get('field.' . $this->id . '.separator', ','));

		return $out;
	}

}
