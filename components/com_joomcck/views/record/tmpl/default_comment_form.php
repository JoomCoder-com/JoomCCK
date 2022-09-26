<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.formvalidation');
$this->comment_form->setFieldAttribute('comment','editor', $this->tmpl_params['comment']->get('tmpl_core.comments_editor', 'tinymce'));
?>

<script type="text/javascript">
Joomla.submitbutton = function(task) {
	if (task == 'article.cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
		<?php echo $this->comment_form->getField('comment')->save(); ?>
		Joomla.submitform(task);
	} else {
		alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
	}
}
</script>

<div style="width:900px; margin-left: -450px" class="modal hide fade" id="commentmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h3 id="commentlabel"><?php echo JText::_('CEDITCOMMENT');?></h3>
	</div>

	<div id="commentframe" class="modal-body" style="overflow-x: hidden; max-height:650px; padding:0;">

	</div>
</div>


<a name="comment-form" id="form-starts"></a>
<div class="well form-horizontal">

	<legend>
		<?php echo JText::_($this->tmpl_params['comment']->get('tmpl_core.comments_add_title_lbl'));?>
	</legend>

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

	<?php $this->comment_form->setFieldAttribute('comment', 'height', $this->tmpl_params['comment']->get('tmpl_core.textarea_height', 300));?>
	<?php echo $this->comment_form->getInput('comment'); ?>

	<?php if($this->tmpl_params['comment']->get('tmpl_core.comments_subscribe', 1) && $this->user->get('id') && in_array($this->section->params->get('events.subscribe_record'), $this->user->getAuthorisedViewLevels())):?>
		<div class="control-group">
			<div class="control-label">
				<input type="hidden" name="jform[subscribe]" value="0">
				<?php echo $this->comment_form->getInput('subscribe');?>
				<!-- <input checked="checked" id="follow" type="checkbox" name="subscribe" value="1"> -->
			</div>
			<div class="controls">
				<label for="follow	">
					<big><?php echo JText::_('CFOLLOW')?></big>
					<br /><small>
						<?php echo JText::_('COMMENTFOLLOWCHECKBOX')?>
					</small>
				</label>
			</div>
		</div>
	<?php endif;?>

	<?php if(in_array($this->type->params->get('comments.comments_private'), $this->user->getAuthorisedViewLevels())):?>
		<div class="control-group">
			<div class="control-label">
				<input name="jform_private" id="prv-chk" value="" class="inputbox" type="checkbox">
				<input type="hidden" id="jform_private" name="jform[private]" value=""/>
			</div>
			<div class="controls">
				<label for="private">
					<big><?php echo JText::_('CPRIVATE')?></big>
					<br /><small>
						<?php echo JText::_('COMMENTPRIVATECHECKBOX')?>
					</small>
				</label>
			</div>
		</div>
		<script type="text/javascript">
			(function($){
				$('#prv-chk').change(function(){
					setval(this);
				});

				setval(document.getElementById('prv-chk'));

				function setval(el) {
					$('#jform_private').val('-1');
					if(el.checked) {
						$('#jform_private').val('1');
					}
				}
			}(jQuery))
		</script>
	<?php endif;?>

	<?php if(in_array($this->type->params->get('comments.comments_access_access'), $this->user->getAuthorisedViewLevels())):?>
		<div class="control-group">
			<?php echo $this->comment_form->getLabel('access'); ?>
			<div class="controls">
				<?php echo $this->comment_form->getInput('access'); ?>
			</div>
		</div>
	<?php endif;?>



	<?php if(in_array($this->type->params->get('comments.comment_attach'), $this->user->getAuthorisedViewLevels())):?>
		<div class="control-group">
			<label class="control-label">
				<?php echo JText::_('CATTACH');?>
			</label>
			<div class="controls">
				<div id="field-alert-0" class="alert alert-error" style="display:none"></div>
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

	<div class="form-actions">
		<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('comment.save')">
			<?php echo HTMLFormatHelper::icon('balloon--plus.png');  ?>
			<?php echo JText::_($this->tmpl_params['comment']->get('tmpl_core.comments_button_title_lbl'));?>
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
<input type="hidden" name="Itemid" value="<?php echo JFactory::getApplication()->input->getInt('Itemid');?>">
<input type="hidden" name="is_new" value="1">
<?php echo JHtml::_( 'form.token' ); ?>
