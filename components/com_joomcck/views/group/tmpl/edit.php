<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access'); ?>
<?php
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if(task == 'group.cancel' || document.formvalidator.isValid(document.getElementById('item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
		else {
			alert('<?php echo $this->escape(\Joomla\CMS\Language\Text::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString(); ?>" method="post" name="adminForm" id="item-form" class="form-horizontal">
	<?php echo HTMLFormatHelper::layout('item', $this); ?>
	<div class="page-header">
		<h1>
			<?php echo empty($this->item->id) ? \Joomla\CMS\Language\Text::_('CNEWGROUP') : \Joomla\CMS\Language\Text::sprintf('CEDITGROUPS', $this->item->title); ?>
		</h1>
	</div>

	<div class="control-group">
		<div class="form-label"><?php echo $this->form->getLabel('id'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
	</div>
	<div class="control-group">
		<div class="form-label"><?php echo $this->form->getLabel('title'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
	</div>
	<div class="control-group">
		<div class="form-label"><?php echo $this->form->getLabel('ordering'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('ordering'); ?></div>
	</div>
	<div class="control-group">
		<div class="form-label"><?php echo $this->form->getLabel('icon'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('icon'); ?></div>
	</div>
	<div class="control-group form-vertical">
		<div class="form-label"><?php echo $this->form->getLabel('description'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
	</div>

	<input type="hidden" id="jform_type_id" name="jform[type_id]" value="<?php echo $this->state->get('groups.type'); ?>"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="return" value="<?php echo $this->state->get('groups.return'); ?>"/>
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
</form>