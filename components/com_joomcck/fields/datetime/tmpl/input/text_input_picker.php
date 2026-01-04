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

$doc = \Joomla\CMS\Factory::getDocument();
$doc->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/vendors/jquery.inputmask/dist/min/inputmask/inputmask.min.js');
$doc->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/vendors/jquery.inputmask/dist/min/inputmask/inputmask.date.extensions.min.js');
$doc->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/vendors/jquery.inputmask/dist/min/inputmask/inputmask.numeric.extensions.min.js');

$class    = ' class="' . $this->params->get('core.field_class', 'form-control') . ($this->required ? ' required' : NULL) . '" ';
$required = $this->required ? 'required="true" ' : NULL;

$mask = $this->params->get('tmpl_text_input_picker.mask', 'd/m/y');
if((int)$mask == 100) {
	$mask = $this->params->get('tmpl_text_input_picker.custom', 'd/m/y');
}

// Convert mask to Tempus Dominus format
$td_format = str_replace(["d","m","y","h","s"], ["dd","MM","yyyy","HH","mm"], $mask);
$comment_format = str_replace(["d","m","y","h","s"], ["31","12","2000","23","59"], $mask);
$php_format = str_replace(["d","m","y","h","s"], ["d","m","Y","H","i"], $mask);
$default = $this->default ? date($php_format, strtotime($this->default)) : '';

// Check if time is included in the mask
$has_time = (strpos($mask, 'h') !== false || strpos($mask, 's') !== false);
?>

<div class="input-group" id="datetimepicker<?php echo $this->id; ?>" data-td-target-input="nearest" data-td-target-toggle="nearest">
	<input <?php echo $this->attr ?> type="text" value="<?php echo $default; ?>" name="dp_text_<?php echo $this->id; ?>" <?php echo $class . $required; ?> id="dp_text_field_<?php echo $this->id; ?>" data-inputmask="'mask': '<?php echo $mask ?>'" data-td-target="#datetimepicker<?php echo $this->id; ?>" />
	<button class="btn btn-outline-secondary" type="button" data-td-target="#datetimepicker<?php echo $this->id; ?>" data-td-toggle="datetimepicker">
		<span class="fas fa-calendar"></span>
	</button>
</div>

<p><small><?php echo \Joomla\CMS\Language\Text::_('F_FORMAT') ?>: <?php echo $comment_format ?></small></p>

<input type="hidden" id="picker<?php echo $this->id; ?>" class="input" name="jform[fields][<?php echo $this->id; ?>][]" value="<?php echo $this->default ?>" />

<script type='text/javascript'>
(function(){
	const textField = document.getElementById('dp_text_field_<?php echo $this->id; ?>');
	const hiddenInput = document.getElementById('picker<?php echo $this->id; ?>');
	const element = document.getElementById('datetimepicker<?php echo $this->id; ?>');
	const hasTime = <?php echo $has_time ? 'true' : 'false'; ?>;

	// Initialize input mask
	Inputmask().mask(textField);

	// Helper function to format date for database
	const formatForDb = (d) => {
		const pad = (n) => n.toString().padStart(2, '0');
		let formatted = d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
		if (hasTime) {
			formatted += ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
		}
		return formatted;
	};

	// Helper function to parse date from text field
	const parseFromMask = (value) => {
		// Parse based on mask format: d/m/y or d/m/y h:s etc.
		const parts = value.split(/[\/\-\.\s:]+/);
		if (parts.length >= 3) {
			const day = parseInt(parts[0]) || 1;
			const month = parseInt(parts[1]) - 1 || 0;
			const year = parseInt(parts[2]) || new Date().getFullYear();
			const hours = parts[3] ? parseInt(parts[3]) : 0;
			const minutes = parts[4] ? parseInt(parts[4]) : 0;
			return new Date(year, month, day, hours, minutes, 0);
		}
		return null;
	};

	// Handle manual text field change
	textField.addEventListener('change', function() {
		const d = parseFromMask(this.value);
		if (d && !isNaN(d)) {
			hiddenInput.value = formatForDb(d);
		}
	});

	// Initialize Tempus Dominus picker
	const picker = new tempusDominus.TempusDominus(element, {
		display: {
			components: {
				calendar: true,
				date: true,
				month: true,
				year: true,
				decades: true,
				clock: hasTime,
				hours: hasTime,
				minutes: hasTime,
				seconds: false
			},
			icons: {
				type: 'icons',
				time: 'fas fa-clock',
				date: 'fas fa-calendar',
				up: 'fas fa-arrow-up',
				down: 'fas fa-arrow-down',
				previous: 'fas fa-chevron-left',
				next: 'fas fa-chevron-right',
				today: 'fas fa-calendar-check',
				clear: 'fas fa-trash',
				close: 'fas fa-xmark'
			}
		},
		localization: {
			format: '<?php echo $td_format; ?>',
			hourCycle: 'h23'
		}
		<?php if ($this->default): ?>,
		defaultDate: new Date('<?php echo $this->default; ?>')
		<?php endif; ?>
	});

	picker.subscribe(tempusDominus.Namespace.events.change, (e) => {
		if (e.date) {
			hiddenInput.value = formatForDb(e.date);
		}
	});

	picker.subscribe(tempusDominus.Namespace.events.error, (e) => {
		if (typeof Joomcck !== 'undefined') {
			Joomcck.fieldError(<?php echo $this->id ?>, e.reason);
		}
	});
})();
</script>
