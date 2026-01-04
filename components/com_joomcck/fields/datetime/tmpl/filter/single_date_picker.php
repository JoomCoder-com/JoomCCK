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
<style>
#filter-collapse,
#vtabs-content,
#tab-ke45f9d3bb288049d6cc4a470829a9e43 {
	overflow: visible;
}
</style>
<div class="input-group" id="datetimepicker<?php echo $this->id; ?>" data-td-target-input="nearest" data-td-target-toggle="nearest">
	<input <?php echo $this->attr ?> type="text" class="form-control" name="bdp_<?php echo $this->id; ?>" data-td-target="#datetimepicker<?php echo $this->id; ?>" />
	<button class="btn btn-outline-secondary" type="button" data-td-target="#datetimepicker<?php echo $this->id; ?>" data-td-toggle="datetimepicker">
		<span class="fas fa-calendar"></span>
	</button>
</div>
<input type="hidden" value="<?php echo $this->value[0];?>" id="filter_<?php echo $module.$this->id;?>" name="filters[<?php echo $this->key;?>][]">

<script type="text/javascript">
(function() {
	const element = document.getElementById('datetimepicker<?php echo $this->id; ?>');
	const hiddenInput = document.getElementById('filter_<?php echo $module.$this->id; ?>');

	const picker = new tempusDominus.TempusDominus(element, {
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
		<?php if (isset($this->value[0]) && $this->value[0]): ?>,
		defaultDate: new Date('<?php echo $this->value[0]; ?>')
		<?php endif; ?>
	});

	picker.subscribe(tempusDominus.Namespace.events.change, (e) => {
		if (e.date) {
			// Format date for database storage
			const d = e.date;
			const pad = (n) => n.toString().padStart(2, '0');
			let formatted = d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
			<?php if ($this->filter_is_time): ?>
			formatted += ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
			<?php endif; ?>
			hiddenInput.value = formatted;
		}
	});
})();
</script>
