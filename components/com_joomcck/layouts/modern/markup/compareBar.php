<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Compare Bar Layout
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

extract($displayData);

?>
<div id="compare" <?php echo !$current->compare ? 'class="hidden"' : ''; ?>>
	<div class="jcck-alert jcck-alert-info flex flex-col gap-3 px-4 py-3 rounded-lg bg-blue-50 text-blue-800 border border-blue-200 mb-4">
		<h4 class="font-semibold text-base"><?php echo Text::sprintf('CCOMPAREMSG', $current->compare) ?></h4>
		<div class="flex items-center gap-2">
			<a rel="nofollow"
			   href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=compare&section_id=' . $current->section->id . '&return=' . Url::back()); ?>"
			   class="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:opacity-90 transition-colors no-underline text-sm"><?php echo Text::_('CCOMPAREVIEW'); ?></a>
			<button onclick="Joomcck.CleanCompare(null, '<?php echo @$current->section->id ?>')"
					class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors"><?php echo Text::_('CCLEANCOMPARE'); ?></button>
		</div>
	</div>
</div>
