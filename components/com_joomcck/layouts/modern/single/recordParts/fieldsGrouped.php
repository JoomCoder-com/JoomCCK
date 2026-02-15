<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Grouped Fields Layout (tabs/accordions/fieldsets)
 *
 * Tailwind CSS + vanilla JS replacement for Bootstrap tabs/accordions.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

extract($displayData);

$params = $current->tmpl_params['record'];
$item = $current->item;
$groupingType = $params->get('tmpl_params.item_grouping_type', 0);

if (!isset($current->item->fields_by_groups) || !count($current->item->fields_by_groups)) return;

$groupIndex = 0;
$uniqueId = 'jcck-record-groups-' . $item->id;

?>

<?php if ($groupingType == 1): // Tabs ?>
	<div id="<?php echo $uniqueId; ?>" class="mt-4">
		<!-- Tab navigation -->
		<nav class="flex flex-wrap gap-1 border-b border-gray-200 mb-4" role="tablist">
			<?php $t = 0; foreach ($current->item->fields_by_groups as $group_id => $fields): ?>
				<button type="button"
						class="jcck-rec-tab px-4 py-2 rounded-t text-sm font-medium transition-colors
							   <?php echo $t === 0 ? 'bg-white text-primary border border-b-white border-gray-200 -mb-px' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'; ?>"
						data-tab-target="#<?php echo $uniqueId ?>-tab-<?php echo $t; ?>"
						role="tab"
						aria-selected="<?php echo $t === 0 ? 'true' : 'false'; ?>">
					<?php if (!empty($item->field_groups[$group_id]['icon']) && $params->get('tmpl_params.show_groupicon', 1)): ?>
						<?php echo HTMLFormatHelper::icon($item->field_groups[$group_id]['icon']) ?>
					<?php endif; ?>
					<?php echo \Joomla\CMS\Language\Text::_($group_id) ?>
				</button>
			<?php $t++; endforeach; ?>
		</nav>

		<!-- Tab panels -->
		<?php $t = 0; foreach ($current->item->fields_by_groups as $group_id => $fields): ?>
			<div id="<?php echo $uniqueId ?>-tab-<?php echo $t; ?>" class="jcck-rec-panel <?php echo $t > 0 ? 'hidden' : ''; ?>" role="tabpanel">
				<?php if ($params->get('tmpl_params.show_groupdescr') && !empty($item->field_groups[$group_id]['descr'])): ?>
					<div class="mb-3 p-3 bg-blue-50 text-blue-700 rounded text-sm"><?php echo $item->field_groups[$group_id]['descr']; ?></div>
				<?php endif; ?>
				<div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-4 fields-list">
					<?php foreach ($fields as $field_id => $field): ?>
						<dt id="<?php echo 'dt-' . $field_id; ?>" class="<?php echo $field->class; ?> font-medium text-gray-600 text-sm pt-0.5">
							<?php if ($field->params->get('core.show_lable') > 1): ?>
								<label id="<?php echo $field->id; ?>-lbl" class="flex items-center gap-1.5">
									<?php if (!$field->params->get('core.label_icon_type', 0) && !empty($field->params->get('core.icon', ''))): ?>
										<?php echo HTMLFormatHelper::icon($field->params->get('core.icon')); ?>
									<?php elseif (!empty($field->params->get('core.label_icon_class', ''))): ?>
										<i class="<?php echo $field->params->get('core.label_icon_class') ?>"></i>
									<?php endif; ?><?php echo $field->label; ?>
								</label>
							<?php endif; ?>
						</dt>
						<dd id="<?php echo 'dd-' . $field_id; ?>" class="text-gray-700 text-sm <?php echo $field->fieldclass; ?><?php echo ($field->params->get('core.label_break') > 1 ? ' col-span-2' : NULL) ?>">
							<?php echo $field->result; ?>
						</dd>
					<?php endforeach; ?>
				</div>
			</div>
		<?php $t++; endforeach; ?>
	</div>

	<script>
	document.addEventListener('DOMContentLoaded', function() {
		var container = document.getElementById('<?php echo $uniqueId; ?>');
		if (!container) return;
		var tabs = container.querySelectorAll('.jcck-rec-tab');
		var panels = container.querySelectorAll('.jcck-rec-panel');
		tabs.forEach(function(btn) {
			btn.addEventListener('click', function() {
				var targetId = this.getAttribute('data-tab-target');
				tabs.forEach(function(b) {
					b.classList.remove('bg-white', 'text-primary', 'border', 'border-b-white', 'border-gray-200', '-mb-px');
					b.classList.add('text-gray-600');
					b.setAttribute('aria-selected', 'false');
				});
				panels.forEach(function(p) { p.classList.add('hidden'); });
				this.classList.add('bg-white', 'text-primary', 'border', 'border-b-white', 'border-gray-200', '-mb-px');
				this.classList.remove('text-gray-600');
				this.setAttribute('aria-selected', 'true');
				var target = container.querySelector(targetId);
				if (target) target.classList.remove('hidden');
			});
		});
	});
	</script>

