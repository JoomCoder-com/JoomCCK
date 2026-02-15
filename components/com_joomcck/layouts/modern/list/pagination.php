<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Pagination Layout
 *
 * Tailwind CSS styling override for Joomla pagination output.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

extract($displayData);

// params
$showPagination = $params->get('tmpl_core.item_pagination', 1);
$showLimitBox = $params->get('tmpl_core.item_limit_box', 0);

// no need to continue if pagination disabled
if(!$showPagination)
	return;

// remove "all" items option, + style select
if($showLimitBox){
	$limitBox = str_replace('<option value="0">' . \Joomla\CMS\Language\Text::_('JALL') . '</option>', '', $pagination->getLimitBox());
	$limitBox = str_replace('class="form-select"', 'class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-1 focus:ring-primary"', $limitBox);
}

?>

<form method="post">
	<div class="flex flex-wrap items-center justify-between gap-4 mt-4">

		<?php if ($pagination->getPagesLinks()): ?>
			<div class="joomcckPageLinks
						[&_.pagination]:flex [&_.pagination]:items-center [&_.pagination]:gap-1
						[&_.page-link]:px-3 [&_.page-link]:py-1.5 [&_.page-link]:text-sm [&_.page-link]:rounded [&_.page-link]:border [&_.page-link]:border-gray-300 [&_.page-link]:text-gray-700 [&_.page-link]:bg-white [&_.page-link]:no-underline
						[&_.page-link:hover]:bg-gray-50
						[&_.active_.page-link]:bg-primary [&_.active_.page-link]:text-white [&_.active_.page-link]:border-primary
						[&_.disabled_.page-link]:opacity-50 [&_.disabled_.page-link]:cursor-not-allowed
						[&_.page-item]:list-none">
				<?php echo $pagination->getPagesLinks() ?>
			</div>
		<?php endif; ?>

		<?php if ($pagination->getPagesCounter()): ?>
			<div class="joomcckPagesCounter text-sm text-gray-500">
				<?php echo $pagination->getPagesCounter(); ?>
			</div>
		<?php endif; ?>

		<?php if ($showLimitBox) : ?>
			<div class="joomcckLimitBox flex items-center gap-2">
				<?php echo $limitBox ?>
				<small class="text-gray-500 text-xs"><?php echo $pagination->getResultsCounter(); ?></small>
			</div>
		<?php endif; ?>
	</div>
</form>
