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
<ul class="list-group mb-3" id="dates_list<?php echo $this->id; ?>">
<?php foreach($this->value AS $date): ?>
	<li class="list-group-item"><a>
		<span class="float-end mdp-close<?php echo $this->id; ?>" style="cursor:pointer;"><?php echo HTMLFormatHelper::icon('cross.png') ?></span>
		<span class="mdp-list<?php echo $this->id; ?>" data-date="<?php echo $date ?>"><?php echo $date ?></span>
		<input type="hidden" name="jform[fields][<?php echo $this->id; ?>][]" value="<?php echo $date ?>" /></a>
	</li>
<?php endforeach; ?>
</ul>

<div class="input-group" id="datetimepicker<?php echo $this->id; ?>" data-td-target-input="nearest" data-td-target-toggle="nearest">
	<input <?php echo $this->attr ?> id="mdpinput<?php echo $this->id; ?>" type="text" class="form-control" name="bdp_<?php echo $this->id; ?>" data-td-target="#datetimepicker<?php echo $this->id; ?>" />
	<button class="btn btn-outline-secondary" type="button" data-td-target="#datetimepicker<?php echo $this->id; ?>" data-td-toggle="datetimepicker">
		<span class="fas fa-calendar"></span>
	</button>
</div>
<?php if($this->params->get('params.max_dates', 0) > 0): ?>
	<small>
		<?php \Joomla\CMS\Language\Text::printf('F_MAX_DATE_INFO', $this->params->get('params.max_dates', 0)) ?>
	</small>
<?php endif; ?>

<script type="text/javascript">
(function() {
	const element = document.getElementById('datetimepicker<?php echo $this->id; ?>');
	const datesList = document.getElementById('dates_list<?php echo $this->id; ?>');
	const input = document.getElementById('mdpinput<?php echo $this->id; ?>');
	const max = parseInt('<?php echo $this->params->get('params.max_dates', 0) ?>');
	const fieldId = <?php echo $this->id ?>;

	// Helper function to format date for display
	const formatForDisplay = (d) => {
		// Simple formatter - uses browser's locale-aware formatting
		const options = {
			year: 'numeric',
			month: '2-digit',
			day: '2-digit'
			<?php if ($this->is_time): ?>,
			hour: '2-digit',
			minute: '2-digit',
			hour12: false
			<?php endif; ?>
		};
		return d.toLocaleString('en-GB', options).replace(',', '');
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

	// Format existing dates on page load
	document.querySelectorAll('.mdp-list<?php echo $this->id; ?>').forEach(el => {
		const dateStr = el.dataset.date;
		if (dateStr) {
			const d = new Date(dateStr);
			if (!isNaN(d)) {
				el.textContent = formatForDisplay(d);
			}
		}
	});

	// Handle remove button clicks
	document.addEventListener('click', (e) => {
		if (e.target.closest('.mdp-close<?php echo $this->id; ?>')) {
			e.target.closest('li').remove();
		}
	});

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
	});

	let isFirstChange = true;
	picker.subscribe(tempusDominus.Namespace.events.change, (e) => {
		// Skip the initial change event
		if (isFirstChange) {
			isFirstChange = false;
			return;
		}

		if (!e.date) return;

		// Check max limit
		if (max > 0 && datesList.querySelectorAll('li').length >= max) {
			if (typeof Joomcck !== 'undefined') {
				Joomcck.fieldError(fieldId, "<?php echo \Joomla\CMS\Language\Text::sprintf('F_ERROR_MAX', $this->params->get('params.max_dates', 0)) ?>");
			}
			return;
		}

		// Create new list item
		const li = document.createElement('li');
		li.className = 'list-group-item';
		li.innerHTML = `<a>
			<span class="float-end mdp-close<?php echo $this->id; ?>" style="cursor:pointer;"><?php echo HTMLFormatHelper::icon('cross.png') ?></span>
			<span class="mdp-list<?php echo $this->id; ?>">${formatForDisplay(e.date)}</span>
			<input type="hidden" name="jform[fields][<?php echo $this->id; ?>][]" value="${formatForDb(e.date)}" />
		</a>`;

		datesList.appendChild(li);
		input.value = '';
	});

	picker.subscribe(tempusDominus.Namespace.events.error, (e) => {
		if (typeof Joomcck !== 'undefined') {
			Joomcck.fieldError(fieldId, e.reason);
		}
	});
})();
</script>