<?php elseif ($groupingType == 2): // Accordions ?>
	<div id="<?php echo $uniqueId; ?>" class="mt-4 space-y-2">
		<?php $a = 0; foreach ($current->item->fields_by_groups as $group_id => $fields): ?>
			<div class="border border-gray-200 rounded-lg overflow-hidden">
				<button type="button"
						class="jcck-acc-toggle w-full flex items-center justify-between px-4 py-3 bg-gray-50 text-left text-sm font-medium text-gray-900 hover:bg-gray-100 transition-colors"
						data-acc-target="#<?php echo $uniqueId ?>-acc-<?php echo $a; ?>"
						aria-expanded="<?php echo $a === 0 ? 'true' : 'false'; ?>">
					<span>
						<?php if (!empty($item->field_groups[$group_id]['icon']) && $params->get('tmpl_params.show_groupicon', 1)): ?>
							<?php echo HTMLFormatHelper::icon($item->field_groups[$group_id]['icon']) ?>
						<?php endif; ?>
						<?php echo \Joomla\CMS\Language\Text::_($group_id) ?>
					</span>
					<i class="fas fa-chevron-down text-xs text-gray-400 transition-transform" style="<?php echo $a === 0 ? 'transform:rotate(180deg)' : ''; ?>"></i>
				</button>
				<div id="<?php echo $uniqueId ?>-acc-<?php echo $a; ?>" class="jcck-acc-content <?php echo $a > 0 ? 'hidden' : ''; ?>">
					<div class="p-4">
						<?php if ($params->get('tmpl_params.show_groupdescr') && !empty($item->field_groups[$group_id]['descr'])): ?>
							<div class="mb-3 p-3 bg-blue-50 text-blue-700 rounded text-sm"><?php echo $item->field_groups[$group_id]['descr']; ?></div>
						<?php endif; ?>
						<div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-4 fields-list">
							<?php foreach ($fields as $field_id => $field): ?>
								<dt id="<?php echo 'dt-' . $field_id; ?>" class="font-medium text-gray-600 text-sm pt-0.5 <?php echo $field->class; ?>">
									<?php if ($field->params->get('core.show_lable') > 1): ?>
										<label id="<?php echo $field->id; ?>-lbl" class="flex items-center gap-1.5">
											<?php if (!$field->params->get('core.label_icon_type', 0) && !empty($field->params->get('core.icon', ''))): ?>
												<?php echo HTMLFormatHelper::icon($field->params->get('core.icon')); ?>
											<?php elseif (!empty($field->params->get('core.label_icon_class', ''))): ?>
												<i class="<?php echo $field->params->get('core.label_icon_class') ?>"></i>
											<?php endif; ?><?php echo $field->label; ?>
										</label>
									<?php endif; ?>
								</dt>
								<dd id="<?php echo 'dd-' . $field_id; ?>" class="text-gray-700 text-sm <?php echo $field->fieldclass; ?><?php echo ($field->params->get('core.label_break') > 1 ? ' col-span-2' : NULL) ?>">
									<?php echo $field->result; ?>
								</dd>
							<?php endforeach; ?>
						</div>
					</div>
				</div>
			</div>
		<?php $a++; endforeach; ?>
	</div>

	<script>
	document.addEventListener('DOMContentLoaded', function() {
		document.querySelectorAll('#<?php echo $uniqueId; ?> .jcck-acc-toggle').forEach(function(btn) {
			btn.addEventListener('click', function() {
				var targetId = this.getAttribute('data-acc-target');
				var content = document.querySelector(targetId);
				var icon = this.querySelector('i');
				if (content) {
					content.classList.toggle('hidden');
					var expanded = !content.classList.contains('hidden');
					this.setAttribute('aria-expanded', expanded);
					icon.style.transform = expanded ? 'rotate(180deg)' : '';
				}
			});
		});
	});
	</script>

<?php else: // Fieldsets (type 3) or plain (type 0) ?>
	<div class="mt-4 space-y-6">
		<?php foreach ($current->item->fields_by_groups as $group_id => $fields): ?>
			<?php if ($groupingType == 3): ?>
				<fieldset class="border border-gray-200 rounded-lg p-4">
					<legend class="text-sm font-semibold text-gray-900 px-2">
						<?php if (!empty($item->field_groups[$group_id]['icon']) && $params->get('tmpl_params.show_groupicon', 1)): ?>
							<?php echo HTMLFormatHelper::icon($item->field_groups[$group_id]['icon']) ?>
						<?php endif; ?>
						<?php echo \Joomla\CMS\Language\Text::_($group_id) ?>
					</legend>
			<?php endif; ?>
				<?php if ($params->get('tmpl_params.show_groupdescr') && !empty($item->field_groups[$group_id]['descr'])): ?>
					<div class="mb-3 p-3 bg-blue-50 text-blue-700 rounded text-sm"><?php echo $item->field_groups[$group_id]['descr']; ?></div>
				<?php endif; ?>
				<div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-4 fields-list">
					<?php foreach ($fields as $field_id => $field): ?>
						<dt id="<?php echo 'dt-' . $field_id; ?>" class="font-medium text-gray-600 text-sm pt-0.5 <?php echo $field->class; ?>">
							<?php if ($field->params->get('core.show_lable') > 1): ?>
								<label id="<?php echo $field->id; ?>-lbl" class="flex items-center gap-1.5">
									<?php if (!$field->params->get('core.label_icon_type', 0) && !empty($field->params->get('core.icon', ''))): ?>
										<?php echo HTMLFormatHelper::icon($field->params->get('core.icon')); ?>
									<?php elseif (!empty($field->params->get('core.label_icon_class', ''))): ?>
										<i class="<?php echo $field->params->get('core.label_icon_class') ?>"></i>
									<?php endif; ?><?php echo $field->label; ?>
								</label>
							<?php endif; ?>
						</dt>
						<dd id="<?php echo 'dd-' . $field_id; ?>" class="text-gray-700 text-sm <?php echo $field->fieldclass; ?><?php echo ($field->params->get('core.label_break') > 1 ? ' col-span-2' : NULL) ?>">
							<?php echo $field->result; ?>
						</dd>
					<?php endforeach; ?>
				</div>
			<?php if ($groupingType == 3): ?>
				</fieldset>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
