<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$links = $this->pagination->getPagesLinks();
?>
<h1><?php echo $this->title;?></h1>

<style>
a.button {
    background: url("../images/nature/arrow1.gif") no-repeat scroll left top #FFFFFF;
    border: 1px solid #DDDDDD;
    color: #444444;
	cursor: pointer;
    font-family: arial;
    font-weight: bold;
    line-height: 1.2em;
    padding: 3px 5px 3px 7px;
}
</style>

<form action="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=auditlog&Itemid='.\Joomla\CMS\Factory::getApplication()->input->getInt('Itemid')); ?>" method="post" name="adminForm" id="sales-form">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<a href="<?php echo JURI::root(TRUE)?>/index.php?option=com_joomcck&view=auditlog&Itemid=<?php echo \Joomla\CMS\Factory::getApplication()->input->getInt('Itemid');?>" class="button">
			<?php echo \Joomla\CMS\Language\Text::_('CBACKTOAUDITLOG');?></a>
		</div>

		<div class="filter-select fltrgt">
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="category">
		<thead>
			<tr>
				<!--
				<th width="1%"><input type="checkbox" name="checkall-toggle" value=""
					onclick="checkAll(this)" /></th>
				</th>
				<th width="1%">
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'ID', 'o.id', $listDirn, $listOrder); ?>
				</th>
				 -->
				<th width="30%">
					<?php echo \Joomla\CMS\Language\Text::_('CEVENT');; ?>
				</th>
				<th nowrap="nowrap" width="1%">
					<?php echo \Joomla\CMS\Language\Text::_('CCREATED');; ?>
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo \Joomla\CMS\Language\Text::_('CEVENTER');; ?>
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo \Joomla\CMS\Language\Text::_('CACTIONS'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items AS $i => $item):?>
				<tr class=" <?php echo $k = 1 - @$k?>">
					<td><?php echo \Joomla\CMS\Language\Text::_($this->type_objects[$item->type_id]->params->get('audit.al'.$item->event.'.msg', 'CAUDLOG'.$item->event));?></td>
					<td nowrap><?php echo $item->date;?></td>
					<td><?php echo $item->user;?></td>
					<td><?php echo $item->actions;?></td>
				</tr>
			<?php endforeach;?>
		</tbody>
		<?php if($links):?>
			<tfoot>
				<tr>
					<td colspan="10">
						<div class="pagination">
							<p class="counter">
								<?php echo $this->pagination->getPagesCounter(); ?>
							</p>
							<?php echo $links; ?>

						</div>
					</td>
				</tr>
			</tfoot>
		<?php endif;?>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
</form>
