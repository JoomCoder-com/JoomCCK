<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

if(!class_exists('CarticleHelper'))
{
	class CarticleHelper
	{

		var $k = 0;

		public function isnext($obj)
		{
			return (isset($obj->items[$this->k]));
		}
		public function display(&$obj)
		{
			if(empty($obj->items[$this->k]))
			{
				return;
			}
			$params = $obj->tmpl_params['list'];
			$item = $obj->items[$this->k];
			unset($obj->items[$this->k++]);
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
						<?php if(in_array($field->key, $this->exclude)) continue; ?>
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
		<?php
		}
	}
}
?>
<?php
$k = 0;
$params = $this->tmpl_params['list'];
$leading = $params->get('tmpl_params.leading', 1);
$cols = $params->get('tmpl_params.blog_cols', 2);
$intro = $params->get('tmpl_params.blog_intro', 6);
$links = $params->get('tmpl_params.blog_links', 5);
$l = 0;
\Joomla\CMS\HTML\HTMLHelper::_('dropdown.init');
$rows = $cols ? ceil($intro / $cols) : 0;
if($rows <= 0) $rows = 0;

$helper = new CarticleHelper();
$helper->k = 0;

$exclude = $params->get('tmpl_params.field_id_exclude');
settype($exclude, 'array');
foreach ($exclude as &$value) {
	$value = $this->fields_keys_by_id[$value];
}
$helper->exclude = $exclude;
?>
<?php if($params->get('tmpl_core.show_title_index')):?>
	<h2><?php echo \Joomla\CMS\Language\Text::_('CONTHISPAGE')?></h2>
	<ul>
		<?php foreach ($this->items AS $item):?>
			<li><a href="#record<?php echo $item->id?>"><?php echo $item->title?></a></li>
		<?php endforeach;?>
	</ul>
<?php endif;?>

<style>
	.dl-horizontal dd {
		margin-bottom: 10px;
	}
	.input-field-full {
		margin-left: 0px !important;
	}
</style>


<?php if($leading && $helper->isnext($this)):?>
	<div class="items-leading">
		<?php for($i = 0; $i < $leading; $i++): ?>
			<div class="leading-<?php echo $i;?>">
				<?php echo $helper->display($this);?>
			</div>
		<?php endfor;?>
	</div>
<?php endif;?>
<div class="clearfix"></div>

<?php if($intro && $helper->isnext($this)):?>
	<?php for($r = 0; $r < $rows; $r++):?>
		<div class="row">
			<?php for($c = 0; $c < $cols; $c++):?>
				<div class="col-md-<?php echo round((12 / $cols));?>">
					<?php echo $helper->display($this);?>
				</div>
			<?php endfor;?>
		</div>
	<?php endfor;?>
<?php endif;?>

<?php if($links && $helper->isnext($this)):?>
<div class="items-more">
	<h3><?php echo \Joomla\CMS\Language\Text::_('CMORERECORDS')?></h3>
	<ul class="nav nav-tabs nav-stacked">
		<?php foreach ($this->items AS $item):?>
			<li class="has-context">
				<div class="float-end controls">
					<div class="btn-group" style="display: none;">
						<?php echo HTMLFormatHelper::bookmark($item, $this->submission_types[$item->type_id], $params);?>
						<?php echo HTMLFormatHelper::follow($item, $this->section);?>
						<?php echo HTMLFormatHelper::repost($item, $this->section);?>
						<?php echo HTMLFormatHelper::compare($item, $this->submission_types[$item->type_id], $this->section);?>
						<?php if($item->controls):?>
							<a href="#" data-bs-toggle="dropdown" class="dropdown-toggle btn btn-sm">
								<?php echo HTMLFormatHelper::icon('gear.png');  ?>
							</a>
							<ul class="dropdown-menu">
								<?php echo list_controls($item->controls);?>
							</ul>
						<?php endif;?>
					</div>
				</div>

				<a <?php echo $item->nofollow ? 'rel="nofollow"' : '';?> href="<?php echo \Joomla\CMS\Router\Route::_($item->url);?>">
					<?php echo $item->title;?>
					<?php echo CEventsHelper::showNum('record', $item->id);?>
				</a>

			</li>
		<?php endforeach;?>
	</ul>
</div>
<?php endif;?>


