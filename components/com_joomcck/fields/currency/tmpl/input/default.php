<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

$class[] = 'form-control';
$class[] = $this->params->get('core.field_class');
$required = NULL;
if($this->required)
{
	$class[] = 'required';
	$required = ' required="true" ';
}

$class	= ' class="'.implode(' ', $class).'" ';
$required = $required;

// Get currency symbol
$currency_symbol = $this->params->get('params.currency_symbol', '$');
if($currency_symbol === 'custom') {
	$currency_symbol = $this->params->get('params.custom_symbol', '$');
}

$symbol_position = $this->params->get('params.symbol_position', 'before');
$show_symbol = $this->params->get('params.show_symbol_input', 1);
$placeholder = $this->params->get('params.input_placeholder', '0.00') ?? '0.00';
$field_size = $this->params->get('params.field_size', 10);
$decimals = $this->params->get('params.decimals_num', 2);
$max_length = $this->params->get('params.max_num', 15);
$min_val = $this->params->get('params.val_min', '');
$max_val = $this->params->get('params.val_max', '');

// Format the current value for display
$display_value = $this->value ?? '';
if($display_value && is_numeric($display_value)) {
	$display_value = number_format($display_value, $decimals, $this->params->get('params.dseparator', '.'), '');
}
?>

<?php if($show_symbol): ?>
<div class="input-group">
	<?php if($symbol_position === 'before'): ?>
		<span class="input-group-text"><?php echo htmlspecialchars($currency_symbol, ENT_QUOTES, 'UTF-8'); ?></span>
	<?php endif; ?>
	
	<input
		type="text"
		name="jform[fields][<?php echo $this->id; ?>]"
		id="field_<?php echo $this->id; ?>"
		value="<?php echo htmlspecialchars($display_value ?? '', ENT_QUOTES, 'UTF-8'); ?>"
		size="<?php echo $field_size; ?>"
		maxlength="<?php echo $max_length; ?>"
		placeholder="<?php echo htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8'); ?>"
		onKeyUp="Joomcck.fieldErrorClear(<?php echo $this->id; ?>); Joomcck.formatCurrency(this, <?php echo $decimals; ?>, <?php echo $max_length; ?>, '<?php echo $max_val; ?>', '<?php echo $min_val; ?>', <?php echo $this->id; ?>, '<?php echo \Joomla\CMS\Language\Text::sprintf('CURRENCY_MINMAX_ERROR', $this->label, $min_val, $max_val, array('jsSafe' => true)); ?>');"
		onBlur="Joomcck.validateCurrencyField(<?php echo $this->id; ?>);"
		<?php echo $class.$required; ?>/>
	
	<?php if($symbol_position === 'after'): ?>
		<span class="input-group-text"><?php echo htmlspecialchars($currency_symbol, ENT_QUOTES, 'UTF-8'); ?></span>
	<?php endif; ?>
</div>
<?php else: ?>
<input
	type="text"
	name="jform[fields][<?php echo $this->id; ?>]"
	id="field_<?php echo $this->id; ?>"
	value="<?php echo htmlspecialchars($display_value ?? '', ENT_QUOTES, 'UTF-8'); ?>"
	size="<?php echo $field_size; ?>"
	maxlength="<?php echo $max_length; ?>"
	placeholder="<?php echo htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8'); ?>"
	onKeyUp="Joomcck.fieldErrorClear(<?php echo $this->id; ?>); Joomcck.formatCurrency(this, <?php echo $decimals; ?>, <?php echo $max_length; ?>, '<?php echo $max_val; ?>', '<?php echo $min_val; ?>', <?php echo $this->id; ?>, '<?php echo \Joomla\CMS\Language\Text::sprintf('CURRENCY_MINMAX_ERROR', $this->label, $min_val, $max_val, array('jsSafe' => true)); ?>');"
	onBlur="Joomcck.validateCurrencyField(<?php echo $this->id; ?>);"
	<?php echo $class.$required; ?>/>
<?php endif; ?>

<script>
// Currency formatting and validation functions
if (typeof Joomcck === 'undefined') {
	Joomcck = {};
}

Joomcck.formatCurrency = function(element, decimals, maxLength, maxVal, minVal, fieldId, errorMsg) {
	var value = element.value;
	
	// Remove any non-numeric characters except decimal point and minus sign
	value = value.replace(/[^\d\.-]/g, '');
	
	// Ensure only one decimal point
	var parts = value.split('.');
	if (parts.length > 2) {
		value = parts[0] + '.' + parts.slice(1).join('');
	}
	
	// Limit decimal places
	if (parts.length === 2 && parts[1].length > decimals) {
		value = parts[0] + '.' + parts[1].substring(0, decimals);
	}
	
	// Update the field value
	element.value = value;
	
	// Validate min/max if specified
	if (value !== '' && !isNaN(value)) {
		var numValue = parseFloat(value);
		if ((maxVal !== '' && numValue > parseFloat(maxVal)) || (minVal !== '' && numValue < parseFloat(minVal))) {
			// Show error - this would integrate with JoomCCK's error system
			console.warn(errorMsg);
		}
	}
};

Joomcck.validateCurrencyField = function(fieldId) {
	var field = document.getElementById('field_' + fieldId);
	if (field) {
		var value = field.value;
		if (value !== '' && !/^-?\d*\.?\d*$/.test(value)) {
			field.value = value.replace(/[^\d\.-]/g, '');
		}
	}
};
</script>