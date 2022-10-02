<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<form action="<?php echo JUri::getInstance()->toString(); ?>" method="post" name="adminForm" id="adminForm">
	<div class="page-header">
		<h1>
			<img src="<?php echo JUri::root(TRUE); ?>/components/com_joomcck/images/icons/packs.png">
			<?php echo JText::_('CREDISPACK'); ?>
		</h1>
	</div>

	<?php echo HTMLFormatHelper::layout('items'); ?>

	<table class="table table-striped">
		<thead>
		<tr>
			<th width="1%"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)"/></th>
			<th class="title" class="nowrap center">
				<?php echo JHtml::_('grid.sort', 'CNAME', 'name', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" class="nowrap center">
				<?php echo JText::_('CSECTIONS'); ?>
			</th>
			<th width="5%" class="nowrap center">
				<?php echo JText::_('Key'); ?>
			</th>
			<th width="5%" class="nowrap center">
				<?php echo JHtml::_('grid.sort', 'CVERSION', 'version', $listDirn, $listOrder); ?>
			</th>
			<th width="5%" class="nowrap center">
				<?php echo JText::_('Build'); ?>
			</th>
			<th width="5%" class="nowrap center">
				<?php echo JText::_('Dowload'); ?>
			</th>
			<th width="1%" class="nowrap">
				<?php echo JHtml::_('grid.sort', 'ID', 'id', $listDirn, $listOrder); ?>
			</th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="12">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php foreach($this->items as $i => $item): ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="nowrap center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=com_joomcck&task=pack.edit&id=' . $item->id); ?>"><?php echo $item->name; ?></a>
				</td>
				<td nowrap="nowrap">
					<a href="<?php echo JRoute::_('index.php?option=com_joomcck&view=packsections&filter_pack=' . $item->id) ?>">
						<?php echo JText::_('CSECTIONS'); ?></a>
					<span class="badgebg-success"><?php echo $item->secnum ?></span>
				</td>
				<td class="nowrap center">
					<?php echo $item->key; ?>
				</td>
				<td nowrap="nowrap">
					<span class="badgebg-success">9.<?php echo $item->version; ?></span>
				</td>
				<td align="center">
					<a class="btn btn-primary btn-mini" href="<?php echo JRoute::_('index.php?option=com_joomcck&task=pack.build&pack_id=' . $item->id) ?>"><?php echo JText::_('CBUILD') ?></a><br/>
				</td>
				<td nowrap="nowrap">
					<small>
						<?php echo $item->download; ?>
						<br/><?php echo JText::_('Size'); ?>: <span class="badgebg-info"><?php echo $item->size; ?></span>
						<br/><?php echo JText::_('CBTIME'); ?>: <?php echo $item->btime != '0000-00-00 00:00:00' ? JDate::getInstance($item->btime) : JText::_('CNEVER'); ?>
					</small>
				</td>
				<td class="center">
					<?php echo (int)$item->id; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>