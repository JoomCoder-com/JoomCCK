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

?>
<article class="has-context<?php if($item->featured) {echo ' featured';}?>">
	<?php echo Joomla\CMS\Layout\LayoutHelper::render(
		'core.list.recordParts.buttonsManage',
		['item' => $item,'section' => $obj->section, 'submissionTypes' => $obj->submission_types, "params" => $params],null,['component' => 'com_joomcck','client' => 'site' ]
	) ?>
	<h2>
		<?php if($params->get('tmpl_core.item_title')):?>
			<?php if(in_array($params->get('tmpl_core.item_link'), $obj->user->getAuthorisedViewLevels())):?>
				<a <?php echo $item->nofollow ? 'rel="nofollow"' : '';?> href="<?php echo \Joomla\CMS\Router\Route::_($item->url);?>">
					<?php echo $item->title?>
				</a>
			<?php else :?>
				<?php echo $item->title?>
			<?php endif;?>
		<?php endif;?>
		<?php echo CEventsHelper::showNum('record', $item->id);?>
	</h2>

	<?php if($params->get('tmpl_core.item_rating')):?>
		<div class="content_rating">
			<?php echo $item->rating;?>
		</div>
	<?php endif;?>


	<dl class="dl-horizontal text-overflow">
		<?php foreach ($item->fields_by_id AS $field):?>
			<?php if(in_array($field->key, $exclude)) continue; ?>
			<?php if($field->params->get('core.show_lable') > 1):?>
				<dt id="<?php echo $field->id;?>-lbl" for="field_<?php echo $field->id;?>" class="<?php echo $field->class;?>" >
					<?php echo $field->label; ?>
					<?php if($field->params->get('core.icon')):?>
						<?php echo HTMLFormatHelper::icon($field->params->get('core.icon'));  ?>
					<?php endif;?>
				</dt>
			<?php endif;?>
			<dd class="input-field<?php echo ($field->params->get('core.label_break') > 1 ? '-full' : NULL)?> <?php echo $field->fieldclass;?>">
				<?php echo $field->result; ?>
			</dd>
		<?php endforeach;?>
	</dl>


	<?php
	$category = array();
	$author = array();
	$details = array();

	if($params->get('tmpl_core.item_categories') && $item->categories_links)
	{
		$category[] = sprintf('%s: %s', (count($item->categories_links) > 1 ? \Joomla\CMS\Language\Text::_('CCATEGORIES') : \Joomla\CMS\Language\Text::_('CCATEGORY')), implode(', ', $item->categories_links));
	}
	if($params->get('tmpl_core.item_user_categories') && $item->ucatid)
	{
		$category[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CUSERCAT'), $item->ucatname_link);
	}
	if($params->get('tmpl_core.item_author') && $item->user_id)
	{
		$author[] = \Joomla\CMS\Language\Text::sprintf('CWRITTENBY', CCommunityHelper::getName($item->user_id, $obj->section));
	}
	if($params->get('tmpl_core.item_author_filter'))
	{
		$author[] = FilterHelper::filterButton('filter_user', $item->user_id, NULL, \Joomla\CMS\Language\Text::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($item->user_id, $obj->section, array('nohtml' => 1))), $obj->section);
	}
	if($params->get('tmpl_core.item_ctime'))
	{
		$author[] = \Joomla\CMS\Language\Text::sprintf('CONDATE', \Joomla\CMS\HTML\HTMLHelper::_('date', $item->created, $params->get('tmpl_core.item_time_format')));
	}

	if($params->get('tmpl_core.item_mtime'))
	{
		$author[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CCHANGEON'), \Joomla\CMS\HTML\HTMLHelper::_('date', $item->modify, $params->get('tmpl_core.item_time_format')));
	}

	if($params->get('tmpl_core.item_extime'))
	{
		$author[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CEXPIREON'), $item->expire ? \Joomla\CMS\HTML\HTMLHelper::_('date', $item->expire, $params->get('tmpl_core.item_time_format')) : \Joomla\CMS\Language\Text::_('CNEVER'));
	}

	if($params->get('tmpl_core.item_type'))
	{
		$details[] = sprintf('%s: %s %s', \Joomla\CMS\Language\Text::_('CTYPE'), $item->type_name, ($params->get('tmpl_core.item_type_filter') ? FilterHelper::filterButton('filter_type', $item->type_id, NULL, \Joomla\CMS\Language\Text::sprintf('CSHOWALLTYPEREC', $item->type_name), $obj->section) : NULL));
	}
	if($params->get('tmpl_core.item_hits'))
	{
		$details[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CHITS'), $item->hits);
	}
	if($params->get('tmpl_core.item_comments_num'))
	{
		$details[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CCOMMENTS'), CommentHelper::numComments($obj->submission_types[$item->type_id], $item));
	}
	if($params->get('tmpl_core.item_vote_num'))
	{
		$details[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CVOTES'), $item->votes);
	}
	if($params->get('tmpl_core.item_favorite_num'))
	{
		$details[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CFAVORITED'), $item->favorite_num);
	}
	if($params->get('tmpl_core.item_follow_num'))
	{
		$details[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CFOLLOWERS'), $item->subscriptions_num);
	}
	?>

	<?php if($params->get('tmpl_core.item_readon')): ?>
		<p>
			<a class="btn btn-primary" href="<?php echo \Joomla\CMS\Router\Route::_($item->url);?>"><?php echo \Joomla\CMS\Language\Text::_('CREADMORE'); ?></a>
		</p>
	<?php endif;?>

	<?php if($category || $author || $details): ?>
		<div class="clearfix"></div>

		<div class="well well-small">
			<?php if($params->get('tmpl_core.item_author_avatar')):?>
				<div class="float-end">
					<img class="img-polaroid" src="<?php echo CCommunityHelper::getAvatar($item->user_id, $params->get('tmpl_core.item_author_avatar_width', 40), $params->get('tmpl_core.item_author_avatar_height', 40));?>" />
				</div>
			<?php endif;?>
			<small>
				<dl>
					<?php if($category):?>
						<dt><?php echo \Joomla\CMS\Language\Text::_('CCATEGORY');?></dt>
						<dd><?php echo implode(' ', $category);?></dd>
					<?php endif;?>
					<?php if($author):?>
						<dt><?php echo \Joomla\CMS\Language\Text::_('Posted');?></dt>
						<dd>
							<?php echo implode(', ', $author);?>
						</dd>
					<?php endif;?>
					<?php if($details):?>
						<dt>Info</dt>
						<dd class="hits">
							<?php echo implode(', ', $details);?>
						</dd>
					<?php endif;?>
				</dl>
			</small>
		</div>
	<?php endif;?>
</article>
