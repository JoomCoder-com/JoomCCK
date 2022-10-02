<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access'); 
$k = $p1 = 0;
$params = $this->tmpl_params['list'];
$total_fields_keys = $this->total_fields_keys;
$fh = new FieldHelper($this->fields_keys_by_id, $this->total_fields_keys);
$exclude = $params->get('tmpl_params.field_id_exclude');
settype($exclude, 'array');
foreach ($exclude as &$value) {
	$value = $this->fields_keys_by_id[$value];
}
JHtml::_('dropdown.init');
?>
<style>
	.avatar {
		text-align: center;
		vertical-align: middle;
	}

	.article-info .container-fluid {
		padding-right: 0px !important;
		padding-left: 0px !important;
	}
	.dl-horizontal dd {
		margin-bottom: 10px;
	}
.input-field-full {
	margin-left: 0px !important;
}
</style>
<?php if($params->get('tmpl_core.show_title_index')):?>
	<h2><?php echo JText::_('CONTHISPAGE')?></h2>
	<ul>
		<?php foreach ($this->items AS $item):?>
			<li><a href="#record<?php echo $item->id?>"><?php echo $item->title?></a></li>
		<?php endforeach;?>
	</ul>
<?php endif;?>

<div>
	<?php foreach ($this->items AS $item):?>
		<?php
		$ll = array();
		?>
		<div class="has-context<?php if($item->featured) echo ' success' ?>">
			<a name="record<?php echo $item->id;?>"></a>
			<?php if($this->user->get('id')):?>
				<div class="float-end controls">
					<div class="btn-group" style="display: none;">
						<?php echo HTMLFormatHelper::bookmark($item, $this->submission_types[$item->type_id], $params);?>
						<?php echo HTMLFormatHelper::follow($item, $this->section);?>
						<?php echo HTMLFormatHelper::repost($item, $this->section);?>
						<?php echo HTMLFormatHelper::compare($item, $this->submission_types[$item->type_id], $this->section);?>
						<?php if($item->controls):?>
							<a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-mini">
								<?php echo HTMLFormatHelper::icon('gear.png');  ?>
							</a>
							<ul class="dropdown-menu">
								<?php echo list_controls($item->controls);?>
							</ul>
						<?php endif;?>
					</div>
				</div>
			<?php endif;?>
			<?php if($params->get('tmpl_core.item_title')):?>
				<?php if($this->submission_types[$item->type_id]->params->get('properties.item_title')):?>
					<div>
						<h2>
							<?php if(in_array($params->get('tmpl_core.item_link'), $this->user->getAuthorisedViewLevels())):?>
								<a <?php echo $item->nofollow ? 'rel="nofollow"' : '';?> href="<?php echo JRoute::_($item->url);?>">
									<?php echo $item->title?>
								</a>
							<?php else:?>
								<?php echo $item->title?>
							<?php endif;?>
							<?php echo CEventsHelper::showNum('record', $item->id);?>
						</h2>
					</div>
				<?php endif;?>
			<?php endif;?>
			<div class="clearfix"></div>

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
				$category[] = sprintf('<dt>%s<dt> <dd>%s<dd>', (count($item->categories_links) > 1 ? JText::_('CCATEGORIES') : JText::_('CCATEGORY')), implode(', ', $item->categories_links));
			}
			if($params->get('tmpl_core.item_user_categories') && $item->ucatid)
			{
				$category[] = sprintf('<dt>%s<dt> <dd>%s<dd>', JText::_('CUSERCAT'), $item->ucatname_link);
			}
			if($params->get('tmpl_core.item_author') && $item->user_id)
			{
				$author[] = JText::sprintf('CWRITTENBY', CCommunityHelper::getName($item->user_id, $this->section));
				if($params->get('tmpl_core.item_author_filter'))
				{
					$author[] = FilterHelper::filterButton('filter_user', $item->user_id, NULL, JText::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($item->user_id, $this->section, array('nohtml' => 1))), $this->section);
				}
			}
			if($params->get('tmpl_core.item_ctime'))
			{
				$author[] = JText::sprintf('CONDATE', JHtml::_('date', $item->created, $params->get('tmpl_core.item_time_format')));
			}

			if($params->get('tmpl_core.item_mtime'))
			{
				$author[] = sprintf('%s: %s', JText::_('CCHANGEON'), JHtml::_('date', $item->modify, $params->get('tmpl_core.item_time_format')));
			}

			if($params->get('tmpl_core.item_extime'))
			{
				$author[] = sprintf('%s: %s', JText::_('CEXPIREON'), $item->expire ? JHtml::_('date', $item->expire, $params->get('tmpl_core.item_time_format')) : JText::_('CNEVER'));
			}

			if($params->get('tmpl_core.item_type'))
			{
				$details[] = sprintf('%s: %s %s', JText::_('CTYPE'), $item->type_name, ($params->get('tmpl_core.item_type_filter') ? FilterHelper::filterButton('filter_type', $item->type_id, NULL, JText::sprintf('CSHOWALLTYPEREC', $item->type_name), $this->section) : NULL));
			}
			if($params->get('tmpl_core.item_hits'))
			{
				$details[] = sprintf('%s: %s', JText::_('CHITS'), $item->hits);
			}
			if($params->get('tmpl_core.item_comments_num'))
			{
				$details[] = sprintf('%s: %s', JText::_('CCOMMENTS'), CommentHelper::numComments($this->submission_types[$item->type_id], $item));
			}
			if($params->get('tmpl_core.item_vote_num'))
			{
				$details[] = sprintf('%s: %s', JText::_('CVOTES'), $item->votes);
			}
			if($params->get('tmpl_core.item_favorite_num'))
			{
				$details[] = sprintf('%s: %s', JText::_('CFAVORITED'), $item->favorite_num);
			}
			if($params->get('tmpl_core.item_follow_num'))
			{
				$details[] = sprintf('%s: %s', JText::_('CFOLLOWERS'), $item->subscriptions_num);
			}
			?>

			<?php if($category || $author || $details || $params->get('tmpl_core.item_rating')): ?>
				<div class="clearfix"></div>

				<div class="well article-info">
					<div class="container-fluid">
						<div class="row-fluid">
							<?php if($params->get('tmpl_core.item_rating')):?>
								<div class="span3">
									<?php echo $item->rating;?>
								</div>
							<?php endif;?>
							<div class="span<?php echo ($params->get('tmpl_core.item_rating') ? 7 : 10);?>">
								<small>
									<dl class="dl-horizontal user-info">
										<?php if($category):?>
											<?php echo implode(' ', $category);?>
										<?php endif;?>
										<?php if($author):?>
											<dt><?php echo JText::_('Posted');?></dt>
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
							<?php if($params->get('tmpl_core.item_author_avatar')):?>
								<div class="span2 avatar">
									<img src="<?php echo CCommunityHelper::getAvatar($item->user_id, $params->get('tmpl_core.item_author_avatar_width', 40), $params->get('tmpl_core.item_author_avatar_height', 40));?>" />
								</div>
							<?php endif;?>
						</div>
					</div>
				</div>
			<?php endif;?>
		</div>
	<?php endforeach;?>
</div>
<div class="clearfix"></div>