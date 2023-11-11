<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');


\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip');

$this->comment_form->setFieldAttribute('comment','editor', $this->tmpl_params['comment']->get('tmpl_core.comments_editor', 'tinymce'));
?>


<div style="width:900px; margin-left: -450px" class="modal hide fade" id="commentmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="commentlabel"><?php echo \Joomla\CMS\Language\Text::_('CEDITCOMMENT');?></h3>
	</div>

	<div id="commentframe" class="modal-body" style="overflow-x: hidden; max-height:650px; padding:0;">

	</div>
</div>


<a name="comment-form" id="form-starts"></a>
<div class="card form-horizontal">

	<div class="card-header">
		<h5 class="m-0"><i class="fas fa-plus"></i> <?php echo \Joomla\CMS\Language\Text::_($this->tmpl_params['comment']->get('tmpl_core.comments_add_title_lbl'));?></h5>
    </div>

	<div class="card-body">
		<?php if(!$this->user->get('id')):?>
            <div class="control-group">
				<?php echo $this->comment_form->getLabel('name') ; ?>
                <div class="controls">
					<?php echo $this->comment_form->getInput('name') ; ?>
                </div>
            </div>
            <div class="control-group">
				<?php echo $this->comment_form->getLabel('email'); ?>
                <div class="controls">
					<?php echo $this->comment_form->getInput('email'); ?>
                </div>
            </div>
		<?php else:?>
            <input type="hidden" name="jform[name]" value="<?php echo CCommunityHelper::getName($this->user->id, $this->section, array('nohtml' => 1)) ?>" >
            <input type="hidden" name="jform[email]" value="<?php echo $this->user->email; ?>" >
		<?php endif;?>


        <div class="clearfix">
	        <?php $this->comment_form->setFieldAttribute('comment', 'height', $this->tmpl_params['comment']->get('tmpl_core.textarea_height', 300));?>
	        <?php echo $this->comment_form->getInput('comment'); ?>
        </div>


        <div class="row">

	        <?php if($this->tmpl_params['comment']->get('tmpl_core.comments_subscribe', 1) && $this->user->get('id') && in_array($this->section->params->get('events.subscribe_record'), $this->user->getAuthorisedViewLevels())):?>
                <div class="col-md-4 mb-3">
			        <?php echo $this->comment_form->renderField('subscribe');?>
                </div>
	        <?php endif;?>


	        <?php if(in_array($this->type->params->get('comments.comments_private'), $this->user->getAuthorisedViewLevels())):?>
                <div class="col-md-4 mb-3">
	                <?php echo $this->comment_form->renderField('private');?>
                </div>
	        <?php endif;?>


	        <?php if(in_array($this->type->params->get('comments.comments_access_access'), $this->user->getAuthorisedViewLevels())):?>
		        <div class="col-md-4 mb-3">
			        <?php echo $this->comment_form->renderField('access'); ?>
                </div>
	        <?php endif;?>

        </div>







		<?php if(in_array($this->type->params->get('comments.comment_attach'), $this->user->getAuthorisedViewLevels())):?>
            <div class="card">
                <div class="card-header bg-white">
                    <strong><?php echo \Joomla\CMS\Language\Text::_('CATTACH');?></strong>
                </div>
                <div class="card-body">
                    <div id="field-alert-0" class="alert alert-danger" style="display:none"></div>
					<?php echo $this->comment_form->getField('attachment')->getInput(array(
						'max_size' => $this->type->params->get('comments.comments_attachment_max'),
						'file_formats' => $this->type->params->get('comments.comments_allowed_formats'),
						'max_count' => $this->type->params->get('comments.comments_max_count', 1),
						'allow_edit_title' => 0,
						'allow_add_descr' => 0
					)); ?>
                </div>
            </div>
		<?php endif;?>



		<?php if($this->type->params->get('comments.comment_captcha') && !$this->user->get('id')):?>
            <div class="formelm">
				<?php echo $this->comment_form->getInput('captcha'); ?>
            </div>
		<?php endif;?>

    </div>

    <div class="card-footer">
        <button type="button" class="btn btn-outline-success" onclick="Joomla.submitbutton('comment.save')">
		    <i class="fas fa-plus"></i>
		    <?php echo \Joomla\CMS\Language\Text::_($this->tmpl_params['comment']->get('tmpl_core.comments_button_title_lbl'));?>
        </button>
    </div>


</div>

<input type="hidden" name="task" value="">
<input type="hidden" name="cid[]" id="cid" value="">
<input type="hidden" name="record_id" value="<?php echo $this->item->id;?>">
<input type="hidden" name="section_id" value="<?php echo $this->item->section_id?>">
<input type="hidden" name="jform[record_id]" value="<?php echo $this->item->id;?>">
<input type="hidden" name="jform[parent_id]" value="1">
<?php //echo $this->comment_form->getInput('parent_id'); ?>
<input type="hidden" name="Itemid" value="<?php echo \Joomla\CMS\Factory::getApplication()->input->getInt('Itemid');?>">
<input type="hidden" name="is_new" value="1">
<?php echo \Joomla\CMS\HTML\HTMLHelper::_( 'form.token' ); ?>
