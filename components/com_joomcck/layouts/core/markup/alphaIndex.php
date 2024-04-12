<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

extract($displayData);

// get markup settings
$markup     = $current->tmpl_params['markup'];

// no need to continue if not enabled
if(!$markup->get('main.alpha'))
	return;

// no need to continue if no items
if(!$current->items)
	return;

?>

<?php if ($current->alpha && $current->alpha_list): ?>
	<div class="alpha-index">
		<?php foreach ($current->alpha as $set): ?>
			<div class="alpha-set">
				<?php foreach ($set as $alpha): ?>
					<?php if (in_array($alpha, $current->alpha_list)): ?>
						<button type="button" class="badge bg-warning hasTooltip"
						        onclick="Joomcck.applyFilter('filter_alpha', '<?php echo $alpha ?>')"
							<?php echo $markup->get('main.alpha_num') ? ' title="' . \Joomla\CMS\Language\Text::plural('CXNRECFOUND',
									@$current->alpha_totals[$alpha]) . '"' : null; ?>><?php echo $alpha; ?></button>
					<?php else: ?>
						<button disabled class="badge text-bg-light shadow-sm px-2 py-1"><?php echo $alpha; ?></button>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>
	<br>
<?php endif; ?>