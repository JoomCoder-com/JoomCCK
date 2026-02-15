<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Fields Default List Layout
 *
 * Tailwind CSS replacement for Bootstrap card-subtitle/card-text.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

extract($displayData);

?>

<div class="jcck-fields-default space-y-3">
	<?php foreach ($item->fields_by_id AS $field):?>
		<?php if(in_array($field->key, $exclude)) continue; ?>
		<?php if($field->params->get('core.show_lable') > 1):?>
			<h6 id="<?php echo $field->id;?>-lbl"
				for="field_<?php echo $field->id;?>"
				class="<?php echo $field->class;?> text-xs font-semibold text-gray-500 uppercase tracking-wide">
				<?php echo $field->label; ?>
				<?php if($field->params->get('core.icon')):?>
					<?php echo HTMLFormatHelper::icon($field->params->get('core.icon'));  ?>
				<?php endif;?>
			</h6>
		<?php endif;?>
		<div class="text-sm text-gray-700 input-field<?php echo ($field->params->get('core.label_break') > 1 ? '-full' : NULL)?> <?php echo $field->fieldclass;?>">
			<?php echo $field->result; ?>
		</div>
	<?php endforeach;?>
</div>
