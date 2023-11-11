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
\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');
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
<form action="<?php echo \Joomla\CMS\Router\Route::_('index.php'); ?>" method="post" name="filterform" id="filter-form">
	<?php if($params->get('filter_search', 1)): ?>
		<div class="mb-3 <?php echo($state->get('records.search') ? ' active' : NULL) ?>">
			<input type="text" placeholder="<?php echo \Joomla\CMS\Language\Text::_($params->get('search_placeholder','Add your keyword here...')) ?>" class="form-control" name="filter_search" value="<?php echo $state->get('records.search'); ?>"/>
		</div>
	<?php endif; ?>

	<?php if(count($f_types) > 1): ?>
		<div class="mb-3 typesFilter">
            <label class="form-label">
				<?php if($params->get('show_icons', 1)): ?>
                    <span class="float-start filter-icon"><?php echo HTMLFormatHelper::icon('block.png'); ?></span>
				<?php endif; ?>
				<?php echo $params->get('type_label'); ?>
            </label>
            <div class="<?php echo($state->get('records.type') ? ' active' : NULL) ?>">
				<?php if($params->get('filter_type_type') == 1): ?>
					<?php foreach($f_types AS $type): ?>
                        <div class="form-check">
                            <input id="type-<?php echo $type->id ?>" type="checkbox" class="form-check-input" name="filters[type][]" value="<?php echo $type->id ?>"<?php echo $type->filter_checked ?>>
                            <label for="type-<?php echo $type->id ?>" class="form-check-label"><?php echo $type->name; ?></label>
                        </div>
					<?php endforeach; ?>
				<?php else : ?>
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $f_types, 'filters[type]', "class='form-select' ", 'id', 'name', $state->get('records.type')); ?>
				<?php endif; ?>
            </div>
        </div>
	<?php endif; ?>

	<?php if($params->get('filter_tags_type')): ?>
		<div class="mb-3">
            <label class="form-label">
				<?php if($params->get('show_icons', 1)): ?>
                    <span class="float-start filter-icon"><?php echo HTMLFormatHelper::icon('price-tag.png'); ?></span>
				<?php endif; ?>
				<?php echo $params->get('tag_label'); ?>
            </label>
            <div class="<?php echo($state->get('records.tag') ? ' active' : NULL) ?>">
				<?php if($params->get('filter_tags_type') == 1): ?>
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('tags.tagform', $section, $state->get('records.tag'), array('id' => 'module_tags')); ?>
				<?php elseif($params->get('filter_tags_type') == 2): ?>
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('tags.tagcheckboxes', $section, $state->get('records.tag')); ?>
				<?php elseif($params->get('filter_tags_type') == 3): ?>
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('tags.tagselect', $section, $state->get('records.tag')); ?>
				<?php elseif($params->get('filter_tags_type') == 4): ?>
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('tags.tagpills', $section, $state->get('records.tag')); ?>
				<?php endif; ?>
            </div>
        </div>
	<?php endif; ?>

	<?php if($params->get('filter_users_type')): ?>
		<div class="mb-3">
            <label class="form-label">
				<?php if($params->get('show_icons', 1)): ?>
                    <span class="float-start filter-icon"><?php echo HTMLFormatHelper::icon('user.png'); ?></span>
				<?php endif; ?>
				<?php echo $params->get('user_label'); ?>
            </label>
            <div class="<?php echo($state->get('records.user') ? ' active' : NULL) ?>">
				<?php if($params->get('filter_users_type') == 1): ?>
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('cusers.form', $section, $state->get('records.user'), array('id' => 'module_users')); ?>
				<?php elseif($params->get('filter_users_type') == 2): ?>
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('cusers.checkboxes', $section, $state->get('records.user')); ?>
				<?php elseif($params->get('filter_users_type') == 3): ?>
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('cusers.select', $section, $state->get('records.user')); ?>
				<?php endif; ?>
            </div>
        </div>
	<?php endif; ?>

	<?php if($params->get('filter_category_type')): ?>
		<div class="mb-3">
            <label class="form-label">
				<?php if($params->get('show_icons', 1)): ?>
                    <span class="float-start filter-icon"><?php echo HTMLFormatHelper::icon('category.png'); ?></span>
				<?php endif; ?>
				<?php echo $params->get('category_label'); ?>
            </label>
            <div class="<?php echo($state->get('records.category') ? ' active' : NULL) ?>">
				<?php if($params->get('filter_category_type') == 1): ?>
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('categories.form', $section, $state->get('records.category'), array('empty_cats' => $params->get('filter_empty_cats', 1))); ?>
				<?php elseif($params->get('filter_category_type') == 2): ?>
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('categories.checkboxes', $section, $state->get('records.category'), array('columns' => 3, 'empty_cats' => $params->get('filter_empty_cats', 1))); ?>
				<?php elseif($params->get('filter_category_type') == 3): ?>
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('categories.select', $section, $state->get('records.category'), array('multiple' => 0, 'empty_cats' => $params->get('filter_empty_cats', 1))); ?>
				<?php elseif($params->get('filter_category_type') == 4): ?>
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('categories.select', $section, $state->get('records.category'), array('multiple' => 1, 'size' => 25, 'empty_cats' => $params->get('filter_empty_cats', 1))); ?>
				<?php endif; ?>
            </div>
        </div>
	<?php endif; ?>

	<?php foreach($filters AS $filter): ?>
		<?php if(in_array($filter->id, (array)$params->get('field_id_exclude', array())))
			continue; ?>
		<?php $f = trim((string) $filter->onRenderFilter($section, ($params->get('filter_fields_template') == 'section' ? FALSE : TRUE))) ?>
		<?php if($f): ?>
			<div class="mb-3">
                <label class="form-label">
					<?php if($params->get('show_icons', 1) && $filter->params->get('core.icon')): ?>
                        <span class="float-start filter-icon"><?php echo HTMLFormatHelper::icon($filter->params->get('core.icon')); ?></span>
					<?php endif; ?>
					<?php echo $filter->label; ?>
					<?php if($filter->params->get('params.filter_descr')): ?>
                        <small rel="tooltip" title="<?php echo \Joomla\CMS\Language\Text::_($filter->params->get('params.filter_descr')); ?>"><i class="fas fa-info-circle text-muted"></i></small>
					<?php endif; ?>
                </label>
                <div class="<?php echo ($filter->isFilterActive() ? ' active' : NULL) ?>">
					<?php if($filter->isFilterActive()): ?>
                        <!-- <img class="filter-close" onclick="Joomcck.cleanFilter('filter_<?php echo $filter->key ?>')" rel="tooltip" data-bs-title="<?php echo \Joomla\CMS\Language\Text::_('CDELETEFILTER') ?>" src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE) ?>/media/com_joomcck/icons/16/cross-circle.png">-->
					<?php endif; ?>
					<?php echo $f; ?>
                </div>
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
		<button type="submit" class="btn btn-outline-success">
			<i class="fas fa-search"></i> <?php echo \Joomla\CMS\Language\Text::_('CSEARCH'); ?>
		</button>
	</div>
</form>


