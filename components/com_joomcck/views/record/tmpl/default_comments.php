<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
if(JoomcckCommentHelper::laded($this->item->id)) {
	return;
}
?>

<?php if($this->type->params->get('comments.comments') == 'core'): ?>

	<?php if(!count($this->comments) && $this->item->params->get('comments.comments_access_post') === 0):?>
		<div class="alert alert-warning">
			<?php echo \Joomla\CMS\Language\Text::_('CMSG_COMMENTSDISABLED')?>
		</div>
		<?php return; ?>
	<?php endif;?>
	<script type="text/javascript">
	<!--

	function setParent(id)
	{
		jQuery('#jform_parent_id').val(id);
	}

	function deleteComment(id)
	{
		if(!confirm('<?php echo \Joomla\CMS\Language\Text::_('CSUREDEL')?>'))
		{
			return;
		}
		jQuery('#cid').val(id);
		Joomla.submitbutton('comments.delete');
	}


	function publishComment(id, task)
	{
		jQuery('#cid').val(id);
		Joomla.submitbutton('comments.' + task);
	}


	function ajax_rateComment(id, rate)
	{
		jQuery.ajax({
			url:'<?php echo \Joomla\CMS\Router\Route::_("index.php?option=com_joomcck&task=rate.comment&tmpl=component", FALSE); ?>',
			data:{comment_id: id, state: rate },
			dataType: 'json'
		}).done(function(json){
			console.log(id);
			if(json.success)
			{
				jQuery('#comment_rate_control_' + id).html('<span class="badge bg-info">' + json.result + '</span>');
			}
			else
			{
				alert(json.error);
			}
		});
	}
	//-->
	</script>

	<?php if(($this->tmpl_params['comment']->get('tmpl_core.comments_title', 1) || $this->tmpl_params['comment']->get('tmpl_core.comments_rss_button', 1))
		&& in_array($this->item->params->get('comments.comments_access_post', $this->type->params->get('comments.comments_access_post')), $this->user->getAuthorisedViewLevels())):?>	<h2 class="mb-3">
			<?php if($this->tmpl_params['comment']->get('tmpl_core.comments_rss_button', 1)):?>
				<span class="float-end">
					<a class="btn btn-sm btn-outline-secondary" href="<?php echo \Joomla\CMS\Router\Route::_($this->item->url.'&format=feed&type=rss')?>" target="_blank">
						<?php echo HTMLFormatHelper::icon('feed.png');  ?>
					<?php echo \Joomla\CMS\Language\Text::_('CCOMMRSS')?></a>
				</span>
			<?php endif;?>
			<?php if($this->tmpl_params['comment']->get('tmpl_core.comments_title', 1)):?>
				<?php echo \Joomla\CMS\Language\Text::_($this->tmpl_params['comment']->get('tmpl_core.comments_title_lbl', 'CCOMMENTS'));?>
			<?php endif;?>
		</h2>
	<?php endif;?>

	<?php if($this->item->params->get('comments.comments_access_post') === 0):?>
		<div class="alert alert-warning">
			<?php echo \Joomla\CMS\Language\Text::_('CMSG_COMMENTSDISABLED')?>
		</div>
	<?php endif;?>

	<form method="post" name="adminForm" id="adminForm" class="form-validate mt-4" enctype="multipart/form-data">

        <?php echo \Joomcck\Layout\Helpers\Layout::render('core.comments.list',['current' => $this]) ?>


		<?php if(MECAccess::allowCommentPost($this->type, $this->item, $this->section)):?>
			<?php if(!CEmeraldHelper::allowType('comment', $this->type, $this->user->id, $this->section, FALSE, '', $this->item->user_id)) : ?>
				<?php $tparams = $this->type->params; ?>
				<?php $plans = $tparams->get('emerald.type_comment_subscription');?>
				<?php if(!is_array($plans)) settype($plans, 'array'); ?>
				<div id="alert alert-warning">
					<?php echo \Joomla\CMS\Language\Text::_($tparams->get('emerald.type_comment_subscription_msg')).'<br>'.
								\Joomla\CMS\HTML\HTMLHelper::link(EmeraldApi::getLink('emlist', FALSE, $plans), \Joomla\CMS\Language\Text::_('CSUBSCRIBE'))?>
				</div>
			<?php else :?>
				<?php echo $this->loadTemplate('comment_form');?>
			<?php endif;?>
		<?php endif;?>
	</form>
	<div class="clearfix"></div>


<?php elseif($this->type->params->get('comments.comments') && $this->item->params->get('comments.comments_access_post') !== 0): ?>
	<?php echo CommentHelper::listComments($this->type, $this->item); ?>
<?php endif;?>


