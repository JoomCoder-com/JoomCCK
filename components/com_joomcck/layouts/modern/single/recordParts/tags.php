<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Record Tags Layout
 *
 * Tailwind CSS flex + badge replacement for Bootstrap dl-horizontal + inline styles.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

extract($displayData);

$app = \Joomla\CMS\Factory::getApplication();

if(!in_array($current->type->params->get('properties.item_can_view_tag', 1), $current->user->getAuthorisedViewLevels()))
	return;

$attach_only = MECAccess::allowAccessAuthor($current->type, 'properties.item_can_add_tag', $current->item->user_id) || MECAccess::allowUserModerate($current->user, $current->section, 'allow_tags') ? false : true;

?>

<?php
if(
	MECAccess::allowAccessAuthor($current->type, 'properties.item_can_add_tag', $current->item->user_id) ||
	MECAccess::allowAccessAuthor($current->type, 'properties.item_can_attach_tag', $current->item->user_id) ||
	MECAccess::allowUserModerate($current->user, $current->section, 'allow_tags')
):
	?>
	<div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-2 items-start">
		<dt id="tags-dt" class="font-medium text-gray-600 text-sm">
			<?php echo \Joomla\CMS\Language\Text::_('CTAGS'); ?> <?php echo HTMLFormatHelper::icon('price-tag.png'); ?>
		</dt>
		<dd id="tags-dd">
			<div id="add-tags-block<?php echo $current->item->id; ?>">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('tags.add_button', $current->item->id, $current->type->params->get('properties.item_tags_max', 25), $attach_only); ?>
			</div>
		</dd>
	</div>

<?php else: ?>
	<?php if($current->item->tags): ?>
		<?php
		if(count($current->item->categories) > 0 && $current->section->params->get('general.filter_mode') == 0) {
			$keys = array_keys($current->item->categories);
			$catid = array_shift($keys);
		}
		$tags = \Joomla\CMS\HTML\HTMLHelper::_('tags.fetch2',
			$current->item->tags,
			$current->item->id,
			$current->section->id,
			$app->input->getInt('cat_id', @$catid),
			$current->type->params->get('properties.item_tag_htmltags', 'h1, h2, h3, h4, h5, h6, strong, em, b, i, big'),
			$current->type->params->get('properties.item_tag_relevance', 0),
			$current->type->params->get('properties.item_tag_num', 0),
			$current->type->params->get('properties.item_tags_max', 25),
			$current->type->params->get('properties.item_tag_nofollow', 1)
		);
		?>
		<div id="tag-list-<?php echo $current->item->id ?>" class="flex flex-wrap items-center gap-1.5 my-3">
			<span class="text-sm text-gray-600 font-medium"><?php echo \Joomla\CMS\Language\Text::_('CTAGS'); ?> <?php echo HTMLFormatHelper::icon('price-tag.png'); ?></span>
			<?php foreach($tags AS $tag): ?>
				<span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-normal bg-gray-100 border border-gray-200 text-gray-700 hover:bg-gray-200 transition-colors">
					<a href="<?php echo $tag['link'] ?>" <?php echo $tag['attr'] ?> class="text-primary hover:underline"><?php echo $tag['tag'] ?></a>
				</span>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
<?php  endif; ?>
