<?php
/**
 * Filter toggle (opens the offcanvas drawer in filters.php).
 *
 * Kept as a plain inline element — no float wrappers. The parent template
 * places this inside the card-header title bar alongside the Add button.
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');

// Count active filters (including free-text search) — drives the badge on
// the toggle button. The search input itself has moved into the drawer.
$activeCount = 0;

if ($displayData->state->get('filter.search'))
{
	$activeCount++;
}

if (is_array($displayData->_filters))
{
	foreach ($displayData->_filters as $filter)
	{
		if ($displayData->input->get('view') === 'tfields' && $filter['id'] === 'filter_type')
		{
			continue;
		}
		if ($displayData->state->get(str_replace('_', '.', $filter['id'])))
		{
			$activeCount++;
		}
	}
}

if (empty($displayData->_filters))
{
	// No registered filters → nothing to toggle. Still emit a hidden search
	// input so views that want only a keyword search continue to submit it.
	?>
	<input type="hidden" name="filter_search" id="filter_search"
	       value="<?php echo htmlspecialchars((string) $displayData->state->get('filter.search'), ENT_QUOTES, 'UTF-8'); ?>"/>
	<?php
	return;
}
?>

<button type="button"
        class="btn btn-sm btn-outline-secondary jc-filter-toggle"
        data-bs-toggle="offcanvas"
        rel="tooltip"
        data-bs-target="#list-filters-box"
        aria-controls="list-filters-box"
        data-bs-original-title="<?php echo htmlspecialchars(Text::_('CFILTER'), ENT_QUOTES, 'UTF-8'); ?>">
	<i class="fas fa-filter" aria-hidden="true"></i>
	<span class="jc-filter-toggle-label"><?php echo htmlspecialchars(Text::_('CFILTER'), ENT_QUOTES, 'UTF-8'); ?></span>
	<?php if ($activeCount): ?>
		<span class="jc-filter-count"><?php echo $activeCount; ?></span>
	<?php endif; ?>
</button>
