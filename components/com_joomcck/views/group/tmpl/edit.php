<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
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
		if(task == 'group.cancel' || document.formvalidator.isValid(document.getElementById('item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JUri::getInstance()->toString(); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal">
	<?php echo HTMLFormatHelper::layout('item', $this); ?>
	<div class="page-header">
		<h1>
			<?php echo empty($this->item->id) ? JText::_('CNEWGROUP') : JText::sprintf('CEDITGROUPS', $this->item->title); ?>
		</h1>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
	</div>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
	</div>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('ordering'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('ordering'); ?></div>
	</div>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('icon'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('icon'); ?></div>
	</div>
	<div class="control-group form-vertical">
		<div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
	</div>

	<input type="hidden" id="jform_type_id" name="jform[type_id]" value="<?php echo $this->state->get('groups.type'); ?>"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="return" value="<?php echo $this->state->get('groups.return'); ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>