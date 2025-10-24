<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

if(empty($this->value)) return;

// Get currency parameters
$currency_symbol = $this->params->get('params.currency_symbol', '$');
if($currency_symbol === 'custom') {
	$currency_symbol = $this->params->get('params.custom_symbol', '$');
}

$symbol_position = $this->params->get('params.symbol_position', 'before');
$decimals = $this->params->get('params.decimals_num', 2);
$decimal_separator = $this->params->get('params.dseparator', '.');
$thousand_separator = $this->params->get('params.separator', ',');

// Format the currency value
$formatted_value = number_format($this->value, $decimals, $decimal_separator, $thousand_separator);

// Build the display string
$display_value = '';
if($symbol_position === 'before') {
	$display_value = $currency_symbol . $formatted_value;
} else {
	$display_value = $formatted_value . $currency_symbol;
}

// Add space between symbol and amount for better readability
if($symbol_position === 'before') {
	$display_value = $currency_symbol . ' ' . $formatted_value;
} else {
	$display_value = $formatted_value . ' ' . $currency_symbol;
}

// Output the formatted currency
echo '<span class="currency-value" data-currency="' . htmlspecialchars($currency_symbol, ENT_QUOTES, 'UTF-8') . '" data-amount="' . htmlspecialchars($this->value, ENT_QUOTES, 'UTF-8') . '">';
echo htmlspecialchars($display_value, ENT_QUOTES, 'UTF-8');
echo '</span>';
?>