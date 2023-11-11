<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>
<div class="form-actions">
<div class="float-end">
	<?php if(!$this->isCheckedOut()):?>
		<?php if(in_array($this->tmpl_params->get('tmpl_core.form_show_apply_button'), $this->user->getAuthorisedViewLevels())):?>
			<button type="button" class="btn-submit btn btn-primary btn-large" onclick="Joomla.submitbutton('form.apply');">
				<?php echo HTMLFormatHelper::icon('tick-button.png');  ?>
				<?php echo \Joomla\CMS\Language\Text::_($this->tmpl_params->get('tmpl_core.form_label_apply_button')) ?>
			</button>
		<?php endif; ?>

		<?php if(in_array($this->tmpl_params->get('tmpl_core.form_show_save_button'), $this->user->getAuthorisedViewLevels())):?>
			<button type="button" class="btn-submit btn btn-light border" onclick="Joomla.submitbutton('form.save');">
				<?php echo HTMLFormatHelper::icon('disk.png');  ?>
				<?php echo \Joomla\CMS\Language\Text::_($this->tmpl_params->get('tmpl_core.form_label_save_button')) ?>
			</button>
		<?php endif; ?>

		<?php if(in_array($this->tmpl_params->get('tmpl_core.form_show_savenew_button'), $this->user->getAuthorisedViewLevels())):?>
			<button type="button" class="btn-submit btn btn-light border" onclick="Joomla.submitbutton('form.save2new');">
				<?php echo HTMLFormatHelper::icon('disk-plus.png');  ?>
				<?php echo \Joomla\CMS\Language\Text::_($this->tmpl_params->get('tmpl_core.form_label_savenew_button')) ?>
			</button>
		<?php endif; ?>

		<?php if(in_array($this->tmpl_params->get('tmpl_core.form_show_savecopy_button'), $this->user->getAuthorisedViewLevels()) && $this->item->id):?>
			<button type="button" class="btn-submit btn btn-light border" onclick="Joomla.submitbutton('form.save2copy');">
				<?php echo HTMLFormatHelper::icon('disks.png');  ?>
				<?php echo \Joomla\CMS\Language\Text::_($this->tmpl_params->get('tmpl_core.form_label_savecopy_button')) ?>
			</button>
		<?php endif; ?>
	<?php endif; ?>

	<?php if(in_array($this->tmpl_params->get('tmpl_core.form_show_close_button'), $this->user->getAuthorisedViewLevels())):?>
		<button type="button" class="btn-submit btn btn-light border" onclick="Joomla.submitbutton('form.cancel');">
			<?php echo HTMLFormatHelper::icon('cross-button.png');  ?>
			<?php echo \Joomla\CMS\Language\Text::_($this->tmpl_params->get('tmpl_core.form_label_close_button')) ?>
		</button>
	<?php endif; ?>
</div>
</div>