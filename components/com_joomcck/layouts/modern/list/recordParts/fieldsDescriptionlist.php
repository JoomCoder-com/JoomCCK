<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Fields Description List Layout
 *
 * Tailwind CSS grid replacement for Bootstrap dl-horizontal.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

extract($displayData);

?>

<div class="jcck-fields-list jcck-fields-dlist grid grid-cols-[auto_1fr] gap-x-4 gap-y-2 text-sm">
	<?php foreach ($item->fields_by_id AS $field):?>
		<?php if(in_array($field->key, $exclude)) continue; ?>
		<?php if($field->params->get('core.show_lable') > 1):?>
			<dt id="<?php echo $field->id;?>-lbl"
				for="field_<?php echo $field->id;?>"
				class="<?php echo $field->class;?> font-medium text-gray-600 whitespace-nowrap">
				<?php echo $field->label; ?>
				<?php if($field->params->get('core.icon')):?>
					<?php echo HTMLFormatHelper::icon($field->params->get('core.icon'));  ?>
				<?php endif;?>
			</dt>
		<?php endif;?>
		<dd class="text-gray-700 truncate input-field<?php echo ($field->params->get('core.label_break') > 1 ? '-full col-span-2' : NULL)?> <?php echo $field->fieldclass;?>">
			<?php echo $field->result; ?>
		</dd>
	<?php endforeach;?>
</div>
