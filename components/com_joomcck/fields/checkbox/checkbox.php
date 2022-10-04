<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') || die();

require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckselectable.php';

class JFormFieldCCheckbox extends CFormFieldSelectable
{
    public function getInput()
    {
        $params     = $this->params;
        $values     = [];
        $this->user = JFactory::getUser();

        if ($params->get('params.sql_source')) {
            $values = $this->_getSqlValues();
        } else {
            $values = explode("\n", $params->get('params.values'));
            ArrayHelper::clean_r($values);
            settype($this->value, 'array');
            $diff = array_diff($this->value, $values);

            if (count($diff)) {
                $values = array_merge($values, $diff);
            }
            ArrayHelper::clean_r($values);

            if ($params->get('params.sort') == 2) {
                asort($values);
            }

            if ($params->get('params.sort') == 3) {
                rsort($values);
            }
        }

        $this->values = $values;

        if ($this->isnew && $this->params->get('params.default_val')) {
            $this->value[] = $this->params->get('params.default_val');
        }

        return $this->_display_input();
    }

    public function onJSValidate()
    {
        $js = "\n\t\tvar chb{$this->id} = jQuery('[name^=\"jform\\\\[fields\\\\]\\\\[$this->id\\\\]\"]:checked').length;";
        if ($this->required) {
            $js .= "\n\t\tif(!chb{$this->id}){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(JText::sprintf("CFIELDREQUIRED", $this->label)) . "');}";
        }
        if ($this->params->get('params.total_limit')) {
            $js .= "\n\t\tif(chb{$this->id} > " . $this->params->get('params.total_limit') . ") {hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(JText::sprintf('F_OPTIONSLIMIT', $this->params->get('params.total_limit'))) . "');}";
        }

        return $js;
    }

    public function validateField($value, $record, $type, $section)
    {
        if ($this->params->get('params.total_limit')) {
            if (count($value) > $this->params->get('params.total_limit')) {
                $this->setError(JText::sprintf('F_VALUESLIMIT', $this->params->get('params.total_limit'), $this->label));
            }
        }

        return parent::validateField($value, $record, $type, $section);
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

    public function onImportForm($heads, $defaults)
    {
        $out = $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id . '.fname'), 'fname');
        $out .= sprintf('<div><small>%s</small></div><input type="text" name="import[field][%d][separator]" value="%s" class="span2" >',
            JText::_('CMULTIVALFIELDSEPARATOR'), $this->id, $defaults->get('field.' . $this->id . '.separator', ','));

        return $out;
    }
}
