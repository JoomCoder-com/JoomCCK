<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Record Details Layout
 *
 * Tailwind CSS grid/flex replacement for Bootstrap dl + float-end avatar.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

extract($displayData);

$category = array();
$author   = array();
$details  = array();

if ($params->get('tmpl_core.item_categories') && $item->categories_links)
{
	$category[] = sprintf('%s: %s', (count($item->categories_links) > 1 ? \Joomla\CMS\Language\Text::_('CCATEGORIES') : \Joomla\CMS\Language\Text::_('CCATEGORY')), implode(', ', $item->categories_links));
}
if ($params->get('tmpl_core.item_user_categories') && $item->ucatid)
{
	$category[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CUSERCAT'), $item->ucatname_link);
}
if ($params->get('tmpl_core.item_author') && $item->user_id)
{
	$author[] = \Joomla\CMS\Language\Text::sprintf('CWRITTENBY', CCommunityHelper::getName($item->user_id, $obj->section));
}
if ($params->get('tmpl_core.item_author_filter'))
{
	$author[] = FilterHelper::filterButton('filter_user', $item->user_id, null, \Joomla\CMS\Language\Text::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($item->user_id, $obj->section, array('nohtml' => 1))), $obj->section);
}
if ($params->get('tmpl_core.item_ctime'))
{
	$author[] = \Joomla\CMS\Language\Text::sprintf('CONDATE', \Joomla\CMS\HTML\HTMLHelper::_('date', $item->created, $params->get('tmpl_core.item_time_format')));
}

if ($params->get('tmpl_core.item_mtime'))
{
	$author[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CCHANGEON'), \Joomla\CMS\HTML\HTMLHelper::_('date', $item->modify, $params->get('tmpl_core.item_time_format')));
}

if ($params->get('tmpl_core.item_extime'))
{
	$author[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CEXPIREON'), $item->expire ? \Joomla\CMS\HTML\HTMLHelper::_('date', $item->expire, $params->get('tmpl_core.item_time_format')) : \Joomla\CMS\Language\Text::_('CNEVER'));
}

if ($params->get('tmpl_core.item_type'))
{
	$details[] = sprintf('%s: %s %s', \Joomla\CMS\Language\Text::_('CTYPE'), $item->type_name, ($params->get('tmpl_core.item_type_filter') ? FilterHelper::filterButton('filter_type', $item->type_id, null, \Joomla\CMS\Language\Text::sprintf('CSHOWALLTYPEREC', $item->type_name), $obj->section) : null));
}
if ($params->get('tmpl_core.item_hits'))
{
	$details[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CHITS'), $item->hits);
}
if ($params->get('tmpl_core.item_comments_num'))
{
	$details[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CCOMMENTS'), CommentHelper::numComments($obj->submission_types[$item->type_id], $item));
}
if ($params->get('tmpl_core.item_vote_num'))
{
	$details[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CVOTES'), $item->votes);
}
if ($params->get('tmpl_core.item_favorite_num'))
{
	$details[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CFAVORITED'), $item->favorite_num);
}
if ($params->get('tmpl_core.item_follow_num'))
{
	$details[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CFOLLOWERS'), $item->subscriptions_num);
}
?>

<?php if ($category || $author || $details): ?>
	<div class="<?php echo isset($containerClass) ? $containerClass : '' ?> text-sm text-gray-600">
		<div class="flex items-start gap-4">
			<div class="flex-1">
				<div class="grid grid-cols-[auto_1fr] gap-x-3 gap-y-1">
					<?php if ($category): ?>
						<dt class="font-medium text-gray-500"><?php echo \Joomla\CMS\Language\Text::_('CCATEGORY'); ?></dt>
						<dd><?php echo implode(' ', $category); ?></dd>
					<?php endif; ?>
					<?php if ($author): ?>
						<dt class="font-medium text-gray-500"><?php echo \Joomla\CMS\Language\Text::_('Posted'); ?></dt>
						<dd><?php echo implode(', ', $author); ?></dd>
					<?php endif; ?>
					<?php if ($details): ?>
						<dt class="font-medium text-gray-500">Info</dt>
						<dd><?php echo implode(', ', $details); ?></dd>
					<?php endif; ?>
				</div>
			</div>
			<?php if ($params->get('tmpl_core.item_author_avatar')): ?>
				<div class="shrink-0">
					<img class="rounded-lg border border-gray-200 object-cover"
						 width="<?php echo $params->get('tmpl_core.item_author_avatar_width', 40); ?>"
						 height="<?php echo $params->get('tmpl_core.item_author_avatar_height', 40); ?>"
						 src="<?php echo CCommunityHelper::getAvatar($item->user_id, $params->get('tmpl_core.item_author_avatar_width', 40), $params->get('tmpl_core.item_author_avatar_height', 40)); ?>"/>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>
