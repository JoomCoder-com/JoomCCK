<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Modal Layout
 *
 * DaisyUI dialog replacement for Bootstrap modal.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

?>

<dialog id="<?php echo $id ?>" class="jcck-modal bg-transparent p-0 rounded-lg shadow-xl max-w-lg w-full backdrop:bg-black/50">
	<div class="bg-white rounded-lg overflow-hidden">
		<div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-b border-gray-200">
			<h3 class="text-base font-semibold text-gray-900"><?php echo $title ?></h3>
			<button type="button"
					class="text-gray-400 hover:text-gray-600 transition-colors"
					onclick="this.closest('dialog').close()"
					aria-label="Close">
				<i class="fas fa-times"></i>
			</button>
		</div>

		<div class="p-4 overflow-y-auto max-h-[70vh]">
			<?php echo $body ?>
		</div>

		<div class="flex justify-end px-4 py-3 bg-gray-50 border-t border-gray-200">
			<button type="button"
					class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors"
					onclick="this.closest('dialog').close()">
				<?php echo Text::_('CCLOSE') ?>
			</button>
		</div>
	</div>
</dialog>

<script>
(function() {
	var dialog = document.getElementById('<?php echo $id ?>');
	if (!dialog) return;

	// Bridge: jQuery .modal('show') calls -> native dialog API
	if (typeof jQuery !== 'undefined') {
		jQuery(document).on('click', '[data-bs-toggle="modal"][data-bs-target="#<?php echo $id ?>"]', function(e) {
			e.preventDefault();
			dialog.showModal();
		});
	}

	// Close on backdrop click
	dialog.addEventListener('click', function(e) {
		if (e.target === dialog) dialog.close();
	});
})();
</script>
