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
<div class="row g-2">
	<div class="col-auto">
		<div class="input-group" id="dpfrom<?php echo $this->id; ?>" data-td-target-input="nearest" data-td-target-toggle="nearest">
			<button class="btn btn-outline-secondary" type="button" data-td-target="#dpfrom<?php echo $this->id; ?>" data-td-toggle="datetimepicker">
				<span class="fas fa-calendar"></span> <?php echo \Joomla\CMS\Language\Text::_('CFROM'); ?>
			</button>
			<input <?php echo $this->attr ?> type="text" class="form-control" name="bdpfrom_<?php echo $this->id; ?>" data-td-target="#dpfrom<?php echo $this->id; ?>" />
		</div>
	</div>
	<div class="col-auto">
		<div class="input-group" id="dpto<?php echo $this->id; ?>" data-td-target-input="nearest" data-td-target-toggle="nearest">
			<button class="btn btn-outline-secondary" type="button" data-td-target="#dpto<?php echo $this->id; ?>" data-td-toggle="datetimepicker">
				<span class="fas fa-calendar"></span> <?php echo \Joomla\CMS\Language\Text::_('CTO'); ?>
			</button>
			<input <?php echo $this->attr ?> type="text" class="form-control" name="bdpto_<?php echo $this->id; ?>" data-td-target="#dpto<?php echo $this->id; ?>" />
		</div>
	</div>
</div>

<input type="hidden" id="pickerfrom<?php echo $this->id; ?>" class="input" name="jform[fields][<?php echo $this->id; ?>][0]" value="<?php echo $this->default ?>" />
<input type="hidden" id="pickerto<?php echo $this->id; ?>" class="input" name="jform[fields][<?php echo $this->id; ?>][1]" value="<?php echo @$this->value[1] ?>" />

<script type="text/javascript">
(function() {
	const fromElement = document.getElementById('dpfrom<?php echo $this->id; ?>');
	const toElement = document.getElementById('dpto<?php echo $this->id; ?>');
	const hiddenFrom = document.getElementById('pickerfrom<?php echo $this->id; ?>');
	const hiddenTo = document.getElementById('pickerto<?php echo $this->id; ?>');

	const pickerConfig = {
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
	};

	// Helper function to format date for database
	const formatForDb = (d) => {
		const pad = (n) => n.toString().padStart(2, '0');
		let formatted = d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
		<?php if ($this->is_time): ?>
		formatted += ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
		<?php endif; ?>
		return formatted;
	};

	// From picker
	const fromConfig = Object.assign({}, pickerConfig);
	<?php if ($this->default): ?>
	fromConfig.defaultDate = new Date('<?php echo $this->default; ?>');
	<?php endif; ?>
	const pickerFrom = new tempusDominus.TempusDominus(fromElement, fromConfig);

	// To picker
	const toConfig = Object.assign({}, pickerConfig);
	<?php if (!empty($this->value[1])): ?>
	toConfig.defaultDate = new Date('<?php echo $this->value[1]; ?>');
	<?php endif; ?>
	const pickerTo = new tempusDominus.TempusDominus(toElement, toConfig);

	// Link pickers: From date sets min for To
	pickerFrom.subscribe(tempusDominus.Namespace.events.change, (e) => {
		if (e.date) {
			pickerTo.updateOptions({ restrictions: { minDate: e.date } });
			hiddenFrom.value = formatForDb(e.date);
		}
	});

	// Link pickers: To date sets max for From
	pickerTo.subscribe(tempusDominus.Namespace.events.change, (e) => {
		if (e.date) {
			pickerFrom.updateOptions({ restrictions: { maxDate: e.date } });
			hiddenTo.value = formatForDb(e.date);
		}
	});

	// Error handlers
	pickerFrom.subscribe(tempusDominus.Namespace.events.error, (e) => {
		if (typeof Joomcck !== 'undefined') {
			Joomcck.fieldError(<?php echo $this->id ?>, e.reason);
		}
	});

	pickerTo.subscribe(tempusDominus.Namespace.events.error, (e) => {
		if (typeof Joomcck !== 'undefined') {
			Joomcck.fieldError(<?php echo $this->id ?>, e.reason);
		}
	});
})();
</script>
