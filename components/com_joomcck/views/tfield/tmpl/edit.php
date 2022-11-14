<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die('Restricted access'); ?>
<?php
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
?>
<script type="text/javascript">
	
	Joomla.submitbutton = function (task)
	{	
		if (task == 'tfield.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
	function ajax_load_sub_params(sel, dir) {
		sel.options[sel.selectedIndex].value;
		console.log(sel.id);
		jQuery.ajax({
			url: '<?php echo JURI::base(); ?>index.php?option=com_joomcck&task=ajax.loadfieldparams&tmpl=component',
			context: jQuery('#config_'+ sel.id + ' .modal-body'),
			dataType: 'html',
			data:{value: sel.options[sel.selectedIndex].value, dir: dir, fid: <?php echo (int)$this->item->id;?> }
		}).done(function(data) {
			if(data.length == 0) {
				jQuery('#tr_'+sel.id+' a.btn').hide();
			} else {
				jQuery('#tr_'+sel.id+' a.btn').show();
			}
			jQuery(this).html(data);
			Joomcck.redrawBS();
		});
	}
	function ajax_loadfieldform(sel)
	{
		jQuery.ajax({
			url: '<?php echo JURI::base(); ?>index.php?option=com_joomcck&task=ajax.loadfieldform&tmpl=component',
			context: jQuery('#additional-form'),
			dataType: 'html',
			data:{field: sel.options[sel.selectedIndex].value}
		}).done(function(data) {
			jQuery(this).html(data);
			Joomcck.redrawBS();
		});
	}
	function ajax_loadpayform(sel)
	{
		jQuery.ajax({
			url: '<?php echo JURI::base(); ?>index.php?option=com_joomcck&task=ajax.loadcommerce&tmpl=component',
			context: jQuery('#additional-pay-form'),
			dataType: 'html',
			data:{gateway: sel.options[sel.selectedIndex].value, fid: <?php echo (int)$this->item->id;?> }
		}).done(function(data) {
			jQuery(this).html(data);
			Joomcck.redrawBS();
		});
	}
</script>

<form action="<?php echo JUri::getInstance()->toString(); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<?php echo HTMLFormatHelper::layout('item', $this); ?>

	<div class="page-geader">
		<h1><?php echo empty($this->item->id) ? JText::_('CNEWFIELD') : JText::sprintf('CEDITFIELDS', $this->item->label); ?></h1>
	</div>

    <div id="joomcckContainer">

	    <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'page-main', 'recall' => true, 'breakpoint' => 768]); ?>
	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-main', JText::_('FS_FORM')); ?>
            <div class="float-start" style="max-width: 450px; margin-right: 20px;">
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('label'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('label'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('field_type'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('field_type'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('published'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('ordering'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('ordering'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('group_id'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('group_id'); ?></div>
                </div>
                <?php echo MFormHelper::renderFieldset($this->params_form, 'general', $this->item->params, 'core'); ?>
                <?php echo MFormHelper::renderFieldset($this->params_form, 'label', $this->item->params, 'core'); ?>
                <?php echo MFormHelper::renderFieldset($this->params_form, 'access2_view', $this->item->params, 'core'); ?>
                <?php echo MFormHelper::renderFieldset($this->params_form, 'access2_submit', $this->item->params, 'core'); ?>
                <?php echo MFormHelper::renderFieldset($this->params_form, 'access2_edit', $this->item->params, 'core'); ?>

            </div>
            <div class="float-start" style="max-width: 450px">
                <legend><?php echo JText::_('CFIELDPARAMS'); ?></legend>
                <div id="additional-form">
                    <?php echo @$this->parameters?>
                </div>
                <div id="additional-pay-form">
                </div>
            </div>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>

	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-emerald', JText::_('FS_EMERALD')); ?>
            <p class="lead"><?php echo JText::_('FS_EMERALDINTEGRATE')?>
            <div class="float-start" style="max-width: 500px; margin-right: 20px;">
                <?php echo MFormHelper::renderFieldset($this->params_form, 'sp', $this->item->params, 'emerald'); ?>
                <?php echo MFormHelper::renderFieldset($this->params_form, 'sp4', $this->item->params, 'emerald'); ?>
                <?php echo MFormHelper::renderFieldset($this->params_form, 'sp3', $this->item->params, 'emerald'); ?>
            </div>
            <div class="float-start" style="max-width: 500px;">
                <?php echo MFormHelper::renderFieldset($this->params_form, 'sp2', $this->item->params, 'emerald'); ?>
                <?php echo MFormHelper::renderFieldset($this->params_form, 'sp21', $this->item->params, 'emerald'); ?>
            </div>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>
	    <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

    </div>

	<input type="hidden" id="jform_type_id" name="jform[type_id]" value="<?php echo JFactory::getApplication()->input->getInt('type_id', $this->state->get('filter.type'));?>" />
	<input type="hidden" id="jform_type_id" name="type_id" value="<?php echo $this->state->get('filter.type',JFactory::getApplication()->input->getInt('type_id'));?>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>