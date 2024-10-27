<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;

defined('_JEXEC') or die('Restricted access'); ?>
<?php
$cols = count($this->items);
$items = $this->items;
$r = 0;
$params = $this->tmpl_params['list'];
$column = array();
\Joomla\CMS\HTML\HTMLHelper::_('dropdown.init');
$exclude = $params->get('tmpl_params.field_id_exclude');
settype($exclude, 'array');
foreach ($exclude as &$value) {
	$value = $this->fields_keys_by_id[$value];
}
$width = (100 - (int)$params->get('tmpl_params.lbl_width', 15)) / $cols;
?>
<style>
.table th {
	font-weight: bolder;
	border-right: 1px dashed whitesmoke;
	background-color: whitesmoke;
	white-space: nowrap;
}
.relative_ctrls {
	position: relative;
}
.user-ctrls {
	position: absolute;
	top:-19px;
	right: 0;
}
</style>
<?php if($params->get('tmpl_core.show_title_index')):?>
	<h2><?php echo \Joomla\CMS\Language\Text::_('CONTHISPAGE')?></h2>
	<ul>
		<?php foreach ($this->items AS $item):?>
			<li><a href="#record<?php echo $item->id?>"><?php echo $item->title?></a></li>
		<?php endforeach;?>
	</ul>
<?php endif;?>


