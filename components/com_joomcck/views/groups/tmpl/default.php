<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access'); ?>
<?php
$user	= JFactory::getUser();
$userId	= $user->get('id');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$canOrder	= true; //$user->authorise('core.edit.state', 'com_joomcck.groups');
$saveOrder	= $listOrder == 'g.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_joomcck&task=groups.ordersave&tmpl=component';
	JHtml::_('sortablelist.sortable', 'groupsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<a class="btn btn-primary" href="index.php?option=com_joomcck&view=tfields&filter_type=<?php echo $this->state->get('groups.type'); ?>">
	<?php echo HTMLFormatHelper::icon('arrow-180.png'); ?>
	<?php JText::printf('CBACKTOFIELD', $this->type->name);?>
</a>

<form action="<?php echo JUri::getInstance()->toString(); ?>" method="post" name="adminForm" id="adminForm">

	<div class="page-header">
		<h1>
			<img src="<?php echo JUri::root(TRUE); ?>/components/com_joomcck/images/icons/sections.png">
			<?php echo JText::sprintf('COB_FIELD_GROPMANAGER', $this->type->name); ?>
		</h1>
	</div>

	<?php echo HTMLFormatHelper::layout('items'); ?>

	<table class="table table-hover" id="groupsList">
		<thead>
			<th width="1%" class="nowrap center hidden-phone">
				<i class="icon-menu-2"></i>
			</th>
			<th width="1%">
				<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
			</th>
			<th class="title">
				<?php echo JText::_('CTITLE'); ?>
			</th>
			<th width="1%">
				<?php echo JText::_('ID'); ?>
			</th>
		</thead>
		<?php echo HTMLFormatHelper::layout('pagenav', $this); ?>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$ordering   = ($listOrder == 'g.ordering');
			$canCreate  = $user->authorise('core.create',     'com_joomcck.group.'.$item->id);
			$canEdit    = $user->authorise('core.edit',       'com_joomcck.group.'.$item->id);
			$canCheckin = $user->authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canChange = true;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="order nowrap center hidden-phone">
					<?php if ($canChange) :
						$disableClassName = '';
						$disabledLabel	  = '';

						if (!$saveOrder) :
							$disabledLabel    = JText::_('JORDERINGDISABLED');
							$disableClassName = 'inactive tip-top';
						endif; ?>
						<span class="sortable-handler <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>" rel="tooltip">
							<i class="icon-menu"></i>
						</span>
						<input type="text" style="display:none"  name="order[]" size="5" value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
					<?php else : ?>
						<span class="sortable-handler inactive" >
							<i class="icon-menu"></i>
						</span>
					<?php endif; ?>
				</td>
				<td width="1%">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td >
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'groups.', $canCheckin); ?>
					<?php endif; ?>

					<a href="<?php echo JRoute::_('index.php?option=com_joomcck&task=group.edit&id='.(int) $item->id);?>">
						<?php echo $item->title; ?>
					</a>

				</td>
				<td>
					<?php echo $item->id?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="type_id" value="<?php echo $this->state->get('groups.type');?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>