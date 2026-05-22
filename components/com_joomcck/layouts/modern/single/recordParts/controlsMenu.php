<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Settings/controls dropdown for a single record view.
 *
 * Tailwind CSS variant of core.single.recordParts.controlsMenu. Override to restructure the menu.
 * $controls is the per-record control array; $record is the full item for additional context.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

extract($displayData);

if (empty($controls)) {
	return;
}
?>
<div class="relative jcck-dropdown-container">
	<button type="button"
			class="bg-white border border-gray-300 text-gray-500 px-2 py-1 rounded text-sm hover:bg-gray-50 transition-colors"
			onclick="this.parentElement.querySelector('.jcck-dropdown-menu').classList.toggle('hidden')">
		<?php echo HTMLFormatHelper::icon('gear.png'); ?>
	</button>
	<ul class="jcck-dropdown-menu hidden absolute right-0 z-10 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 py-1 min-w-48">
		<?php echo list_controls($controls); ?>
	</ul>
</div>
