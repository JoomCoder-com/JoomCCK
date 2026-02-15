<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Filter Warnings Layout
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

extract($displayData);

$markup = $current->tmpl_params['markup'];

if (!$markup->get('filters.worns') || !count($current->worns)) return;

?>
<div class="filter-worns flex flex-wrap gap-2 mb-4">
	<?php foreach ($current->worns as $worn):
		if(!is_string($worn->text)) continue;
		?>
		<div class="inline-flex items-start gap-2 px-3 py-2 bg-blue-50 text-blue-800 border border-blue-200 rounded-lg text-sm" role="status">
			<div class="flex-1">
				<div class="font-medium"><i class="fas fa-filter text-xs"></i> <?php echo $worn->label ?></div>
				<div class="text-blue-700"><?php echo $worn->text ?></div>
			</div>
			<button type="button"
					class="text-blue-400 hover:text-blue-600 transition-colors mt-0.5"
					onclick="Joomcck.cleanFilter('<?php echo $worn->name ?>')"
					title="<?php echo Text::_('CDELETEFILTER') ?>">
				<i class="fas fa-times text-xs"></i>
			</button>
		</div>
	<?php endforeach; ?>
	<?php if (count($current->worns) > 1): ?>
		<button onclick="Joomla.submitbutton('records.cleanall');"
				class="inline-flex flex-col items-center px-3 py-2 bg-red-50 text-red-700 border border-red-200 rounded-lg text-sm hover:bg-red-100 transition-colors">
			<div class="font-medium"><?php echo Text::_('CORESET'); ?></div>
			<?php echo Text::_('CODELETEALLFILTERS'); ?>
		</button>
	<?php endif; ?>
</div>
