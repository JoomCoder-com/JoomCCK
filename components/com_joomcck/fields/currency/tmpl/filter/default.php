<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

$min_label = $this->params->get('params.label_min', 'Min');
$max_label = $this->params->get('params.label_max', 'Max');
$steps = $this->params->get('params.steps', '0.01');
$decimals = $this->params->get('params.decimals_num', 2);

// Get currency symbol for display
$currency_symbol = $this->params->get('params.currency_symbol', '$');
if($currency_symbol === 'custom') {
	$currency_symbol = $this->params->get('params.custom_symbol', '$');
}

$symbol_position = $this->params->get('params.symbol_position', 'before');

// Get current filter values
$filter_value = $this->getFilterValue();
$min_value = '';
$max_value = '';

if(is_array($filter_value) && count($filter_value) == 2) {
	$min_value = $filter_value[0];
	$max_value = $filter_value[1];
}

$field_name = 'filter_' . $this->field->id;
?>

<div class="currency-filter-container">
	<div class="row">
		<div class="col-md-6">
			<label for="<?php echo $field_name; ?>_min" class="form-label">
				<?php echo htmlspecialchars($min_label, ENT_QUOTES, 'UTF-8'); ?>
			</label>
			<div class="input-group input-group-sm">
				<?php if($symbol_position === 'before'): ?>
					<span class="input-group-text"><?php echo htmlspecialchars($currency_symbol, ENT_QUOTES, 'UTF-8'); ?></span>
				<?php endif; ?>
				
				<input
					type="number"
					class="form-control"
					id="<?php echo $field_name; ?>_min"
					name="<?php echo $field_name; ?>[0]"
					value="<?php echo htmlspecialchars($min_value, ENT_QUOTES, 'UTF-8'); ?>"
					step="<?php echo $steps; ?>"
					placeholder="<?php echo htmlspecialchars($min_label, ENT_QUOTES, 'UTF-8'); ?>"
					onchange="Joomcck.submitFilterForm();"
				/>
				
				<?php if($symbol_position === 'after'): ?>
					<span class="input-group-text"><?php echo htmlspecialchars($currency_symbol, ENT_QUOTES, 'UTF-8'); ?></span>
				<?php endif; ?>
			</div>
		</div>
		
		<div class="col-md-6">
			<label for="<?php echo $field_name; ?>_max" class="form-label">
				<?php echo htmlspecialchars($max_label, ENT_QUOTES, 'UTF-8'); ?>
			</label>
			<div class="input-group input-group-sm">
				<?php if($symbol_position === 'before'): ?>
					<span class="input-group-text"><?php echo htmlspecialchars($currency_symbol, ENT_QUOTES, 'UTF-8'); ?></span>
				<?php endif; ?>
				
				<input
					type="number"
					class="form-control"
					id="<?php echo $field_name; ?>_max"
					name="<?php echo $field_name; ?>[1]"
					value="<?php echo htmlspecialchars($max_value, ENT_QUOTES, 'UTF-8'); ?>"
					step="<?php echo $steps; ?>"
					placeholder="<?php echo htmlspecialchars($max_label, ENT_QUOTES, 'UTF-8'); ?>"
					onchange="Joomcck.submitFilterForm();"
				/>
				
				<?php if($symbol_position === 'after'): ?>
					<span class="input-group-text"><?php echo htmlspecialchars($currency_symbol, ENT_QUOTES, 'UTF-8'); ?></span>
				<?php endif; ?>
			</div>
		</div>
	</div>
	
	<?php if(!empty($min_value) || !empty($max_value)): ?>
	<div class="mt-2">
		<small class="text-muted">
			<?php 
			if(!empty($min_value) && !empty($max_value)) {
				echo \Joomla\CMS\Language\Text::sprintf('CURRENCY_FILTER_RANGE', 
					($symbol_position === 'before' ? $currency_symbol . ' ' : '') . number_format($min_value, $decimals) . ($symbol_position === 'after' ? ' ' . $currency_symbol : ''),
					($symbol_position === 'before' ? $currency_symbol . ' ' : '') . number_format($max_value, $decimals) . ($symbol_position === 'after' ? ' ' . $currency_symbol : '')
				);
			} elseif(!empty($min_value)) {
				echo \Joomla\CMS\Language\Text::sprintf('CURRENCY_FILTER_MIN', 
					($symbol_position === 'before' ? $currency_symbol . ' ' : '') . number_format($min_value, $decimals) . ($symbol_position === 'after' ? ' ' . $currency_symbol : '')
				);
			} elseif(!empty($max_value)) {
				echo \Joomla\CMS\Language\Text::sprintf('CURRENCY_FILTER_MAX', 
					($symbol_position === 'before' ? $currency_symbol . ' ' : '') . number_format($max_value, $decimals) . ($symbol_position === 'after' ? ' ' . $currency_symbol : '')
				);
			}
			?>
		</small>
	</div>
	<?php endif; ?>
</div>

<style>
.currency-filter-container .input-group-text {
	font-weight: bold;
	background-color: #f8f9fa;
	border-color: #ced4da;
}

.currency-filter-container .form-control:focus {
	border-color: #80bdff;
	box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>