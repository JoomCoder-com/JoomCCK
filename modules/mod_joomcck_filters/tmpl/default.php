<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
?>
<style>
	.filter-icon {
		margin-right: 5px;
		line-height: 30px;
	}

	#filter-form input[type="text"][name^="filters"],
	#filter-form input[type="text"][class="cdate-field"],
	#filter-form select {
		box-sizing: border-box;
		margin: 0;
		min-height: 28px;
	}

	#filter-form select {
		margin-bottom: 5px;
	}

	.well.active {
		border: 3px solid;
		position: relative;
	}

	.well.active img.filter-close {
		position: absolute;
		top: -7px;
		right: -7px;
		cursor: pointer;
	}
</style>
<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="filterform" id="filter-form">
	<?php if($params->get('filter_search', 1)): ?>
		<div class="row <?php echo($state->get('records.search') ? ' active' : NULL) ?>">
			<input type="text" class="col-md-12" name="filter_search" value="<?php echo $state->get('records.search'); ?>"/>
		</div>
	<?php endif; ?>

	<?php if(count($f_types) > 1): ?>
		<legend>
			<?php if($params->get('show_icons', 1)): ?>
				<span class="float-start filter-icon"><?php echo HTMLFormatHelper::icon('block.png'); ?></span>
			<?php endif; ?>
			<?php echo $params->get('type_label'); ?>
		</legend>
		<div class="well well-small<?php echo($state->get('records.type') ? ' active' : NULL) ?>">
			<?php if($params->get('filter_type_type') == 1): ?>
				<?php foreach($f_types AS $type): ?>
					<label class="checkbox">
						<input id="type-<?php echo $type->id ?>" type="checkbox" name="filters[type][]" value="<?php echo $type->id ?>"<?php echo $type->filter_checked ?>>
						<?php echo $type->name; ?>
					</label>
				<?php endforeach; ?>
			<?php else : ?>
				<?php echo JHtml::_('select.genericlist', $f_types, 'filters[type]', NULL, 'id', 'name', $state->get('records.type')); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if($params->get('filter_tags_type')): ?>
		<legend>
			<?php if($params->get('show_icons', 1)): ?>
				<span class="float-start filter-icon"><?php echo HTMLFormatHelper::icon('price-tag.png'); ?></span>
			<?php endif; ?>
			<?php echo $params->get('tag_label'); ?>
		</legend>
		<div class="well well-small<?php echo($state->get('records.tag') ? ' active' : NULL) ?>">
			<?php if($params->get('filter_tags_type') == 1): ?>
				<?php echo JHtml::_('tags.tagform', $section, $state->get('records.tag'), array('id' => 'module_tags')); ?>
			<?php elseif($params->get('filter_tags_type') == 2): ?>
				<?php echo JHtml::_('tags.tagcheckboxes', $section, $state->get('records.tag')); ?>
			<?php elseif($params->get('filter_tags_type') == 3): ?>
				<?php echo JHtml::_('tags.tagselect', $section, $state->get('records.tag')); ?>
			<?php elseif($params->get('filter_tags_type') == 4): ?>
				<?php echo JHtml::_('tags.tagpills', $section, $state->get('records.tag')); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if($params->get('filter_users_type')): ?>
		<legend>
			<?php if($params->get('show_icons', 1)): ?>
				<span class="float-start filter-icon"><?php echo HTMLFormatHelper::icon('user.png'); ?></span>
			<?php endif; ?>
			<?php echo $params->get('user_label'); ?>
		</legend>
		<div class="well well-small<?php echo($state->get('records.user') ? ' active' : NULL) ?>">
			<?php if($params->get('filter_users_type') == 1): ?>
				<?php echo JHtml::_('cusers.form', $section, $state->get('records.user'), array('id' => 'module_users')); ?>
			<?php elseif($params->get('filter_users_type') == 2): ?>
				<?php echo JHtml::_('cusers.checkboxes', $section, $state->get('records.user')); ?>
			<?php elseif($params->get('filter_users_type') == 3): ?>
				<?php echo JHtml::_('cusers.select', $section, $state->get('records.user')); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if($params->get('filter_category_type')): ?>
		<legend>
			<?php if($params->get('show_icons', 1)): ?>
				<span class="float-start filter-icon"><?php echo HTMLFormatHelper::icon('category.png'); ?></span>
			<?php endif; ?>
			<?php echo $params->get('category_label'); ?>
		</legend>
		<div class="well well-small<?php echo($state->get('records.category') ? ' active' : NULL) ?>">
			<?php if($params->get('filter_category_type') == 1): ?>
				<?php echo JHtml::_('categories.form', $section, $state->get('records.category'), array('empty_cats' => $params->get('filter_empty_cats', 1))); ?>
			<?php elseif($params->get('filter_category_type') == 2): ?>
				<?php echo JHtml::_('categories.checkboxes', $section, $state->get('records.category'), array('columns' => 3, 'empty_cats' => $params->get('filter_empty_cats', 1))); ?>
			<?php elseif($params->get('filter_category_type') == 3): ?>
				<?php echo JHtml::_('categories.select', $section, $state->get('records.category'), array('multiple' => 0, 'empty_cats' => $params->get('filter_empty_cats', 1))); ?>
			<?php elseif($params->get('filter_category_type') == 4): ?>
				<?php echo JHtml::_('categories.select', $section, $state->get('records.category'), array('multiple' => 1, 'size' => 25, 'empty_cats' => $params->get('filter_empty_cats', 1))); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php foreach($filters AS $filter): ?>
		<?php if(in_array($filter->id, (array)$params->get('field_id_exclude', array())))
			continue; ?>
		<?php $f = $filter->onRenderFilter($section, ($params->get('filter_fields_template') == 'section' ? FALSE : TRUE)) ?>
		<?php if(trim($f)): ?>
			<legend>
				<?php if($params->get('show_icons', 1) && $filter->params->get('core.icon')): ?>
					<span class="float-start filter-icon"><?php echo HTMLFormatHelper::icon($filter->params->get('core.icon')); ?></span>
				<?php endif; ?>
				<?php echo $filter->label; ?>
				<?php if($filter->params->get('params.filter_descr')): ?>
					<small rel="tooltip" data-original-title="<?php echo JText::_($filter->params->get('params.filter_descr')); ?>"><i class="icon-help"></i></small>
				<?php endif; ?>
			</legend>
			<div class="well well-small<?php echo ($filter->isFilterActive() ? ' active' : NULL) ?>">
				<?php if($filter->isFilterActive()): ?>
					<!-- <img class="filter-close" onclick="Joomcck.cleanFilter('filter_<?php echo $filter->key ?>')" rel="tooltip" data-original-title="<?php echo JText::_('CDELETEFILTER') ?>" src="<?php echo JUri::root(TRUE) ?>/media/com_joomcck/icons/16/cross-circle.png">-->
				<?php endif; ?>
				<?php echo $f; ?>
			</div>
		<?php endif; ?>

	<?php endforeach; ?>

	<input type="hidden" name="option" value="com_joomcck">
	<input type="hidden" name="view" value="records">
	<input type="hidden" name="section_id" value="<?php echo $section->id; ?>">
	<input type="hidden" name="cat_id" value="<?php echo $cat_id; ?>">
	<?php if($user_id > 0): ?>
		<input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
	<?php endif; ?>
	<input type="hidden" name="view_what" value="<?php echo $vw; ?>">
	<input type="hidden" name="task" value="records.filters">
	<input type="hidden" name="limitstart" value="0">

	<div class="form-actions">
		<button type="submit" class="btn btn-primary btn-large">
			<?php echo JText::_('CSEARCH'); ?>
		</button>
	</div>
</form>


