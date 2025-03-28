<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');

?>
<?php
$user	= \Joomla\CMS\Factory::getApplication()->getIdentity();
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
$alert = \Joomla\CMS\Language\Text::_('CMAKESELECTION');

/**
 *
 * @param int $s current status
 * @param array $t list of statuses
 * @param int $o order ID
 */
function status($s, $t, $o)
{
	?>
	<select name="status"  style="max-width:150px;" class="form-control" onchange="Joomcck.changeStatus(this, <?php echo $o->id?>)">
		<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $t, 'value', 'text', $s, true);?>
	</select>
	<div id="bar_<?php echo $o->id; ?>" style="max-width:150px; display:none;" class="progress progress-striped active">
		<div class="bar" style="width: 100%;"></div>
	</div>
	<?php
}
?>

<script type="text/javascript">
!function($)
{
	Joomcck.changeStatus = function (select, order)
	{
		var b = $('#bar_' + order);
		b.slideDown('quick', function(){
			$.ajax({
				url: '<?php echo \Joomla\CMS\Router\Route::_("index.php?option=com_joomcck&task=ajax.status&tmpl=component", FALSE); ?>',
				dataType: 'json',
				type: 'POST',
				data:{
					order_id:order,
					status:select.value
				},
			}).done(function(json) {
				if(!json)
				{
					return;
				}
				if(!json.success)
				{
					alert(json.error);
					return;
				}
				b.slideUp('quick');
			});
		});
	}
}(jQuery);
</script>


<div class="page-header"><h1>
	<?php if(!$this->all_sales):?>
		<?php echo \Joomla\CMS\Language\Text::_('CMYSALERORDERS')?>
	<?php else:?>
		<?php echo \Joomla\CMS\Language\Text::_('CALLSALERORDERS')?>
	<?php endif;?>

	<?php if($this->cur_section):?>
		- <?php echo $this->cur_section->name;?>
	<?php endif;?>
</h1>
</div>

