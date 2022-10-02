<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
$app = JFactory::getApplication();
?>

<?php if($this->close): ?>
	<script type="text/javascript">
		window.parent.SqueezeBox.close();
	</script>
<?php endif; ?>


<form method="post" name="adminForm" id="adminForm" class="form-horizontal">

	<?php if($app->input->get('tmpl') == 'component'): ?>
		<style type="text/css">
			body.component {
				padding-top: 60px;
			}
			.content-form {
				padding: 20px;
			}
		</style>
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner">
				<div class="float-end">
					<button class="btn" type="button" onclick="javascript:Joomcck.submitTask('templates.apply')"><i class="icon-edit"></i> <?php echo JText::_('CSAVE') ?></button>
					<button class="btn" type="button" onclick="javascript:Joomla.submitbutton('templates.saveclose')"><i class="icon-save"></i> <?php echo JText::_('CSAVECLOSE') ?></button>
					<button class="btn" type="button" onclick="<?php echo(!JFactory::getApplication()->input->get('inner') ? 'parent.SqueezeBox.close();' : "javascript:Joomla.submitbutton('templates.cancel')"); ?>"><i
							class="icon-cancel "></i> <?php echo JText::_('CCLOSE') ?></a></button>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="content-form" id="page-params">
		<div class="row-fluid">
			<?php echo MFormHelper::renderForm($this->form, $this->params->toArray(), array(), MFormHelper::FIELDSET_SEPARATOR_FIELDSET, MFormHelper::STYLE_TABLE, MFormHelper::GROUP_SEPARATOR_TAB);; ?>
		</div>
	</div>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="name" value="<?php echo $this->name; ?>"/>
	<input type="hidden" name="type" value="<?php echo $this->type; ?>"/>
	<input type="hidden" name="config" value="<?php echo $this->config; ?>"/>
	<?php if($app->input->get('inner')) : ?>
		<input type="hidden" name="inner" value="1">
		<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->get('return', base64_encode($_SERVER['HTTP_REFERER'])) ?>"/>
	<?php endif; ?>
	<?php echo JHtml::_('form.token'); ?>
	<script type="text/javascript">
		Joomcck.redrawBS();
	</script>
</form>
