<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

JHtml::_('behavior.modal', 'a.modal_' . $this->id);
?>

<?php if(!$this->isadmin): ?>
	<div class="alert <?php echo $this->access ? 'alert-success' : ''; ?>">
		<?php echo $this->text; ?>
		<?php if(!empty($this->subscr)): ?>
			<br>
			<small><a
					href="<?php echo EmeraldApi::getLink('list', TRUE, $this->subscr); ?>"><?php echo JText::_('CSUBSCRIBENOW'); ?></a>
			</small>
		<?php endif; ?>
	</div>

	<?php if($client == 'full' && $this->default->get('quiz') && $this->default->get('method', $this->params->get('params.activ_mode')) == 'quiz'): ?>
		<h4><?php echo JText::_('DC_PASSQUIZ'); ?></h4>
		<div class="quiz-box">
			<?php echo $this->_render_quiz(); ?>
		</div>
	<?php endif; ?>
<?php return; endif; ?>

<?php if ($this->_is_parent($record) && $this->default->get('method', $this->params->get('params.activ_mode')) == 'manual'): ?>
	<a class="modal_<?php echo $this->id; ?> memodal-button" title="<?php echo JText::_('CSTEPAPROVEUSER'); ?>"
	   href="index.php?option=com_joomcck&view=users&layout=modal&tmpl=component&field=<?php echo $this->id; ?>_<?php echo $record->id; ?>"
	   rel="{handler: 'iframe', size: {x: 800, y: 500}}">
		<?php echo JText::_('CSTEPAPROVEUSER'); ?></a>

	<script>
		function jSelectUser_<?php echo $this->id; ?>_<?php echo $record->id; ?>(id, title) {
			jQuery.ajax({
				url:      "<?php echo JRoute::_('index.php?option=com_joomcck&task=ajax.field_call&tmpl=component'); ?>",
				type:     'post',
				dataType: 'json',
				data:     {
					field_id: <?php echo $this->id; ?>,
					func:    "_approveUser",
					record_id: <?php echo $record->id?>,
					user_id: id
				}
			}).done(function(json) {
					SqueezeBox.close();
					if(!json) {
						return;
					}
					if(!json.success) {
						alert(json.error);
						return;
					}
					alert('<?php echo JText::_('CSTEPUSERAPPROVED');?>')
				});
		}
	</script>
<?php endif; ?>