<table class="table table-hover">
	<tr>
		<th width="<?php echo (int)$params->get('tmpl_params.lbl_width', 15); ?>%">
			<?php if($params->get('tmpl_params.item_icon_title')):?>
				<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE)?>/media/com_joomcck/icons/16/<?php echo $params->get('tmpl_params.item_icon_title_icon', 'edit.png')?>" align="absmiddle">
			<?php endif; ?>
			<?php echo  \Joomla\CMS\Language\Text::_($params->get('tmpl_params.lbl_title', 'CTITLE')); ?>
		</th>
		<?php for ($i=0; $i< $cols; $i++): ?>
			<td class="has-context" width="<?php echo $width; ?>%">
				<div class="relative_ctrls">
					<?php echo Layout::render(
						'core.list.recordParts.buttonsManage',
						['item' => $item,'disabled' => ['compare'], 'section' => $this->section, 'submissionTypes' => $this->submission_types, "params" => $params]) ?>
				<?php if($this->submission_types[$items[$i]->type_id]->params->get('properties.item_title')):?>
					<<?php echo $params->get('tmpl_params.title_tag', 'h2')?>>
						<?php if($params->get('tmpl_core.item_link')):?>
							<a <?php echo $items[$i]->nofollow ? 'rel="nofollow"' : '';?> href="<?php echo \Joomla\CMS\Router\Route::_($items[$i]->url);?>">
								<?php echo $items[$i]->title?>
							</a>
						<?php else :?>
							<?php echo $items[$i]->title?>
						<?php endif;?>
						<?php echo CEventsHelper::showNum('record', $items[$i]->id);?>
					</<?php echo $params->get('tmpl_params.title_tag', 'h2')?>>
				<?php endif;?>
				</div>
			</td>
		<?php endfor; ?>
	</tr>

	<?php if($params->get('tmpl_core.item_author')):?>
		<tr>
			<th>
				<?php if($params->get('tmpl_params.item_icon_author')):?>
					<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE);?>/media/com_joomcck/icons/16/user.png" align="absmiddle" />
				<?php endif;?>
				<?php echo \Joomla\CMS\Language\Text::_('CAUTHOR');?>
			</th>
			<?php for ($i=0; $i < $cols; $i++): ?>
				<td valign="middle">
					<?php if($params->get('tmpl_core.item_author_avatar') == 1):?>
						<img align="absmiddle" src="<?php echo CCommunityHelper::getAvatar($items[$i]->user_id, $params->get('tmpl_core.item_author_avatar_width', 20), $params->get('tmpl_core.item_author_avatar_height', 20));?>" />
					<?php endif;?>
					<?php echo CCommunityHelper::getName($items[$i]->user_id, $this->section);?>
					<?php if($params->get('tmpl_core.item_author_filter') && $items[$i]->user_id):?>
						<?php echo FilterHelper::filterButton('filter_user', $items[$i]->user_id, NULL, \Joomla\CMS\Language\Text::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($items[$i]->user_id, $this->section, array('nohtml' => 1))), $this->section);?>
					<?php endif;?>
				</td>
			<?php endfor; ?>
		</TR>
	<?php endif;?>

	<?php if($params->get('tmpl_core.item_rating')):?>
		<tr>
			<th>
				<?php if($params->get('tmpl_params.item_icon_rating')):?>
					<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE);?>/media/com_joomcck/icons/16/star.png" align="absmiddle" />
				<?php endif;?>
				<?php echo \Joomla\CMS\Language\Text::_('CRATING');?>
			</th>
			<?php for ($i=0; $i < $cols; $i++): ?>
				<td nowrap="nowrap"><?php echo $items[$i]->rating;?></td>
			<?php endfor; ?>
		</TR>
	<?php endif;?>

	<?php if($params->get('tmpl_core.item_type')):?>
		<tr>
			<th>
				<?php if($params->get('tmpl_params.item_icon_type')):?>
					<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE);?>/media/com_joomcck/icons/16/block.png" align="absmiddle" />
				<?php endif;?>
				<?php echo \Joomla\CMS\Language\Text::_('CTYPE');?>
			</th>
			<?php for ($i=0; $i < $cols; $i++): ?>
				<td nowrap="nowrap">
					<?php echo $items[$i]->type_name;?>
					<?php if($params->get('tmpl_core.item_type_filter')):?>
						<?php echo FilterHelper::filterButton('filter_type', $items[$i]->type_id, NULL, \Joomla\CMS\Language\Text::sprintf('CSHOWALLTYPEREC', $items[$i]->type_name), $this->section);?>
					<?php endif;?>
				</td>
			<?php endfor; ?>
		</TR>
	<?php endif;?>

	<?php if($params->get('tmpl_core.item_user_categories') && $this->section->params->get('personalize.pcat_submit')):?>
		<tr>
			<th>
				<?php if($params->get('tmpl_params.item_icon_user_categories')):?>
					<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE);?>/media/com_joomcck/icons/16/category.png" align="absmiddle" />
				<?php endif;?>
				<?php echo \Joomla\CMS\Language\Text::_($params->get('tmpl_params.lbl_category', 'CCATEGORY'));?>
			</th>
			<?php for ($i=0; $i < $cols; $i++): ?>
				<td nowrap="nowrap">
					<?php echo $items[$i]->ucatname;?>
				</td>
			<?php endfor; ?>
		</TR>
	<?php endif;?>

	<?php if($params->get('tmpl_core.item_categories')):?>
		<tr>
			<th>
				<?php if($params->get('tmpl_params.item_icon_categories')):?>
					<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE);?>/media/com_joomcck/icons/16/category.png" align="absmiddle" />
				<?php endif;?>
				<?php echo \Joomla\CMS\Language\Text::_('CCATEGORY');?>
			</th>
			<?php for ($i=0; $i < $cols; $i++): ?>
				<td>
					<?php echo implode(', ', $items[$i]->categories_links);?>
				</td>
			<?php endfor; ?>
		</TR>
	<?php endif;?>

	<?php if($params->get('tmpl_core.item_ctime')):?>
		<tr>
			<th>
				<?php if($params->get('tmpl_params.item_icon_ctime')):?>
					<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE);?>/media/com_joomcck/icons/16/calendar-day.png" align="absmiddle" />
				<?php endif;?>
				<?php echo \Joomla\CMS\Language\Text::_('CCREATED');?>
			</th>
			<?php for ($i=0; $i < $cols; $i++): ?>
				<td nowrap="nowrap">
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('date', $items[$i]->created, $params->get('tmpl_core.item_time_format'));?>
				</td>
			<?php endfor; ?>
		</TR>
	<?php endif;?>

	<?php if($params->get('tmpl_core.item_mtime')):?>
		<tr>
			<th>
				<?php if($params->get('tmpl_params.item_icon_mtime')):?>
					<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE);?>/media/com_joomcck/icons/16/calendar-day.png" align="absmiddle" />
				<?php endif;?>
				<?php echo \Joomla\CMS\Language\Text::_('CCHANGED');?>
			</th>
			<?php for ($i=0; $i < $cols; $i++): ?>
				<td nowrap="nowrap">
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('date', $items[$i]->modify, $params->get('tmpl_core.item_time_format'));?>
				</td>
			<?php endfor; ?>
		</TR>
	<?php endif;?>

	<?php if($params->get('tmpl_core.item_extime')):?>
		<tr>
			<th>
				<?php if($params->get('tmpl_params.item_icon_mtime')):?>
					<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE);?>/media/com_joomcck/icons/16/calendar-day.png" align="absmiddle" />
				<?php endif;?>
				<?php echo \Joomla\CMS\Language\Text::_('CEXPIRE');?>
			</th>
			<?php for ($i=0; $i < $cols; $i++): ?>
				<td nowrap="nowrap">
					<?php echo ( $items[$i]->expire ? \Joomla\CMS\HTML\HTMLHelper::_('date', $items[$i]->expire, $params->get('tmpl_core.item_time_format')) : \Joomla\CMS\Language\Text::_('CNEVER'));?>
				</td>
			<?php endfor; ?>
		</TR>
	<?php endif;?>

	<?php if($params->get('tmpl_core.item_comments_num')):?>
		<tr>
			<th>
				<?php if($params->get('tmpl_params.item_icon_comments_num')):?>
					<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE);?>/media/com_joomcck/icons/16/balloon-left.png" align="absmiddle" />
				<?php endif;?>
				<?php echo \Joomla\CMS\Language\Text::_('CCOMMENTS');?>
			</th>
			<?php for ($i=0; $i < $cols; $i++): ?>
				<td nowrap="nowrap" align="center">
					<?php echo CommentHelper::numComments($this->submission_types[$items[$i]->type_id], $items[$i]);?>
				</td>
			<?php endfor; ?>
		</TR>
	<?php endif;?>

	<?php if($params->get('tmpl_core.item_vote_num')):?>
		<tr>
			<th>
				<?php if($params->get('tmpl_params.item_icon_vote_num')):?>
					<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE);?>/media/com_joomcck/icons/16/star.png" align="absmiddle" />
				<?php endif;?>
				<?php echo \Joomla\CMS\Language\Text::_('CVOTES');?>
			</th>
			<?php for ($i=0; $i < $cols; $i++): ?>
				<td nowrap="nowrap" align="center">
					<?php echo $items[$i]->votes;?>
				</td>
			<?php endfor; ?>
		</TR>
	<?php endif;?>

	<?php if($params->get('tmpl_core.item_favorite_num')):?>
		<tr>
			<th>
				<?php if($params->get('tmpl_params.item_icon_favorite_num')):?>
					<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE);?>/media/com_joomcck/icons/16/star.png" align="absmiddle" />
				<?php endif;?>
				<?php echo \Joomla\CMS\Language\Text::_('CFAVORITE');?>
			</th>
			<?php for ($i=0; $i < $cols; $i++): ?>
				<td nowrap="nowrap" align="center">
					<?php echo $items[$i]->favorite_num;?>
				</td>
			<?php endfor; ?>
		</TR>
	<?php endif;?>

	<?php if($params->get('tmpl_core.item_follow_num')):?>
		<tr>
			<th>
				<?php if($params->get('tmpl_params.item_icon_follow_num')):?>
					<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE);?>/media/com_joomcck/icons/16/follow1.png" align="absmiddle" />
				<?php endif;?>
				<?php echo \Joomla\CMS\Language\Text::_('CFOLLOWERS');?>
			</th>
			<?php for ($i=0; $i < $cols; $i++): ?>
				<td nowrap="nowrap" align="center">
					<?php echo $items[$i]->subscriptions_num;?>
				</td>
			<?php endfor; ?>
		</TR>
	<?php endif;?>

	<?php if($params->get('tmpl_core.item_hits')):?>
		<tr>
			<th>
				<?php if($params->get('tmpl_params.item_icon_hits')):?>
					<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE);?>/media/com_joomcck/icons/16/hand-point-090.png" align="absmiddle" />
				<?php endif;?>
				<?php echo \Joomla\CMS\Language\Text::_('CHITS');?>
			</th>
			<?php for ($i=0; $i < $cols; $i++): ?>
				<td nowrap="nowrap" align="center">
					<?php echo $items[$i]->hits;?>
				</td>
			<?php endfor; ?>
		</TR>
	<?php endif;?>

	<?php foreach ($this->total_fields_keys AS $key => $field):?>
		<?php if(in_array($field->key, $exclude)) continue; ?>
		<tr>
			<th>
				<?php if($field->params->get('core.icon') && $params->get('tmpl_params.item_icon_fields')):?>
					<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE);?>/media/com_joomcck/icons/16/<?php echo $field->params->get('core.icon');?>" align="absmiddle" />
				<?php endif;?>
				<?php echo \Joomla\CMS\Language\Text::_($field->label);?>
			</th>
			<?php for ($i=0; $i < $cols; $i++): ?>
				<?php if(isset($items[$i]->fields_by_key[$key])):?>
					<td class="<?php echo $field->params->get('core.field_class')?>">
						<?php echo $items[$i]->fields_by_key[$key]->result;?>
					</td>
				<?php else:?>
					<td nowrap="nowrap" align="center">
					- -
					</td>
				<?php endif;?>
			<?php endfor; ?>

		</tr>
	<?php endforeach;?>
</table>