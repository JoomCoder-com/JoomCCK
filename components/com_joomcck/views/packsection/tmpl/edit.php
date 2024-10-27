<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access'); ?>
<?php
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');

?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'packsection.cancel' || document.formvalidator.isValid(document.getElementById('item-form'))) {
			<?php //echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		}
		else {
			alert('<?php echo $this->escape(\Joomla\CMS\Language\Text::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}

	function changeSection(sid)
	{
		if(sid)
		{
			jQuery.ajax({
				url: '<?php echo \Joomla\CMS\Uri\Uri::base(TRUE);?>/index.php?option=com_joomcck&task=ajax.loadpacksection&tmpl=component',
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

<form action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString(); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<?php echo HTMLFormatHelper::layout('item', $this); ?>
	<div class="page-header">
		<h1>
			<?php echo empty($this->item->id) ? \Joomla\CMS\Language\Text::_('CNEWPACKSECTION') : \Joomla\CMS\Language\Text::_('CEDITPACKSECTION'); ?>
		</h1>
	</div>

	<div class="row">
		<div class="col-md-6 form-horizontal">
			<fieldset class="adminform">
				<legend><?php echo \Joomla\CMS\Language\Text::_('CSECTIONSETTINGS'); ?></legend>
				<div class="control-group">
					<div class="form-label inline">
						<?php echo $this->form->getLabel('id'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('id'); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="form-label inline">
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
		<div class="col-md-6 form-horizontal">
			<fieldset class="adminform">
				<legend><?php echo \Joomla\CMS\Language\Text::_('CTYPESETTINGS'); ?></legend>
				<div id="additional-form">
					<?php echo @$this->parameters?>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" id="jform_pack_id" name="jform[pack_id]" value="<?php echo $this->state->get('pack');?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="return" value="<?php echo $this->state->get('groups.return');?>" />
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
</form>