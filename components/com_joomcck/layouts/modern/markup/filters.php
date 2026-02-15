<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Filters Panel Layout
 *
 * Tailwind CSS flex sidebar + vanilla JS tabs replacement for Bootstrap collapse + vertical tabs.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

extract($displayData);

$markup = $current->tmpl_params['markup'];

if(!in_array($markup->get('filters.show_more'), $current->user->getAuthorisedViewLevels()) && !$markup->get('filters.filters'))

?>

<div class="jcck-filter-panel hidden border border-gray-200 rounded-lg bg-white shadow-sm mb-4" id="filter-collapse">
	<!-- Header with actions -->
	<div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200 rounded-t-lg">
		<h3 class="flex items-center gap-2 text-base font-semibold text-gray-900">
			<?php echo HTMLFormatHelper::icon('funnel.png'); ?>
			<?php echo \Joomla\CMS\Language\Text::_('CMORESEARCHOPTIONS') ?>
		</h3>
		<div class="flex items-center gap-1">
			<button class="bg-primary text-white px-3 py-1.5 rounded-lg text-sm font-medium hover:opacity-90 transition-colors flex items-center gap-1.5"
					onclick="Joomla.submitbutton('records.filters')">
				<?php echo HTMLFormatHelper::icon('tick-button.png'); ?>
				<?php echo \Joomla\CMS\Language\Text::_('CAPPLY'); ?>
			</button>
			<?php if (count($current->worns)): ?>
				<button class="bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors flex items-center gap-1.5"
						type="button"
						onclick="Joomla.submitbutton('records.cleanall')">
					<?php echo HTMLFormatHelper::icon('cross-button.png'); ?>
					<?php echo \Joomla\CMS\Language\Text::_('CRESETFILTERS'); ?>
				</button>
			<?php endif; ?>
			<button class="bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors flex items-center gap-1.5"
					type="button"
					onclick="document.getElementById('filter-collapse').classList.add('hidden')">
				<?php echo HTMLFormatHelper::icon('minus-button.png'); ?>
				<?php echo \Joomla\CMS\Language\Text::_('CCLOSE'); ?>
			</button>
		</div>
	</div>

	<!-- Filter tabs + content -->
	<div class="flex min-h-[300px]">
		<!-- Vertical tab navigation -->
		<nav class="w-48 shrink-0 border-r border-gray-200 bg-gray-50/50 py-2" id="jcck-filter-tabs">
			<?php $firstTab = true; ?>
			<?php if (in_array($markup->get('filters.filter_type'), $current->user->getAuthorisedViewLevels()) && (count($current->submission_types) > 1)): ?>
				<button type="button"
						class="jcck-filter-tab w-full text-left px-4 py-2 text-sm font-medium transition-colors <?php echo $firstTab ? 'text-primary bg-white border-r-2 border-primary' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'; ?>"
						data-filter-target="#ftab-types">
					<?php echo ($markup->get('filters.filter_type_icon') ? HTMLFormatHelper::icon('block.png') : null) . ' ' . \Joomla\CMS\Language\Text::_($markup->get('filters.type_label', 'Content Type')) ?>
				</button>
				<?php $firstTab = false; ?>
			<?php endif; ?>

			<?php if (in_array($markup->get('filters.filter_tags'), $current->user->getAuthorisedViewLevels())): ?>
				<button type="button"
						class="jcck-filter-tab w-full text-left px-4 py-2 text-sm font-medium transition-colors <?php echo $firstTab ? 'text-primary bg-white border-r-2 border-primary' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'; ?>"
						data-filter-target="#ftab-tags">
					<?php echo ($markup->get('filters.filter_tag_icon') ? HTMLFormatHelper::icon('price-tag.png') : null) . ' ' . \Joomla\CMS\Language\Text::_($markup->get('filters.tag_label', 'CTAGS')) ?>
				</button>
				<?php $firstTab = false; ?>
			<?php endif; ?>

			<?php if (in_array($markup->get('filters.filter_user'), $current->user->getAuthorisedViewLevels())): ?>
				<button type="button"
						class="jcck-filter-tab w-full text-left px-4 py-2 text-sm font-medium transition-colors <?php echo $firstTab ? 'text-primary bg-white border-r-2 border-primary' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'; ?>"
						data-filter-target="#ftab-users">
					<?php echo ($markup->get('filters.filter_user_icon') ? HTMLFormatHelper::icon('user.png') : null) . ' ' . \Joomla\CMS\Language\Text::_($markup->get('filters.user_label', 'CAUTHOR')) ?>
				</button>
				<?php $firstTab = false; ?>
			<?php endif; ?>

			<?php if (in_array($markup->get('filters.filter_cat'), $current->user->getAuthorisedViewLevels()) && $current->section->categories && ($current->section->params->get('general.filter_mode') == 0)): ?>
				<button type="button"
						class="jcck-filter-tab w-full text-left px-4 py-2 text-sm font-medium transition-colors <?php echo $firstTab ? 'text-primary bg-white border-r-2 border-primary' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'; ?>"
						data-filter-target="#ftab-cats">
					<?php echo ($markup->get('filters.filter_category_icon') ? HTMLFormatHelper::icon('category.png') : null) . ' ' . \Joomla\CMS\Language\Text::_($markup->get('filters.category_label', 'CCATEGORY')) ?>
				</button>
				<?php $firstTab = false; ?>
			<?php endif; ?>

			<?php if (count($current->filters) && $markup->get('filters.filter_fields')): ?>
				<?php foreach ($current->filters as $filter): ?>
					<?php if ($filter->params->get('params.filter_hide')) continue; ?>
					<button type="button"
							class="jcck-filter-tab w-full text-left px-4 py-2 text-sm font-medium transition-colors <?php echo $firstTab ? 'text-primary bg-white border-r-2 border-primary' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'; ?>"
							data-filter-target="#ftab-<?php echo $filter->key ?>">
						<?php echo ($markup->get('filters.filter_tag_icon') && $filter->params->get('core.icon') ? HTMLFormatHelper::icon($filter->params->get('core.icon')) : null) . ' ' . $filter->label ?>
					</button>
					<?php $firstTab = false; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</nav>

		<!-- Tab content panels -->
		<div class="flex-1 p-4" id="jcck-filter-content">
			<?php $firstPanel = true; ?>

			<?php if (in_array($markup->get('filters.filter_type'), $current->user->getAuthorisedViewLevels()) && (count($current->submission_types) > 1)): ?>
				<div class="jcck-filter-panel-content <?php echo !$firstPanel ? 'hidden' : ''; ?>" id="ftab-types">
					<?php if ($markup->get('filters.filter_type_type') == 1): ?>
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('types.checkbox', $current->total_types, $current->submission_types, $current->state->get('records.type')); ?>
					<?php elseif ($markup->get('filters.filter_type_type') == 3): ?>
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('types.toggle', $current->total_types, $current->submission_types, $current->state->get('records.type')); ?>
					<?php else : ?>
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('types.select', $current->total_types_option, $current->state->get('records.type')); ?>
					<?php endif; ?>
				</div>
				<?php $firstPanel = false; ?>
			<?php endif; ?>

			<?php if (in_array($markup->get('filters.filter_tags'), $current->user->getAuthorisedViewLevels())): ?>
				<div class="jcck-filter-panel-content <?php echo !$firstPanel ? 'hidden' : ''; ?>" id="ftab-tags">
					<?php if ($markup->get('filters.filter_tags_type') == 1): ?>
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('tags.tagform', $current->section, $current->state->get('records.tag')); ?>
					<?php elseif ($markup->get('filters.filter_tags_type') == 2): ?>
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('tags.tagcheckboxes', $current->section, $current->state->get('records.tag')); ?>
					<?php elseif ($markup->get('filters.filter_tags_type') == 3): ?>
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('tags.tagselect', $current->section, $current->state->get('records.tag')); ?>
					<?php elseif ($markup->get('filters.filter_tags_type') == 4): ?>
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('tags.tagtoggle', $current->section, $current->state->get('records.tag')); ?>
					<?php endif; ?>
				</div>
				<?php $firstPanel = false; ?>
			<?php endif; ?>

			<?php if (in_array($markup->get('filters.filter_user'), $current->user->getAuthorisedViewLevels())): ?>
				<div class="jcck-filter-panel-content <?php echo !$firstPanel ? 'hidden' : ''; ?>" id="ftab-users">
					<?php if ($markup->get('filters.filter_users_type') == 1): ?>
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('cusers.form', $current->section, $current->state->get('records.user')); ?>
					<?php elseif ($markup->get('filters.filter_users_type') == 2): ?>
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('cusers.checkboxes', $current->section, $current->state->get('records.user')); ?>
					<?php elseif ($markup->get('filters.filter_users_type') == 3): ?>
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('cusers.select', $current->section, $current->state->get('records.user')); ?>
					<?php endif; ?>
				</div>
				<?php $firstPanel = false; ?>
			<?php endif; ?>

			<?php if (in_array($markup->get('filters.filter_cat'), $current->user->getAuthorisedViewLevels()) && $current->section->categories && ($current->section->params->get('general.filter_mode') == 0)): ?>
				<div class="jcck-filter-panel-content <?php echo !$firstPanel ? 'hidden' : ''; ?>" id="ftab-cats">
					<?php if ($markup->get('filters.filter_category_type') == 1): ?>
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('categories.form', $current->section, $current->state->get('records.category')); ?>
					<?php elseif ($markup->get('filters.filter_category_type') == 2): ?>
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('categories.checkboxes', $current->section, $current->state->get('records.category'), array('columns' => 3)); ?>
					<?php elseif ($markup->get('filters.filter_category_type') == 3): ?>
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('categories.select', $current->section, $current->state->get('records.category'), array('multiple' => 0)); ?>
					<?php elseif ($markup->get('filters.filter_category_type') == 4): ?>
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('categories.select', $current->section, $current->state->get('records.category'), array('multiple' => 1, 'size' => 25)); ?>
					<?php elseif ($markup->get('filters.filter_category_type') == 5): ?>
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.catselector', "filters[cats][]", $current->section->id, $current->state->get('records.category')); ?>
					<?php endif; ?>
				</div>
				<?php $firstPanel = false; ?>
			<?php endif; ?>

			<?php if (count($current->filters) && $markup->get('filters.filter_fields')): ?>
				<?php foreach ($current->filters as $filter): ?>
					<?php if ($filter->params->get('params.filter_hide')) continue; ?>
					<div class="jcck-filter-panel-content <?php echo !$firstPanel ? 'hidden' : ''; ?>" id="ftab-<?php echo $filter->key ?>">
						<?php if ($filter->params->get('params.filter_descr') && $markup->get('filters.filter_descr')): ?>
							<p class="text-xs text-gray-500 mb-3">
								<?php echo \Joomla\CMS\Language\Text::_($filter->params->get('params.filter_descr')); ?>
							</p>
						<?php endif; ?>
						<?php echo $filter->onRenderFilter($current->section); ?>
					</div>
					<?php $firstPanel = false; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	var filterTabs = document.querySelectorAll('#jcck-filter-tabs .jcck-filter-tab');
	var filterPanels = document.querySelectorAll('#jcck-filter-content .jcck-filter-panel-content');

	filterTabs.forEach(function(tab) {
		tab.addEventListener('click', function() {
			var targetId = this.getAttribute('data-filter-target');
			var targetPanel = document.querySelector(targetId);

			// Deactivate all
			filterTabs.forEach(function(t) {
				t.classList.remove('text-primary', 'bg-white', 'border-r-2', 'border-primary');
				t.classList.add('text-gray-600');
			});
			filterPanels.forEach(function(p) {
				p.classList.add('hidden');
			});

			// Activate clicked
			this.classList.add('text-primary', 'bg-white', 'border-r-2', 'border-primary');
			this.classList.remove('text-gray-600');
			if (targetPanel) targetPanel.classList.remove('hidden');
		});
	});

	// Bridge for Bootstrap collapse toggle
	document.querySelectorAll('[data-bs-target="#filter-collapse"]').forEach(function(btn) {
		btn.addEventListener('click', function(e) {
			e.preventDefault();
			var panel = document.getElementById('filter-collapse');
			if (panel) panel.classList.toggle('hidden');
		});
	});
});
</script>
