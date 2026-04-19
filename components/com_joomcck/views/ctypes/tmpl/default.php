<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');

$user   = Factory::getApplication()->getIdentity();
$userId = $user->get('id');

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm" class="cck-list-shell">

	<div class="cck-list-titlebar mb-4">
		<h2 class="cck-list-title">
			<img src="<?php echo Uri::root(true); ?>/components/com_joomcck/images/icons/types.png" alt="">
			<span><?php echo Text::_('COB_CONT_TYPES'); ?></span>
		</h2>
		<div class="cck-list-title-actions">
			<?php echo HTMLFormatHelper::layout('search', $this); ?>
			<?php echo Layout::render('admin.list.add', $this); ?>
		</div>
	</div>

	<div class="cck-list-action-bar">
		<?php echo HTMLFormatHelper::layout('items'); ?>
		<?php echo Layout::render('admin.list.ordering', $this); ?>
	</div>

	<?php echo HTMLFormatHelper::layout('filters', $this); ?>

	<div class="card cck-list-card">
		<div class="card-body">
			<table class="table table-hover align-middle mb-0" id="articlelist">
				<thead>
				<tr>
					<th class="cck-col-check">
						<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)"/>
					</th>
					<th class="w-1 text-center">
						<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('grid.sort', 'CNAME', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<th class="w-1 text-center">
						<?php echo Text::_('CFIELDS'); ?>
					</th>
					<th class="w-10 d-none d-md-table-cell">
						<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
					</th>
					<th class="w-1 text-end d-none d-md-table-cell">
						<?php echo HTMLHelper::_('grid.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($this->items as $i => $item):
					$canDelete  = $user->authorise('core.delete', 'com_joomcck.type.' . $item->id);
					$canEdit    = $user->authorise('core.edit', 'com_joomcck.type.' . $item->id);
					$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
					$canEditOwn = $user->authorise('core.edit.own', 'com_joomcck.type.' . $item->id) && $item->user_id == $userId;
					$canChange  = $user->authorise('core.edit.state', 'com_joomcck.type.' . $item->id) && $canCheckin;
					?>
					<tr>
						<td class="cck-col-check">
							<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="text-center">
							<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'ctypes.', $canChange); ?>
						</td>
						<td>
							<div class="d-flex align-items-center gap-2 flex-wrap">
								<?php if ($item->checked_out): ?>
									<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'ctypes.', $canCheckin); ?>
								<?php endif; ?>

								<?php if ($canEdit || $canEditOwn): ?>
									<a class="cck-item-title" href="<?php echo Route::_('index.php?option=com_joomcck&task=ctype.edit&id=' . (int) $item->id); ?>">
										<?php echo $this->escape($item->name); ?>
									</a>
								<?php else: ?>
									<span class="cck-item-title"><?php echo $this->escape($item->name); ?></span>
								<?php endif; ?>

								<?php echo Layout::render('admin.ctypes.recordParts.buttonsManage', ['current' => $this, 'item' => $item, 'i' => $i]); ?>
							</div>
						</td>
						<td class="text-center">
							<a class="text-decoration-none" href="<?php echo Route::_('index.php?option=com_joomcck&view=tfields&filter_type=' . $item->id); ?>">
								<?php echo Text::_('CFIELDS'); ?>
								<span class="badge ms-1 <?php echo $item->fieldnum ? 'bg-success' : 'bg-light text-dark border'; ?>"><?php echo $item->fieldnum; ?></span>
							</a>
						</td>
						<td class="d-none d-md-table-cell">
							<?php echo $item->language; ?>
						</td>
						<td class="text-end d-none d-md-table-cell">
							<span class="cck-id"><?php echo (int) $item->id; ?></span>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>

		<div class="card-footer">
			<?php echo Layout::render('admin.list.pagination', ['pagination' => $this->pagination]); ?>
		</div>
	</div>
</form>
