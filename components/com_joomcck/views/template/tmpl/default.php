<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('bootstrap.tooltip');
/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task) {
		if(task == 'ctype.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		} else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JUri::getInstance()->toString(); ?>" method="post" name="adminForm" id="adminForm" class="form-horizontal">
	<?php echo HTMLFormatHelper::layout('item', $this); ?>
	<div class="page-header">
		<h1>
			<?php echo JText::_('CNEWSECTION'); ?>
		</h1>
	</div>

	<div class="label">Press Ctrl-Q to toggle Full Screen editing.</div>
	<div class=""><?php echo $this->form->getInput('source'); ?></div>
	<input type="hidden" name="task" value=""/>
	<?php echo $this->form->getInput('ident'); ?>
	<?php echo JHtml::_('form.token'); ?>
</form>