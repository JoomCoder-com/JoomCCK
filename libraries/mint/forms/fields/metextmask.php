<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldMETextMask extends \Joomla\CMS\Form\FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'METextMask';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 * @since   11.1
	 */
	protected function getInput()
	{
		$opt = array();

		$opt[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '', \Joomla\CMS\Language\Text::_('Do not use'));
		$opt[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '(###) ### #######', \Joomla\CMS\Language\Text::_('Phone'));
		$opt[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '(###) ###-####', \Joomla\CMS\Language\Text::_('Phone US'));
		$opt[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 'mm/dd/yyyy', \Joomla\CMS\Language\Text::_('Date'));
		$opt[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '#####-###', \Joomla\CMS\Language\Text::_('Code'));
		$opt[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '#### #### #### ####', \Joomla\CMS\Language\Text::_('Credit Card'));
		$opt[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '#', \Joomla\CMS\Language\Text::_('Integer'));
		$opt[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '#####.##', \Joomla\CMS\Language\Text::_('Decimal'));
		$opt[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '#,###.##', \Joomla\CMS\Language\Text::_('Numeric with format'));
		$opt[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '$#,###.##', \Joomla\CMS\Language\Text::_('Dollar'));
		$opt[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '€#,###.##', \Joomla\CMS\Language\Text::_('Euro'));
		$opt[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 'custom', \Joomla\CMS\Language\Text::_('Custom'));
		
		if(!$this->value) $this->value = new stdClass();
		if(!isset($this->value->type)) $this->value->type = false;
		if(!isset($this->value->mask_type)) $this->value->mask_type = false;
		if(!isset($this->value->mask)) $this->value->mask = false;

		$out = '';

		$add = '<br><input style="float:none;" type="radio" name="'.$this->name.'[type]" value="string" '.((!$this->value->type || $this->value->type == 'string' ) ? 'checked' : '').'> '.\Joomla\CMS\Language\Text::_('String');
		$add .= ' <input style="float:none;" type="radio" name="'.$this->name.'[type]" value="date" '.($this->value->type == 'date' ? 'checked' : '').'> '.\Joomla\CMS\Language\Text::_('Date');
		$add .= ' <input style="float:none;"  type="radio" name="'.$this->name.'[type]" value="number" '.($this->value->type == 'number' ? 'checked' : '').'> '.\Joomla\CMS\Language\Text::_('Number');
		$display = ($this->value->mask_type) ? 'block' : 'none';
		$readonly = ($this->value->mask_type && $this->value->mask_type != 'custom') ? 'readonly' : '';

		$out .= \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $opt, $this->name.'[mask_type]', 'class="form-select" onchange="changeMask'.$this->id.'(this.value)"', 'value', 'text', $this->value->mask_type, $this->id."mask_type");

		$out .= '<input class="form-control" type="text" style="display: '.$display.'" name="'.$this->name.'[mask]" id="'.$this->id.'_mask"
			value="'.$this->value->mask.'" '.$readonly.'>';
		$out .= '<br><span id="add_'.$this->id.'"></span>';
		$out .= '
		<script type="text/javascript">
		function changeMask'.$this->id.'(value)
		{
			if(value == "custom")
			{
				value = "'.($this->value->mask && $this->value->mask_type == 'custom' ? $this->value->mask : '').'";
				$("#add_'.$this->id.'").html(\''.$add.'\');
				$("#'.$this->id.'_mask").css("display", "block");
				$("#'.$this->id.'_mask").removeAttr("readonly");
			}
			else if(!value)
			{
				value = "";
				$("#'.$this->id.'_mask").css("display", "none");
				$("#add_'.$this->id.'").empty();
				$("#'.$this->id.'_mask").attr("readonly", "true");
			}
			else
			{
				$("#add_'.$this->id.'").empty();
				$("#'.$this->id.'_mask").css("display", "block");
				$("#'.$this->id.'_mask").attr("readonly", "true");
			}
			$("#'.$this->id.'_mask").val(value)
		}
		'.($this->value->mask_type && $this->value->mask_type == 'custom' ? 'changeMask'.$this->id.'("'.$this->value->mask_type.'")'  : '').'
		</script>
		';
		return $out;
	}
}
