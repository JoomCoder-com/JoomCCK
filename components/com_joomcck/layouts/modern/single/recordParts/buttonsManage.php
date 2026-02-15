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

defined('_JEXEC') or die();

extract($displayData);

$item = $current->item;
$params = $current->tmpl_params['record'];

$containerClass = !isset($containerClass) ? 'ml-auto controls' : $containerClass;

?>

<?php if(!$current->print):?>
	<div class="<?php echo $containerClass ?>">
		<div class="flex items-center gap-1">
			<?php if($params->get('tmpl_core.item_print')):?>
				<a class="bg-white border border-gray-300 text-gray-700 px-2 py-1 rounded text-sm hover:bg-gray-50 transition-colors cursor-pointer"
				   onclick="window.open('<?php echo \Joomla\CMS\Router\Route::_($current->item->url.'&tmpl=component&print=1');?>','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;">
					<?php echo HTMLFormatHelper::icon('printer.png', \Joomla\CMS\Language\Text::_('CPRINT'));  ?></a>
			<?php endif;?>

			<?php if($current->user->get('id')):?>
				<?php echo HTMLFormatHelper::bookmark($item, $current->type, $params);?>
				<?php echo HTMLFormatHelper::follow($item, $current->section);?>
				<?php echo HTMLFormatHelper::repost($item, $current->section);?>
				<?php if($item->controls):?>
					<div class="relative jcck-dropdown-container">
						<button type="button"
								class="bg-white border border-gray-300 text-gray-500 px-2 py-1 rounded text-sm hover:bg-gray-50 transition-colors"
								onclick="this.parentElement.querySelector('.jcck-dropdown-menu').classList.toggle('hidden')">
							<?php echo HTMLFormatHelper::icon('gear.png');  ?>
						</button>
						<ul class="jcck-dropdown-menu hidden absolute right-0 z-10 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 py-1 min-w-48">
							<?php echo list_controls($item->controls);?>
						</ul>
					</div>
				<?php endif;?>
			<?php endif;?>
		</div>
	</div>
<?php else:?>
	<div class="ml-auto controls">
		<a href="#" class="bg-white border border-gray-300 text-gray-700 px-2 py-1 rounded text-sm hover:bg-gray-50 transition-colors cursor-pointer"
		   onclick="window.print();return false;">
			<?php echo HTMLFormatHelper::icon('printer.png', \Joomla\CMS\Language\Text::_('CPRINT'));  ?>
		</a>
	</div>
<?php endif;?>

<script>
(function() {
	document.addEventListener('click', function(e) {
		if (!e.target.closest('.jcck-dropdown-container')) {
			document.querySelectorAll('.jcck-dropdown-menu').forEach(function(m) { m.classList.add('hidden'); });
		}
	});
})();
</script>
