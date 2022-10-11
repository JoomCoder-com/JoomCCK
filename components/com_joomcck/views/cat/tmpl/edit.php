<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// Load the tooltip behavior.
JHtml::_('bootstrap.tooltip');
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if(task == 'cat.cancel' || document.formvalidator.isValid(document.getElementById('item-form'))) {

			Joomla.submitform(task, document.getElementById('item-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_joomcck&view=category&section_id=' . \Joomla\CMS\Factory::getApplication()->input->getInt('section_id',0) . '&layout=edit&id=' . (int)$this->item->id); ?>" method="post" name="adminForm" id="item-form"
	  class="form-validate form-horizontal">
	<?php echo HTMLFormatHelper::layout('item', $this); ?>
	<div class="page-header">
		<h1>
			<?php echo empty($this->item->id) ? JText::_('CNEWCATEGORY') : JText::sprintf('CEDITCATEGORYS', $this->item->title); ?>
		</h1>
	</div>

	<ul class="nav nav-tabs">
		<li class="active"><a href="#details" data-toggle="tab"><?php echo JText::_('COM_JOOMCCK_FIELDSET_DETAILS'); ?></a></li>
		<li><a href="#options" data-toggle="tab"><?php echo JText::_('COM_JOOMCCK_FIELDSET_OPTIONS'); ?></a></li>
		<li><a href="#relative" data-toggle="tab"><?php echo JText::_('CRELATIVECAT'); ?></a></li>
		<li><a href="#metadata" data-toggle="tab"><?php echo JText::_('X_SECFSLMETA'); ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane active" id="details">
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('title'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('title'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('alias'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('alias'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('parent_id'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('parent_id'); ?>
				</div>
			</div>
			<legend><?php echo $this->form->getLabel('description'); ?></legend>
			<?php echo $this->form->getInput('description'); ?>
			<?php echo MFormHelper::renderFieldset($this->form, 'general', $this->item->params, 'params', MFormHelper::FIELDSET_SEPARATOR_HEADER); ?>
			<?php echo MFormHelper::renderFieldset($this->form, 'general_tmpl', $this->item->params, 'params', MFormHelper::FIELDSET_SEPARATOR_HEADER); ?>
		</div>
		<div class="tab-pane" id="options">
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('published'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('published'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('access'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('access'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('language'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('language'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('id'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('id'); ?>
				</div>
			</div>
			<?php echo $this->loadTemplate('options'); ?>
		</div>
		<div class="tab-pane" id="relative">
			<?php echo JHtml::_('mrelements.catselector', 'jform[relative_cats][]', $this->item->section_id, $this->item->relative_cats_ids, 0); ?>
		</div>
		<div class="tab-pane" id="metadata">
			<?php echo $this->loadTemplate('metadata'); ?>
		</div>
	</div>


	<div>
		<?php echo $this->form->getInput('section_id'); ?>
		<input type="hidden" name="task" value=""/>
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
