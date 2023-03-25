<?php
    /**
     * Joomcck by joomcoder
     * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
     * Author Website: https://www.joomcoder.com/
     *
     * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
     * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
     */

defined('_JEXEC') || die('Restricted access');
$view = JFactory::getApplication()->input->getCmd('view');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.modal');
?>
<script type="text/javascript">

	Joomla.submitbutton = function(task) {
		if(task == 'section.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<form action="<?php echo JUri::getInstance()->toString(); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<div class="float-end search-box">
		<div class="form-inline">
			<button type="button" class="btn btn-danger float-end" onclick="Joomla.submitbutton('<?php echo $view; ?>.cancel')">
				<?php echo JText::_('CCANCEL'); ?>
			</button>
			<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('<?php echo $view; ?>.save')">
				<?php echo JText::_('CSAVE'); ?>
			</button>
		</div>
	</div>
	<div class="page-header">
		<h1>
			<img src="<?php echo JUri::root(true); ?>/components/com_joomcck/images/icons/fast.png">
			<?php echo JText::_('CNEWSECTIONFAST'); ?>
		</h1>
	</div>

	<div class="tab-pane active" id="page-main">
		<div class="float-start" style="max-width: 500px; min-width:600px; margin-right: 20px;">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('menu'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('menu'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('type'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('type'); ?></div>
			</div>
		</div>
	</div>
	
	<input type="hidden" name="qs" value="1"/>
	<input type="hidden" name="task" value=""/>
	<?php echo JHtml::_('form.token'); ?>
</form>