<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Form Field class for the Joomla Platform.
 * Supports a generic list of options.
 *
 * @since  1.7.0
 */
class JFormFieldMlist extends \Joomla\CMS\Form\FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.7.0
	 */
	protected $type = 'Mlist';

	/**
	 * Method to get the field input markup for a generic list.
	 * Use the multiple attribute to enable multiselect.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   3.7.0
	 */
	protected function getInput()
	{
		$condition = $this->getAttribute('condition', '{}');
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= !empty($this->class) ? ' class="' . $this->class . '"' : 'form-select';
		$attr .= !empty($this->size) ? ' size="' . $this->size . '"' : '';
		$attr .= $this->multiple ? ' multiple' : '';
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= $this->autofocus ? ' autofocus' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true' || (string) $this->disabled == '1'|| (string) $this->disabled == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();

		// Create a read-only list (no name) with hidden input(s) to store the value(s).
		if ((string) $this->readonly == '1' || (string) $this->readonly == 'true')
		{
			$html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);

			// E.g. form field type tag sends $this->value as array
			if ($this->multiple && is_array($this->value))
			{
				if (!count($this->value))
				{
					$this->value[] = '';
				}

				foreach ($this->value as $value)
				{
					$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($value, ENT_COMPAT, 'UTF-8') . '"/>';
				}
			}
			else
			{
				$html[] = '<input type="hidden" name="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"/>';
			}
		}
		else
		// Create a regular list passing the arguments in an array.
		{
			$listoptions = array();
			$listoptions['option.key'] = 'value';
			$listoptions['option.text'] = 'text';
			$listoptions['list.select'] = $this->value;
			$listoptions['id'] = $this->id;
			$listoptions['list.translate'] = false;
			$listoptions['option.attr'] = 'optionattr';
			$listoptions['list.attr'] = trim($attr);

			$html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $options, $this->name, $listoptions);
			if($condition != '[]') {

				$html[] = "
				<script>
				(function($){
					var options = [];
					$('#{$this->id} option').each(function(i, e){
						options.push(parseInt($(this).val()));	
					});
					var conditions = JSON.parse('{$condition}');
					var inputs = [];
					var ids = {};
					$.each(conditions, function(i, list){
						var neg = (i.substr(0, 3) == 'not');
						if(neg) {
							i = i.substr(3, i.length - 3);
						}
						i = parseInt(i);

						inputs[i] = [];
						$.each(list, function(k, id){
							var show = neg ? false : true;
							inputs[i][id] = show;
							ids[id] = neg;
						});
					});

					$('#{$this->id}').change(function(){
						var v = parseInt($(this).val());
						$.each(ids, function(id, neg){
							if(inputs[v] == undefined ) {
								(neg ? show(id) : hide(id));
							} else if(inputs[v][id] == true){
								show(id);
							} else if (inputs[v][id] == undefined) {
								(neg ? show(id) : hide(id));
							} else { 
								hide(id);
							}
						});
					});
					
					function show(l) {
						if(l.substr(0, 9) == 'fieldset-') {
							$('#' + l).show() 
						} else {
							$('#tr_' + l).show() 
						}
					}
					function hide(l) {
						if(l.substr(0, 9) == 'fieldset-') {
							$('#' + l).hide() 
						} else {
							$('#tr_' + l).hide() 
						}
					}
				}(jQuery));
				</script>
				";
			}
		}

		return implode($html);
	}

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   3.7.0
	 */
	protected function getOptions()
	{
		$fieldname = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname);
		$options   = array();

		foreach ($this->element->xpath('option') as $option)
		{
			// Filter requirements
			if ($requires = explode(',', (string) $option['requires']))
			{
				// Requires multilanguage
				if (in_array('multilanguage', $requires) && !\Joomla\CMS\Language\Multilanguage::isEnabled())
				{
					continue;
				}

				// Requires associations
				if (in_array('associations', $requires) && !\Joomla\CMS\Language\Associations::isEnabled())
				{
					continue;
				}

				// Requires adminlanguage
				if (in_array('adminlanguage', $requires) && !\Joomla\CMS\Helper\ModuleHelper::isAdminMultilang())
				{
					continue;
				}

				// Requires vote plugin
				if (in_array('vote', $requires) && !\Joomla\CMS\Plugin\PluginHelper::isEnabled('content', 'vote'))
				{
					continue;
				}
			}

			$value = (string) $option['value'];
			$text  = trim((string) $option) != '' ? trim((string) $option) : $value;

			$disabled = (string) $option['disabled'];
			$disabled = ($disabled == 'true' || $disabled == 'disabled' || $disabled == '1');
			$disabled = $disabled || ($this->readonly && $value != $this->value);

			$checked = (string) $option['checked'];
			$checked = ($checked == 'true' || $checked == 'checked' || $checked == '1');

			$selected = (string) $option['selected'];
			$selected = ($selected == 'true' || $selected == 'selected' || $selected == '1');

			$tmp = array(
					'value'    => $value,
					'text'     => \Joomla\CMS\Language\Text::alt($text, $fieldname),
					'disable'  => $disabled,
					'class'    => (string) $option['class'],
					'selected' => ($checked || $selected),
					'checked'  => ($checked || $selected),
			);

			// Set some event handler attributes. But really, should be using unobtrusive js.
			$tmp['onclick']  = (string) $option['onclick'];
			$tmp['onchange'] = (string) $option['onchange'];

			if ((string) $option['showon'])
			{
				$tmp['optionattr'] = " data-showon='" .
					json_encode(
						\Joomla\CMS\Form\FormHelper::parseShowOnConditions((string) $option['showon'], $this->formControl, $this->group)
						)
					. "'";
			}
			// Add the option object to the result set.
			$options[] = (object) $tmp;
		}

		if ($this->element['useglobal'])
		{
			$tmp        = new stdClass;
			$tmp->value = '';
			$tmp->text  = \Joomla\CMS\Language\Text::_('JGLOBAL_USE_GLOBAL');
			$component  = \Joomla\CMS\Factory::getApplication()->input->getCmd('option');

			// Get correct component for menu items
			if ($component == 'com_menus')
			{
				$link      = $this->form->getData()->get('link');
				$uri       = new \Joomla\CMS\Uri\Uri($link);
				$component = $uri->getVar('option', 'com_menus');
			}

			$params = \Joomla\CMS\Component\ComponentHelper::getParams($component);
			$value  = $params->get($this->fieldname);

			// Try with global configuration
			if (is_null($value))
			{
				$value = \Joomla\CMS\Factory::getConfig()->get($this->fieldname);
			}

			// Try with menu configuration
			if (is_null($value) && \Joomla\CMS\Factory::getApplication()->input->getCmd('option') == 'com_menus')
			{
				$value = \Joomla\CMS\Component\ComponentHelper::getParams('com_menus')->get($this->fieldname);
			}

			if (!is_null($value))
			{
				$value = (string) $value;

				foreach ($options as $option)
				{
					if ($option->value === $value)
					{
						$value = $option->text;

						break;
					}
				}

				$tmp->text = \Joomla\CMS\Language\Text::sprintf('JGLOBAL_USE_GLOBAL_VALUE', $value);
			}

			array_unshift($options, $tmp);
		}

		reset($options);

		return $options;
	}

	/**
	 * Method to add an option to the list field.
	 *
	 * @param   string  $text        Text/Language variable of the option.
	 * @param   array   $attributes  Array of attributes ('name' => 'value' format)
	 *
	 * @return  \Joomla\CMS\Form\Field\ListField  For chaining.
	 *
	 * @since   3.7.0
	 */
	public function addOption($text, $attributes = array())
	{
		if ($text && $this->element instanceof SimpleXMLElement)
		{
			$child = $this->element->addChild('option', $text);

			foreach ($attributes as $name => $value)
			{
				$child->addAttribute($name, $value);
			}
		}

		return $this;
	}

	/**
	 * Method to get certain otherwise inaccessible properties from the form field object.
	 *
	 * @param   string  $name  The property name for which to get the value.
	 *
	 * @return  mixed  The property value or null.
	 *
	 * @since   3.7.0
	 */
	public function __get($name)
	{
		if ($name == 'options')
		{
			return $this->getOptions();
		}

		return parent::__get($name);
	}
}
