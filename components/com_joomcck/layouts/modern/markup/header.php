<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Markup Header Layout
 *
 * Tailwind CSS flex replacement for Bootstrap page-header + float-end.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

extract($displayData);

$markup = $current->tmpl_params['markup'];

?>

<?php if (($current->section->params->get('personalize.personalize') && $current->input->getInt('user_id')) || $current->isMe): ?>
	<?php echo $current->loadTemplate('user_block'); ?>

<?php elseif ($markup->get('title.title_show')): ?>
	<div class="mb-6 pb-2 border-b border-gray-200">
		<div class="flex items-center justify-between">
			<h1 class="text-2xl font-bold text-gray-900">
				<?php echo $current->escape(Mint::_($current->title)); ?>
				<?php if ($current->category->id): ?>
					<?php echo CEventsHelper::showNum('category', $current->category->id, true); ?>
				<?php else: ?>
					<?php echo CEventsHelper::showNum('section', $current->section->id, true); ?>
				<?php endif; ?>
			</h1>
			<?php if (in_array($current->section->params->get('events.subscribe_category'), $current->user->getAuthorisedViewLevels()) && $current->input->getInt('cat_id')): ?>
				<div class="shrink-0">
					<?php echo HTMLFormatHelper::followcat($current->input->getInt('cat_id'), $current->section); ?>
				</div>
			<?php elseif (in_array($current->section->params->get('events.subscribe_section'), $current->user->getAuthorisedViewLevels())): ?>
				<div class="shrink-0">
					<?php echo HTMLFormatHelper::followsection($current->section); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

<?php elseif ($current->appParams->get('show_page_heading', 0) && $current->appParams->get('page_heading', '')) : ?>
	<div class="mb-6 pb-2 border-b border-gray-200">
		<h1 class="text-2xl font-bold text-gray-900">
			<?php echo $current->escape($current->appParams->get('page_heading')); ?>
		</h1>
	</div>
<?php endif; ?>
