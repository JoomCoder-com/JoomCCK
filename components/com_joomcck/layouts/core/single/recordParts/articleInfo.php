<?php
/**
 * Joomcck by joomcoder
 * Core Layout - Article Info Card (rating, category, author, details, avatar)
 *
 * Extracted from default_record_default.php lines 200-236.
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
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
	$category[] = sprintf('<dt>%s<dt> <dd>%s<dd>', (count($item->categories_links) > 1 ? \Joomla\CMS\Language\Text::_('CCATEGORIES') : \Joomla\CMS\Language\Text::_('CCATEGORY')), implode(', ', $item->categories_links));
}
if ($params->get('tmpl_core.item_user_categories') && $item->ucatid) {
	$category[] = sprintf('<dt>%s<dt> <dd>%s<dd>', \Joomla\CMS\Language\Text::_('CUCAT'), $item->ucatname_link);
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
<div class="card article-info p-4">
	<div class="row">
		<?php if ($params->get('tmpl_core.item_rating')): ?>
			<div class="col-md-4">
				<?php echo $item->rating; ?>
			</div>
		<?php endif; ?>
		<div class="col-md-<?php echo ($params->get('tmpl_core.item_rating') ? 7 : 11); ?>">
			<small>
				<dl class="dl-horizontal user-info">
					<?php if ($category): ?>
						<?php echo implode(' ', $category); ?>
					<?php endif; ?>
					<?php if ($author): ?>
						<dt><?php echo \Joomla\CMS\Language\Text::_('Posted'); ?></dt>
						<dd>
							<?php echo implode(', ', $author); ?>
						</dd>
					<?php endif; ?>
					<?php if ($details): ?>
						<dt>Info</dt>
						<dd class="hits">
							<?php echo implode(', ', $details); ?>
						</dd>
					<?php endif; ?>
				</dl>
			</small>
		</div>
		<?php if ($params->get('tmpl_core.item_author_avatar')): ?>
			<div class="col-md-1 avatar">
				<img class="w-100 rounded shadow-sm" src="<?php echo CCommunityHelper::getAvatar($item->user_id, $params->get('tmpl_core.item_author_avatar_width', 40), $params->get('tmpl_core.item_author_avatar_height', 40)); ?>" />
			</div>
		<?php endif; ?>
	</div>
</div>
