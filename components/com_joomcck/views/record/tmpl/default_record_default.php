<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

$item = $this->item;
$params = $this->tmpl_params['record'];
$icons = array();
$category = array();
$author = array();
$details = array();
$started = FALSE;
$i = $o = 0;

?>
<style>
	.dl-horizontal dd {
		margin-bottom: 10px;
	}

.line-brk {
	margin-left: 0px !important;
}
<?php echo $params->get('tmpl_params.css');?>
</style>

<?php
if($params->get('tmpl_core.item_categories') && $item->categories_links)
{
	$category[] = sprintf('<dt>%s<dt> <dd>%s<dd>', (count($item->categories_links) > 1 ? JText::_('CCATEGORIES') : JText::_('CCATEGORY')), implode(', ', $item->categories_links));
}
if($params->get('tmpl_core.item_user_categories') && $item->ucatid)
{
	$category[] = sprintf('<dt>%s<dt> <dd>%s<dd>', JText::_('CUCAT'), $item->ucatname_link);
}
if($params->get('tmpl_core.item_author') && $item->user_id)
{
	$a[] = JText::sprintf('CWRITTENBY', CCommunityHelper::getName($item->user_id, $this->section));
	if($params->get('tmpl_core.item_author_filter'))
	{
		$a[] = FilterHelper::filterButton('filter_user', $item->user_id, NULL, JText::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($item->user_id, $this->section, array('nohtml' => 1))), $this->section);
	}
	$author[] = implode(' ', $a);
}
if($params->get('tmpl_core.item_ctime'))
{
	$author[] = JText::sprintf('CONDATE', JHtml::_('date', $item->created, $params->get('tmpl_core.item_time_format')));
}

if($params->get('tmpl_core.item_mtime'))
{
	$author[] = JText::_('CMTIME').': '.JHtml::_('date', $item->modify, $params->get('tmpl_core.item_time_format'));
}
if($params->get('tmpl_core.item_extime'))
{
	$author[] = JText::_('CEXTIME').': '.($item->expire ? JHtml::_('date', $item->expire, $params->get('tmpl_core.item_time_format')) : JText::_('CNEVER'));
}

