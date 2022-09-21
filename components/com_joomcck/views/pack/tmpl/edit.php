<?php
/**
 * Emerald by JoomBoost
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access'); ?>
<?php
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if(task == 'pack.cancel' || document.formvalidator.isValid(document.id('item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>


<form action="<?php echo JUri::getInstance()->toString(); ?>" method="post" name="adminForm" id="item-form" class="form-validate form-horizontal">
	<?php echo HTMLFormatHelper::layout('item', $this); ?>
	<div class="page-header">
		<h1>
			<?php echo empty($this->item->id) ? JText::_('CNEWPACK') : JText::sprintf('CEDITPACK', $this->item->name); ?>
		</h1>
	</div>

	<div class="control-group">
		<div class="control-label inline">
			<?php echo $this->form->getLabel('id'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('id'); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label inline">
			<?php echo $this->form->getLabel('name'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('name'); ?>
		</div>
	</div>
	<div class="control-group">
		<?php echo $this->form->getLabel('description'); ?><br>
		<?php echo $this->form->getInput('description'); ?>
	</div>

	<legend><?php echo JText::_('CPACKSETTINGS'); ?></legend>
	<div class="control-group">
		<div class="control-label inline">
			<?php echo $this->form->getLabel('user'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('user'); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label inline">
			<?php echo $this->form->getLabel('addkey'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('addkey'); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label inline">
			<?php echo $this->form->getLabel('demo'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('demo'); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label inline">
			<?php echo $this->form->getLabel('version'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('version'); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label inline">
			<?php echo $this->form->getLabel('author_email'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('author_email'); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label inline">
			<?php echo $this->form->getLabel('author_url'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('author_url'); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label inline">
			<?php echo $this->form->getLabel('author_name'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('author_name'); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label inline">
			<?php echo $this->form->getLabel('copyright'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('copyright'); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label inline">
			<?php echo $this->form->getLabel('add_files'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('add_files'); ?>
		</div>
	</div>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="return" value="<?php echo $this->state->get('groups.return'); ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>