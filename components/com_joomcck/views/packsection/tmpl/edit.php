<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access'); ?>
<?php
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
JHtml::_('script', 'system/tabs.js', false, true);
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'packsection.cancel' || document.formvalidator.isValid(document.getElementById('item-form'))) {
			<?php //echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}

	function changeSection(sid)
	{
		if(sid)
		{
			jQuery.ajax({
				url: '<?php echo JUri::base(TRUE);?>/index.php?option=com_joomcck&task=ajax.loadpacksection&tmpl=component',
				context: jQuery("#additional-form"),
				dataType: 'html',
				data:{id: sid}
			}).done(function(html) {
				jQuery(this).html(html);
				Joomcck.redrawBS();
			});
		}
	}

</script>

<form action="<?php echo JUri::getInstance()->toString(); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<?php echo HTMLFormatHelper::layout('item', $this); ?>
	<div class="page-header">
		<h1>
			<?php echo empty($this->item->id) ? JText::_('CNEWPACKSECTION') : JText::_('CEDITPACKSECTION'); ?>
		</h1>
	</div>

	<div class="row">
		<div class="span6 form-horizontal">
			<fieldset class="adminform">
				<legend><?php echo JText::_('CSECTIONSETTINGS'); ?></legend>
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
						<?php echo $this->form->getLabel('section_id'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('section_id'); ?>
					</div>
				</div>
				<div class="clr"></div>
				<?php echo MFormHelper::renderGroup($this->form, array(), 'params') ?>
			</fieldset>
		</div>
		<div class="span6 form-horizontal">
			<fieldset class="adminform">
				<legend><?php echo JText::_('CTYPESETTINGS'); ?></legend>
				<div id="additional-form">
					<?php echo @$this->parameters?>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" id="jform_pack_id" name="jform[pack_id]" value="<?php echo $this->state->get('pack');?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="return" value="<?php echo $this->state->get('groups.return');?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>