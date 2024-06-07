<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
$listOrder	= $displayData->escape($displayData->state->get('list.ordering'));
$listDirn	= $displayData->escape($displayData->state->get('list.direction'));
?>
<div class="clearfix"></div>
<div class="d-flex justify-content-end">
	<div class="me-2">
		<select  name="directionTable" id="directionTable" class="form-select" onchange="Joomcck.orderTable('<?php echo $listOrder ?>')">
			<option value=""><?php echo \Joomla\CMS\Language\Text::_('JFIELD_ORDERING_DESC'); ?></option>
			<option value="asc" <?php if($listDirn == 'asc')
			{
				echo 'selected="selected"';
			} ?>><?php echo \Joomla\CMS\Language\Text::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
			<option value="desc" <?php if($listDirn == 'desc')
			{
				echo 'selected="selected"';
			} ?>><?php echo \Joomla\CMS\Language\Text::_('JGLOBAL_ORDER_DESCENDING'); ?></option>
		</select>
	</div>
	<div>
		<select name="sortTable" id="sortTable" class="form-select" onchange="Joomcck.orderTable('<?php echo $listOrder ?>')">
			<option value=""><?php echo \Joomla\CMS\Language\Text::_('JGLOBAL_SORT_BY'); ?></option>
			<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $displayData->getSortFields(), 'value', 'text', $listOrder); ?>
		</select>
	</div>
</div>