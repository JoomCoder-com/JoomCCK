<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

if (empty($displayData->_filters)) return;

// Enqueue drawer styles. Done once per request — the drawer layout is the
// single point of inclusion for admin filter UI on list views.
$cssPath = '/media/com_joomcck/css/admin-filters-drawer.css';
$cssVer  = @filemtime(JPATH_ROOT . $cssPath) ?: 'auto';
Factory::getDocument()->addStyleSheet(Uri::root(true) . $cssPath, ['version' => (string) $cssVer]);

$view = Factory::getApplication()->input->getCmd('view');

// Build the list of active chips. Each chip carries:
//   - inputId: the DOM id of the hidden filter control (for the × clear)
//   - label:   the human label shown before ":"
//   - value:   the selected option's text (parsed from the pre-rendered <select> options)
$chips = [];

$searchVal = (string) $displayData->state->get('filter.search');
if ($searchVal !== '')
{
	$chips[] = [
		'inputId' => 'filter_search',
		'label'   => Text::_('CSEARCH'),
		'value'   => $searchVal,
	];
}

// Extract the selected <option>'s visible text from a pre-rendered options string.
// Returns null when no option is marked selected.
$selectedOptionText = static function ($optionsHtml) {
	if (!is_string($optionsHtml) || $optionsHtml === '') return null;
	if (preg_match('/<option[^>]*\bselected\b[^>]*>([^<]*)<\/option>/i', $optionsHtml, $m))
	{
		$text = html_entity_decode(trim($m[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
		return $text !== '' ? $text : null;
	}
	return null;
};

// Filter names are registered as placeholder-style text (e.g. "- Select Section -")
// to double as the blank <option> label. Strip the "- " decoration and the
// leading "Select " verb so labels inside the drawer and chips read cleanly.
$cleanLabel = static function ($raw) {
	$label = trim((string) $raw, " \t\n\r\0\x0B-");
	$label = preg_replace('/^\s*select\s+/i', '', $label);
	return $label !== '' ? $label : (string) $raw;
};

foreach ($displayData->_filters as $filter)
{
	// Skip the type filter on the tfields view — matches search.php behavior.
	if ($view === 'tfields' && $filter['id'] === 'filter_type') continue;

	// Source of truth is the rendered <option selected> markup. Checking
	// state via !$value would falsely drop "0" selections (e.g. filter_state
	// = "0" for Unpublished), since "0" is falsy in PHP.
	$valueText = $selectedOptionText($filter['element']);
	if ($valueText === null) continue;

	$chips[] = [
		'inputId' => $filter['id'],
		'label'   => $cleanLabel($filter['name']),
		'value'   => $valueText,
	];
}

$chipInputIds = array_column($chips, 'inputId');
?>

<?php if ($chips): ?>
<ul class="jc-chips" aria-label="<?php echo htmlspecialchars(Text::_('CFILTER'), ENT_QUOTES, 'UTF-8'); ?>">
	<?php foreach ($chips as $chip): ?>
	<li>
		<span class="jc-chip">
			<span class="jc-chip-label"><?php echo htmlspecialchars($chip['label'], ENT_QUOTES, 'UTF-8'); ?>:</span>
			<span class="jc-chip-value" title="<?php echo htmlspecialchars($chip['value'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($chip['value'], ENT_QUOTES, 'UTF-8'); ?></span>
			<button type="button" class="jc-chip-close" data-jc-clear-filter="<?php echo htmlspecialchars($chip['inputId'], ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?php echo htmlspecialchars(Text::_('JSEARCH_FILTER_CLEAR'), ENT_QUOTES, 'UTF-8'); ?>">&times;</button>
		</span>
	</li>
	<?php endforeach; ?>
	<?php if (count($chips) > 1): ?>
	<li>
		<button type="button" class="jc-chip jc-chip-clear" id="jc-chips-clear-all">
			<i class="fas fa-eraser" aria-hidden="true"></i>
			<span><?php echo htmlspecialchars(Text::_('JSEARCH_FILTER_CLEAR'), ENT_QUOTES, 'UTF-8'); ?></span>
		</button>
	</li>
	<?php endif; ?>
</ul>
<?php endif; ?>

<div class="offcanvas offcanvas-end jc-filters-offcanvas" tabindex="-1" id="list-filters-box" aria-labelledby="list-filters-box-title">
	<div class="offcanvas-header">
		<h5 class="offcanvas-title" id="list-filters-box-title">
			<i class="fas fa-filter" aria-hidden="true"></i>
			<?php echo htmlspecialchars(Text::_('CFILTER'), ENT_QUOTES, 'UTF-8'); ?>
		</h5>
		<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="<?php echo htmlspecialchars(Text::_('JCLOSE'), ENT_QUOTES, 'UTF-8'); ?>"></button>
	</div>

	<div class="jc-filters-body">
		<div class="jc-filter-row">
			<label class="form-label jc-filter-label" for="filter_search">
				<?php echo htmlspecialchars(Text::_('CSEARCH'), ENT_QUOTES, 'UTF-8'); ?>
			</label>
			<input type="text" class="form-control" name="filter_search" id="filter_search"
			       placeholder="<?php echo htmlspecialchars(Text::_('CSEARCHPLACEHOLDER'), ENT_QUOTES, 'UTF-8'); ?>"
			       value="<?php echo htmlspecialchars((string) $displayData->state->get('filter.search'), ENT_QUOTES, 'UTF-8'); ?>"/>
		</div>
		<?php foreach ($displayData->_filters as $filter): ?>
			<?php if ($view === 'tfields' && $filter['id'] === 'filter_type') continue; ?>
			<div class="jc-filter-row">
				<label class="form-label jc-filter-label" for="<?php echo htmlspecialchars($filter['id'], ENT_QUOTES, 'UTF-8'); ?>">
					<?php echo htmlspecialchars($cleanLabel($filter['name']), ENT_QUOTES, 'UTF-8'); ?>
				</label>
				<select class="form-select" name="<?php echo htmlspecialchars($filter['id'], ENT_QUOTES, 'UTF-8'); ?>" id="<?php echo htmlspecialchars($filter['id'], ENT_QUOTES, 'UTF-8'); ?>">
					<option value=""><?php echo htmlspecialchars($filter['name'], ENT_QUOTES, 'UTF-8'); ?></option>
					<?php echo $filter['element']; ?>
				</select>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="jc-filters-footer">
		<button type="button" class="btn btn-outline-secondary btn-sm" id="jc-filters-clear">
			<i class="fas fa-eraser" aria-hidden="true"></i>
			<?php echo htmlspecialchars(Text::_('JSEARCH_FILTER_CLEAR'), ENT_QUOTES, 'UTF-8'); ?>
		</button>
		<button type="submit" class="btn btn-primary btn-sm" id="jc-filters-apply">
			<i class="fas fa-check" aria-hidden="true"></i>
			<?php echo htmlspecialchars(Text::_('CAPPLY'), ENT_QUOTES, 'UTF-8'); ?>
		</button>
	</div>
</div>

<script>
(function () {
	var form = document.getElementById('adminForm');
	if (!form) return;

	// Filter ids rendered inside the drawer — used by "Clear all" to reset them
	// without having to scan the whole form.
	var drawerFilterIds = <?php echo json_encode(array_map(static function ($f) { return $f['id']; }, array_values(array_filter($displayData->_filters, static function ($f) use ($view) { return !($view === 'tfields' && $f['id'] === 'filter_type'); }))), JSON_UNESCAPED_SLASHES); ?>;

	function clearFilter(id) {
		var el = document.getElementById(id);
		if (!el) return;
		el.value = '';
		form.submit();
	}

	function clearAll() {
		drawerFilterIds.forEach(function (id) {
			var el = document.getElementById(id);
			if (el) el.value = '';
		});
		var s = document.getElementById('filter_search');
		if (s) s.value = '';
		form.submit();
	}

	// Per-chip × → clear that single filter
	document.querySelectorAll('[data-jc-clear-filter]').forEach(function (btn) {
		btn.addEventListener('click', function () {
			clearFilter(btn.getAttribute('data-jc-clear-filter'));
		});
	});

	var clearAllChip = document.getElementById('jc-chips-clear-all');
	if (clearAllChip) clearAllChip.addEventListener('click', clearAll);

	var clearBtn = document.getElementById('jc-filters-clear');
	if (clearBtn) clearBtn.addEventListener('click', clearAll);

	// Auto-submit when a drawer filter <select> changes — restores the
	// immediate-filter behavior of the legacy inline filter bar.
	document.querySelectorAll('#list-filters-box select').forEach(function (select) {
		select.addEventListener('change', function () {
			form.submit();
		});
	});
})();
</script>
