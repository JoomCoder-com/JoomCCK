<?php
/**
 * Secondary action bar for admin list views.
 *
 * Emits Edit / Publish / Unpublish / Delete plus per-view extras
 * (Reset, Mass, Manage Groups). The primary "Add" action lives in a
 * separate layout (admin.list.add) so templates can render it in the
 * card-header title bar.
 *
 * Backward compatibility: legacy templates that still call
 * HTMLFormatHelper::layout('items') will get only the secondary actions;
 * they must add a Layout::render('admin.list.add', $this) call separately
 * if they also want the Add button rendered by this layout.
 *
 * Also the single point of inclusion for admin-list.css — all list views
 * route through this layout, so enqueue once here.
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$cssPath = '/media/com_joomcck/css/admin-list.css';
$cssVer  = @filemtime(JPATH_ROOT . $cssPath) ?: 'auto';
Factory::getDocument()->addStyleSheet(Uri::root(true) . $cssPath, ['version' => (string) $cssVer]);

$view   = Factory::getApplication()->input->getCmd('view');
$single = preg_replace('/s$/iU', '', $view);

$showEdit        = !in_array($view, ['tags', 'votes', 'items'], true);
$showPublish     = !in_array($view, ['packs', 'packsections', 'tags', 'votes'], true);
?>

<div class="cck-list-primary-actions" role="group" aria-label="<?php echo htmlspecialchars(Text::_('CACTIONS', true), ENT_QUOTES, 'UTF-8'); ?>">

	<?php if ($showEdit || $showPublish): ?>
	<div class="btn-group btn-group-sm" role="group">
		<?php if ($showEdit): ?>
			<button type="button" class="btn btn-outline-secondary"
			        onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo Text::_('SELECTFIRST', true); ?>');}else{Joomla.submitbutton('<?php echo $single; ?>.edit');}">
				<i class="fas fa-pen" aria-hidden="true"></i>
				<span class="ms-1"><?php echo Text::_('CEDIT'); ?></span>
			</button>
		<?php endif; ?>

		<?php if ($showPublish): ?>
			<button type="button" class="btn btn-outline-secondary"
			        onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo Text::_('SELECTFIRST', true); ?>');}else{Joomla.submitbutton('<?php echo $view; ?>.publish');}">
				<i class="fas fa-check" aria-hidden="true"></i>
				<span class="ms-1"><?php echo Text::_('C_TOOLBAR_PUB'); ?></span>
			</button>
			<button type="button" class="btn btn-outline-secondary"
			        onclick="if (document.adminForm.boxchecked.value==0){alert('<?php echo Text::_('SELECTFIRST', true); ?>');}else{Joomla.submitbutton('<?php echo $view; ?>.unpublish');}">
				<i class="fas fa-ban" aria-hidden="true"></i>
				<span class="ms-1"><?php echo Text::_('C_TOOLBAR_UNPUB'); ?></span>
			</button>
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<div class="btn-group btn-group-sm" role="group">
		<button type="button" class="btn btn-outline-danger"
		        onclick="listButtonClick('<?php echo $view; ?>.delete')">
			<i class="fas fa-trash" aria-hidden="true"></i>
			<span class="ms-1"><?php echo Text::_('CDELETE'); ?></span>
		</button>
	</div>

	<?php if ($view === 'tfields'): ?>
		<a class="btn btn-sm btn-outline-secondary"
		   href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=groups&type_id=' . $displayData->state->get('filter.type')); ?>">
			<?php echo HTMLFormatHelper::icon('block.png'); ?>
			<span class="ms-1"><?php echo Text::_('CMANAGEGROUP'); ?></span>
		</a>
	<?php endif; ?>

	<?php if ($view === 'items'): ?>
		<div class="btn-group btn-group-sm" role="group">
			<button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" type="button" aria-expanded="false">
				<i class="fas fa-rotate-left" aria-hidden="true"></i>
				<span class="ms-1"><?php echo Text::_('CRESET'); ?></span>
			</button>
			<ul class="dropdown-menu">
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_hits')"><?php echo Text::_('C_TOOLBAR_RESET_HITS'); ?></a></li>
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_com')"><?php echo Text::_('C_TOOLBAR_RESET_COOMENT'); ?></a></li>
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_vote')"><?php echo Text::_('C_TOOLBAR_RESET_RATING'); ?></a></li>
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_fav')"><?php echo Text::_('C_TOOLBAR_RESET_FAVORIT'); ?></a></li>
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_ctime')"><?php echo Text::_('C_TOOLBAR_RESET_CTIME'); ?></a></li>
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_mtime')"><?php echo Text::_('C_TOOLBAR_RESET_MTIME'); ?></a></li>
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.reset_extime')"><?php echo Text::_('C_TOOLBAR_RESET_EXTIME'); ?></a></li>
			</ul>
		</div>
		<div class="btn-group btn-group-sm" role="group">
			<button id="massDropdownButton" type="button" class="btn btn-outline-secondary dropdown-toggle"
			        data-bs-toggle="dropdown" aria-expanded="false">
				<i class="fas fa-layer-group" aria-hidden="true"></i>
				<span class="ms-1"><?php echo Text::_('CMASS'); ?></span>
			</button>
			<ul class="dropdown-menu" aria-labelledby="massDropdownButton">
				<li><a class="dropdown-item" href="javascript:void(0);" onclick="listButtonClick('items.change_core');"><?php echo Text::_('C_TOOLBAR_MASSOP3'); ?></a></li>
			</ul>
		</div>
	<?php endif; ?>
</div>

<script type="text/javascript">
	if (typeof window.listButtonClick !== 'function') {
		window.listButtonClick = function (task) {
			if (document.adminForm.boxchecked.value == 0) {
				alert('<?php echo Text::_('C_MSG_SELECTITEM', true); ?>');
				return;
			}
			if (confirm('<?php echo Text::_('CSURE'); ?>')) {
				Joomla.submitbutton(task);
			}
		};
	}
</script>
