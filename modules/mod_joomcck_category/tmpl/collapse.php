<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
if(!function_exists('mod_getChildsDef')) { function mod_getChildsDef($category, $params, $parents, $k = 1) {
	if($params->get('mode', 0) != 2) return;

	$model = \Joomla\CMS\MVC\Model\BaseDatabaseModel::getInstance('Categories', 'joomcckModel');

	$model->section = $params->get('section_id');
	$model->parent_id = $category->id;
	$model->order = $params->get('order') == 1 ? 'c.lft ASC' : 'c.title ASC';
	$model->levels = $category->level + 1;
	$model->all = 0;
	$model->nums = $params->get('cat_nums', 'current');
	$list = $model->getItems();
	if(!$list) return;
	?>

	<style>
        .zmodstyle {
            background: #fff;
            -webkit-box-shadow: 0 1px 2px 0 rgba(0,0,0,.16);
            -moz-box-shadow: 0 1px 2px 0 rgba(0,0,0,.16);
            box-shadow: 0 1px 2px 0 rgba(0,0,0,.16);
            padding: 5px 12px;
            border-radius: 4px;
            position: relative;
            margin-bottom: 5px;
        }

        .zaccordion {
            margin: 1rem 0;
            padding: 0;
            list-style: none;
        }
        .zaccordion a{
            text-decoration: none;
        }

        .zchildcatblock {
            background: transparent;
            border: 0px;
        }
	</style>

	<div class="collapse" id="collapseCategory<?php echo $category->id?>">
		<div class="card card-body zchildcatblock">
			<ul class="">
				<?php foreach($list as $cat ) :
					if (!$params->get('cat_empty', 1) && !$cat->records_num) continue;
					$class = array();
					$class[] = 'item-'.$cat->id;
					if(in_array($cat->id, $parents))
						$class[] = 'active';
					if($cat->childs_num)
						$class[] = 'parent';
					?>
					<li class="<?php echo implode(' ', $class);?>">

						<a href="<?php echo \Joomla\CMS\Router\Route::_($cat->link)?>">
							<?php echo \Joomla\CMS\Language\Text::_($cat->title);?>
							<?php if($params->get('cat_nums', 0)):?>
								<span class="badge bg-primary "><?php echo $cat->records_num; ?></span>
							<?php endif;?>
						</a>
						<?php if(in_array($cat->id, $parents) && $cat->childs_num):?>
							<?php mod_getChildsDef($cat, $params, $parents, $k + 1);?>
						<?php endif;?>
					</li>
				<?php endforeach;?>
			</ul>
		</div>
	</div>
<?php }}?>
<div>

	<?php if ( $headerText ) : ?>
		<div class="js_cc"><?php echo $headerText; ?></div>
	<?php endif; ?>

	<?php if($cat_id && !$params->get('init_cat')): ?>
		<p class="<?php echo $params->get('section_class');?>">
		<ul class="list-group">
			<li class="list-group-item">
				<a href="<?php echo \Joomla\CMS\Router\Route::_($section->link);?>">
					<?php echo HTMLFormatHelper::icon($section->params->get('personalize.text_icon', 'home.png'));?>
					<?php echo $section->name;?></a>
			</li>
			<?php $category = ItemsStore::getCategory($cat_id); ?>
			<?php if($category->parent_id > 1): ?>
				<li class="list-group-item">
					<?php $category = ItemsStore::getCategory($category->parent_id); ?>
					<a href="<?php echo \Joomla\CMS\Router\Route::_(Url::records($section, $category)); ?>">
						<?php echo HTMLFormatHelper::icon('arrow-180.png');?>
						<?php echo  \Joomla\CMS\Language\Text::_($category->title); ?></a>
				</li>
			<?php endif;?>
		</ul>
		</p>
	<?php endif; ?>

	<ul class="zaccordion">
		<?php foreach ($categories as $cat) :
			if (!$params->get('cat_empty', 1) && !$cat->records_num) continue;
			$class = array();
			$class[] = 'item-'.$cat->id;
			if($cat->id ==  \Joomla\CMS\Factory::getApplication()->input->getInt('cat_id',0) && \Joomla\CMS\Factory::getApplication()->input->getWord('option','') == 'com_joomcck' && \Joomla\CMS\Factory::getApplication()->input->getInt('section_id') == $params->get('section_id'))
				$class[] = 'active';
			if($cat->childs_num)
				$class[] = 'parent';
			?>
			<li class=" <?php echo implode(' ', $class);?>">
				<?php if($cat->childs_num):?>
				<div class="zmodstyle" data-bs-toggle="collapse" href="#collapseCategory<?php echo $cat->id ?>" role="button" aria-expanded="false" aria-controls="collapseCategory<?php echo $cat->id ?>">
					<?php else: ?>
					<div class="zmodstyle">
						<?php endif;?>
						<a  href="<?php echo \Joomla\CMS\Router\Route::_($cat->link)?>">
							<?php echo \Joomla\CMS\Language\Text::_($cat->title);?>
							<?php if($params->get('cat_nums', 0)):?>
								<span class="badge bg-primary float-end"><?php echo $cat->records_num; ?></span>
							<?php endif;?>
						</a>
						<?php if($cat->childs_num):?>
					</div>
				<?php endif;?>
					<?php if($cat->childs_num):?>
						<?php mod_getChildsDef($cat, $params, $parents);?>
					<?php endif;?>
			</li>
		<?php endforeach;?>
		<?php if($params->get('records') && $section->records):
			foreach ($section->records as $i => $rec):
				if($params->get('records_limit') && $i == $params->get('records_limit') ):
					$rec->title = \Joomla\CMS\Language\Text::_('CMORERECORDS');
					$rec->id = -1;
					$rec->url = $section->link;
				endif;
				?>
				<li class="list-group-item <?php echo implode(' ', $class);?>">
					<a href="<?php echo \Joomla\CMS\Router\Route::_($rec->url)?>">
						<?php echo $rec->title;?>
					</a>
				</li>
			<?php endforeach;?>
		<?php endif; ?>
	</ul>

	<?php if ( $footerText ) : ?>
		<div class="js_cc<?php echo $params->get( 'moduleclass_sfx' ) ?>"><?php echo $footerText; ?></div>
	<?php endif; ?>

</div>

<div class="clearfix"> </div>