<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckselectable.php';
//require_once JPATH_ROOT. '/components/com_joomcck/library/php/fields/joomcckfield.php';


class JFormFieldCtext extends CFormFieldSelectable
{

	public function getInput()
	{
		$mask = $this->params->get('params.mask', 0);

		if(!$this->request->getInt('id') && $this->params->get('params.default_val') && !$this->value)
		{
			$this->value = JText::_($this->params->get('params.default_val'));
		}

		if($mask->mask_type)
		{
			JFactory::getDocument()->addScript(JURI::root(TRUE) . '/media/mint/js/mask/masks.js');
			JFactory::getDocument()->addScript(JURI::root(TRUE) . '/components/com_joomcck/fields/text/text.js');
			switch($mask->mask_type)
			{
				case '(###) ### #######' :
				case '(###) ###-####' :
				case '#####-###' :
				case '#### #### #### ####' :
					$type = 'string';
					break;
				case 'mm/dd/yyyy' :
				case 'mm/dd/yyyy' :
					$type = "date";
					break;
				case '#' :
				case '#####.##' :
				case '#,###.##' :
				case '$#,###.##' :
				case '€#,###.##' :
					$type = "number";
					break;
				default :
					$type = $mask->type;
			}
			$this->mask_type = $type;
		}

		return $this->_display_input();
	}

	public function onJSValidate()
	{
		$js = "\n\t\tvar txt{$this->id} = jQuery('[name^=\"jform\\\\[fields\\\\]\\\\[$this->id\\\\]\"]').val();";
		if($this->required)
		{
			$js .= "\n\t\tif(!txt{$this->id}){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(JText::sprintf("CFIELDREQUIRED", $this->label)) . "');}";
		}
		if($this->params->get('params.regex_val'))
		{
			$js .= "\n\t\tif(txt{$this->id} && !txt{$this->id}.match(/^" . $this->params->get('params.regex_val') . "$/g)){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(JText::sprintf("CFIELDREGEX", $this->label)) . "');}";
		}

		return $js;
	}

	public function validateField($value, $record, $type, $section)
	{
		$mask = $this->params->get('params.mask', 0);
		if($mask->mask_type && $this->params->get('params.show_mask', 1))
		{
			if($value == $mask->mask)
			{
				$value = FALSE;
			}
		}
		if($value && $this->params->get('params.regex_val'))
		{
			if(!preg_match('/^' . $this->params->get('params.regex_val') . '$/iU', $value))
			{
				$this->setError(JText::sprintf('CFIELDREGEX', $this->label));

				return FALSE;
			}
		}
		if($value && $this->params->get('params.is_unique') && !$type['id'])
		{
			$db = JFactory::getDbo();

			$db->setQuery(sprintf("SELECT id FROM `#__js_res_record_values` 
				WHERE field_value = '%s' AND record_id != %d AND field_id = %d", 
				$db->escape($value), @$record->id, $this->id));
			if($db->loadResult())
			{
				$this->setError(JText::sprintf('CFIELDUNIQUE', $this->label, $value));

				return FALSE;
			}
		}

		return parent::validate($value, $section);

	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		return $this->onPrepareSave($value, $record, $type, $section);
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		if($this->params->get('params.mask.mask') && $this->params->get('params.show_mask', 1) && ($value == $this->params->get('params.mask.mask')))
		{
			return null;
		}

		$filter = JFilterInput::getInstance();
		return $filter->clean($value);
	}

	public function onRenderFull($record, $type, $section)
	{
		return $this->_render('full', $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		return $this->_render('list', $record, $type, $section);
	}

	private function _render($view, $record, $type, $section)
	{
		if(!$this->value)
		{
			return;
		}
		$readmore = NULL;
		if($view == 'list' && $this->params->get('params.length', 0) > 0)
		{
			$this->value = HTMLFormatHelper::substrHTML($this->value, $this->params->get('params.length'));
			$readmore    = JHtml::link($record->url, $this->params->get('params.seemore', '>>>'), array('title' => JText::_('TEXT_READMORE')));
		}
		$value = $this->value;
		if($this->params->get('params.filter_enable'))
		{
			$tip = ($this->params->get('params.filter_tip') ? JText::sprintf($this->params->get('params.filter_tip'), '<b>' . $this->label . '</b>', '<b>' . $value . '</b>') : NULL);

			switch($this->params->get('params.filter_linkage'))
			{
				case 1 :
					$value = FilterHelper::filterLink('filter_' . $this->id, $value, $value, $this->type_id, $tip, $section);
					break;

				case 2 :
					$value = $value . ' ' . FilterHelper::filterButton('filter_' . $this->id, $value, $this->type_id, $tip, $section, $this->params->get('params.filter_icon', 'funnel-small.png'));
					break;
			}
		}

		$this->value    = $value;
		$this->readmore = $readmore;

		return $this->_display_output($view, $record, $type, $section);
	}

	public function onImport($value, $params, $record = null)
	{
        if(empty($value) && $this->params->get('params.default_val')) {
            $value = $this->params->get('params.default_val');
        }
        
        if(empty($value)) {
            return FALSE;
        }

        if($this->params->get('params.maxlength') > 0 && strlen(strip_tags($value)) > $this->params->get('params.maxlength')) {
            $value = HTMLFormatHelper::cutHTML($value, $this->params->get('params.maxlength'));
        }
        
        if($this->params->get('params.regex_val'))
		{
            if(!preg_match('/^' . $this->params->get('params.regex_val') . '$/iU', $value))
			{
                JError::raiseNotice(100, JText::sprintf('CFIELDREGEX', $this->label).': ' . $value);
				return FALSE;
			}
        }
        
        if($this->params->get('params.is_unique'))
        {
            $db = JFactory::getDbo();

            $db->setQuery(sprintf("SELECT id FROM `#__js_res_record_values` 
                WHERE field_value = '%s' AND record_id != %d AND field_id = %d", 
                $db->escape($value), @$record->id, $this->id));
            if($db->loadResult())
            {
                JError::raiseNotice(100, JText::sprintf('CFIELDUNIQUE', $this->label, $value));
                return FALSE;
            }
        }
		return $value;
	}
}
