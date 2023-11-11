<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;
return;
$options = array(
	\Joomla\CMS\HTML\HTMLHelper::_('select.option', 'c', \Joomla\CMS\Language\Text::_('JLIB_HTML_BATCH_COPY')),
	\Joomla\CMS\HTML\HTMLHelper::_('select.option', 'm', \Joomla\CMS\Language\Text::_('JLIB_HTML_BATCH_MOVE'))
);
$published	= $this->state->get('filter.published');
$section	= $this->escape($this->state->get('filter.section'));
?>
<fieldset class="batch">
	<legend><?php echo \Joomla\CMS\Language\Text::_('COM_JOOMCCK_BATCH_OPTIONS');?></legend>
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('batch.access');?>

	<?php if ($published >= 0) : ?>
		<label id="batch-choose-action-lbl" for="batch-category-id">
			<?php echo \Joomla\CMS\Language\Text::_('COM_JOOMCCK_BATCH_CATEGORY_LABEL'); ?>
		</label>
		<select name="batch[category_id]" class="form-control" id="batch-category-id">
			<option value=""><?php echo \Joomla\CMS\Language\Text::_('JSELECT') ?></option>
			<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('category.categories', 'com_joomcck', array('published' => $published)));?>
		</select>
		<?php echo \Joomla\CMS\HTML\HTMLHelper::_( 'select.radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'); ?>
	<?php endif; ?>

	<button type="submit" onclick="submitbutton('category.batch');">
		<?php echo \Joomla\CMS\Language\Text::_('JGLOBAL_BATCH_PROCESS'); ?>
	</button>
	<button type="button" onclick="document.getElementById('batch-category-id').value='';document.getElementById('batch-access').value=''">
		<?php echo \Joomla\CMS\Language\Text::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
</fieldset>