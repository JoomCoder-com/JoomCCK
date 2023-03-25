<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
?>
<style>
.filter-label {
	font-size: 1.2em;
	cursor: pointer;
	margin-bottom: 8px;
	margin-top: 8px;
	color: #e76f08;
	border-bottom: 2px dashed #e7af86;
}
#filter-form .collapsed {
	color: #000000;
	border-bottom: 0px solid #e76f08;
}
.filter-icon img {
	margin-bottom: 3px;
}
#filter-form input,
#filter-form select {
	max-width: 99%;
	box-sizing: border-box;
	height: 28px;
}
</style>
<form action="<?php echo JRoute::_('index.php');?>" method="post" name="filterform" id="filter-form">
	<?php if($params->get('filter_search', 1)):?>
		<div class="row <?php echo ( $state->get('records.search')? ' active' : NULL)?>">
			<input type="text" class="col-md-12" name="filter_search" value="<?php echo $state->get('records.search');?>" />
		</div>
	<?php endif;?>

	<?php if(count($f_types) > 1):?>
		<div data-toggle="collapse" data-target="#filter-types" class="filter-label <?php echo ( !!$state->get('records.type') ? NULL : 'collapsed')?>">
			<?php if($params->get('show_icons', 1)):?>
				<span class="filter-icon"><?php echo HTMLFormatHelper::icon('block.png');?></span>
			<?php endif;?>
			<?php echo $params->get('type_label');?>
		</div>
		<div id="filter-types" class="<?php echo ($state->get('records.type') ? 'in' : NULL)?> collapse">
			<?php if($params->get('filter_type_type') == 1):?>
				<?php foreach ($f_types AS $type):?>
					<label class="checkbox">
						<input id="type-<?php echo $type->id?>" type="checkbox" name="filters[type][]" value="<?php echo $type->id?>"<?php echo $type->filter_checked?>>
						<?php echo $type->name;?>
					</label>
				<?php endforeach;?>
			<?php else :?>
				<?php echo JHtml::_('select.genericlist', $f_types, 'filters[type]', null, 'id', 'name', $state->get('records.type'));?>
			<?php endif;?>
		</div>
	<?php endif;?>

	<?php if($params->get('filter_tags_type')):?>
		<div data-toggle="collapse" data-target="#filter-tags" class="filter-label <?php echo ( !!$state->get('records.tag') ? NULL : 'collapsed')?>">
			<?php if($params->get('show_icons', 1)):?>
				<span class="filter-icon"><?php echo HTMLFormatHelper::icon('price-tag.png');?></span>
			<?php endif;?>
			<?php echo $params->get('tag_label');?>
		</div>
		<div id="filter-tags" class="<?php echo ($state->get('records.tag') ? 'in' : NULL)?> collapse">
			<?php if($params->get('filter_tags_type') == 1):?>
				<?php echo JHtml::_('tags.tagform', $section, $state->get('records.tag'));?>
			<?php elseif($params->get('filter_tags_type') == 2):?>
				<?php echo JHtml::_('tags.tagcheckboxes', $section, $state->get('records.tag'));?>
			<?php elseif($params->get('filter_tags_type') == 3):?>
				<?php echo JHtml::_('tags.tagselect', $section, $state->get('records.tag'));?>
			<?php elseif($params->get('filter_tags_type') == 4):?>
				<?php echo JHtml::_('tags.tagpills', $section, $state->get('records.tag'));?>
			<?php endif;?>
		</div>
	<?php endif;?>

	<?php if($params->get('filter_users_type')):?>
		<div data-toggle="collapse" data-target="#filter-users" class="filter-label <?php echo ( !!$state->get('records.user') ? NULL : 'collapsed')?>">
			<?php if($params->get('show_icons', 1)):?>
				<span class="filter-icon"><?php echo HTMLFormatHelper::icon('user.png');?></span>
			<?php endif;?>
			<?php echo $params->get('user_label');?>
		</div>
		<div id="filter-users" class="<?php echo ($state->get('records.user') ? 'in' : NULL)?> collapse">
			<?php if($params->get('filter_users_type') == 1):?>
				<?php echo JHtml::_('cusers.form', $section, $state->get('records.user'));?>
			<?php elseif($params->get('filter_users_type') == 2):?>
				<?php echo JHtml::_('cusers.checkboxes', $section, $state->get('records.user'));?>
			<?php elseif($params->get('filter_users_type') == 3):?>
				<?php echo JHtml::_('cusers.select', $section, $state->get('records.user'));?>
			<?php endif;?>
		</div>
	<?php endif;?>

	<?php if($params->get('filter_category_type')):?>
		<div data-toggle="collapse" data-target="#filter-cats" class="filter-label <?php echo ( !!$state->get('records.category') ? NULL : 'collapsed')?>">
			<?php if($params->get('show_icons', 1)):?>
				<span class="filter-icon"><?php echo HTMLFormatHelper::icon('category.png');?></span>
			<?php endif;?>
			<?php echo $params->get('category_label');?>
		</div>
		<div id="filter-cats" class="<?php echo ($state->get('records.category') ? 'in' : NULL)?> collapse">
			<?php if($params->get('filter_category_type') == 1):?>
				<?php echo JHtml::_('categories.form', $section, $state->get('records.category'), array('empty_cats' => $params->get('filter_empty_cats', 1)));?>
			<?php elseif($params->get('filter_category_type') == 2):?>
				<?php echo JHtml::_('categories.checkboxes', $section, $state->get('records.category'), array('columns' => 3, 'empty_cats' => $params->get('filter_empty_cats', 1)));?>
			<?php elseif($params->get('filter_category_type') == 3):?>
				<?php echo JHtml::_('categories.select', $section, $state->get('records.category'), array('multiple' => 0, 'empty_cats' => $params->get('filter_empty_cats', 1)));?>
			<?php elseif($params->get('filter_category_type') == 4):?>
				<?php echo JHtml::_('categories.select', $section, $state->get('records.category'), array('multiple' => 1, 'size' => 25, 'empty_cats' => $params->get('filter_empty_cats', 1)));?>
			<?php endif;?>
		</div>
	<?php endif;?>

	<?php foreach ($filters AS  $filter):?>
		<?php if(in_array($filter->id, (array)$params->get('field_id_exclude', array()))) continue;?>
		<div data-toggle="collapse" data-target="#filter-<?php echo  $filter->key ?>" class="filter-label <?php echo ($filter->isFilterActive() ? NULL : 'collapsed')?>">
			<?php if($params->get('show_icons', 1) && $filter->params->get('core.icon')):?>
				<span class="filter-icon"><?php echo HTMLFormatHelper::icon($filter->params->get('core.icon'));?></span>
			<?php endif;?>
			<?php echo $filter->label;?>
			<?php if($filter->params->get('params.filter_descr')):?>
				<small rel="tooltip" data-original-title="<?php echo JText::_($filter->params->get('params.filter_descr'));?>"><i class="icon-help"></i></small>
			<?php endif;?>
		</div>
		<div id="filter-<?php echo  $filter->key ?>" class="<?php echo ($filter->isFilterActive() ? 'in' : NULL)?> collapse">
			<?php if($filter->isFilterActive()):?>
				<!-- <img class="filter-close" onclick="Joomcck.cleanFilter('filter_<?php echo $filter->key?>')" rel="tooltip" data-original-title="<?php echo JText::_('CDELETEFILTER')?>" src="<?php echo JUri::root(true)?>/media/mint/icons/16/cross-circle.png">-->
			<?php endif;?>
			<?php echo $filter->onRenderFilter($section, TRUE);?>
		</div>

	<?php endforeach;?>

	<input type="hidden" name="option" value="com_joomcck">
	<input type="hidden" name="view" value="records">
	<input type="hidden" name="section_id" value="<?php echo $section->id;?>">
	<input type="hidden" name="cat_id" value="<?php echo $cat_id;?>">
	<?php if($user_id > 0): ?>
		<input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
	<?php endif; ?>
	<input type="hidden" name="view_what" value="<?php echo $vw;?>">
	<input type="hidden" name="task" value="records.filters">
	<input type="hidden" name="limitstart" value="0">
	<div class="form-actions">
	<button type="submit" class="btn btn-primary btn-large">
		<?php echo JText::_('CSEARCH');?>
	</button>
	</div>
</form>
<script type="text/javascript">
	jQuery('.textboxlist-bit-editable-input').on('focus', function(){
		jQuery(this).closest('.collapse').css('overflow', 'visible');
	}).on('blur', function(){
		jQuery(this).closest('.collapse').css('overflow', 'hidden');
	});
</script>


