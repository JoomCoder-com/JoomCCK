<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	<!--
	Joomla.submitbutton = function(task) {
		if(task == 'ctype.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
	//-->
</script>

<form action="<?php echo JUri::getInstance()->toString(); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<?php echo HTMLFormatHelper::layout('item', $this); ?>
	<div class="page-header">
		<h1>
			<?php echo empty($this->item->id) ? JText::_('CNEWTYPE') : JText::sprintf('CEDITTYPES', $this->item->name); ?>
		</h1>
	</div>
	<ul class="nav nav-tabs">
		<li class="active"><a href="#page-main" data-toggle="tab"><?php echo JText::_('FS_FORM') ?></a></li>
		<li><a href="#page-params" data-toggle="tab"><?php echo JText::_('FS_GENERAL') ?></a></li>
		<li><a href="#page-submission" data-toggle="tab"><?php echo JText::_('FS_SUBMISPARAMS') ?></a></li>
		<li><a href="#page-limit" data-toggle="tab"><?php echo JText::_('CCATEGORYLIMIT') ?></a></li>
		<li><a href="#page-comments" data-toggle="tab"><?php echo JText::_('FS_COMMPARAMS') ?></a></li>
		<li><a href="#page-audit" data-toggle="tab"><?php echo JText::_('FS_AUDIT') ?></a></li>
		<li><a href="#page-emerald" data-toggle="tab"><?php echo JText::_('FS_EMERALD') ?></a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane active" id="page-main">
			<div class="pull-left" style="max-width: 600px; margin-right: 20px;">
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('published'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('language'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('language'); ?></div>
				</div>
				<?php echo $this->form->getLabel('description'); ?>
				<?php echo $this->form->getInput('description'); ?>

			</div>
			<div class="pull-left" style="max-width: 600px;">
				<?php echo MFormHelper::renderFieldset($this->params_form, 'prop', $this->item->params, 'properties'); ?>
			</div>
		</div>
		<div class="tab-pane" id="page-params">
			<div class="pull-left" style="max-width: 600px; margin-right: 20px;">
				<?php echo MFormHelper::renderFieldset($this->params_form, 'templates', $this->item->params, 'properties'); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'tags', $this->item->params, 'properties'); ?>
			</div>
			<div class="pull-left" style="max-width: 600px;">
				<?php echo MFormHelper::renderFieldset($this->params_form, 'title', $this->item->params, 'properties'); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'rating', $this->item->params, 'properties'); ?>
			</div>
		</div>
		<div class="tab-pane" id="page-submission">
			<div class="pull-left" style="max-width: 600px; margin-right: 20px;">
				<?php echo MFormHelper::renderFieldset($this->params_form, 'submit', $this->item->params, 'submission'); ?>
			</div>
			<div class="pull-left" style="max-width: 600px;">
				<?php echo MFormHelper::renderFieldset($this->params_form, 'categories', $this->item->params, 'submission'); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'metadata', $this->item->params, 'submission'); ?>
			</div>
		</div>
		<div class="tab-pane" id="page-limit">
			<div class="pull-left" style="max-width: 600px; margin-right: 20px;">
				<?php echo MFormHelper::renderGroup($this->params_form, $this->item->params, 'category_limit'); ?>
			</div>
			<div class="pull-left">
				<legend><?php echo JText::_('CCATEGORYLIMIT') ?></legend>
				<?php echo JHtml::_('mrelements.catselector', 'params[category_limit][category][]', 0, @$this->item->params['category_limit']['category'], 0); ?>
			</div>
		</div>
		<div class="tab-pane" id="page-comments">
			<div class="pull-left" style="max-width: 600px; margin-right: 20px;">
				<?php echo MFormHelper::renderGroup($this->params_form, $this->item->params, 'comments'); ?>
				<div id="comments-params"></div>
			</div>
		</div>
		<div class="tab-pane" id="page-audit">
			<div class="pull-left" style="max-width: 600px; margin-right: 20px;">
				<?php echo MFormHelper::renderFieldset($this->params_form, 'ver', $this->item->params, 'audit'); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'verl', $this->item->params, 'audit'); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'verw', $this->item->params, 'audit'); ?>
			</div>
			<div class="pull-left" style="max-width: 600px;">
				<?php echo MFormHelper::renderFieldset($this->params_form, 'ver2', $this->item->params, 'audit'); ?>
			</div>
		</div>
		<div class="tab-pane" id="page-emerald">
			<p class="lead"><?php echo JText::_('FS_EMERALDINTEGRATE') ?>

			<div class="pull-left" style="max-width: 600px; margin-right: 20px;">
				<?php echo MFormHelper::renderFieldset($this->params_form, 'type_subscr', $this->item->params, 'emerald'); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'type_subscr6', $this->item->params, 'emerald'); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'type_subscr13', $this->item->params, 'emerald'); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'type_subscr1', $this->item->params, 'emerald'); ?>
			</div>
			<div class="pull-left" style="max-width: 600px;">
				<?php echo MFormHelper::renderFieldset($this->params_form, 'type_subscr2', $this->item->params, 'emerald'); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'type_subscr3', $this->item->params, 'emerald'); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'type_subscr12', $this->item->params, 'emerald'); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'type_subscr4', $this->item->params, 'emerald'); ?>
				<?php echo MFormHelper::renderFieldset($this->params_form, 'type_subscr5', $this->item->params, 'emerald'); ?>
			</div>
		</div>
	</div>

	<input type="hidden" name="task" value=""/>
	<?php echo JHtml::_('form.token'); ?>
</form>
<script type="text/javascript">
	<!--
	!function($) {
		function loadCommentParams() {
			$.ajax({
				url: '<?php echo JRoute::_('index.php?option=com_joomcck&task=ajax.loadcommentparams&tmpl=component');?>',
				context: $('#comments-params'),
				dataType: 'html',
				data: {
					adp: $('#params_comments_comments').val(),
					type:<?php echo JRequest::getInt('id')?>
				}
			}).done(function(data) {
				$(this).html(data);
				Joomcck.redrawBS();
			});
		}

		loadCommentParams();
		$('#params_comments_comments').change(loadCommentParams);

	}(window.jQuery)
	//-->
</script>