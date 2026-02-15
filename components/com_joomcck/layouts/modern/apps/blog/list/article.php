<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Blog Article Card Layout
 *
 * Tailwind CSS card replacement for Bootstrap card article.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;

defined('_JEXEC') or die();

extract($displayData);
?>

<article class="jcck-card bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-4 relative<?php if ($item->featured) echo ' ring-2 ring-amber-300'; ?>">

	<div class="p-5">

		<div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10">
			<?php echo Layout::render(
				'core.list.recordParts.buttonsManage',
				['item' => $item, 'section' => $obj->section, 'submissionTypes' => $obj->submission_types, "params" => $params]) ?>
		</div>

		<div class="mb-4">
			<h2 id="record<?php echo $item->id ?>" class="text-xl font-bold text-gray-900">
				<?php if ($params->get('tmpl_core.item_title')): ?>
					<?php if (in_array($params->get('tmpl_core.item_link'), $obj->user->getAuthorisedViewLevels())): ?>
						<a class="text-gray-900 hover:text-primary no-underline transition-colors" <?php echo $item->nofollow ? 'rel="nofollow"' : ''; ?>
						   href="<?php echo \Joomla\CMS\Router\Route::_($item->url); ?>">
							<?php echo $item->title ?>
						</a>
					<?php else : ?>
						<?php echo $item->title ?>
					<?php endif; ?>
				<?php endif; ?>
				<?php echo CEventsHelper::showNum('record', $item->id); ?>
			</h2>
		</div>

		<!-- Image field -->
		<?php if($params->get('tmpl_params.field_image',0) && isset($item->fields_by_id[$params->get('tmpl_params.field_image',0)])):?>
			<div id="record-image-<?php echo $item->id ?>" class="mb-4">
				<?php echo $item->fields_by_id[$params->get('tmpl_params.field_image',0)]->result ?>
			</div>
		<?php endif; ?>

		<!-- rating field -->
		<?php if ($params->get('tmpl_core.item_rating')): ?>
			<div class="content_rating mb-4">
				<?php echo $item->rating; ?>
			</div>
		<?php endif; ?>

		<!-- fields list -->
		<?php echo Layout::render(
			'core.list.recordParts.fields' . ucfirst($params->get('tmpl_params.fields_list_layout', 'default')),
			['item' => $item, 'params' => $params, 'exclude' => $exclude]
		); ?>

		<?php if ($params->get('tmpl_core.item_readon')): ?>
			<p class="mt-4">
				<a class="bg-primary text-white px-4 py-2 rounded-lg font-medium hover:opacity-90 transition-colors inline-block no-underline"
				   href="<?php echo \Joomla\CMS\Router\Route::_($item->url); ?>">
					<?php echo \Joomla\CMS\Language\Text::_('CREADMORE'); ?>
				</a>
			</p>
		<?php endif; ?>

	</div>

	<?php echo Layout::render('core.list.recordParts.details',['item' => $item,'params' => $params,'obj' => $obj,'containerClass' => 'px-5 py-3 bg-gray-50 border-t border-gray-200 text-sm']) ?>

</article>