if($params->get('tmpl_core.item_type'))
{
	$details[] = sprintf('%s: %s %s', JText::_('CTYPE'), $this->type->name, ($params->get('tmpl_core.item_type_filter') ? FilterHelper::filterButton('filter_type', $item->type_id, NULL, JText::sprintf('CSHOWALLTYPEREC', $this->type->name), $this->section) : NULL));
}
if($params->get('tmpl_core.item_hits'))
{
	$details[] = sprintf('%s: %s', JText::_('CHITS'), $item->hits);
}
if($params->get('tmpl_core.item_comments_num'))
{
	$details[] = sprintf('%s: %s', JText::_('CCOMMENTS'), CommentHelper::numComments($this->type, $this->item));
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

<article class="<?php echo $this->appParams->get('pageclass_sfx')?><?php if($item->featured) echo ' article-featured' ?>">
	<?php if(!$this->print):?>
		<div class="pull-right controls">
			<div class="btn-group">
				<?php if($params->get('tmpl_core.item_print')):?>
					<a class="btn btn-mini" onclick="window.open('<?php echo JRoute::_($this->item->url.'&tmpl=component&print=1');?>','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;">
						<?php echo HTMLFormatHelper::icon('printer.png', JText::_('CPRINT'));  ?></a>
				<?php endif;?>

				<?php if($this->user->get('id')):?>
					<?php echo HTMLFormatHelper::bookmark($item, $this->type, $params);?>
					<?php echo HTMLFormatHelper::follow($item, $this->section);?>
					<?php echo HTMLFormatHelper::repost($item, $this->section);?>
					<?php if($item->controls):?>
						<a href="#" data-toggle="dropdown" class="dropdown-toggle btn btn-mini">
							<?php echo HTMLFormatHelper::icon('gear.png');  ?></a>
						<ul class="dropdown-menu">
							<?php echo list_controls($item->controls);?>
						</ul>
					<?php endif;?>
				<?php endif;?>
			</div>
		</div>
	<?php else:?>
		<div class="pull-right controls">
			<a href="#" class="btn btn-mini" onclick="window.print();return false;"><?php echo HTMLFormatHelper::icon('printer.png', JText::_('CPRINT'));  ?></a>
		</div>
	<?php endif;?>
	<?php if($params->get('tmpl_core.item_title')):?>
		<?php if($this->type->params->get('properties.item_title')):?>
			<div class="page-header">
				<<?php echo $params->get('tmpl_params.title_tag', 'h1')?>>
					<?php echo $item->title?>
					<?php echo CEventsHelper::showNum('record', $item->id);?>
				</<?php echo $params->get('tmpl_params.title_tag', 'h1')?>>
			</div>
		<?php endif;?>
	<?php endif;?>
	<div class="clearfix"></div>

	<?php if(isset($this->item->fields_by_groups[null])):?>
		<dl class="dl-horizontal fields-list">
			<?php foreach ($this->item->fields_by_groups[null] as $field_id => $field):?>
				<dt id="<?php echo 'dt-'.$field_id; ?>" class="<?php echo $field->class;?>">
					<?php if($field->params->get('core.show_lable') > 1):?>
						<label id="<?php echo $field->id;?>-lbl">
							<?php echo $field->label; ?>
							<?php if($field->params->get('core.icon')):?>
								<?php echo HTMLFormatHelper::icon($field->params->get('core.icon'));  ?>
							<?php endif;?>
						</label>
						<?php if($field->params->get('core.label_break') > 1):?>
						<?php endif;?>
					<?php endif;?>
				</dt>
				<dd id="<?php echo 'dd-'.$field_id; ?>" class="<?php echo $field->fieldclass;?><?php echo ($field->params->get('core.label_break') > 1 ? ' line-brk' : NULL) ?>">
					<?php echo $field->result; ?>
				</dd>
			<?php endforeach;?>
		</dl>
		<?php unset($this->item->fields_by_groups[null]);?>
	<?php endif;?>

	<?php if(in_array($params->get('tmpl_params.item_grouping_type', 0), array(1)) && count($this->item->fields_by_groups)):?>
		<div class="clearfix"></div>
		<div class="tabbable <?php echo $params->get('tmpl_params.tabs_position');  ?>">
			<ul class="nav <?php echo $params->get('tmpl_params.tabs_style', 'nav-tabs');  ?>" id="tabs-list">
				<?php if(isset($this->item->fields_by_groups)):?>
					<?php foreach ($this->item->fields_by_groups as $group_id => $fields) :?>
						<li>
							<a href="#tab-<?php echo $o++?>" data-toggle="tab">
								<?php if(!empty($item->field_groups[$group_id]['icon']) && $params->get('tmpl_params.show_groupicon', 1)): ?>
									<?php echo HTMLFormatHelper::icon($item->field_groups[$group_id]['icon']) ?>
								<?php endif; ?>
								<?php echo JText::_($group_id)?>
							</a>
						</li>
					<?php endforeach;?>
				<?php endif;?>
			</ul>
	<?php endif;?>

	<?php if(isset($this->item->fields_by_groups)):?>
		<?php foreach ($this->item->fields_by_groups as $group_name => $fields) :?>
			<?php $started = true;?>
			<?php group_start($this, $group_name, 'tab-'.$i++);?>
			<dl class="dl-horizontal fields-list fields-group<?php echo $i;?>">
				<?php foreach ($fields as $field_id => $field):?>
					<dt id="<?php echo 'dt-'.$field_id; ?>" class="<?php echo $field->class;?>">
						<?php if($field->params->get('core.show_lable') > 1):?>
							<label id="<?php echo $field->id;?>-lbl">
								<?php echo $field->label; ?>
								<?php if($field->params->get('core.icon')):?>
									<?php echo HTMLFormatHelper::icon($field->params->get('core.icon'));  ?>
								<?php endif;?>
							</label>
							<?php if($field->params->get('core.label_break') > 1):?>
							<?php endif;?>
						<?php endif;?>
					</dt>
					<dd id="<?php echo 'dd-'.$field_id; ?>" class="<?php echo $field->fieldclass;?><?php echo ($field->params->get('core.label_break') > 1 ? ' line-brk' : NULL) ?>">
						<?php echo $field->result; ?>
					</dd>
				<?php endforeach;?>
			</dl>
			<?php group_end($this);?>
		<?php endforeach;?>
	<?php endif;?>

	<?php if($started):?>
		<?php total_end($this);?>
	<?php endif;?>

	<?php if(in_array($params->get('tmpl_params.item_grouping_type', 0), array(1))  && count($this->item->fields_by_groups)):?>
		</div>
		<div class="clearfix"></div>
		<br />
	<?php endif;?>

	<?php echo $this->loadTemplate('tags');?>

	<?php if($category || $author || $details || $params->get('tmpl_core.item_rating')): ?>
		<div class="well article-info">
			<div class="row-fluid">
				<?php if($params->get('tmpl_core.item_rating')):?>
					<div class="span2">
						<?php echo $item->rating;?>
					</div>
				<?php endif;?>
				<div class="span<?php echo ($params->get('tmpl_core.item_rating') ? 8 : 10);?>">
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
	<?php endif;?>
</article>

<?php if($started):?>
	<script type="text/javascript">
		<?php if(in_array($params->get('tmpl_params.item_grouping_type', 0), array(1))):?>
			jQuery('#tabs-list a:first').tab('show');
		<?php elseif(in_array($params->get('tmpl_params.item_grouping_type', 0), array(2))):?>
			jQuery('#tab-main').collapse('show');
		<?php endif;?>
	</script>
<?php endif;?>






<?php
function group_start($data, $label, $name)
{
	static $start = false;
	$icon = '';
	if(!empty($data->item->field_groups[$label]['icon']) && $data->tmpl_params['record']->get('tmpl_params.show_groupicon', 1)) {
		$icon = HTMLFormatHelper::icon($data->item->field_groups[$label]['icon']);
	}
	switch ($data->tmpl_params['record']->get('tmpl_params.item_grouping_type', 0))
	{
		//tab
		case 1:
			if(!$start)
			{
				echo '<div class="tab-content" id="tabs-box">';
				$start = TRUE;
			}
			echo '<div class="tab-pane" id="'.$name.'">';
			break;
		//slider
		case 2:
			if(!$start)
			{
				echo '<div class="accordion" id="accordion2">';
				$start = TRUE;
			}
			echo '<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#'.$name.'">
					     '.$icon. ' '. $label.'
					</a>
				</div>
				<div id="'.$name.'" class="accordion-body collapse">
					<div class="accordion-inner">';
			break;
		// fieldset
		case 3:
			echo "<legend>{$icon} {$label}</legend>";
		break;
	}

	if($data->tmpl_params['record']->get('tmpl_params.show_groupdescr') && !empty($data->item->field_groups[$label]['descr']))
	{
		echo $data->item->field_groups[$label]['descr'];
	}
}

function group_end($data)
{
	switch ($data->tmpl_params['record']->get('tmpl_params.item_grouping_type', 0))
	{
		case 1:
			echo '</div>';
		break;
		case 2:
			echo '</div></div></div>';
		break;
	}
}

function total_end($data)
{
	switch ($data->tmpl_params['record']->get('tmpl_params.item_grouping_type', 0))
	{
		//tab
		case 1:
			echo '</div>';
		break;
		case 2:
			echo '</div>';
		break;
	}
}

