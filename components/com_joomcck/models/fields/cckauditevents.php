<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfieldradio');

class JFormFieldCckAuditEvents extends \Joomla\CMS\Form\FormField
{

	public $type = 'CckAuditEvents';

	public function getInput()
	{

		$options   = array();
		$options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '0', \Joomla\CMS\Language\Text::_('JNO'), 'value', 'text');
		$options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '1', \Joomla\CMS\Language\Text::_('JYES'), 'value', 'text');

		$index = (int)$this->element->attributes()->index;

		/*$input   = array();
		$input[] = '<fieldset id="params_audit_al' . $index . '" class="radio btn-group m-0">';

		$value = isset($this->value->on) ? $this->value->on : $this->default;




		foreach($options as $i => $option)
		{
			// Initialize some option attributes.
			$checked = ((string)$option->value == (string)$value) ? ' checked="checked"' : '';
			$input[] = '<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '[on]' . '" value="'
				. htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . ' />';

			$input[] = '<label for="' . $this->id . $i . '" >'
				. \Joomla\CMS\Language\Text::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) . '</label>';

			$required = '';
		}
		$input[] = '</fieldset>';*/

		$inputData = [
			'options' => $options,
			'id' => $this->id,
			'name' => $this->name . '[on]',
			'class' => $this->class,
			'onchange' => $this->onchange,
			'dataAttribute' => '',
			'disabled' => '',
			'readonly' => '',
			'value' => isset($this->value->on) ? $this->value->on : 0

		];


		$input = Joomla\CMS\Layout\LayoutHelper::render('joomla.form.field.radio.switcher',$inputData);

		$value = isset($this->value->msg) && !empty($this->value->msg) ? $this->value->msg : 'CAUDLOG' . $index;

		$msg = '<input class="form-control form-control-sm" type="text" value="' . $value . '" name="' . $this->name . '[msg]">';

		$patern = '<style>fieldset[id*="params_audit"]{margin: 0px !important;padding: 0px !important}fieldset[id*="params_audit"] .switcher{width: 8rem;}</style><div class="d-flex align-items-center"><div>%s</div><div>%s</div></div>';
		return sprintf($patern, $input, $msg);
	}
}
