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

		$label = \Joomla\CMS\Language\Text::_($this->params->get('params.' . $value));
		$icon  = $this->params->get('params.icon_' . $value, ($value == 'true' ? 'tick.png' : 'cross.png'));
		$icon  = \Joomla\CMS\HTML\HTMLHelper::image(\Joomla\CMS\Uri\Uri::root() . 'media/com_joomcck/icons/16/' . $icon, $label, array(
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

			$db = \Joomla\CMS\Factory::getDbo();
			if($this->params->get('params.no_value') == 0 || $this->value == 'true')
			{
				$ids = $this->getIds("SELECT record_id FROM `#__js_res_record_values`
					WHERE field_value + 0 = " . ($this->value == 'true' ? 1 : -1) . "
					AND section_id = " . (int)$section->id . "
					AND field_key = " . $db->quote($this->key));

				return $ids;

				//$sql = implode(',', $ids);

				//$query->where("r.id IN ({$sql})");
			}
			elseif($this->params->get('params.no_value') == 1 && $this->value == 'false')
			{
				$ids = $this->getIds("SELECT record_id FROM `#__js_res_record_values`
					WHERE field_value + 0 = 1
					AND section_id = " . (int)$section->id . "
					AND field_key = " . $db->quote($this->key));

				return $ids;

				//$sql = implode(',', $ids);

				//$query->where("r.id NOT IN ({$sql})");
			}

		}
	}

	public function onRenderFilter($section, $module = FALSE)
	{
		$document = \Joomla\CMS\Factory::getDocument();

		$label['true']  = \Joomla\CMS\Language\Text::_($this->params->get('params.true'));
		$label['false'] = \Joomla\CMS\Language\Text::_($this->params->get('params.false'));
		$icon           = array();
		$icon['true']   = \Joomla\CMS\HTML\HTMLHelper::image(\Joomla\CMS\Uri\Uri::root() . 'media/com_joomcck/icons/16/' . $this->params->get('params.icon_true', 'tick.png'), $label['true'], array(
			'align' => 'absmiddle'
		));
		$icon['false']  = \Joomla\CMS\HTML\HTMLHelper::image(\Joomla\CMS\Uri\Uri::root() . 'media/com_joomcck/icons/16/' . $this->params->get('params.icon_false', 'cross.png'), $label['false'], array(
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

			// true
			$db    = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(TRUE);
			$q1    = "SELECT count(record_id) as num
				FROM `#__js_res_record_values`
				WHERE section_id = " . (int)$section->id . " AND field_key = " . $db->quote($this->key) . "
				AND field_value = '1' GROUP BY field_value";
			$db->setQuery($q1);

			if($nums[1] = $db->loadResult())
			{

				$badgeClass = $nums[1] > 0 ? 'bg-success' : 'bg-light border text-dark';

				$label['true'] .= " <span class='badge $badgeClass'>{$nums[1]}</span>";
			}

			// false
			if($this->params->get('params.no_value') == 0)
			{
				$q0 = "SELECT count(record_id) as num
				FROM `#__js_res_record_values`
				WHERE section_id = " . (int)$section->id . " AND field_key = " . $db->quote($this->key) . "
				AND field_value = '-1' GROUP BY field_value";
			}
			else
			{
				$sql = "SELECT record_id FROM `#__js_res_record_values`
					WHERE field_value = '1'
					AND section_id = " . (int)$section->id . "
					AND field_key = " . $db->quote($this->key);
				$db->setQuery($sql);
				$ids   = $db->loadColumn();
				$ids[] = 0;

				$q0 = "SELECT COUNT(r.id) as num
					FROM `#__js_res_record` AS r
					WHERE r.section_id = " . (int)$section->id . "
					AND r.hidden = 0 AND r.id NOT IN (" . implode(',', $ids) . ")";
				if(!CStatistics::hasUnPublished($section->id))
				{
					$q0 .= ' AND r.published = 1';
				}
			}

			$db->setQuery($q0);

			if($nums[0] = $db->loadResult())
			{
				$badgeClass = $nums[0] > 0 ? 'bg-success' : 'bg-light border text-dark';

				$label['false'] .= " <span class='badge $badgeClass'>{$nums[0]}</span>";
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

			$js .= "\n\t\tif(!bfield_y.prop('checked') && !bfield_n.prop('checked')){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(\Joomla\CMS\Language\Text::sprintf('CFIELDREQUIRED', $this->label)) . "');}";
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

		$label = \Joomla\CMS\Language\Text::_($this->params->get('params.' . $value));
		$icon  = $this->params->get('params.icon_' . $value);
		if($icon)
		{
			$icon = \Joomla\CMS\HTML\HTMLHelper::image(\Joomla\CMS\Uri\Uri::root() . 'media/com_joomcck/icons/16/' . $icon, strip_tags($label),
				array(
					'align' => 'absmiddle'
				));
		}

		if($this->params->get('params.filter_enable'))
		{
			$tip = ($this->params->get('params.filter_tip') ? \Joomla\CMS\Language\Text::sprintf($this->params->get('params.filter_tip'), '<b>' . $this->label . '</b>', '<b>' . $label . '</b>') : NULL);
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