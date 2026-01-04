<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
$filter_id = $module.$this->id;
?>
<style>
#filter-collapse,
#vtabs-content,
#tab-ke45f9d3bb288049d6cc4a470829a9e43 {
	overflow: visible;
}
</style>
<div class="row g-2">
	<div class="col-auto">
		<div class="input-group" id="dpfrom<?php echo $filter_id; ?>" data-td-target-input="nearest" data-td-target-toggle="nearest">
			<button class="btn btn-outline-secondary" type="button" data-td-target="#dpfrom<?php echo $filter_id; ?>" data-td-toggle="datetimepicker">
				<span class="fas fa-calendar"></span> <?php echo \Joomla\CMS\Language\Text::_('FROM'); ?>
			</button>
			<input type="text" name="bdpfrom_<?php echo $filter_id; ?>" value="" class="form-control" data-td-target="#dpfrom<?php echo $filter_id; ?>" />
		</div>
	</div>
	<div class="col-auto">
		<div class="input-group" id="dpto<?php echo $filter_id; ?>" data-td-target-input="nearest" data-td-target-toggle="nearest">
			<button class="btn btn-outline-secondary" type="button" data-td-target="#dpto<?php echo $filter_id; ?>" data-td-toggle="datetimepicker">
				<span class="fas fa-calendar"></span> <?php echo \Joomla\CMS\Language\Text::_('TO'); ?>
			</button>
			<input type="text" name="bdpto_<?php echo $filter_id; ?>" value="" class="form-control" data-td-target="#dpto<?php echo $filter_id; ?>" />
		</div>
	</div>
</div>
<input type="hidden" value="<?php echo @$this->value[0];?>" id="filter0_<?php echo $filter_id;?>" name="filters[<?php echo $this->key;?>][0]">
<input type="hidden" value="<?php echo @$this->value[1];?>" id="filter1_<?php echo $filter_id;?>" name="filters[<?php echo $this->key;?>][1]">

<script type="text/javascript">
(function() {
	const fromElement = document.getElementById('dpfrom<?php echo $filter_id; ?>');
	const toElement = document.getElementById('dpto<?php echo $filter_id; ?>');
	const hiddenFrom = document.getElementById('filter0_<?php echo $filter_id; ?>');
	const hiddenTo = document.getElementById('filter1_<?php echo $filter_id; ?>');

	const pickerConfig = {
		display: {
			components: {
				calendar: true,
				date: true,
				month: true,
				year: true,
				decades: true,
				clock: <?php echo $this->filter_is_time ? 'true' : 'false'; ?>,
				hours: <?php echo $this->filter_is_time ? 'true' : 'false'; ?>,
				minutes: <?php echo $this->filter_is_time ? 'true' : 'false'; ?>,
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
			format: '<?php echo $this->td_filter_format; ?>',
			hourCycle: 'h23'
		}
	};

	// Helper function to format date for database
	const formatForDb = (d) => {
		const pad = (n) => n.toString().padStart(2, '0');
		let formatted = d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
		<?php if ($this->filter_is_time): ?>
		formatted += ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
		<?php endif; ?>
		return formatted;
	};

	// From picker
	const fromConfig = Object.assign({}, pickerConfig);
	<?php if (!empty($this->value[0])): ?>
	fromConfig.defaultDate = new Date('<?php echo $this->value[0]; ?>');
	<?php endif; ?>
	const pickerFrom = new tempusDominus.TempusDominus(fromElement, fromConfig);

	// To picker
	const toConfig = Object.assign({}, pickerConfig);
	<?php if (!empty($this->value[1])): ?>
	toConfig.defaultDate = new Date('<?php echo $this->value[1]; ?>');
	<?php endif; ?>
	const pickerTo = new tempusDominus.TempusDominus(toElement, toConfig);

	// Track if first change (to avoid setting restrictions on initial load)
	let fromChanged = false;
	let toChanged = false;

	// Link pickers: From date sets min for To
	pickerFrom.subscribe(tempusDominus.Namespace.events.change, (e) => {
		if (e.date) {
			if (fromChanged) {
				pickerTo.updateOptions({ restrictions: { minDate: e.date } });
			}
			fromChanged = true;
			hiddenFrom.value = formatForDb(e.date);
		}
	});

	// Link pickers: To date sets max for From
	pickerTo.subscribe(tempusDominus.Namespace.events.change, (e) => {
		if (e.date) {
			if (toChanged) {
				pickerFrom.updateOptions({ restrictions: { maxDate: e.date } });
			}
			toChanged = true;
			hiddenTo.value = formatForDb(e.date);
		}
	});

	// Error handlers
	pickerFrom.subscribe(tempusDominus.Namespace.events.error, (e) => {
		if (typeof Joomcck !== 'undefined') {
			Joomcck.fieldError('<?php echo $filter_id ?>', e.reason);
		}
	});

	pickerTo.subscribe(tempusDominus.Namespace.events.error, (e) => {
		if (typeof Joomcck !== 'undefined') {
			Joomcck.fieldError('<?php echo $filter_id ?>', e.reason);
		}
	});
})();
</script>
