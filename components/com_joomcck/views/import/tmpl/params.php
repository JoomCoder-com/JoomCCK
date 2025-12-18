<?php
/**
 * by joomcoder
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');
defined('_JEXEC') or die();
?>
<div class="row mt-3">
	<div class="col-md-6">
		<h5 class="border-bottom pb-2 mb-3"><?php echo \Joomla\CMS\Language\Text::_('CIMPORTPARAMS')?></h5>
		<div class="mb-3">
			<label class="form-label" for="importname"><?php echo \Joomla\CMS\Language\Text::_('CNAME');?>
				<span class="text-danger">*</span>
			</label>
			<input type="text" class="form-control" name="import[name]" id="importname" value="<?php echo $this->item->params->get('name'); ?>">
		</div>
		<div class="mb-3">
			<label class="form-label" for="importfieldid"><?php echo \Joomla\CMS\Language\Text::_('ID');?>
				<span class="text-danger">*</span>
			</label>
			<?php echo $this->fieldlist('id', $this->item->params->get('field.id'));?>
			<small class="text-muted"><?php echo \Joomla\CMS\Language\Text::_('CIMPORTUNIQID')?></small>
		</div>
		<div class="mb-3">
			<label class="form-label" for="importmethod"><?php echo \Joomla\CMS\Language\Text::_('CIMPORTMETHOD');?></label>
			<select name="import[method]" id="importmethod" class="form-select">
				<option value="update"<?php echo ($this->item->params->get('method', 'update') == 'update') ? ' selected' : ''; ?>><?php echo \Joomla\CMS\Language\Text::_('CIMPORTMETHOD_UPDATE');?></option>
				<option value="skip"<?php echo ($this->item->params->get('method') == 'skip') ? ' selected' : ''; ?>><?php echo \Joomla\CMS\Language\Text::_('CIMPORTMETHOD_SKIP');?></option>
				<option value="duplicate"<?php echo ($this->item->params->get('method') == 'duplicate') ? ' selected' : ''; ?>><?php echo \Joomla\CMS\Language\Text::_('CIMPORTMETHOD_DUPLICATE');?></option>
			</select>
			<small class="text-muted"><?php echo \Joomla\CMS\Language\Text::_('CIMPORTMETHOD_DESC')?></small>
		</div>
		<?php if($this->section->categories || ($this->section->params->get('personalize.personalize', 0) && in_array($this->section->params->get('personalize.pcat_submit', 0), $this->user->getAuthorisedViewLevels()))): ?>
			<h5 class="border-bottom pb-2 mb-3 mt-4"><?php echo \Joomla\CMS\Language\Text::_('CCATEGORIES')?></h5>
			<div class="mb-3">
				<label class="form-label" for="importfieldcategory"><?php echo \Joomla\CMS\Language\Text::_('CCATEGORY');?></label>
				<?php echo $this->fieldlist('category', $this->item->params->get('field.category'));?>
			</div>
			<div id="import-progress" class="progress mb-3" style="display: none; height: 8px;">
				<div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%;"></div>
			</div>
			<div id="cat-list"></div>
		<?php endif; ?>
	</div>
	<div class="col-md-6">
		<h5 class="border-bottom pb-2 mb-3"><?php echo \Joomla\CMS\Language\Text::_('CIMPORTFIELDASSOC')?></h5>

		<?php if($this->type->params->get('properties.item_title') == 1): ?>
			<div class="mb-3">
				<label class="form-label" for="importfieldtitle"><?php echo \Joomla\CMS\Language\Text::_('CTITLE');?>
					<span class="text-danger">*</span>
				</label>
				<?php echo $this->fieldlist('title', $this->item->params->get('field.title'));?>
			</div>
		<?php endif;?>

		<div class="mb-3">
			<label class="form-label" for="importfieldctime"><?php echo \Joomla\CMS\Language\Text::_('CIMPORT_CTIME');?></label>
			<?php echo $this->fieldlist('ctime', $this->item->params->get('field.ctime'));?>
		</div>

		<div class="mb-3">
			<label class="form-label" for="importfieldmtime"><?php echo \Joomla\CMS\Language\Text::_('CIMPORT_MTIME');?></label>
			<?php echo $this->fieldlist('mtime', $this->item->params->get('field.mtime'));?>
		</div>

		<?php foreach($this->fields AS $field): ?>
			<?php $form = $field->onImportForm($this->heads, $this->item->params); if(empty($form)) continue; ?>
			<div class="mb-3 <?php echo ($field->required ? 'required' : '') ?>">
				<label class="form-label"><?php echo $field->label;?>
					<?php if($field->required): ?>
						<span class="text-danger">*</span>
					<?php endif;?>
				</label>
				<?php echo $form;?>
			</div>
		<?php endforeach;?>
	</div>
</div>

<script type="text/javascript">
	if(jQuery('#importfieldcategory').val() && jQuery('#importfieldcategory').val() != '0') {
		categoryload();
	}
</script>