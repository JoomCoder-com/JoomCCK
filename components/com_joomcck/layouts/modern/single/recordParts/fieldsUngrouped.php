<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Ungrouped Fields Layout
 *
 * Tailwind CSS grid replacement for Bootstrap dl-horizontal.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

extract($displayData);

if (!isset($current->item->fields_by_groups[null])) return;

?>
<div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-4 fields-list">
	<?php foreach ($current->item->fields_by_groups[null] as $field_id => $field): ?>
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
<?php unset($current->item->fields_by_groups[null]); ?>
