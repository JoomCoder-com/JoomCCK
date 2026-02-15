<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Checkbox Toggle Layout
 *
 * Tailwind CSS + DaisyUI replacement for Bootstrap btn-check toggle with tooltip.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

extract($displayData);

// Initialize field attributes
$class     = $class ? ' ' . $class : '';
$disabled  = $disabled ? ' disabled' : '';
$required  = $required ? ' required' : '';
$autofocus = $autofocus ? ' autofocus' : '';
$checked   = $checked ? ' checked' : '';

$onclick  = $onclick ? ' onclick="' . $onclick . '"' : '';
$onchange = $onchange ? ' onchange="' . $onchange . '"' : '';

?>
<div class="inline-flex items-center group relative">
	<input
		type="checkbox"
		name="<?php echo $name; ?>"
		id="<?php echo $id; ?>"
		class="peer hidden"
		value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>"
		<?php echo $checked . $disabled . $required . $autofocus . $onclick . $onchange; ?>
	>
	<label class="inline-flex items-center justify-center w-8 h-8 rounded-lg cursor-pointer transition-all
				  text-gray-500 bg-white border border-gray-300 hover:bg-gray-50
				  peer-checked:bg-primary peer-checked:text-white peer-checked:border-primary
				  <?php echo $class; ?>"
		   for="<?php echo $id; ?>"
		   title="<?php echo htmlspecialchars($label . ' - ' . $description, ENT_QUOTES, 'UTF-8'); ?>">
		<i class="<?php echo $iconLabel ?>"></i>
	</label>
	<div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1.5 bg-gray-900 text-white text-xs rounded-lg
				opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50 pointer-events-none">
		<strong><?php echo $label ?></strong><br><?php echo $description ?>
		<div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
	</div>
</div>
