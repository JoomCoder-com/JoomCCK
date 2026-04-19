<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die('Restricted access');

$user   = Factory::getApplication()->getIdentity();
$userId = $user->get('id');

HTMLHelper::_('dropdown.init');
HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<form action="<?php echo $this->action; ?>" method="post" name="adminForm" id="adminForm" class="cck-list-shell">

	<div class="cck-list-titlebar mb-4">
		<h2 class="cck-list-title">
			<i class="fas fa-shapes text-muted"></i>
			<span><?php echo Text::_('COB_APPS_MANAGER'); ?></span>
		</h2>
		<div class="cck-list-title-actions">
			<?php echo HTMLFormatHelper::layout('search', $this); ?>
		</div>
	</div>

	<?php echo HTMLFormatHelper::layout('filters', $this); ?>

	<div class="row g-3 mt-1">
		<?php foreach ($this->items as $i => $item):
			$canCheckin   = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
			$item->params = new \Joomla\Registry\Registry($item->params);
			?>
			<div class="col-md-4 col-lg-3">
				<div class="card cck-list-card h-100">
					<div class="card-header d-flex align-items-center gap-2">
						<a class="cck-item-title flex-grow-1 text-decoration-none" href="<?php echo Route::_('index.php?option=com_joomcck&view=app&id=' . (int) $item->id); ?>">
							<?php echo $this->escape($item->name); ?>
						</a>
					</div>
					<div class="card-body">
						<div class="d-flex flex-wrap gap-2">
							<span rel="tooltip" data-bs-toggle="tooltip" data-bs-original-title="<?php echo Text::_('CRECORDS'); ?>"
							      class="badge <?php echo $item->records ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-light text-dark border'; ?>">
								<i class="fas fa-file me-1"></i><?php echo $item->records; ?>
							</span>
							<span rel="tooltip" data-bs-toggle="tooltip" data-bs-original-title="<?php echo Text::_('CCATEGORIES'); ?>"
							      class="badge <?php echo $item->categories ? 'bg-success-subtle text-success-emphasis border border-success-subtle' : 'bg-light text-dark border'; ?>">
								<i class="fas fa-folder me-1"></i><?php echo $item->categories; ?>
							</span>
						</div>
					</div>
					<div class="card-footer d-flex align-items-center gap-2">
						<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'sections.', true); ?>
						<?php if ($item->checked_out): ?>
							<?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'sections.', $canCheckin); ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
