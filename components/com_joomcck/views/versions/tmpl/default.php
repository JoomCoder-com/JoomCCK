<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
$user = JFactory::getUser();
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$links = $this->pagination->getPagesLinks();
$back = NULL;
if(JFactory::getApplication()->input->getString('return'))
{
	$back = Url::get_back('return');;
}
if(!$back)
{
	$back = Url::record($this->record);
}
?>
<h1><?php echo JText::_('CAUDITVERSIONS')?> : <?php echo $this->record->title;?> v.<?php echo $this->record->version;?></h1>
<form action="" method="post" name="adminForm" id="adminForm">

	<div class="btn-toolbar">

		<div class="btn-group float-end">
			<button type="button" class="btn" onclick="location.href = '<?php echo $back;?>'">
				<?php echo HTMLFormatHelper::icon('arrow-180.png');  ?>
				<?php echo JText::_('CGOBACK'); ?>
			</button>
			<button class="btn" onclick="Joomla.submitbutton('versions.delete')" type="button">
				<?php echo HTMLFormatHelper::icon('cross-button.png');  ?>
				<?php echo JText::_('CDELETE')?>
			</button>
		</div>
	</div>
	<div class="clearfix"></div>

	<table class="table table-striped">
		<thead>
			<tr>
				<th width="1%">#</th>
				<th width="1%"><input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this);" /></th>
				<th>
					<?php echo JHtml::_('grid.sort',  'V', 'av.version', $listDirn, $listOrder); ?>
				</th>
				<th nowrap="nowrap" width="1%">
					<?php echo JHtml::_('grid.sort',  'CCREATED', 'av.ctime', $listDirn, $listOrder); ?>
				</th>
				<th nowrap="nowrap" width="1%">
					<?php echo JHtml::_('grid.sort',  'CUSER', 'av.username', $listDirn, $listOrder); ?>
				</th>
				<th width="1%"><?php echo JText::_('CACTIONS'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items AS $i => $item):?>
				<tr class="row<?php echo $k = 1 - @$k?>">
					<td><?php echo $this->pagination->getRowOffset($i); ?></td>
					<td class="center"><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
					<td>v.<?php echo $item->version;?></td>
					<td nowrap><?php echo $item->date;?></td>

					<td nowrap>
						<?php echo $item->username;?>
					</td>
					<td>
						<a rel="tooltip" data-original-title="Compare v.<?php echo $item->version;?>" href="<?php echo $url = JRoute::_('index.php?option=com_joomcck&view=diff&record_id=' . $item->record_id . '&version=' .$item->version.'&return=' . Url::back()); ?>">
							<?php echo trim(HTMLFormatHelper::icon('edit-diff.png'));?></a>
						<a rel="tooltip" data-original-title="Rollback v.<?php echo $item->version;?>" href="<?php echo Url::task('records.rollback', $item->record_id.'&version='.$item->version); ?>">
							<?php echo trim(HTMLFormatHelper::icon('arrow-merge-180-left.png'));?></a>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>
		<?php if($links):?>
			<tfoot>
				<tr>
					<td colspan="6">
						<div class="pagination">
							<p class="counter">
								<?php echo $this->pagination->getPagesCounter(); ?>
								<?php echo $this->pagination->getLimitBox(); ?>
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
	<input type="hidden" name="record_id" value="<?php echo JFactory::getApplication()->input->getInt('record_id'); ?>" />
	<input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->getBase64('return'); ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
