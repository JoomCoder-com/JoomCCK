<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();

$item = $this->item;
$params = $this->tmpl_params['record'];
$icons = array();
$category = array();
$author = array();
$details = array();
$started = FALSE;
$i = $o = 0;


if($params->get('tmpl_core.item_categories') && $item->categories_links)
{
	$category[] = sprintf('<dt>%s<dt> <dd>%s<dd>', (count($item->categories_links) > 1 ? \Joomla\CMS\Language\Text::_('CCATEGORIES') : \Joomla\CMS\Language\Text::_('CCATEGORY')), implode(', ', $item->categories_links));
}
if($params->get('tmpl_core.item_user_categories') && $item->ucatid)
{
	$category[] = sprintf('<dt>%s<dt> <dd>%s<dd>', \Joomla\CMS\Language\Text::_('CUCAT'), $item->ucatname_link);
}
if($params->get('tmpl_core.item_author') && $item->user_id)
{
	$a[] = \Joomla\CMS\Language\Text::sprintf('CWRITTENBY', CCommunityHelper::getName($item->user_id, $this->section));
	if($params->get('tmpl_core.item_author_filter'))
	{
		$a[] = FilterHelper::filterButton('filter_user', $item->user_id, NULL, \Joomla\CMS\Language\Text::sprintf('CSHOWALLUSERREC', CCommunityHelper::getName($item->user_id, $this->section, array('nohtml' => 1))), $this->section);
	}
	$author[] = implode(' ', $a);
}
if($params->get('tmpl_core.item_ctime'))
{
	$author[] = \Joomla\CMS\Language\Text::sprintf('CONDATE', \Joomla\CMS\HTML\HTMLHelper::_('date', $item->created, $params->get('tmpl_core.item_time_format')));
}

if($params->get('tmpl_core.item_mtime'))
{
	$author[] = \Joomla\CMS\Language\Text::_('CMTIME').': '.\Joomla\CMS\HTML\HTMLHelper::_('date', $item->modify, $params->get('tmpl_core.item_time_format'));
}
if($params->get('tmpl_core.item_extime'))
{
	$author[] = \Joomla\CMS\Language\Text::_('CEXTIME').': '.($item->expire ? \Joomla\CMS\HTML\HTMLHelper::_('date', $item->expire, $params->get('tmpl_core.item_time_format')) : \Joomla\CMS\Language\Text::_('CNEVER'));
}

if($params->get('tmpl_core.item_type'))
{
	$details[] = sprintf('%s: %s %s', \Joomla\CMS\Language\Text::_('CTYPE'), $this->type->name, ($params->get('tmpl_core.item_type_filter') ? FilterHelper::filterButton('filter_type', $item->type_id, NULL, \Joomla\CMS\Language\Text::sprintf('CSHOWALLTYPEREC', $this->type->name), $this->section) : NULL));
}
if($params->get('tmpl_core.item_hits'))
{
	$details[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CHITS'), $item->hits);
}
if($params->get('tmpl_core.item_comments_num'))
{
	$details[] = sprintf('%s: %s', \Joomla\CMS\Language\Text::_('CCOMMENTS'), CommentHelper::numComments($this->type, $this->item));
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
<style>
	.dl-horizontal dd {
		margin-bottom: 10px;
	}

.line-brk {
	margin-left: 0px !important;
}
<?php echo $params->get('tmpl_params.css');?>
</style>
<article class="<?php echo $this->appParams->get('pageclass_sfx')?><?php if($item->featured) echo ' article-featured' ?>">

    <?php echo Layout::render('core.single.recordParts.buttonsManage',['current' => $this]) ?>
	<?php echo Layout::render('core.single.recordParts.title',['current' => $this]) ?>

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
				<dd id="<?php echo 'dd-'.$field_id; ?>" class="mb-4 <?php echo $field->fieldclass;?><?php echo ($field->params->get('core.label_break') > 1 ? ' line-brk' : NULL) ?>">
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

                    <?php $firstActive = false; $f = 0; ?>

					<?php foreach ($this->item->fields_by_groups as $group_id => $fields) :?>

                        <?php $active = ($f == 0) ? 'active' : '' ?>

						<li class="nav-item">
							<a class="nav-link <?php echo $active ?>"  href="#tab-<?php echo $o++?>" data-bs-toggle="tab">
								<?php if(!empty($item->field_groups[$group_id]['icon']) && $params->get('tmpl_params.show_groupicon', 1)): ?>
									<?php echo HTMLFormatHelper::icon($item->field_groups[$group_id]['icon']) ?>
								<?php endif; ?>
								<?php echo \Joomla\CMS\Language\Text::_($group_id)?>
							</a>
						</li>
                    <?php $f++ ?>
					<?php endforeach;?>
				<?php endif;?>
			</ul>
	<?php endif;?>

	<?php if(isset($this->item->fields_by_groups)):?>

        <?php $j = 0 ?>

		<?php foreach ($this->item->fields_by_groups as $group_name => $fields) :?>



			<?php $started = true;?>

			<?php group_start($this, $group_name, 'tab-'.$i++,$j);?>
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

			<?php $j++ ?>

            <?php $active = true; ?>

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

	<?php echo Layout::render('core.single.recordParts.tags',['current' => $this]) ?>

	<?php if($category || $author || $details || $params->get('tmpl_core.item_rating')): ?>
		<div class="card article-info p-4">
			<div class="row">
				<?php if($params->get('tmpl_core.item_rating')):?>
					<div class="col-md-4">
						<?php echo $item->rating;?>
					</div>
				<?php endif;?>
				<div class="col-md-<?php echo ($params->get('tmpl_core.item_rating') ? 7 : 11);?>">
					<small>
						<dl class="dl-horizontal user-info">
							<?php if($category):?>
								<?php echo implode(' ', $category);?>
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
				<?php if($params->get('tmpl_core.item_author_avatar')):?>
					<div class="col-md-1 avatar">
						<img class="w-100 rounded shadow-sm" src="<?php echo CCommunityHelper::getAvatar($item->user_id, $params->get('tmpl_core.item_author_avatar_width', 40), $params->get('tmpl_core.item_author_avatar_height', 40));?>" />
					</div>
				<?php endif;?>
			</div>
		</div>
	<?php endif;?>
</article>

<?php if($started):?>
	<script type="text/javascript">
		<?php if(in_array($params->get('tmpl_params.item_grouping_type', 0), array(1))):?>
			//jQuery('#tabs-list a:first').tab('show');
		<?php elseif(in_array($params->get('tmpl_params.item_grouping_type', 0), array(2))):?>
			jQuery('#tab-main').collapse('show');
		<?php endif;?>
	</script>
<?php endif;?>






<?php
function group_start($data, $label, $name,$j = 0)
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

            if( $j == 0){

	            echo '<div class="tab-pane show active" id="'.$name.'">';

            }

            else
	            echo '<div class="tab-pane" id="'.$name.'">';


			break;
		//slider
		case 2:

			HTMLHelper::_('bootstrap.collapse');

			if(!$start)
			{
				echo '<div class="accordion" id="accordion2">';
				$start = TRUE;
			}
			echo '<div class="accordion-group">
				<div class="accordion-heading">
					<a class="accordion-toggle" data-bs-toggle="collapse" data-bs-parent="#accordion2" href="#'.$name.'">
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