<form action="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=elements&layout=saler&Itemid='.\Joomla\CMS\Factory::getApplication()->input->getInt('Itemid')); ?>" method="post" name="adminForm" id="sales-form">

	<div class="controls controls-row">
		<div class="input-append float-start">
			<input type="text" name="filter_search" size="16" id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>"/>
			<button class="btn" type="submit">
				<?php echo HTMLFormatHelper::icon('magnifier.png');  ?>
			</button>
			<?php if($this->state->get('filter.search')) :?>
			<button class="btn<?php echo ($this->state->get('filter.search') ? ' btn-warning' : NULL); ?>" type="button"
				onclick="Joomcck.setAndSubmit('filter_search', '');" rel="tooltip" data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('JSEARCH_FILTER_CLEAR'); ?>">
				<?php echo HTMLFormatHelper::icon('eraser.png');  ?>
			</button>
			<?php endif; ?>
			<button class="btn<?php if($this->state->get('filter.section') || $this->state->get('filter.status')) echo ' btn-warning'; ?>" type="button" data-toggle="collapse" data-target="#filters-block" >
				<?php echo HTMLFormatHelper::icon('funnel.png');  ?>
			</button>
		</div>
		<button class="btn float-end" onclick="window.location ='<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=elements&layout=addsale'); ?>';return false;">
			<?php echo HTMLFormatHelper::icon('plus.png');  ?>
			<?php echo \Joomla\CMS\Language\Text::_('CADDSALE'); ?>
		</button>
	</div>
	<div class="clearfix"> </div>

	<div class="collapse btn-toolbar" id="filters-block">
		<div class="well well-small">
			<select name="filter_section" style="max-width:150px;" onchange="this.form.submit()">
				<option value=""><?php echo \Joomla\CMS\Language\Text::_('CSELECTSECTION');?></option>
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->filter_sections, 'value', 'text', $this->state->get('filter.section'), true);?>
			</select>

			<select name="filter_status" style="max-width:150px;" onchange="this.form.submit()">
				<option value=""><?php echo \Joomla\CMS\Language\Text::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->statuses, 'value', 'text', $this->state->get('filter.status'), true);?>
			</select>
		</div>
	</div>
	<div class="clearfix"> </div>

	<table class="table table-hover">
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
			<th>
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'CNAME', 'o.name', $listDirn, $listOrder); ?>
			</th>
			<th nowrap="nowrap" width="1%">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'CAMOUNT', 'section', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" nowrap="nowrap">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'CCREATED', 'o.ctime', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" nowrap="nowrap">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'CSTATUS', 'o.status', $listDirn, $listOrder); ?>
			</th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($this->orders AS $i => $order):?>
			<tr class=" <?php echo $k = 1 - @$k?>">
				<td>
					<?php if($order->gateway == 'CMANUAL' || !$order->gateway):?>
						<a href="javascript:void(0)" onclick="if(confirm('<?php echo \Joomla\CMS\Language\Text::_('CCONFIRMDELET_1')?>')){window.location = '<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&task=sale.delete&id='.$order->id)?>'}">
							<?php echo HTMLFormatHelper::icon('cross.png');  ?></a>
					<?php endif;?>
					<button type="button" class="btn btn-sm btn-light border rounded" data-toggle="collapse" data-target="#field<?php echo $order->id?>"><i class="icon-arrow-down-3"></i></button>

					<a href="<?php echo Url::record($order->record_id)?>">
						<?php echo $order->name?></a>
					<a onclick="Joomcck.setAndSubmit('filter_search', 'pid:<?php echo $order->record_id;?>');" href="javascript:void(0);" rel="tooltip" data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CORDERSPRODFILTER')?>">
						<?php echo HTMLFormatHelper::icon('funnel-small.png');  ?></a>
					<?php echo CEventsHelper::showNum('record', $order->record_id);?>



					<div id="field<?php echo $order->id?>" class="field-data collapse">
						<br />
						<table class="table table-bordered table-condenced table-stripped">
							<?php if($order->saler_id != $userId):?>
								<tr>
									<td><?php echo \Joomla\CMS\Language\Text::_('CSELLER')?>:</td>
									<td><?php echo CCommunityHelper::getName($order->saler_id, $order->section_id)?>
										<a onclick="Joomcck.setAndSubmit('filter_search', 'sid:<?php echo $order->saler_id;?>');" href="javascript:void(0);" rel="tooltip" data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CORDERSSALERFILTER')?>">
											<?php echo HTMLFormatHelper::icon('funnel-small.png');  ?></a>
									</td>
								</tr>
							<?php endif;?>
							<tr>
								<td><?php echo \Joomla\CMS\Language\Text::_('CBUYER')?>:</td>
								<td><?php echo CCommunityHelper::getName($order->user_id, $order->section_id)?>
									<a onclick="Joomcck.setAndSubmit('filter_search', 'bid:<?php echo $order->user_id;?>');" href="javascript:void(0);" rel="tooltip" data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CORDERSBUYERFILTER')?>">
										<?php echo HTMLFormatHelper::icon('funnel-small.png');  ?></a>
								</td>
							</tr>
							<tr>
								<td><?php echo \Joomla\CMS\Language\Text::_('COID')?>:</td>
								<td><?php echo $order->id?></td>
							</tr>
							<tr>
								<td><?php echo \Joomla\CMS\Language\Text::_($order->gateway);?>:</td>
								<td><?php echo $order->gateway_id?></td>
							</tr>
							<tr>
								<td><?php echo \Joomla\CMS\Language\Text::_('CSECTION')?>:</td>
								<td><?php echo $order->section?></td>
							</tr>
							<?php if($order->comment):?>
								<tr>
									<td><?php echo \Joomla\CMS\Language\Text::_('CCOMMENT')?>:</td>
									<td><?php echo $order->comment?></td>
								</tr>
							<?php endif;?>
							<?php if($order->field):?>
								<tr valign="top">
									<td><?php echo \Joomla\CMS\Language\Text::_('CCONNTENT')?>:</td>
									<td><?php echo $order->field; ?></td>
								</tr>
							<?php endif;?>
						</table>
					</div>

				</td>

				<td nowrap="nowrap"><strong><?php echo $order->amount?> <?php echo $order->currency?></strong></td>
				<td><?php echo \Joomla\CMS\HTML\HTMLHelper::_('date', $order->ctime, 'Y/m/d');?></td>
				<td>
					<div><?php status($order->status, $this->stat, $order);?></div>
				</td>
			</tr>
		<?php endforeach;?>
		</tbody>

	</table>
	<div class="pagination float-end">
		<?php echo $this->pagination->getPagesCounter(); ?>
		<?php echo $this->pagination->getLimitBox();?>
	</div>

	<div class="float-start pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
</form>