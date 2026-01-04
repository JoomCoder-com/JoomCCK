<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
?>

<div class="input-group" id="datetimepicker<?php echo $this->id; ?>" data-td-target-input="nearest" data-td-target-toggle="nearest">
	<input <?php echo $this->attr ?> type="text" class="form-control" name="bdp_<?php echo $this->id; ?>" data-td-target="#datetimepicker<?php echo $this->id; ?>" />
	<button class="btn btn-outline-secondary" type="button" data-td-target="#datetimepicker<?php echo $this->id; ?>" data-td-toggle="datetimepicker">
		<span class="fas fa-calendar"></span>
	</button>
</div>
<input type="hidden" id="picker<?php echo $this->id; ?>" class="input" name="jform[fields][<?php echo $this->id; ?>][]" value="<?php echo $this->default ?>" />

<script type="text/javascript">
(function() {
	const element = document.getElementById('datetimepicker<?php echo $this->id; ?>');
	const hiddenInput = document.getElementById('picker<?php echo $this->id; ?>');

	const picker = new tempusDominus.TempusDominus(element, {
		display: {
			components: {
				calendar: true,
				date: true,
				month: true,
				year: true,
				decades: true,
				clock: <?php echo $this->is_time ? 'true' : 'false'; ?>,
				hours: <?php echo $this->is_time ? 'true' : 'false'; ?>,
				minutes: <?php echo $this->is_time ? 'true' : 'false'; ?>,
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
			format: '<?php echo $this->td_format; ?>',
			hourCycle: 'h23'
		}
		<?php if ($this->default): ?>,
		defaultDate: new Date('<?php echo $this->default; ?>')
		<?php endif; ?>
	});

	picker.subscribe(tempusDominus.Namespace.events.change, (e) => {
		if (e.date) {
			// Format date for database storage (yyyy-MM-dd HH:mm:ss)
			const d = e.date;
			const pad = (n) => n.toString().padStart(2, '0');
			let formatted = d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
			<?php if ($this->is_time): ?>
			formatted += ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
			<?php endif; ?>
			hiddenInput.value = formatted;
		}
	});

	picker.subscribe(tempusDominus.Namespace.events.error, (e) => {
		if (typeof Joomcck !== 'undefined') {
			Joomcck.fieldError(<?php echo $this->id ?>, e.reason);
		}
	});
})();
</script>
