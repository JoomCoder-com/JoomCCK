<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access'); ?>
<?php
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');

// required css file fix issues of UI/UX
\Joomla\CMS\Factory::getDocument()->addStyleSheet(\Joomla\CMS\Uri\Uri::root().'/media/com_joomcck/css/joomcck.css');

// Extended version notice for extended-only fieldsets
require_once JPATH_SITE . '/components/com_joomcck/library/php/helpers/extended.php';
MExtendedHelper::renderNotice();
?>
<script type="text/javascript">
	
	Joomla.submitbutton = function (task)
	{	
		if (task == 'tfield.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		else {
			alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
	function ajax_load_sub_params(sel, dir) {

        let selected = $(sel).find(':selected').val();
        let selectedId = $(sel).attr('id');

		jQuery.ajax({
			url: '<?php echo \Joomla\CMS\Uri\Uri::base(); ?>index.php?option=com_joomcck&task=ajax.loadfieldparams&tmpl=component',
			context: jQuery('#config_'+ selectedId + ' .modal-body'),
			dataType: 'html',
			data:{value: selected, dir: dir, fid: <?php echo (int)$this->item->id;?> }
		}).done(function(data) {
			if(data.length == 0) {
				jQuery('#tr_'+selectedId+' a.btn').hide();
			} else {
				jQuery('#tr_'+selectedId+' a.btn').show();
			}
			jQuery(this).html(data);
			Joomcck.redrawBS();
		});
	}
    function ajax_loadfieldform(sel)
    {
        let selected = $(sel).find(':selected').val();
        let typeId = $('#jform_type_id').val(); // Get the type_id from the hidden input

        jQuery.ajax({
            url: '<?php echo \Joomla\CMS\Uri\Uri::base(); ?>index.php?option=com_joomcck&task=ajax.loadfieldform&tmpl=component',
            context: jQuery('#additional-form'),
            dataType: 'html',
            data:{
                field: selected,
                type_id: typeId // Add the type_id to the request
            }
        }).done(function(data) {
            jQuery(this).html(data);
            Joomcck.redrawBS();
        });
    }
	function ajax_loadpayform(sel)
	{

        let selected = $(sel).find(':selected').val();


		jQuery.ajax({
			url: '<?php echo \Joomla\CMS\Uri\Uri::root(); ?>index.php?option=com_joomcck&task=ajax.loadcommerce&tmpl=component',
			context: jQuery('#additional-pay-form'),
			dataType: 'html',
			data:{gateway: selected, fid: <?php echo (int) $this->item->id;?> }
		}).done(function(data) {
			jQuery(this).html(data);
			Joomcck.redrawBS();
		});
	}
</script>

<form action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString(); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<?php echo HTMLFormatHelper::layout('item', $this); ?>

	<div class="page-header">
		<h1><?php echo empty($this->item->id) ? Text::_('CNEWFIELD') : Text::sprintf('CEDITFIELDS', $this->item->label); ?></h1>
	</div>

    <div id="joomcckContainer">

	    <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'page-main', 'recall' => true, 'breakpoint' => 768]); ?>
	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-main', Text::_('FS_FORM')); ?>

            <div class="row">
                <div class="col-md-4">
                    <div class="control-group">
                        <div class="form-label"><?php echo $this->form->getLabel('id'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="form-label"><?php echo $this->form->getLabel('label'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('label'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="form-label"><?php echo $this->form->getLabel('field_type'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('field_type'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="form-label"><?php echo $this->form->getLabel('published'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('published'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="form-label"><?php echo $this->form->getLabel('ordering'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('ordering'); ?></div>
                    </div>
                    <div class="control-group">
                        <div class="form-label"><?php echo $this->form->getLabel('group_id'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('group_id'); ?></div>
                    </div>

                </div>
                <div class="col-md-8">
                    <legend><?php echo Text::_('CFIELDPARAMS'); ?></legend>
                    <div id="additional-form">
		                <?php echo @$this->parameters?>
                    </div>
                    <div id="additional-pay-form">
                    </div>
                </div>
            </div>

	    <?php echo HTMLHelper::_('uitab.endTab'); ?>
	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-display', Text::_('XML_LABEL_F_DISPLAY')); ?>
            <?php echo $this->params_form->renderFieldset('display'); ?>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>
	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-general', Text::_('XML_LABEL_F_GP')); ?>
             <?php echo $this->params_form->renderFieldset('general'); ?>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>

	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-label', Text::_('CLABEL')); ?>
	        <?php echo $this->params_form->renderFieldset('label'); ?>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>

	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-label', Text::_('CACCESS')); ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="m-0"><?php echo Text::_('XML_LABEL_F_VIEW_ACCESS'); ?></h5>
                </div>
                <div class="card-body">
                    <?php echo $this->params_form->renderFieldset('access2_view'); ?>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="m-0"><?php echo Text::_('XML_LABEL_F_SUBMIT_ACCESS'); ?></h5>
                </div>
                <div class="card-body">
                    <?php echo $this->params_form->renderFieldset('access2_submit'); ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="m-0"><?php echo Text::_('XML_LABEL_F_EDIT_ACCESS'); ?></h5>
                </div>
                <div class="card-body">
                    <?php echo $this->params_form->renderFieldset('access2_edit'); ?>
                </div>
            </div>
	    <?php echo HTMLHelper::_('uitab.endTab'); ?>

	    <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'page-emerald', Text::_('FS_EMERALD')); ?>
            <p class="lead"><?php echo Text::_('FS_EMERALDINTEGRATE')?>
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

	<input type="hidden" id="jform_type_id" name="jform[type_id]" value="<?php echo \Joomla\CMS\Factory::getApplication()->input->getInt('type_id', $this->state->get('filter.type'));?>" />
	<input type="hidden" id="jform_type_id" name="type_id" value="<?php echo $this->state->get('filter.type',\Joomla\CMS\Factory::getApplication()->input->getInt('type_id'));?>" />
	<input type="hidden" name="task" value="" />
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
</form>