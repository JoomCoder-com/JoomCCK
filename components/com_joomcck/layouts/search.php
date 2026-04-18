<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');

// Count active filters (including free-text search) — drives the badge on
// the filter toggle. The search input itself has been moved into the drawer.
$activeCount = 0;

if ($displayData->state->get('filter.search'))
{
	$activeCount++;
}

if (is_array($displayData->_filters))
{
	foreach ($displayData->_filters as $filter)
	{
		if ($displayData->input->get('view') == 'tfields' && $filter['id'] == 'filter_type')
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
	// input so views that want only a keyword search continue to work.
	?>
	<div class="float-end search-box">
		<div class="input-group">
			<input type="text" class="form-control" aria-label="<?php echo \Joomla\CMS\Language\Text::_('CSEARCHPLACEHOLDER'); ?>" placeholder="<?php echo \Joomla\CMS\Language\Text::_('CSEARCHPLACEHOLDER'); ?>" name="filter_search" id="filter_search" value="<?php echo htmlspecialchars((string) $displayData->state->get('filter.search'), ENT_QUOTES, 'UTF-8'); ?>"/>
		</div>
	</div>
	<?php
	return;
}

?>

<div class="float-end search-box">
	<button type="button" class="btn btn-outline-secondary jc-filter-toggle" data-bs-toggle="offcanvas" rel="tooltip" data-bs-target="#list-filters-box" aria-controls="list-filters-box" data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CFILTER'); ?>">
		<?php echo HTMLFormatHelper::icon('funnel.png'); ?>
		<span class="jc-filter-toggle-label"><?php echo \Joomla\CMS\Language\Text::_('CFILTER'); ?></span>
		<?php if ($activeCount): ?>
			<span class="jc-filter-count"><?php echo $activeCount; ?></span>
		<?php endif; ?>
	</button>
</div>
