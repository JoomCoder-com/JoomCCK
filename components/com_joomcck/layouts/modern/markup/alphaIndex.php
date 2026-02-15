<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Alpha Index Layout
 *
 * Tailwind CSS flex + badge replacement for Bootstrap badges.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

extract($displayData);

$markup = $current->tmpl_params['markup'];

if(!$markup->get('main.alpha'))
	return;

if(!$current->items)
	return;

?>

<?php if ($current->alpha && $current->alpha_list): ?>
	<div class="alpha-index mb-4">
		<?php foreach ($current->alpha as $set): ?>
			<div class="flex flex-wrap gap-1 mb-1">
				<?php foreach ($set as $alpha): ?>
					<?php if (in_array($alpha, $current->alpha_list)): ?>
						<button type="button"
								class="jcck-badge bg-amber-100 text-amber-800 border border-amber-300 px-2 py-1 text-xs font-medium rounded cursor-pointer hover:bg-amber-200 transition-colors"
								onclick="Joomcck.applyFilter('filter_alpha', '<?php echo $alpha ?>')"
							<?php echo $markup->get('main.alpha_num') ? ' title="' . \Joomla\CMS\Language\Text::plural('CXNRECFOUND',
									@$current->alpha_totals[$alpha]) . '"' : null; ?>>
							<?php echo $alpha; ?>
						</button>
					<?php else: ?>
						<button disabled
								class="px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-400 border border-gray-200 cursor-not-allowed">
							<?php echo $alpha; ?>
						</button>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
