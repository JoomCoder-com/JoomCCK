<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

extract($displayData);

$markup     = $current->tmpl_params['markup'];

if(!in_array($markup->get('filters.show_more'), $current->user->getAuthorisedViewLevels()) && !$markup->get('filters.filters'))

?>

    <div class="fade collapse separator-box" id="filter-collapse">
        <div class="btn-group float-end">
            <button class="btn btn-sm btn-primary" onclick="Joomla.submitbutton('records.filters')">
                <img src="<?php echo JURI::root(true) ?>/media/com_joomcck/icons/16/tick-button.png" align="absmiddle"
                     alt="<?php echo JText::_('CAPPLY'); ?>"/>
				<?php echo JText::_('CAPPLY'); ?></button>
			<?php if (count($current->worns)): ?>
                <button class="btn btn-light btn-sm border" type="button"
                        onclick="Joomla.submitbutton('records.cleanall')">
                    <img src="<?php echo JURI::root(true) ?>/media/com_joomcck/icons/16/cross-button.png"
                         align="absmiddle"
                         alt="<?php echo JText::_('CRESETFILTERS'); ?>"/>
					<?php echo JText::_('CRESETFILTERS'); ?></button>
			<?php endif; ?>
            <button class="btn btn-light btn-sm border" type="button" data-bs-toggle="collapse"
                    data-bs-target="#filter-collapse">
                <img src="<?php echo JURI::root(true) ?>/media/com_joomcck/icons/16/minus-button.png" align="absmiddle"
                     alt="<?php echo JText::_('CCLOSE'); ?>"/>
				<?php echo JText::_('CCLOSE'); ?></button>
        </div>
        <h3>
            <img src="<?php echo JURI::root(true) ?>/media/com_joomcck/icons/16/funnel.png" align="absmiddle"
                 alt="<?php echo JText::_('CMORESEARCHOPTIONS'); ?>"/>
			<?php echo JText::_('CMORESEARCHOPTIONS') ?>
        </h3>
        <div class="clearfix"></div>


        <div class="d-flex align-items-start">
            <ul class="nav nav-tabs flex-column me-3" id="vtabs">
				<?php if (in_array($markup->get('filters.filter_type'), $current->user->getAuthorisedViewLevels()) && (count($current->submission_types) > 1)): ?>
                    <li class="nav-item"><a class="nav-link active" href="#tab-types"
                                            data-bs-toggle="tab"><?php echo ($markup->get('filters.filter_type_icon') ? HTMLFormatHelper::icon('block.png') : null) . JText::_($markup->get('filters.type_label', 'Content Type')) ?></a>
                    </li>
				<?php endif; ?>

				<?php if (in_array($markup->get('filters.filter_tags'), $current->user->getAuthorisedViewLevels())): ?>
                    <li class="nav-item"><a class="nav-link" href="#tab-tags"
                                            data-bs-toggle="tab"><?php echo ($markup->get('filters.filter_tag_icon') ? HTMLFormatHelper::icon('price-tag.png') : null) . JText::_($markup->get('filters.tag_label', 'CTAGS')) ?></a>
                    </li>
				<?php endif; ?>

				<?php if (in_array($markup->get('filters.filter_user'), $current->user->getAuthorisedViewLevels())): ?>
                    <li class="nav-item"><a class="nav-link" href="#tab-users"
                                            data-bs-toggle="tab"><?php echo ($markup->get('filters.filter_user_icon') ? HTMLFormatHelper::icon('user.png') : null) . JText::_($markup->get('filters.user_label', 'CAUTHOR')) ?></a>
                    </li>
				<?php endif; ?>

				<?php if (in_array($markup->get('filters.filter_cat'), $current->user->getAuthorisedViewLevels()) && $current->section->categories && ($current->section->params->get('general.filter_mode') == 0)): ?>
                    <li class="nav-item"><a class="nav-link" href="#tab-cats"
                                            data-bs-toggle="tab"><?php echo ($markup->get('filters.filter_category_icon') ? HTMLFormatHelper::icon('category.png') : null) . JText::_($markup->get('filters.category_label', 'CCATEGORY')) ?></a>
                    </li>
				<?php endif; ?>

				<?php if (count($current->filters) && $markup->get('filters.filter_fields')): ?>
					<?php foreach ($current->filters as $filter): ?>
						<?php if ($filter->params->get('params.filter_hide')) continue; ?>
                        <li class="nav-item"><a class="nav-link" href="#tab-<?php echo $filter->key ?>"
                                                id="<?php echo $filter->key ?>"
                                                data-bs-toggle="tab"><?php echo ($markup->get('filters.filter_tag_icon') && $filter->params->get('core.icon') ? HTMLFormatHelper::icon($filter->params->get('core.icon')) : null) . ' ' . $filter->label ?></a>
                        </li>
					<?php endforeach; ?>
				<?php endif; ?>
            </ul>
            <div class="tab-content flex-grow-1 align-self-stretch" id="vtabs-content">
				<?php if (in_array($markup->get('filters.filter_type'), $current->user->getAuthorisedViewLevels()) && (count($current->submission_types) > 1)): ?>
                    <div class="tab-pane fade show active" id="tab-types">
						<?php if ($markup->get('filters.filter_type_type') == 1): ?>
							<?php echo JHtml::_('types.checkbox', $current->total_types, $current->submission_types, $current->state->get('records.type')); ?>
						<?php elseif ($markup->get('filters.filter_type_type') == 3): ?>
							<?php echo JHtml::_('types.toggle', $current->total_types, $current->submission_types, $current->state->get('records.type')); ?>
						<?php else : ?>
							<?php echo JHtml::_('types.select', $current->total_types_option, $current->state->get('records.type')); ?>
						<?php endif; ?>
                    </div>
				<?php endif; ?>


				<?php if (in_array($markup->get('filters.filter_tags'), $current->user->getAuthorisedViewLevels())): ?>
                    <div class="tab-pane fade" id="tab-tags">
						<?php if ($markup->get('filters.filter_tags_type') == 1): ?>
							<?php echo JHtml::_('tags.tagform', $current->section, $current->state->get('records.tag')); ?>
						<?php elseif ($markup->get('filters.filter_tags_type') == 2): ?>
							<?php echo JHtml::_('tags.tagcheckboxes', $current->section, $current->state->get('records.tag')); ?>
						<?php elseif ($markup->get('filters.filter_tags_type') == 3): ?>
							<?php echo JHtml::_('tags.tagselect', $current->section, $current->state->get('records.tag')); ?>
						<?php elseif ($markup->get('filters.filter_tags_type') == 4): ?>
							<?php echo JHtml::_('tags.tagtoggle', $current->section, $current->state->get('records.tag')); ?>
						<?php endif; ?>
                    </div>
				<?php endif; ?>

				<?php if (in_array($markup->get('filters.filter_user'), $current->user->getAuthorisedViewLevels())): ?>
                    <div class="tab-pane fade" id="tab-users">
						<?php if ($markup->get('filters.filter_users_type') == 1): ?>
							<?php echo JHtml::_('cusers.form', $current->section, $current->state->get('records.user')); ?>
						<?php elseif ($markup->get('filters.filter_users_type') == 2): ?>
							<?php echo JHtml::_('cusers.checkboxes', $current->section, $current->state->get('records.user')); ?>
						<?php elseif ($markup->get('filters.filter_users_type') == 3): ?>
							<?php echo JHtml::_('cusers.select', $current->section, $current->state->get('records.user')); ?>
						<?php endif; ?>
                    </div>
				<?php endif; ?>

				<?php if (in_array($markup->get('filters.filter_cat'), $current->user->getAuthorisedViewLevels()) && $current->section->categories && ($current->section->params->get('general.filter_mode') == 0)): ?>
                    <div class="tab-pane fade" id="tab-cats">
						<?php if ($markup->get('filters.filter_category_type') == 1): ?>
							<?php echo JHtml::_('categories.form', $current->section, $current->state->get('records.category')); ?>
						<?php elseif ($markup->get('filters.filter_category_type') == 2): ?>
							<?php echo JHtml::_('categories.checkboxes', $current->section, $current->state->get('records.category'), array('columns' => 3)); ?>
						<?php elseif ($markup->get('filters.filter_category_type') == 3): ?>
							<?php echo JHtml::_('categories.select', $current->section, $current->state->get('records.category'), array('multiple' => 0)); ?>
						<?php elseif ($markup->get('filters.filter_category_type') == 4): ?>
							<?php echo JHtml::_('categories.select', $current->section, $current->state->get('records.category'), array('multiple' => 1, 'size' => 25)); ?>
						<?php elseif ($markup->get('filters.filter_category_type') == 5): ?>
                                <?php echo JHtml::_('mrelements.catselector', "filters[cats][]", $current->section->id, $current->state->get('records.category')); ?>
						<?php endif; ?>
                    </div>
				<?php endif; ?>
				<?php if (count($current->filters) && $markup->get('filters.filter_fields')): ?>
					<?php foreach ($current->filters as $filter): ?>
						<?php if ($filter->params->get('params.filter_hide')) continue; ?>
                        <div class="tab-pane fade" id="tab-<?php echo $filter->key ?>">
							<?php if ($filter->params->get('params.filter_descr') && $markup->get('filters.filter_descr')): ?>
                                <p>
                                    <small><?php echo JText::_($filter->params->get('params.filter_descr')); ?></small>
                                </p>
							<?php endif; ?>
							<?php echo $filter->onRenderFilter($current->section); ?>
                        </div>
					<?php endforeach; ?>
				<?php endif; ?>
            </div><!--  tab-content -->
        </div><!--  tabable -->
        <br>
    </div><!--  collapse -->
