<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
?>

<?php
$user	= JFactory::getUser();
$userId	= $user->get('id');
$colors = array(
	1 => '721111',
	2 => 'ff0000',
	3 => '121896',
	4 => 'e59112',
	5 => '0c8c0e',
);
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$alert = JText::_('CMAKESELECTION');
$links = $this->pagination->getPagesLinks();
?>


<div class="page-header"><h1><?php echo JText::_('CMYORDERHIST')?></h1></div>

<form action="<?php echo JRoute::_('index.php?option=com_joomcck&view=elements&layout=buyer'); ?>" method="post" name="adminForm">

	<div class="controls controls-row">
		<div class="input-append">
			<input class="span3" type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>"/>
			<button class="btn" type="submit" rel="tooltip" data-original-title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
				<?php echo HTMLFormatHelper::icon('magnifier.png');  ?>
			</button>
			<?php if($this->state->get('filter.search')) :?>
			<button class="btn<?php echo ($this->state->get('filter.search') ? ' btn-warning' : NULL); ?>" type="button"
				onclick="Joomcck.setAndSubmit('filter_search', '');" rel="tooltip" data-original-title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>">
				<?php echo HTMLFormatHelper::icon('eraser.png');  ?>
			</button>
			<?php endif; ?>
			<button class="btn<?php if($this->state->get('filter.section') || $this->state->get('filter.status')) echo ' btn-warning'; ?>" type="button" data-toggle="collapse" data-target="#filters-block" >
				<?php echo HTMLFormatHelper::icon('funnel.png');  ?>
			</button>
		</div>
	</div>
	<div class="clearfix"> </div>

	<div class="collapse btn-toolbar" id="filters-block">
		<div class="well well-small">
			<select name="filter_status" style="max-width:150px;" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', $this->statuses, 'value', 'text', $this->state->get('filter.status'), true);?>
			</select>

			<?php if(count($this->filter_sections) > 1):?>
				<select name="filter_section" style="max-width:150px;" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('CSELECTSECTION');?></option>
					<?php echo JHtml::_('select.options', $this->filter_sections, 'value', 'text', $this->state->get('filter.section'), true);?>
				</select>
			<?php endif;?>
		</div>
	</div>
	<div class="clearfix"> </div>

	<table class="table table-striped">
		<thead>
			<!--
			<th width="1%"><input type="checkbox" name="checkall-toggle" value=""
				onclick="checkAll(this)" /></th>
			</th>
			<th width="1%">
				<?php echo JHtml::_('grid.sort',  'ID', 'o.id', $listDirn, $listOrder); ?>
			</th>
			 -->
			<th>
				<?php echo JHtml::_('grid.sort',  'CONAME', 'o.name', $listDirn, $listOrder); ?>
			</th>
			<th nowrap="nowrap" width="1%">
				<?php echo JHtml::_('grid.sort',  'CAMOUNT', 'section', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" nowrap="nowrap">
				<?php echo JHtml::_('grid.sort',  'CCREATED', 'o.ctime', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" nowrap="nowrap">
				<?php echo JHtml::_('grid.sort',  'CMODIFIED', 'o.mtime', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" nowrap="nowrap">
				<?php echo JHtml::_('grid.sort',  'CSTATUS', 'o.status', $listDirn, $listOrder); ?>
			</th>
		</thead>
		<tbody>
		<?php foreach ($this->orders AS $i => $order):?>
			<tr class=" <?php echo $k = 1 - @$k?>">
				<td>
					<span>
						<?php if($order->field):?>
							<button type="button" class="btn btn-micro" data-toggle="collapse" data-target="#field<?php echo $order->id?>"><i class="icon-arrow-down-3"></i></button>
						<?php endif;?>

						<a href="<?php echo Url::record($order->record_id)?>">
							<?php echo $order->name?></a>

						<?php echo CEventsHelper::showNum('record', $order->record_id);?>
					</span>
					<div class="clearfix"></div>


					<small>
						<span><?php echo JText::_('ID')?>: <?php echo $order->id?> | </span>
						<span><?php echo $order->gateway;?>: <?php echo $order->gateway_id?> | </span>
						<span><?php echo JText::_('CSECTION')?>: <?php echo $order->section?> </span>
					</small>

					<?php if($order->comment):?>
						<p><?php echo $order->comment;?></p>
					<?php endif;?>

					<?php if($order->field):?>
						<div  class="field-data collapse" id="field<?php echo $order->id?>"><br><?php echo $order->field;?></div>
					<?php endif;?>
				</td>

				<td nowrap="nowrap"><strong><?php echo $order->amount?> <?php echo $order->currency?></strong></td>
				<td><?php echo JHtml::_('date', $order->ctime, 'Y/m/d');?></td>
				<td><?php echo JHtml::_('date', $order->mtime, 'Y/m/d');?></td>
				<td style="color:#<?php echo $colors[$order->status] ?>"><?php echo $this->stat[$order->status];?></td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>

	<div class="pagination pull-right">
		<?php echo $this->pagination->getPagesCounter(); ?>
		<?php echo $this->pagination->getLimitBox();?>
	</div>

	<div class="pull-left pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
