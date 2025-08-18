<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomla\CMS\Factory;

defined('_JEXEC') or die();

extract($displayData);

// some inits
$app         = \Joomla\CMS\Factory::getApplication();

// no need to continue if user not allowed to view tags
if(!in_array($current->type->params->get('properties.item_can_view_tag', 1), $current->user->getAuthorisedViewLevels()))
	return;

// set attach only
$attach_only = MECAccess::allowAccessAuthor($current->type, 'properties.item_can_add_tag', $current->item->user_id) || MECAccess::allowUserModerate($current->user, $current->section, 'allow_tags') ? false : true;


?>

<?php
if(
	MECAccess::allowAccessAuthor($current->type, 'properties.item_can_add_tag', $current->item->user_id) ||
	MECAccess::allowAccessAuthor($current->type, 'properties.item_can_attach_tag', $current->item->user_id) ||
	MECAccess::allowUserModerate($current->user, $current->section, 'allow_tags')
):
	?>
	<dl class="dl-horizontal">
		<dt id="tags-dt">
			<?php echo \Joomla\CMS\Language\Text::_('CTAGS'); ?> <?php echo HTMLFormatHelper::icon('price-tag.png'); ?>
		</dt>
		<dd id="tags-dd">
			<div id="add-tags-block<?php echo $current->item->id; ?>">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('tags.add_button', $current->item->id, $current->type->params->get('properties.item_tags_max', 25), $attach_only); ?>
			</div>
		</dd>
	</dl>

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
		<style>
            .tag_list .tag_list_item {
                padding: 6px 6px 4px 6px;
            }

            .tag_list .tag_list_item.label {
                font-size: 12px;
                font-weight: 400;
                background-color: #F7F7FA;
                border: solid 2px #DFE2E8;
            }

            .tag_list .tag_list_item.label a {
                color: #35bdb5;
            }
		</style>
		<div id="tag-list-<?php echo $current->item->id ?>" class="tag_list">
			<span class="tag_list_item"><?php echo \Joomla\CMS\Language\Text::_('CTAGS'); ?> <?php echo HTMLFormatHelper::icon('price-tag.png'); ?></span>
			<?php foreach($tags AS $tag): ?>
				<span class="label label-default tag_list_item"><a href="<?php echo $tag['link'] ?>" <?php echo $tag['attr'] ?>><?php echo $tag['tag'] ?></a></span>
			<?php endforeach; ?>
		</div>
		<div class="clearfix"></div>
		<br>
	<?php endif; ?>
<?php  endif; ?>