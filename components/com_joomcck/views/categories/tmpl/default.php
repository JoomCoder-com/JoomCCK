<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

// Include the component HTML helpers.
\Joomla\CMS\HTML\HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip');
\Joomla\CMS\HTML\HTMLHelper::_('behavior.multiselect');

$user      = \Joomla\CMS\Factory::getApplication()->getIdentity();
$userId    = $user->get('id');
$section   = $this->section_id;
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$ordering  = ($listOrder == 'ordering');
$saveOrder = $listOrder == 'ordering';
$saveOrder = ($listOrder == 'ordering' && $listDirn == 'asc');
if($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_joomcck&task=usercategories.saveOrderAjax&tmpl=component';
	\Joomla\CMS\HTML\HTMLHelper::_('sortablelist.sortable', 'categoryList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, FALSE, TRUE);
}
?>
<style>
	<!--
	a.saveorder {
		width: 16px;
		height: 16px;
		display: block;
		overflow: hidden;
		background: url('<?php echo HTMLFormatHelper::icon('disk.png');  ?>') no-repeat;
		float: right;
		margin-right: 8px;
	}

	a.saveorder.inactive {
	/ / background-position : 0 - 16 px;
	}

	-->
</style>

<div class="page-header">
    <h1>
        <?php echo \Joomla\CMS\Language\Text::sprintf('CSECTIONCATS', $this->section->name); ?>
    </h1>
</div>


<form action="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=categories&section_id=' . $section); ?>" method="post" name="adminForm" id="adminForm">
	<div class="btn-toolbar clearfix">
		<div class="float-start">
			<button type="button" class="btn" onclick="location.href = '<?php echo \Joomla\CMS\Router\Route::_(Url::user('created', $user->get('id'), $this->section->id)); ?>'">
				<?php echo HTMLFormatHelper::icon('arrow-180.png'); ?>
				<?php echo \Joomla\CMS\Language\Text::_('CBACKTOSECTION'); ?>
			</button>
		</div>
		<div class="float-end">
			<button type="button" style="margin-right: 5px" class="btn btn-primary" onclick="Joomla.submitbutton('usercategory.add');">
				<?php echo HTMLFormatHelper::icon('plus-button.png'); ?>
				<?php echo \Joomla\CMS\Language\Text::_('CADDNEW'); ?>
			</button>

			<?php if(count($this->items)) : ?>
				<div class="btn-group" style="margin-right: 5px">
					<button type="button" class="btn" onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo \Joomla\CMS\Language\Text::_('CMAKESELECTION') ?>');}else{Joomla.submitbutton('usercategories.publish');}">
						<?php echo HTMLFormatHelper::icon('tick-button.png'); ?>
						<?php echo \Joomla\CMS\Language\Text::_('CPUB'); ?>
					</button>
					<button type="button" class="btn" onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo \Joomla\CMS\Language\Text::_('CMAKESELECTION') ?>');}else{Joomla.submitbutton('usercategories.unpublish')};">
						<?php echo HTMLFormatHelper::icon('minus-button.png'); ?>
						<?php echo \Joomla\CMS\Language\Text::_('CUNPUB'); ?>
					</button>
				</div>
				<button type="button" class="btn btn-danger"
						onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo \Joomla\CMS\Language\Text::_('CMAKESELECTION') ?>');}else{if(!confirm('<?php echo \Joomla\CMS\Language\Text::_('CSUREDEL') ?>')){return;}Joomla.submitbutton('usercategories.delete')};">
					<?php echo HTMLFormatHelper::icon('cross-button.png'); ?>
					<?php echo \Joomla\CMS\Language\Text::_('CDELETE'); ?>
				</button>
			<?php endif; ?>
		</div>
	</div>
	<?php if(!count($this->items)) : ?>
		<div class="alert">
			<?php echo \Joomla\CMS\Language\Text::_('CADDNEWCATEGORY'); ?>
		</div>
	<?php else: ?>
		<table class="table" id="categoryList">
			<thead>
			<th width="1%">
				<?php echo \Joomla\CMS\Language\Text::_('#'); ?>
			</th>
			<th width="1%" class="nowrap center hidden-phone">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', '<i class="icon-menu-2"></i>', 'ordering', $listDirn, $listOrder, NULL, 'asc', 'JGRID_HEADING_ORDERING'); ?>
			</th>
			<th width="1%"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo \Joomla\CMS\Language\Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/></th>
			<th width="5%">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
			</th>
			<th>
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JGLOBAL_TITLE', 'name', $listDirn, $listOrder); ?>
			</th>

			<th width="1%">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" nowrap="nowrap">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
			</th>
			</thead>
			<tbody>
			<?php
			$originalOrders = array();
			foreach($this->items as $i => $item):
				$orderkey   = $item->ordering;
				$canEdit    = TRUE; //$user->authorise('core.edit',			$section.'.usercategory.'.$item->id);
				$canCheckin = TRUE; //$user->authorise('core.admin', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
				$canEditOwn = TRUE; //$user->authorise('core.edit.own',		$section.'.usercategory.'.$item->id) && $item->user_id == $userId;
				$canChange  = TRUE; //$user->authorise('core.edit.state',	$section.'.usercategory.'.$item->id) && $canCheckin;
				?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $user->id; ?>" item-id="<?php echo $item->id ?>" parents="" level="">
					<td>
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td class="order nowrap center hidden-phone">
						<?php if($canChange) :
							$disableClassName = '';
							$disabledLabel    = '';
							if(!$saveOrder) :
								$disabledLabel    = \Joomla\CMS\Language\Text::_('JORDERINGDISABLED');
								$disableClassName = 'inactive tip-top';
							endif; ?>
							<span class="sortable-handler <?php echo $disableClassName ?>" title="<?php echo $disabledLabel ?>" rel="tooltip">
							<i class="icon-menu"></i>
						</span>

						<?php else : ?>
							<span class="sortable-handler inactive">
							<i class="icon-menu"></i>
						</span>
						<?php endif; ?>
						<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $orderkey; ?>"/>
					</td>
					<td class="center">
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="center">
						<?php echo \Joomla\CMS\HTML\HTMLHelper::_('jgrid.published', $item->published, $i, 'usercategories.', $canChange); ?>
					</td>
					<td>
						<?php if($canEdit || $canEditOwn) : ?>
							<a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=category&id=' . $item->id . '&section_id=' . $section); ?>">
								<?php echo $this->escape($item->name); ?></a>
						<?php else : ?>
							<?php echo $this->escape($item->name); ?>
						<?php endif; ?>
					</td>

					<td class="center">
						<?php echo $this->escape($item->access_level); ?>
					</td>
					<td class="center">
						<?php echo (int)$item->id; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<div class="pagination float-end">
			<?php echo $this->pagination->getPagesCounter(); ?>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>

		<div class="float-start pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php endif; ?>

	<input type="hidden" name="section_id" value="<?php echo $section; ?>"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="limitstart" value="0"/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir"
		   value="<?php echo $listDirn; ?>"/>
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>


</form>
