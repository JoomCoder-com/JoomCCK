<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Record Navigation Layout
 *
 * Tailwind CSS flex replacement for Bootstrap row/col + btn-outline-primary.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

extract($displayData);

if (!$current->navigation || (!$current->navigation->next && !$current->navigation->previous)) {
	return;
}

$typeParams = $current->type->params;
$prevText = $typeParams->get('properties.navigation_prev_text', '');
$nextText = $typeParams->get('properties.navigation_next_text', '');

$prevText = !empty($prevText) ? $prevText : \Joomla\CMS\Language\Text::_('CPREVIOUS_RECORD');
$nextText = !empty($nextText) ? $nextText : \Joomla\CMS\Language\Text::_('CNEXT_RECORD');

$navPosition = $current->navigation->position ?? 'bottom';
$positionClass = 'nav-position-' . $navPosition;
?>

<div class="joomcck-navigation <?php echo $positionClass; ?> my-6">
	<div class="flex items-start gap-4">
		<?php if ($current->navigation->previous): ?>
			<div class="flex-1 text-left">
				<a href="<?php echo $current->navigation->previous->url; ?>"
				   class="inline-flex items-center gap-2 border border-primary text-primary px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary hover:text-white transition-colors"
				   title="<?php echo $current->navigation->previous->title ?>">
					<i class="fa fa-chevron-left" aria-hidden="true"></i>
					<span><?php echo htmlspecialchars($prevText); ?></span>
				</a>
				<div class="mt-1">
					<small class="text-gray-500 text-xs"><?php echo \Joomla\CMS\HTML\Helpers\StringHelper::truncate($current->navigation->previous->title, 50) ?></small>
				</div>
			</div>
		<?php else: ?>
			<div class="flex-1"></div>
		<?php endif; ?>

		<?php if ($current->navigation->next): ?>
			<div class="flex-1 text-right">
				<a href="<?php echo $current->navigation->next->url; ?>"
				   class="inline-flex items-center gap-2 border border-primary text-primary px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary hover:text-white transition-colors"
				   title="<?php echo $current->navigation->next->title ?>">
					<span><?php echo htmlspecialchars($nextText); ?></span>
					<i class="fa fa-chevron-right" aria-hidden="true"></i>
				</a>
				<div class="mt-1">
					<small class="text-gray-500 text-xs"><?php echo \Joomla\CMS\HTML\Helpers\StringHelper::truncate($current->navigation->next->title, 50) ?></small>
				</div>
			</div>
		<?php else: ?>
			<div class="flex-1"></div>
		<?php endif; ?>
	</div>
</div>
