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
$view = \Joomla\CMS\Factory::getApplication()->input->getCmd('view');
\Joomla\CMS\HTML\HTMLHelper::_('behavior.formvalidator');
\Joomla\CMS\HTML\HTMLHelper::_('behavior.keepalive');
\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.modal');
?>
<script type="text/javascript">

	Joomla.submitbutton = function(task) {
		if(task == 'section.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
		else {
			alert('<?php echo $this->escape(\Joomla\CMS\Language\Text::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<form action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString(); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<div class="float-end search-box">
		<div class="form-inline">
			<button type="button" class="btn btn-danger float-end" onclick="Joomla.submitbutton('<?php echo $view; ?>.cancel')">
				<?php echo \Joomla\CMS\Language\Text::_('CCANCEL'); ?>
			</button>
			<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('<?php echo $view; ?>.save')">
				<?php echo \Joomla\CMS\Language\Text::_('CSAVE'); ?>
			</button>
		</div>
	</div>
	<div class="page-header">
		<h1>
			<img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/components/com_joomcck/images/icons/fast.png">
			<?php echo \Joomla\CMS\Language\Text::_('CNEWSECTIONFAST'); ?>
		</h1>
	</div>

	<div class="tab-pane active" id="page-main">
		<div class="float-start" style="max-width: 500px; min-width:600px; margin-right: 20px;">
			<div class="control-group">
				<div class="form-label"><?php echo $this->form->getLabel('name'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
			</div>
			<div class="control-group">
				<div class="form-label"><?php echo $this->form->getLabel('menu'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('menu'); ?></div>
			</div>
			<div class="control-group">
				<div class="form-label"><?php echo $this->form->getLabel('type'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('type'); ?></div>
			</div>
		</div>
	</div>
	
	<input type="hidden" name="qs" value="1"/>
	<input type="hidden" name="task" value=""/>
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
</form>