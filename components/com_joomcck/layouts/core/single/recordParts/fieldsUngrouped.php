<?php
/**
 * Joomcck by joomcoder
 * Core Layout - Ungrouped Fields (dl-horizontal)
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

extract($displayData);

if (!isset($current->item->fields_by_groups[null])) return;

?>
<dl class="dl-horizontal fields-list">
	<?php foreach ($current->item->fields_by_groups[null] as $field_id => $field): ?>
		<dt id="<?php echo 'dt-' . $field_id; ?>" class="<?php echo $field->class; ?>">
			<?php if ($field->params->get('core.show_lable') > 1): ?>
				<label id="<?php echo $field->id; ?>-lbl">
					<?php if (!$field->params->get('core.label_icon_type', 0) && !empty($field->params->get('core.icon', ''))): ?>
						<?php echo HTMLFormatHelper::icon($field->params->get('core.icon')); ?>
					<?php elseif (!empty($field->params->get('core.label_icon_class', ''))): ?>
						<i class="<?php echo $field->params->get('core.label_icon_class') ?>"></i>
					<?php endif; ?><?php echo $field->label; ?>
				</label>
			<?php endif; ?>
		</dt>
		<dd id="<?php echo 'dd-' . $field_id; ?>" class="mb-4 <?php echo $field->fieldclass; ?><?php echo ($field->params->get('core.label_break') > 1 ? ' line-brk' : NULL) ?>">
			<?php echo $field->result; ?>
		</dd>
	<?php endforeach; ?>
</dl>
<?php unset($current->item->fields_by_groups[null]); ?>
