<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Single Record Management Buttons Layout
 *
 * Tailwind CSS flex + DaisyUI dropdown replacement for Bootstrap btn-group.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die();

extract($displayData);

$item = $current->item;
$params = $current->tmpl_params['record'];

$containerClass = !isset($containerClass) ? 'ml-auto controls' : $containerClass;

?>

<?php if (!$current->print): ?>
	<div class="<?php echo $containerClass ?>">
		<div class="flex items-center gap-1">
			<?php if ($params->get('tmpl_core.item_print')): ?>
				<a class="bg-white border border-gray-300 text-gray-700 px-2 py-1 rounded text-sm hover:bg-gray-50 transition-colors cursor-pointer"
				   onclick="window.open('<?php echo Route::_($current->item->url . '&tmpl=component&print=1'); ?>','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;">
					<?php echo HTMLFormatHelper::icon('printer.png', Text::_('CPRINT')); ?></a>
			<?php endif; ?>

			<?php if ($current->user->get('id')): ?>
				<?php echo Layout::render('core.single.recordParts.buttonBookmark', ['record' => $item, 'type' => $current->type, 'params' => $params]); ?>
				<?php echo Layout::render('core.single.recordParts.buttonFollow',   ['record' => $item, 'section' => $current->section, 'params' => $params]); ?>
				<?php echo Layout::render('core.single.recordParts.buttonRepost',   ['record' => $item, 'section' => $current->section]); ?>
				<?php if (is_object($params) && $params->get('tmpl_core.item_edit_button')): ?>
					<?php echo Layout::render('core.single.recordParts.buttonEdit', ['record' => $item, 'type' => $current->type, 'section' => $current->section]); ?>
				<?php endif; ?>
				<?php echo Layout::render('core.single.recordParts.controlsMenu', ['controls' => $item->controls, 'record' => $item]); ?>
			<?php endif; ?>
		</div>
	</div>
<?php else: ?>
	<div class="ml-auto controls">
		<a href="#" class="bg-white border border-gray-300 text-gray-700 px-2 py-1 rounded text-sm hover:bg-gray-50 transition-colors cursor-pointer"
		   onclick="window.print();return false;">
			<?php echo HTMLFormatHelper::icon('printer.png', Text::_('CPRINT')); ?>
		</a>
	</div>
<?php endif; ?>

<script>
(function() {
	document.addEventListener('click', function(e) {
		if (!e.target.closest('.jcck-dropdown-container')) {
			document.querySelectorAll('.jcck-dropdown-menu').forEach(function(m) { m.classList.add('hidden'); });
		}
	});
})();
</script>
