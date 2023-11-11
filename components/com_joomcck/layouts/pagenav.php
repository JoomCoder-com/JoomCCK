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
<tfoot>
<tr>
	<td colspan="20">
		<div class="float-end">
			<div class="btn-group">
				<select  name="directionTable" id="directionTable" class="input-medium select" onchange="Joomcck.orderTable('<?php echo $listOrder ?>')">
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
			<div class="btn-group">
				<select name="sortTable" id="sortTable" class="input-medium select" onchange="Joomcck.orderTable('<?php echo $listOrder ?>')">
					<option value=""><?php echo \Joomla\CMS\Language\Text::_('JGLOBAL_SORT_BY'); ?></option>
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $displayData->getSortFields(), 'value', 'text', $listOrder); ?>
				</select>
			</div>
			<?php echo str_replace(array('<option value="0">' . \Joomla\CMS\Language\Text::_('JALL') . '</option>', 'class="form-control'), array('', 'class="select'), $displayData->pagination->getLimitBox()); ?>
		</div>
		<div style="float-start">
			<small>
				<?php if($displayData->pagination->getPagesCounter()): ?>
					<?php echo $displayData->pagination->getPagesCounter(); ?> |
				<?php endif; ?>
				<?php echo $displayData->pagination->getResultsCounter(); ?>
			</small>
		</div>
		<?php if($displayData->pagination->getPagesLinks()): ?>
			<div class="clearfix"></div>
			<div style="text-align: center;" class="pagination">
				<?php echo str_replace('<ul>', '<ul class="pagination-list">', $displayData->pagination->getPagesLinks()); ?>
			</div>
			<div class="clearfix"></div>
		<?php endif; ?>
	</td>
</tr>
</tfoot>