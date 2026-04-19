<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
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

$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$hasSection = (bool) $this->state->get('filter.section', false);

HTMLHelper::_('dropdown.init');
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<form action="<?php echo Route::_('index.php?option=com_joomcck&view=moderators'); ?>" method="post" id="adminForm" name="adminForm" class="cck-list-shell">

	<div class="cck-list-titlebar mb-4">
		<h2 class="cck-list-title">
			<img src="<?php echo Uri::root(true); ?>/components/com_joomcck/images/icons/moders.png" alt="">
			<span><?php echo Text::_('CMODERLIST'); ?></span>
		</h2>
		<div class="cck-list-title-actions">
			<?php echo HTMLFormatHelper::layout('search', $this); ?>
			<?php if ($hasSection): ?>
				<?php echo Layout::render('admin.list.add', $this); ?>
			<?php endif; ?>
		</div>
	</div>

	<?php if ($hasSection): ?>
		<div class="cck-list-action-bar">
			<?php echo HTMLFormatHelper::layout('items'); ?>
		</div>
	<?php endif; ?>

	<?php echo HTMLFormatHelper::layout('filters', $this); ?>

	<div class="card cck-list-card">

		<div class="card-body">
			<?php if ($hasSection): ?>
				<?php if (count($this->items) > 0): ?>
					<table class="table table-hover align-middle mb-0">
						<thead>
						<tr>
							<th class="w-1 text-center"><?php echo Text::_('#'); ?></th>
							<th class="cck-col-check">
								<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)"/>
							</th>
							<th class="w-1"></th>
							<th>
								<?php echo HTMLHelper::_('grid.sort', 'User', 'u.username', $listDirn, $listOrder); ?>
							</th>
							<th class="w-10 d-none d-md-table-cell">
								<?php echo HTMLHelper::_('grid.sort', 'Date', 'm.ctime', $listDirn, $listOrder); ?>
							</th>
							<th class="w-1 text-center">
								<?php echo HTMLHelper::_('grid.sort', 'State', 'm.published', $listDirn, $listOrder); ?>
							</th>
							<th class="w-1 text-end d-none d-md-table-cell">
								<?php echo HTMLHelper::_('grid.sort', 'ID', 'm.id', $listDirn, $listOrder); ?>
							</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($this->items as $i => $item):
							$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
							?>
							<tr>
								<td class="text-center text-muted small">
									<?php echo $this->pagination->getRowOffset($i); ?>
								</td>
								<td class="cck-col-check">
									<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
								</td>
								<td>
									<img src="<?php echo CCommunityHelper::getAvatar($item->user_id, 28, 28); ?>" class="rounded-circle" width="28" height="28" alt="">
								</td>
								<td>
									<div class="d-flex align-items-center gap-2 flex-wrap">
										<?php if ($item->checked_out): ?>
											<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'moderators.', $canCheckin); ?>
										<?php endif; ?>
										<a class="cck-item-title" href="<?php echo Route::_('index.php?option=com_joomcck&task=moderator.edit&id=' . (int) $item->id); ?>">
											<?php echo CCommunityHelper::getName($item->user_id, $this->section_model->getItem($item->section_id), ['nohtml' => 1]); ?>
										</a>
										<?php if ($item->icon && $item->icon != -1): ?>
											<img src="<?php echo Uri::root(true); ?>/components/com_joomcck/images/moderator/<?php echo $item->icon; ?>" alt=""/>
										<?php endif; ?>
									</div>
									<?php if ($item->description): ?>
										<div class="small text-muted mt-1"><?php echo $item->description; ?></div>
									<?php endif; ?>
								</td>
								<td class="text-center d-none d-md-table-cell">
									<small class="text-muted"><?php echo HTMLHelper::_('date', $item->ctime, 'd M Y'); ?></small>
								</td>
								<td class="text-center">
									<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'moderators.', true); ?>
								</td>
								<td class="text-end d-none d-md-table-cell">
									<span class="cck-id"><?php echo (int) $item->id; ?></span>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php else: ?>
					<div class="alert alert-info m-3 mb-0">
						<?php echo Text::_('CADDMODER'); ?>
					</div>
				<?php endif; ?>
			<?php else: ?>
				<div class="alert alert-info m-3 mb-0">
					<?php echo Text::_('CPLEASESELECTSECTION'); ?>
				</div>
			<?php endif; ?>

			<input type="hidden" name="section_id" value="<?php echo $this->state->get('filter.section'); ?>"/>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="limitstart" value="0"/>
			<input type="hidden" name="boxchecked" value="0"/>
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>

		<?php if ($hasSection && count($this->items) > 0): ?>
			<div class="card-footer">
				<?php echo Layout::render('admin.list.pagination', ['pagination' => $this->pagination]); ?>
			</div>
		<?php endif; ?>
	</div>
</form>
