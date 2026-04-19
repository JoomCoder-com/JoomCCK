<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Html\Helpers\Dropdown;
use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');

$user   = Factory::getApplication()->getIdentity();
$userId = $user->get('id');
HTMLHelper::_('dropdown.init');
HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');
$listOrder = $this->state->get('list.ordering', 'a.id');
$listDirn  = $this->state->get('list.direction', 'desc');
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm" class="cck-list-shell">

	<div class="cck-list-titlebar mb-4">
		<h2 class="cck-list-title">
			<img src="<?php echo Uri::root(true); ?>/components/com_joomcck/images/icons/sections.png" alt="">
			<span><?php echo Text::_('COB_SECTIONMANAGER'); ?></span>
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
			<table class="table table-hover align-middle mb-0">
				<thead>
				<tr>
					<th class="cck-col-check">
						<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/>
					</th>
					<th class="w-1 text-center">
						<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
					</th>
					<th>
						<?php echo HTMLHelper::_('grid.sort', 'CSECTIONNAME', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<th class="w-10 d-none d-md-table-cell">
						<?php echo Text::_('CCATEGORIES'); ?>
					</th>
					<th class="w-5 text-center d-none d-md-table-cell">
						<?php echo Text::_('CRECORDS'); ?>
					</th>
					<th class="w-10 d-none d-lg-table-cell">
						<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
					</th>
					<th class="w-1 text-end d-none d-md-table-cell">
						<?php echo HTMLHelper::_('grid.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
				</thead>

				<tbody>
				<?php foreach ($this->items as $i => $item):
					$canCheckin   = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
					$item->params = new \Joomla\Registry\Registry($item->params);
					?>
					<tr>
						<td class="cck-col-check">
							<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="text-center">
							<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'sections.', true); ?>
						</td>
						<td>
							<div class="d-flex align-items-center gap-2 flex-wrap">
								<?php if ($item->checked_out): ?>
									<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'sections.', $canCheckin); ?>
								<?php endif; ?>
								<a class="cck-item-title" href="<?php echo Route::_('index.php?option=com_joomcck&task=section.edit&id=' . (int) $item->id); ?>">
									<?php echo $this->escape($item->name); ?>
								</a>
								<?php
								Dropdown::edit($item->id, 'section.');

								Dropdown::addCustomItem(
									'<i class="fas fa-trash text-danger"></i> ' . Text::_('C_TOOLBAR_DELETE'), 'javascript:void(0)',
									'onclick="if(!confirm(\'' . Text::_('C_TOOLBAR_CONFIRMDELET') . '\')){return;}Joomla.listItemTask(\'cb' . $i . '\',\'sections.delete\')"'
								);

								if ($item->published):
									Dropdown::unpublish('cb' . $i, 'sections.');
								else:
									Dropdown::publish('cb' . $i, 'sections.');
								endif;

								if ($item->checked_out):
									Dropdown::divider();
									Dropdown::checkin('cb' . $i, 'sections.');
								endif;

								Dropdown::divider();
								Dropdown::addCustomItem('<i class="fas fa-eye"></i> ' . Text::_('C_OPENSECTION'), Route::_(Url::records($item)));
								Dropdown::divider();
								Dropdown::addCustomItem(
									'<i class="fas fa-folder-open"></i> ' . Text::_('C_MANAGE_CATS') . ' <span class="badge ms-1' . ($item->categories ? ' bg-success' : ' bg-light text-dark border') . '">' . $item->categories . '</span>',
									Route::_('index.php?option=com_joomcck&view=cats&section_id=' . $item->id)
								);

								echo Dropdown::render();
								?>
							</div>
						</td>
						<td class="d-none d-md-table-cell">
							<a class="text-decoration-none" rel="tooltip" data-bs-toggle="tooltip"
							   title="<?php echo Text::_('CCATEGOY_MANAGE'); ?>"
							   href="<?php echo Route::_('index.php?option=com_joomcck&view=cats&section_id=' . $item->id); ?>">
								<?php echo Text::_('CCATEGORIES'); ?>
								<span class="badge ms-1 <?php echo $item->fieldnum ? 'bg-success' : 'bg-light text-dark border'; ?>"><?php echo $item->fieldnum; ?></span>
							</a>
						</td>
						<td class="text-center d-none d-md-table-cell">
							<span class="badge <?php echo $item->records ? 'bg-info' : 'bg-light text-dark border'; ?>"><?php echo $item->records; ?></span>
						</td>
						<td class="d-none d-lg-table-cell">
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
			<input type="hidden" name="id" value="0"/>
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
