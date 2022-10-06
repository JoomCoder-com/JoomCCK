<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfieldradio');

class JFormFieldCckAuditEvents extends JFormField
{

	public $type = 'CckAuditEvents';

	public function getInput()
	{

		$options   = array();
		$options[] = JHtml::_('select.option', '0', 'JNO', 'value', 'text');
		$options[] = JHtml::_('select.option', '1', 'JYES', 'value', 'text');

		$index = (int)$this->element->attributes()->index;

		$input   = array();
		$input[] = '<fieldset id="params_audit_al' . $index . '" class="radio btn-group">';

		$value = isset($this->value->on) ? $this->value->on : $this->default;

		foreach($options as $i => $option)
		{
			// Initialize some option attributes.
			$checked = ((string)$option->value == (string)$value) ? ' checked="checked"' : '';
			$input[] = '<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '[on]' . '" value="'
				. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . ' />';

			$input[] = '<label for="' . $this->id . $i . '" >'
				. JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) . '</label>';

			$required = '';
		}
		$input[] = '</fieldset>';

		$value = isset($this->value->msg) && !empty($this->value->msg) ? $this->value->msg : 'CAUDLOG' . $index;

		$msg = '<input style="width:100%" class="input-medium" type="text" value="' . $value . '" name="' . $this->name . '[msg]">';

		$patern = '<div class="row"><div class="span4">%s</div><div class="span7">%s</div></div>';
		return sprintf($patern, implode('', $input), $msg);
	}
}
