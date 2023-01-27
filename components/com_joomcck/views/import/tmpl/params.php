<?php
/**
 * by JoomBoost
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>
<br>
<div class="row">
	<div class="col-md-6">
		<legend><?php echo JText::_('CIMPORTPARAMS')?></legend>
		<div class="control-group">
			<label class="control-label" for="name"><?php echo JText::_('CNAME');?>
				<span class="float-end" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED') ?>">
					<?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
			</label>

			<div class="controls">
				<div class="row">
					<input type="text" class="col-md-12" name="import[name]" id=importname"  value="<?php echo $this->item->params->get('name'); ?>">
				</div>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="name"><?php echo JText::_('ID');?>
				<span class="float-end" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED') ?>">
					<?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
			</label>

			<div class="controls">
				<div class="row">
					<?php echo $this->fieldlist('id', $this->item->params->get('field.id'));?>
					<small><?php echo JText::_('CIMPORTUNIQID')?></small>
				</div>
			</div>
		</div>
		<?php if($this->section->categories || ($this->section->params->get('personalize.personalize', 0) && in_array($this->section->params->get('personalize.pcat_submit', 0), $this->user->getAuthorisedViewLevels()))): ?>
			<legend><?php echo JText::_('CCATEGORIES')?></legend>
			<div class="control-group">
				<label class="control-label" for="name"><?php echo JText::_('CCATEGORY');?>
					<span class="float-end" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED') ?>">
						<?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
				</label>

				<div class="controls">
					<div class="row">
						<?php echo $this->fieldlist('category', $this->item->params->get('field.category'));?>
					</div>
				</div>
			</div>
			<div id="progress" class="progress progress-striped active" style="display: none;">
				<div class="bar" style="width: 100%;"></div>
			</div>
			<div id="cat-list">
			</div>
		<?php endif; ?>
	</div>
	<div class="col-md-6">
		<legend><?php echo JText::_('CIMPORTFIELDASSOC')?></legend>

		<?php if($this->type->params->get('properties.item_title') == 1): ?>
			<div class="control-group">
				<label class="control-label" for="name"><?php echo JText::_('CTITLE');?>
					<span class="float-end" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED') ?>">
						<?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
				</label>

				<div class="controls">
					<div class="row">
						<?php echo $this->fieldlist('title', $this->item->params->get('field.title'));?>
					</div>
				</div>
			</div>
		<?php endif;?>

		<div class="control-group">
			<label class="control-label" for="name">
				<?php echo JText::_('CTIME');?>
			</label>

			<div class="controls">
				<div class="row">
					<?php echo $this->fieldlist('ctime', $this->item->params->get('field.ctime'));?>
				</div>
			</div>
		</div>

		<div class="control-group">
			<label class="control-label" for="name">
				<?php echo JText::_('MTIME');?>
			</label>

			<div class="controls">
				<div class="row">
					<?php echo $this->fieldlist('mtime', $this->item->params->get('field.mtime'));?>
				</div>
			</div>
		</div>

		<?php foreach($this->fields AS $field): ?>
			<?php $form = $field->onImportForm($this->heads, $this->item->params); if(empty($form)) continue;  ?>
			<div class="control-group">
				<label class="control-label" for="type"><?php echo $field->label;?>
					<?php if($field->required): ?>
						<span class="float-end" rel="tooltip" data-original-title="<?php echo JText::_('CREQUIRED') ?>">
							<?php echo HTMLFormatHelper::icon('asterisk-small.png');  ?></span>
					<?php endif;?>
				</label>

				<div class="controls">
					<div class="row <?php echo ($field->required ? 'required' : null) ?>">
						<?php echo $form;?>
					</div>
				</div>
			</div>
		<?php endforeach;?>
	</div>
</div>

<script type="text/javascript">
	if(jQuery('#importfieldcategory').val() != '') {
		categoryload();
	}
</script>