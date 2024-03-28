<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

?>
<div id="fld-<?php echo $field->id; ?>"
     class="control-group odd<?php echo $k = 1 - $k ?> <?php echo 'field-' . $field->id; ?> <?php echo $field->fieldclass; ?>">
	<?php if ($field->params->get('core.show_lable') == 1 || $field->params->get('core.show_lable') == 3): ?>
		<label id="lbl-<?php echo $field->id; ?>" for="field_<?php echo $field->id; ?>"
		       class="control-label <?php echo $field->class; ?>">
			<?php if ($field->params->get('core.icon') && $current->tmpl_params->get('tmpl_core.item_icon_fields')): ?>
				<?php echo HTMLFormatHelper::icon($field->params->get('core.icon')); ?>
			<?php endif; ?>
			<?php if ($field->required): ?>
				<span class="float-end" rel="tooltip"
				      title="<?php echo Text::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
			<?php endif; ?>

			<?php if ($field->description): ?>
				<span class="float-end" rel="tooltip" style="cursor: help;"
				      title="<?php echo htmlspecialchars(($field->translateDescription ? Text::_($field->description) : $field->description), ENT_COMPAT, 'UTF-8'); ?>">
									<?php echo HTMLFormatHelper::icon('question-small-white.png'); ?>
								</span>
			<?php endif; ?>
			<?php echo $field->label; ?>
		</label>
		<?php if (in_array($field->params->get('core.label_break'), array(1, 3))): ?>
			<div style="clear: both;"></div>
		<?php endif; ?>
	<?php endif; ?>
	<div class="controls<?php if (in_array($field->params->get('core.label_break'), array(1, 3))) echo '-full'; ?><?php echo(in_array($field->params->get('core.label_break'), array(1, 3)) ? ' line-brk' : null) ?><?php echo $field->fieldclass ?>">
		<div id="field-alert-<?php echo $field->id ?>" class="alert alert-danger"
		     style="display:none"></div>
		<?php echo $field->result; ?>
	</div>
</div>
