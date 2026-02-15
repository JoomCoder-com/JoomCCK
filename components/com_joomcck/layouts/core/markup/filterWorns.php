<?php
/**
 * Joomcck by joomcoder
 * Core Layout - Filter Warnings
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

extract($displayData);

$markup = $current->tmpl_params['markup'];

if (!$markup->get('filters.worns') || !count($current->worns)) return;

?>
<div class="filter-worns">
	<?php foreach ($current->worns as $worn):
		if(!is_string($worn->text)) continue;
		?>
		<div class="alert alert-info alert-dismissible fade show float-start" role="alert">
			<div><i class="fas fa-filter"></i> <?php echo $worn->label ?></div>
			<?php echo $worn->text ?>
			<button type="button" class="btn-close hasTooltip" data-bs-dismiss="alert" aria-label="Close"
					onclick="Joomcck.cleanFilter('<?php echo $worn->name ?>')"
					title="<?php echo Text::_('CDELETEFILTER') ?>">
			</button>
		</div>
	<?php endforeach; ?>
	<?php if (count($current->worns) > 1): ?>
		<button onclick="Joomla.submitbutton('records.cleanall');" class="alert alert-danger float-start">
			<div><?php echo Text::_('CORESET'); ?></div>
			<?php echo Text::_('CODELETEALLFILTERS'); ?>
		</button>
	<?php endif; ?>
	<div class="clearfix"></div>
</div>
<br>
