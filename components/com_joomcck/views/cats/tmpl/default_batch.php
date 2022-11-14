<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;
return;
$options = array(
	JHtml::_('select.option', 'c', JText::_('JLIB_HTML_BATCH_COPY')),
	JHtml::_('select.option', 'm', JText::_('JLIB_HTML_BATCH_MOVE'))
);
$published	= $this->state->get('filter.published');
$section	= $this->escape($this->state->get('filter.section'));
?>
<fieldset class="batch">
	<legend><?php echo JText::_('COM_JOOMCCK_BATCH_OPTIONS');?></legend>
	<?php echo JHtml::_('batch.access');?>

	<?php if ($published >= 0) : ?>
		<label id="batch-choose-action-lbl" for="batch-category-id">
			<?php echo JText::_('COM_JOOMCCK_BATCH_CATEGORY_LABEL'); ?>
		</label>
		<select name="batch[category_id]" class="form-control" id="batch-category-id">
			<option value=""><?php echo JText::_('JSELECT') ?></option>
			<?php echo JHtml::_('select.options', JHtml::_('category.categories', 'com_joomcck', array('published' => $published)));?>
		</select>
		<?php echo JHtml::_( 'select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'); ?>
	<?php endif; ?>

	<button type="submit" onclick="submitbutton('category.batch');">
		<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
	</button>
	<button type="button" onclick="document.getElementById('batch-category-id').value='';document.getElementById('batch-access').value=''">
		<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
</fieldset>