<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<form action="<?php echo Uri::getInstance()->toString(); ?>" method="post" name="adminForm" id="adminForm" class="cck-list-shell">

	<div class="cck-list-titlebar mb-4">
		<h2 class="cck-list-title">
			<img src="<?php echo Uri::root(true); ?>/components/com_joomcck/images/icons/sections.png" alt="">
			<span><?php echo Text::sprintf('CREDISPACKSECTIONS', $this->pack->name); ?></span>
		</h2>
		<div class="cck-list-title-actions">
			<a href="<?php echo Route::_('index.php?option=com_joomcck&view=packs'); ?>" class="btn btn-sm btn-outline-secondary">
				<i class="fas fa-arrow-left" aria-hidden="true"></i>
				<span class="ms-1"><?php echo Text::_('COB_BACKTOPACKER'); ?></span>
			</a>
			<?php echo Layout::render('admin.list.add', $this); ?>
		</div>
	</div>

	<div class="cck-list-action-bar">
		<?php echo HTMLFormatHelper::layout('items'); ?>
	</div>

	<div class="card cck-list-card">
		<div class="card-body">
			<table class="table table-hover align-middle mb-0">
				<thead>
				<tr>
					<th class="cck-col-check">
						<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)"/>
					</th>
					<th>
						<?php echo HTMLHelper::_('grid.sort', 'Name', 'name', $listDirn, $listOrder); ?>
					</th>
					<th class="w-1 text-end">
						<?php echo HTMLHelper::_('grid.sort', 'ID', 'id', $listDirn, $listOrder); ?>
					</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ($this->items as $i => $item): ?>
					<tr>
						<td class="cck-col-check">
							<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
						</td>
						<td>
							<a class="cck-item-title" href="<?php echo Route::_('index.php?option=com_joomcck&task=packsection.edit&id=' . $item->id); ?>"><?php echo $item->name; ?></a>
						</td>
						<td class="text-end">
							<span class="cck-id"><?php echo (int) $item->id; ?></span>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<input type="hidden" name="pack_id" value="<?php echo $this->state->get('pack'); ?>"/>
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
