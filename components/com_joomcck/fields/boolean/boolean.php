<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckfield.php';

class JFormFieldCBoolean extends CFormField
{
	public function getInput()
	{
		if($this->value === NULL && $this->params->get('params.default_val') > 0)
		{
			$this->value = ($this->params->get('params.default_val') == 1 ? 1 : -1);
		}
		return $this->_display_input();
	}

	public function onFilterWornLabel($section)
	{
		$value = $this->value;

		$label = JText::_($this->params->get('params.' . $value));
		$icon  = $this->params->get('params.icon_' . $value, ($value == 'true' ? 'tick.png' : 'cross.png'));
		$icon  = JHtml::image(JURI::root() . 'media/mint/icons/16/' . $icon, $label, array(
			'align' => 'absmiddle'
		));

		switch($this->params->get('params.view_what', 'both'))
		{
			case 'label':
				$value = $label;
				break;
			case 'icon':
				$value = $icon;
				break;
			default:
				$value = $icon . ' ' . $label;
		}

		return $value;
	}

	public function onFilterWhere($section, &$query)
	{
		if($this->value)
		{

			if($this->params->get('params.no_value') == 0 || $this->value == 'true')
			{
				$ids = $this->getIds("SELECT record_id FROM `#__js_res_record_values`
					WHERE field_value + 0 = " . ($this->value == 'true' ? 1 : -1) . "
					AND section_id = {$section->id}
					AND field_key = '{$this->key}'");

				return $ids;

				//$sql = implode(',', $ids);

				//$query->where("r.id IN ({$sql})");
			}
			elseif($this->params->get('params.no_value') == 1 && $this->value == 'false')
			{
				$ids = $this->getIds("SELECT record_id FROM `#__js_res_record_values`
					WHERE field_value + 0 = 1
					AND section_id = {$section->id}
					AND field_key = '{$this->key}'");

				return $ids;

				//$sql = implode(',', $ids);

				//$query->where("r.id NOT IN ({$sql})");
			}

		}
	}

	public function onRenderFilter($section, $module = FALSE)
	{
		$document = JFactory::getDocument();

		$label['true']  = JText::_($this->params->get('params.true'));
		$label['false'] = JText::_($this->params->get('params.false'));
		$icon           = array();
		$icon['true']   = JHtml::image(JURI::root() . 'media/mint/icons/16/' . $this->params->get('params.icon_true', 'tick.png'), $label['true'], array(
			'align' => 'absmiddle'
		));
		$icon['false']  = JHtml::image(JURI::root() . 'media/mint/icons/16/' . $this->params->get('params.icon_false', 'cross.png'), $label['false'], array(
			'align' => 'absmiddle'
		));

		switch($this->params->get('params.view_what', 'both'))
		{
			case 'both':
				$label['true']  = $icon['true'] . ' ' . $label['true'];
				$label['false'] = $icon['false'] . ' ' . $label['false'];
				break;
			case 'icon':
				$label['true']  = $icon['true'];
				$label['false'] = $icon['false'];
				break;

		}

		$nums = array();
		if($this->params->get('params.filter_show_number', 1))
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(TRUE);
			$q1    = "SELECT count(record_id) as num
				FROM `#__js_res_record_values`
				WHERE section_id = '{$section->id}' AND field_key = '{$this->key}'
				AND field_value = '1' GROUP BY field_value";
			$db->setQuery($q1);
			if($nums[1] = $db->loadResult())
			{
				$label['true'] .= "  <span class=badge>{$nums[1]}</span>";
			}

			if($this->params->get('params.no_value') == 0)
			{
				$q0 = "SELECT count(record_id) as num
				FROM `#__js_res_record_values`
				WHERE section_id = '{$section->id}' AND field_key = '{$this->key}'
				AND field_value = '-1' GROUP BY field_value";
			}
			else
			{
				$sql = "SELECT record_id FROM `#__js_res_record_values`
					WHERE field_value = '1'
					AND section_id = {$section->id}
					AND field_key = '{$this->key}'";
				$db->setQuery($sql);
				$ids   = $db->loadColumn();
				$ids[] = 0;

				$q0 = "SELECT COUNT(r.id) as num
					FROM `#__js_res_record` AS r
					WHERE r.section_id = '{$section->id}'
					AND r.hidden = 0 AND r.id NOT IN (" . implode(',', $ids) . ")";
				if(!CStatistics::hasUnPublished($section->id))
				{
					$q0 .= ' AND r.published = 1';
				}
			}

			$db->setQuery($q0);
			if($nums[0] = $db->loadResult())
			{
				$label['false'] .= "  <span class=badge>{$nums[0]}</span>";
			}
		}

		$this->labelvalue = $label;

		return $this->_display_filter($section, $module);

	}

	public function onJSValidate()
	{
		$js = FALSE;
		if($this->required)
		{
			$js = "\n\t\tvar bfield_y = jQuery('#boolyes{$this->id}')";
			$js .= "\n\t\tvar bfield_n = jQuery('#boolno{$this->id}')";

			$js .= "\n\t\tif(!bfield_y.prop('checked') && !bfield_n.prop('checked')){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(JText::sprintf('CFIELDREQUIRED', $this->label)) . "');}";
		}

		return $js;
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		return;
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		return (int)($value == 1 ? 1 : ($value == -1 ? -1 : NULL));
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
		if($this->value == 1)
		{
			$value = 'true';
		}
		elseif($this->value == -1)
		{
			$value = 'false';
		}
		elseif(empty($value) && $this->params->get('params.no_value') == 1)
		{
			$value = 'false';
		}
		else
		{
			return;
		}

		$label = JText::_($this->params->get('params.' . $value));
		$icon  = $this->params->get('params.icon_' . $value);
		if($icon)
		{
			$icon = JHtml::image(JURI::root() . 'media/mint/icons/16/' . $icon, strip_tags($label),
				array(
					'align' => 'absmiddle'
				));
		}

		if($this->params->get('params.filter_enable'))
		{
			$tip = ($this->params->get('params.filter_tip') ? JText::sprintf($this->params->get('params.filter_tip'), '<b>' . $this->label . '</b>', '<b>' . $label . '</b>') : NULL);
			switch($this->params->get('params.filter_linkage'))
			{
				case 1:
					$label = FilterHelper::filterLink('filter_' . $this->id, $value, $label, $this->type_id, $tip, $section);
					break;

				case 2:
					$label .= ' ' . FilterHelper::filterButton('filter_' . $this->id, $value, $this->type_id, $tip, $section, $this->params->get('params.filter_icon', 'funnel-small.png'));
					break;
			}
		}

		switch($this->params->get('params.view_what', 'both'))
		{
			case 'label':
				$out = $label;
				break;

			case 'icon':
				if($this->params->get('params.filter_enable') && $this->params->get('params.filter_linkage') == 1)
				{
					$out = FilterHelper::filterLink('filter_' . $this->id, $value, $icon, $this->type_id, $tip, $section);
				}
				else
				{
					$out = $icon;
				}
				break;

			default:
				$out = $icon . ' ' . $label;
		}

		$this->print = $out;

		return $this->_display_output($client, $record, $type, $section);
	}

	public function onImport($value, $params, $record = NULL)
	{
		if(strtolower($value) == 'false')
		{
			$value = 0;
		}

		return ($value ? 1 : -1);
	}

	public function onImportForm($heads, $defaults)
	{
		return $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id));
	}
}