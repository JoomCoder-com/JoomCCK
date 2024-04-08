<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();


\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');


$app = \Joomla\CMS\Factory::getApplication();

?>

<br>
<form action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString();?>" method="post" name="adminForm" id="adminForm" class=" form-horizontal"  style="padding: 20px">
	<?php if(!$this->user->get('id')):?>
		<div class="control-group">
			<?php echo $this->form->getLabel('name') ; ?>
			<div class="controls">
				<?php echo $this->form->getInput('name') ; ?>
			</div>
		</div>
		<div class="control-group">
			<?php echo $this->form->getLabel('email'); ?>
			<div class="controls">
				<?php echo $this->form->getInput('email'); ?>
			</div>
		</div>
	<?php else:?>
		<input type="hidden" name="jform[name]" value="<?php echo CCommunityHelper::getName($this->user->id, $this->section, array('nohtml' => 1)) ?>" >
		<input type="hidden" name="jform[email]" value="<?php echo $this->user->email; ?>" >
	<?php endif;?>

	<?php
	    $this->form->setFieldAttribute('comment','editor', $this->tmpl_params['comment']->get('tmpl_core.comments_editor', 'tinymce'));
	    echo $this->form->getInput('comment');
	?>

	<?php if($this->tmpl_params['comment']->get('tmpl_core.comments_subscribe', 1) && $this->user->get('id') && in_array($this->section->params->get('events.subscribe_record'), $this->user->getAuthorisedViewLevels())):?>
		<div class="control-group">
			<div for="follow" class="control-label">
				<?php echo $this->form->getInput('subscribe');?>
				<!-- <input checked="checked" id="follow" type="checkbox" name="subscribe" value="1"> -->
			</div>
			<div class="controls">
				<label for="follow	">
					<big><?php echo \Joomla\CMS\Language\Text::_('CFOLLOW')?></big>
					<br /><small>
						<?php echo \Joomla\CMS\Language\Text::_('COMMENTFOLLOWCHECKBOX')?>
					</small>
				</label>
			</div>
		</div>
	<?php endif;?>

	<?php if(in_array($this->type->params->get('comments.comments_private'), $this->user->getAuthorisedViewLevels())):?>
		<div class="control-group">
			<div class="control-label">
				<input name="jform_private" id="prv-chk" value="" class="form-control" type="checkbox" <?php echo ($this->item->private == 1 ? 'checked' : NULL); ?>>
				<input type="hidden" id="jform_private" name="jform[private]" value=""/>
			</div>
			<div class="controls">
				<label for="private">
					<big><?php echo \Joomla\CMS\Language\Text::_('CPRIVATE')?></big>
					<br /><small>
						<?php echo \Joomla\CMS\Language\Text::_('COMMENTPRIVATECHECKBOX')?>
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
			<?php echo $this->form->getLabel('access'); ?>
			<div class="controls">
				<?php echo $this->form->getInput('access'); ?>
			</div>
		</div>
	<?php endif;?>



	<?php if(in_array($this->type->params->get('comments.comment_attach'), $this->user->getAuthorisedViewLevels())):?>
		<div class="control-group">
			<label class="control-label">
				<?php echo \Joomla\CMS\Language\Text::_('CATTACH');?>
			</label>
			<div class="controls">
			<div id="field-alert-0" class="alert alert-danger" style="display:none"></div>
				<?php echo $this->form->getField('attachment')->getInput(array(
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
			<?php echo $this->form->getInput('captcha'); ?>
		</div>
	<?php endif;?>

	<div class="form-actions">
		<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('comment.save')">
			<?php echo HTMLFormatHelper::icon('balloon--plus.png');  ?>
			<?php echo !empty($this->item->id) ? \Joomla\CMS\Language\Text::_('CSAVE') : \Joomla\CMS\Language\Text::_($this->tmpl_params['comment']->get('tmpl_core.comments_button_title_lbl'));?>
		</button>
	</div>

	<?php echo $this->form->getInput('id'); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_joomcck" />
	<input type="hidden" name="tmpl" value="<?php echo $app->input->getCmd('tmpl');?>" />
	<input type="hidden" name="cid[]" id="cid" value="<?php echo $this->item->id;?>" />
	<input type="hidden" name="record_id" value="<?php echo $this->item->record_id;?>" />
	<input type="hidden" name="section_id" value="<?php echo $app->input->getInt('section_id')?>" />
	<input type="hidden" name="jform[record_id]" value="<?php echo $this->item->record_id;?>" />
	<?php echo $this->form->getInput('parent_id'); ?>
	<input type="hidden" name="Itemid" value="<?php echo $app->input->getInt('Itemid');?>" />
	<?php if (!$this->item->id && !$this->item->parent_id) : ?>
	<input type="hidden" name="is_new" value="1" />
	<?php endif; ?>
	<?php if ($app->input->getInt('id')) : ?>
	<input type="hidden" name="is_edited" value="1" />
	<?php endif; ?>
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_( 'form.token' ); ?>

</form>

<?php /*Joomla.submitbutton('comments.edit')  ajaxCommentEdit(<?php echo $this->item->id;?>);  */ ?>