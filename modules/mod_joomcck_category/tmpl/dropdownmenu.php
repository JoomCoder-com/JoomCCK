<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die('Restricted access');

if (!function_exists('mod_getChildsDrop'))
{

	function mod_getChildsDrop($category, $params, $k = 1)
	{

		$category->records = modJoomcckCategoriesHelper::getCatRecords($category->id, $params); ?>
		<?php if (count($category->children) || count($category->records)) : ?>

        <ul class="dropdown-menu">
			<?php foreach ($category->children as $i => $cat) :
				if (!$params->get('tmpl_params.subcat_empty', 1) && !$cat->num_current && !$cat->num_all) continue; ?>
            <li class="dropdown<?php if ($cat->childs_num) echo '-submenu'; ?><?php /*if(\Joomla\CMS\Factory::getApplication()->input->getInt('cat_id') == $cat->id) echo ' open';*/ ?>">
				<?php if ($params->get('tmpl_params.subcat_limit', 5) <= $i && (count($category->children) > $params->get('tmpl_params.subcat_limit', 5))): ?>
                <a class="dropdown-item"
                   href="<?php echo $category->link; ?>"><?php echo \Joomla\CMS\Language\Text::_('CMORECATS') . '...' ?></a></li>
				<?php break; ?>
			<?php else: ?>
                <a class="dropdown-item" href="<?php echo \Joomla\CMS\Router\Route::_($cat->link) ?>">
					<?php echo $cat->title; ?>
					<?php if ($params->get('tmpl_params.subcat_nums', 0) && $cat->params->get('submission')): ?>
                        <span class="small">(<?php echo (int) $cat->records_num; ?>)</span>
					<?php endif; ?>
                </a>

				<?php if ($cat->childs_num): ?>
					<?php mod_getChildsDrop($cat, $params, $k + 1); ?>
				<?php endif; ?>
			<?php endif; ?>
                </li>
			<?php endforeach; ?>
			<?php if ($params->get('records') && count($category->records)):
				foreach ($category->records as $i => $rec):
					if ($params->get('records_limit') && $i == $params->get('records_limit')):
						$rec->title = \Joomla\CMS\Language\Text::_('CMORERECORDS');
						$rec->id    = -1;
						$rec->url   = $category->link;
					endif;
					?>
                    <li class="dropdown">
                        <a class="dropdown-item" href="<?php echo \Joomla\CMS\Router\Route::_($rec->url) ?>">
							<?php echo $rec->title; ?>
                        </a>
                    </li>
				<?php endforeach; ?>
			<?php endif; ?>
        </ul>
	<?php endif; ?>
	<?php }

} ?>

<nav class="navbar navbar-expand-lg bg-light">
    <div class="container-fluid">
		<?php if ($params->get('show_section', 1)) : ?>
            <a class="navbar-brand" href="<?php echo \Joomla\CMS\Router\Route::_(Url::records($section)) ?>">
				<?php echo HTMLFormatHelper::icon($section->params->get('personalize.text_icon', 'home.png')); ?><?php echo $section->name; ?>
            </a>
		<?php endif; ?>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">


            <ul class="navbar-nav">
				<?php foreach ($categories as $cat) :
					if (!$params->get('tmpl_params.cat_empty', 1) && !$cat->num_current && !$cat->num_all) continue; ?>


					<?php $hasSubCats = $cat->childs_num || ($params->get('records') && $cat->records_num) ?>

                    <li class="nav-item <?php echo $hasSubCats ? 'dropdown' : '' ?>">

                            <a <?php echo $hasSubCats ? 'data-bs-toggle="dropdown" aria-expanded="false"' : ''  ?> class="nav-link <?php echo $hasSubCats ? 'dropdown-toggle' : '' ?>" href="<?php echo $hasSubCats ? '#' : \Joomla\CMS\Router\Route::_($cat->link) ?>">
				            <?php echo $cat->title; ?>
	                            <?php if ($params->get('tmpl_params.cat_nums', 0) && $cat->params->get('submission')): ?>
                                    <span class="small">(<?php echo (int) $cat->records_num; ?>)</span>
	                            <?php endif; ?>
                            </a>
                            <?php if ($cat->childs_num || ($params->get('records') && $cat->records_num)): ?>
                                <?php mod_getChildsDrop($cat, $params); ?>
                            <?php endif; ?>



                    </li>
				<?php endforeach; ?>

				<?php if ($params->get('records') && $section->records):
					foreach ($section->records as $i => $rec):
						if ($params->get('records_limit') && $i == $params->get('records_limit')):
							$rec->title = \Joomla\CMS\Language\Text::_('CMORERECORDS');
							$rec->id    = -1;
							$rec->url   = $section->link;
						endif;
						?>
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="<?php echo \Joomla\CMS\Router\Route::_($rec->url) ?>">
								<?php echo $rec->title; ?>
                            </a>
                        </li>
					<?php endforeach; ?>
				<?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="clearfix"></div>



