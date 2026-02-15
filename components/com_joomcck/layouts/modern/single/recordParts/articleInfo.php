<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Article Info Card (rating, category, author, details, avatar)
 *
 * Tailwind CSS replacement for Bootstrap card + row/col layout.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

extract($displayData);

$params = $current->tmpl_params['record'];
$item = $current->item;

// Build data arrays
$category = array();
$author = array();
$details = array();

if ($params->get('tmpl_core.item_categories') && $item->categories_links) {
	$catLabel = count($item->categories_links) > 1 ? \Joomla\CMS\Language\Text::_('CCATEGORIES') : \Joomla\CMS\Language\Text::_('CCATEGORY');
	$category[] = array('label' => $catLabel, 'value' => implode(', ', $item->categories_links));
}
if ($params->get('tmpl_core.item_user_categories') && $item->ucatid) {
	$category[] = array('label' => \Joomla\CMS\Language\Text::_('CUCAT'), 'value' => $item->ucatname_link);
}
if ($params->get('tmpl_core.item_author') && $item->user_id) {
	$a = array();
	$a[] = \Joomla\CMS\Language\Text::sprintf('CWRITTENBY', CCommunityHelper::getName($item->user_id, $current->section));
	if ($params->get('tmpl_core.item_author_filter')) {
		$a[] = FilterHelper::filterButton('filter_user', $item->user_id, NULL, \Joomla\CMS\Language\Text::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($item->user_id, $current->section, array('nohtml' => 1))), $current->section);
	}
	$author[] = implode(' ', $a);
}
if ($params->get('tmpl_core.item_ctime')) {
	$author[] = \Joomla\CMS\Language\Text::sprintf('CONDATE', \Joomla\CMS\HTML\HTMLHelper::_('date', $item->created, $params->get('tmpl_core.item_time_format')));
}
if ($params->get('tmpl_core.item_mtime')) {
	$author[] = \Joomla\CMS\Language\Text::_('CMTIME') . ': ' . \Joomla\CMS\HTML\HTMLHelper::_('date', $item->modify, $params->get('tmpl_core.item_time_format'));
}
if ($params->get('tmpl_core.item_extime')) {
	$author[] = \Joomla\CMS\Language\Text::_('CEXTIME') . ': ' . ($item->expire ? \Joomla\CMS\HTML\HTMLHelper::_('date', $item->expire, $params->get('tmpl_core.item_time_format')) : \Joomla\CMS\Language\Text::_('CNEVER'));
}
if ($params->get('tmpl_core.item_type')) {
	$details[] = sprintf('%s: %s %s', \Joomla\CMS\Language\Text::_('CTYPE'), $current->type->name, ($params->get('tmpl_core.item_type_filter') ? FilterHelper::filterButton('filter_type', $item->type_id, NULL, \Joomla\CMS\Language\Text::sprintf('CSHOWALLTYPEREC', $current->type->name), $current->section) : NULL));
}
if ($params->get('tmpl_core.item_hits')) {
	$details[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CHITS'), $item->hits);
}
if ($params->get('tmpl_core.item_comments_num')) {
	$details[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CCOMMENTS'), CommentHelper::numComments($current->type, $current->item));
}
if ($params->get('tmpl_core.item_favorite_num')) {
	$details[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CFAVORITED'), $item->favorite_num);
}
if ($params->get('tmpl_core.item_follow_num')) {
	$details[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CFOLLOWERS'), $item->subscriptions_num);
}

if (!$category && !$author && !$details && !$params->get('tmpl_core.item_rating')) return;

?>
<div class="mt-6 bg-gray-50 rounded-lg border border-gray-200 p-4">
	<div class="flex items-start gap-4">
		<?php if ($params->get('tmpl_core.item_rating')): ?>
			<div class="shrink-0">
				<?php echo $item->rating; ?>
			</div>
		<?php endif; ?>
		<div class="flex-1 min-w-0">
			<div class="grid grid-cols-[auto_1fr] gap-x-3 gap-y-2 text-sm">
				<?php foreach ($category as $cat): ?>
					<dt class="font-medium text-gray-500"><?php echo $cat['label']; ?></dt>
					<dd class="text-gray-700"><?php echo $cat['value']; ?></dd>
				<?php endforeach; ?>
				<?php if ($author): ?>
					<dt class="font-medium text-gray-500"><?php echo \Joomla\CMS\Language\Text::_('Posted'); ?></dt>
					<dd class="text-gray-700"><?php echo implode(', ', $author); ?></dd>
				<?php endif; ?>
				<?php if ($details): ?>
					<dt class="font-medium text-gray-500">Info</dt>
					<dd class="text-gray-700"><?php echo implode(', ', $details); ?></dd>
				<?php endif; ?>
			</div>
		</div>
		<?php if ($params->get('tmpl_core.item_author_avatar')): ?>
			<div class="shrink-0">
				<img class="w-10 h-10 rounded-full object-cover shadow-sm" src="<?php echo CCommunityHelper::getAvatar($item->user_id, $params->get('tmpl_core.item_author_avatar_width', 40), $params->get('tmpl_core.item_author_avatar_height', 40)); ?>" alt="" />
			</div>
		<?php endif; ?>
	</div>
</div>
