<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

JHTML::_('bootstrap.tooltip', '*[rel^="tooltip"]');

$user_id = $this->input->getInt('user_id', 0);
$app     = JFactory::getApplication();

$markup     = $this->tmpl_params['markup'];
$listparams = $this->tmpl_params['list'];

$listOrder = @$this->ordering;
$listDirn  = @$this->ordering_dir;

$back = null;
if ($this->input->getString('return'))
{
	$back = Url::get_back('return');
}

$isMe         = $this->isMe;
$current_user = JFactory::getUser($this->input->getInt('user_id', $this->user->get('id')));


?>
<?php if ($markup->get('main.css')): ?>
    <style>
        <?php echo $markup->get('main.css');?>
    </style>
<?php endif; ?>
<!--  ---------------------------- Show page header ---------------------------------- -->

<!--  If section is personalized load user block -->
<?php if (($this->section->params->get('personalize.personalize') && $this->input->getInt('user_id')) || $this->isMe): ?>
	<?php echo $this->loadTemplate('user_block'); ?>


    <!-- If title is allowed to be shown -->
<?php elseif ($markup->get('title.title_show')): ?>
    <div class="page-header">
		<?php if (in_array($this->section->params->get('events.subscribe_category'), $this->user->getAuthorisedViewLevels()) && $this->input->getInt('cat_id')): ?>
            <div class="float-end">
				<?php echo HTMLFormatHelper::followcat($this->input->getInt('cat_id'), $this->section); ?>
            </div>
		<?php elseif (in_array($this->section->params->get('events.subscribe_section'), $this->user->getAuthorisedViewLevels())): ?>
            <div class="float-end">
				<?php echo HTMLFormatHelper::followsection($this->section); ?>
            </div>
		<?php endif; ?>
        <h1>
			<?php echo $this->escape(Mint::_($this->title)); ?>
			<?php if ($this->category->id): ?>
				<?php echo CEventsHelper::showNum('category', $this->category->id, true); ?>
			<?php else: ?>
				<?php echo CEventsHelper::showNum('section', $this->section->id, true); ?>
			<?php endif; ?>
        </h1>
    </div>


    <!-- If menu parameters title is set -->
<?php elseif ($this->appParams->get('show_page_heading', 0) && $this->appParams->get('page_heading', '')) : ?>
    <div class="page-header">
        <h1>
			<?php echo $this->escape($this->appParams->get('page_heading')); ?>
        </h1>
    </div>
<?php endif; ?>

<div id="compare" <?php echo !$this->compare ? 'class="hide"' : ''; ?>>
    <div class="alert alert-info alert-block">
        <h4><?php echo JText::sprintf('CCOMPAREMSG', $this->compare) ?></h4>
        <br><a rel="nofollow"
               href="<?php echo JRoute::_('index.php?option=com_joomcck&view=compare&section_id=' . $this->section->id . '&return=' . Url::back()); ?>"
               class="btn btn-primary"><?php echo JText::_('CCOMPAREVIEW'); ?></a>
        <button onclick="Joomcck.CleanCompare(null, '<?php echo @$this->section->id ?>')"
                class="btn"><?php echo JText::_('CCLEANCOMPARE'); ?></button>
    </div>
</div>

<!-- --------------  Show description of the current category or section ---------------------- -->
<?php if ($this->description): ?>
	<?php echo $this->description; ?>
<?php endif; ?>

<form method="post" action="<?php echo $this->action; ?>" name="adminForm" id="adminForm" enctype="multipart/form-data">
    <!-- --------------  Show menu and filters ---------------------- -->
	<?php if (in_array($markup->get('menu.menu'), $this->user->getAuthorisedViewLevels()) || in_array($markup->get('menu.menu'), $this->user->getAuthorisedViewLevels())): ?>
        <div class="clearfix"></div>
        <div class="navbar" id="cnav">
            <div class="navbar-inner w-100 mb-3">
				<?php if ($markup->get('filters.filters')): ?>
                    <div class="form-inline navbar-form float-end search-form">
                        <span style="display: none;">Search box</span>
                        <div class="input-group">
							<?php if (in_array($markup->get('filters.show_search'), $this->user->getAuthorisedViewLevels())): ?>
                                <input class="form-control form-control-sm" type="text"
                                       style="max-width: 100px; min-width: 50px;"
                                       placeholder="<?php echo JText::_('CSEARCHPLACEHOLDER'); ?>" name="filter_search"
                                       value="<?php echo htmlentities($this->state->get('records.search'), ENT_COMPAT, 'utf-8'); ?>"/>
							<?php endif; ?>
							<?php if (in_array($markup->get('filters.show_more'), $this->user->getAuthorisedViewLevels())): ?>
                                <button type="button" class="btn btn-sm btn-light border" data-bs-toggle="collapse"
                                        data-bs-target="#filter-collapse" rel="tooltip"
                                        data-bs-title="<?php echo JText::_('CMORESEARCHOPTIONS') ?>">
									<?php echo HTMLFormatHelper::icon('binocular.png'); ?>
                                </button>
							<?php endif; ?>
                        </div>
                    </div>
				<?php endif; ?>

				<?php if ($markup->get('menu.menu')): ?>
                    <ul class="nav mt-3">
						<?php if (($app->input->getString('view_what') || $app->input->getInt('user_id') || $app->input->getInt('ucat_id') || $back) && $markup->get('menu.menu_all')): ?>
                            <li>
                                <a href="<?php echo $back ? $back : JRoute::_(Url::records($this->section)) ?>">
									<?php if ($markup->get('menu.menu_all_records_icon')): ?>
										<?php echo HTMLFormatHelper::icon('navigation-180.png'); ?>
									<?php endif; ?>
									<?php echo $back ? JText::_('CGOBACK') : JText::_($markup->get('menu.menu_all_records', 'All Records')); ?>
                                </a>
                            </li>
						<?php endif; ?>

						<?php if ($app->input->getString('cat_id') && $markup->get('menu.menu_home_button')): ?>
                            <li>
                                <a href="<?php echo Url::records($this->section) ?>">
									<?php if ($markup->get('menu.menu_home_icon')): ?>
										<?php echo HTMLFormatHelper::icon($this->section->get('personalize.text_icon', 'home.png')); ?>
									<?php endif; ?>
									<?php echo JText::_($markup->get('menu.menu_home_label', 'Home')); ?>
                                </a>
                            </li>
						<?php endif; ?>

						<?php if (!empty($this->category->parent_id) && ($this->category->parent_id > 1) && $markup->get('menu.menu_up')): ?>
                            <li>
                                <a href="<?php echo Url::records($this->section, $this->category->parent_id) ?>">
									<?php if ($markup->get('menu.menu_up_icon')): ?>
										<?php echo HTMLFormatHelper::icon('arrow-curve-090-left.png'); ?>
									<?php endif; ?>
									<?php echo JText::_($markup->get('menu.menu_up_label', 'Up')); ?>
                                </a>
                            </li>
						<?php endif; ?>

						<?php if (!empty($this->postbuttons)): ?>
							<?php if (count($this->postbuttons) > 1): ?>
								<?php $l = array();
								foreach ($this->postbuttons as $type)
								{
									$o = array();
									if (in_array($type->params->get('submission.submission'), $this->user->getAuthorisedViewLevels()) || MECAccess::allowNew($type, $this->section))
									{
										$o[] = '<a class="dropdown-item" href="' . Url::add($this->section, $type, $this->category) . '">' . JText::_($type->name) . '</a>';
									}
									else
									{
										$o[] = '<a  class="dropdown-item" class="disabled" rel="tooltipright" data-bs-title="' . JText::sprintf($markup->get('menu.menu_user_register', 'Register or login to submit %s'), JText::_($type->name)) . '">' . JText::_($type->name) . '</a>';
									}
									if ($o)
									{
										$l[] = '<li>' . implode('', $o) . '</li>';
									}
								}
								?>
								<?php if ($l): ?>
                                    <li class="dropdown me-2">
                                        <a href="#" class="dropdown-toggle btn btn-light border btn-sm"
                                           data-bs-toggle="dropdown">
											<?php if ($markup->get('menu.menu_newrecord_icon')): ?>
												<?php echo HTMLFormatHelper::icon('plus.png'); ?>
											<?php endif; ?>
											<?php echo JText::_($markup->get('menu.menu_newrecord_label', 'Post here')) ?>
                                            <b class="caret"></b>
                                        </a>
                                        <ul class="dropdown-menu">
											<?php echo implode("\n", $l); ?>
                                        </ul>
                                    </li>
								<?php endif; ?>
							<?php elseif (count($this->postbuttons) == 1) : ?>
								<?php $submit = array_values($this->postbuttons);
								$submit       = array_shift($submit); ?>
                                <li class="dropdown me-2">
                                    <a
										<?php if (!(in_array($submit->params->get('submission.submission'), $this->user->getAuthorisedViewLevels()) || MECAccess::allowNew($submit, $this->section))): ?>
                                            class="disabled tip-bottom  btn btn-light border btn-sm" rel="tooltip" href="#"
                                            data-bs-title="<?php echo JText::sprintf($markup->get('menu.menu_user_register', 'Register or login to submit <b>%s</b>'), JText::_($submit->name)) ?>"
										<?php else: ?>
                                            class="btn btn-light border btn-sm" href="<?php echo Url::add($this->section, $submit, $this->category); ?>"
										<?php endif; ?>
                                    >
										<?php if ($markup->get('menu.menu_newrecord_icon')): ?>
											<?php echo HTMLFormatHelper::icon('plus.png'); ?>
										<?php endif; ?>
										<?php echo JText::sprintf($markup->get('menu.menu_user_single', 'Post %s here'), JText::_($submit->name)); ?>
                                    </a>
                                </li>
							<?php endif; ?>
						<?php endif; ?>

						<?php if (count($this->list_templates) > 1 && in_array($markup->get('menu.menu_templates'), $this->user->getAuthorisedViewLevels()) && $this->items): ?>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
									<?php if ($markup->get('menu.menu_templates_icon')): ?>
										<?php echo HTMLFormatHelper::icon('zones.png'); ?>
									<?php endif; ?>
									<?php echo JText::_($markup->get('menu.menu_templates_label', 'Switch view')) ?>
                                    <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
									<?php foreach ($this->list_templates as $id => $template): ?>
										<?php $tmpl = explode('.', $id);
										$tmpl       = $tmpl[0];
										?>
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0)"
                                               onclick="Joomcck.applyFilter('filter_tpl', '<?php echo $id ?>')">
												<?php echo ($this->list_template == $tmpl) ? '<strong>' : ''; ?>
												<?php echo $template; ?>
												<?php echo ($this->list_template == $tmpl) ? '</strong>' : ''; ?>
                                            </a>
                                        </li>
									<?php endforeach; ?>
                                </ul>
                            </li>
						<?php endif; ?>

						<?php if (in_array($markup->get('menu.menu_ordering'), $this->user->getAuthorisedViewLevels()) && $this->items): ?>
                            <li class="dropdown me-2">
                                <a href="#" class="dropdown-toggle btn btn-light border btn-sm"
                                   data-bs-toggle="dropdown">
									<?php if ($markup->get('menu.menu_ordering_icon')): ?>
										<?php echo HTMLFormatHelper::icon('sort.png'); ?>
									<?php endif; ?>
									<?php echo JText::_($markup->get('menu.menu_ordering_label', 'Sort By')) ?>
                                    <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
									<?php if (@$this->items[0]->searchresult): ?>
                                        <li>
											<?php echo JHtml::_('mrelements.sort', ($markup->get('menu.menu_order_ctime_icon') ? HTMLFormatHelper::icon('document-search-result.png') : null) . ' ' . JText::_('CORDERRELEVANCE'), 'searchresult', $listDirn, $listOrder); ?></li>
									<?php endif; ?>

									<?php if (in_array($markup->get('menu.menu_order_ctime'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li>
											<?php echo JHtml::_('mrelements.sort', ($markup->get('menu.menu_order_ctime_icon') ? HTMLFormatHelper::icon('core-ctime.png') : null) . ' ' . JText::_($markup->get('menu.menu_order_ctime_label', 'Created')), 'r.ctime', $listDirn, $listOrder); ?></li>
									<?php endif; ?>

									<?php if (in_array($markup->get('menu.menu_order_mtime'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li>
											<?php echo JHtml::_('mrelements.sort', ($markup->get('menu.menu_order_mtime_icon') ? HTMLFormatHelper::icon('core-ctime.png') : null) . ' ' . JText::_($markup->get('menu.menu_order_mtime_label', 'Modified')), 'r.mtime', $listDirn, $listOrder); ?></li>
									<?php endif; ?>
									<?php if (in_array($markup->get('menu.menu_order_extime'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li>
											<?php echo JHtml::_('mrelements.sort', ($markup->get('menu.menu_order_extime_icon') ? HTMLFormatHelper::icon('core-ctime.png') : null) . ' ' . JText::_($markup->get('menu.menu_order_extime_label', 'Expire')), 'r.extime', $listDirn, $listOrder); ?></li>
									<?php endif; ?>

									<?php if (in_array($markup->get('menu.menu_order_title'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li>
											<?php echo JHtml::_('mrelements.sort', ($markup->get('menu.menu_order_title_icon') ? HTMLFormatHelper::icon('edit.png') : null) . ' ' . JText::_($markup->get('menu.menu_order_title_label', 'Title')), 'r.title', $listDirn, $listOrder); ?></li>
									<?php endif; ?>

									<?php if (in_array($markup->get('menu.menu_order_hits'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li>
											<?php echo JHtml::_('mrelements.sort', ($markup->get('menu.menu_order_hits_icon') ? HTMLFormatHelper::icon('hand-point-090.png') : null) . ' ' . JText::_($markup->get('menu.menu_order_hits_label', 'Hist')), 'r.hits', $listDirn, $listOrder); ?></li>
									<?php endif; ?>

									<?php if (in_array($markup->get('menu.menu_order_votes_result'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li>
											<?php echo JHtml::_('mrelements.sort', ($markup->get('menu.menu_order_votes_result_icon') ? HTMLFormatHelper::icon('star.png') : null) . ' ' . JText::_($markup->get('menu.menu_order_votes_result_label', 'Votes')), 'r.votes_result', $listDirn, $listOrder); ?></li>
									<?php endif; ?>

									<?php if (in_array($markup->get('menu.menu_order_comments'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li>
											<?php echo JHtml::_('mrelements.sort', ($markup->get('menu.menu_order_comments_icon') ? HTMLFormatHelper::icon('balloon-left.png') : null) . ' ' . JText::_($markup->get('menu.menu_order_comments_label', 'Comments')), 'r.comments', $listDirn, $listOrder); ?></li>
									<?php endif; ?>

									<?php if (in_array($markup->get('menu.menu_order_favorite_num'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li>
											<?php echo JHtml::_('mrelements.sort', ($markup->get('menu.menu_order_favorite_num_icon') ? '<img src="' . JURI::root(true) . '/media/com_joomcck/icons/bookmarks/' . $listparams->get('tmpl_core.bookmark_icons', 'star') . '/state1.png" > ' : null) . ' ' . JText::_($markup->get('menu.menu_order_favorite_num_label', 'Number of bookmarks')), 'r.favorite_num', $listDirn, $listOrder); ?></li>
									<?php endif; ?>

									<?php if (in_array($markup->get('menu.menu_order_username'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li>
											<?php echo JHtml::_('mrelements.sort', ($markup->get('menu.menu_order_username_icon') ? HTMLFormatHelper::icon('user.png') : null) . ' ' . JText::_($markup->get('menu.menu_order_username_label', 'user name')), $this->section->params->get('personalize.author_mode'), $listDirn, $listOrder); ?></li>
									<?php endif; ?>

									<?php if (in_array($markup->get('menu.menu_order_fields'), $this->user->getAuthorisedViewLevels())): ?>
										<?php foreach ($this->sortable as $field): ?>
                                            <li>
												<?php echo JHtml::_('mrelements.sort', ($markup->get('menu.menu_order_fields_icon') && ($icon = $field->params->get('core.icon')) ? HTMLFormatHelper::icon($icon) : null) . ' ' . JText::_($field->label), FieldHelper::sortName($field), $listDirn, $listOrder); ?></li>
										<?php endforeach; ?>
									<?php endif; ?>
                                </ul>
                            </li>
						<?php endif; ?>

						<?php if (in_array($markup->get('menu.menu_user'), $this->user->getAuthorisedViewLevels()) && $this->user->id && !$this->isMe): ?>
							<?php $counts = $this->_getUsermenuCounts($markup); ?>
                            <li class="dropdown me-2" id="joomcck-user-menu">
                                <a href="#" class="dropdown-toggle btn btn-light border btn-sm"
                                   data-bs-toggle="dropdown">
									<?php if ($markup->get('menu.menu_user_icon')): ?>
										<?php echo HTMLFormatHelper::icon('user.png'); ?>
									<?php endif; ?>
									<?php echo JText::_($markup->get('menu.menu_user_label', 'My Menu')) ?>
                                    <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
									<?php if ($markup->get('menu.menu_user_my')): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo JRoute::_(Url::user('created')); ?>">
												<?php if ($markup->get('menu.menu_user_my_icon')): ?>
													<?php echo HTMLFormatHelper::icon($this->section->params->get('personalize.text_icon', 'home.png')); ?>
												<?php endif; ?>
												<?php echo JText::_($markup->get('menu.menu_user_my_label', 'My Homepage')) ?>
                                                <span class="badge bg-light text-muted border"><?php echo $counts->created; ?></span>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_followed') && $counts->followed): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo JRoute::_(Url::user('follow')); ?>">
												<?php if ($markup->get('menu.menu_user_follow_icon')): ?>
                                                    <img src="<?php echo JURI::root(true); ?>/media/com_joomcck/icons/16/follow1.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo JText::_($markup->get('menu.menu_user_follow_label', 'Watched')) ?>
                                                <span class="badge bg-light text-muted border"><?php echo $counts->followed; ?></span>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_evented') && CEventsHelper::getNum('section', $this->section->id)): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo JRoute::_(Url::user('events')); ?>">
												<?php if ($markup->get('menu.menu_user_events_icon')): ?>
                                                    <img src="<?php echo JURI::root(true); ?>/media/com_joomcck/icons/16/bell.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo JText::_($markup->get('menu.menu_user_events_label', 'With new events')) ?>
												<?php echo CEventsHelper::showNum('section', $this->section->id) ?>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_favorite') && $counts->favorited): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo JRoute::_(Url::user('favorited')); ?>">
												<?php if ($markup->get('menu.menu_user_favorite_icon')): ?>
                                                    <img src="<?php echo JURI::root(true) . '/media/com_joomcck/icons/bookmarks/' . $listparams->get('tmpl_core.bookmark_icons', 'star') . '/state1.png'; ?>"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo JText::_($markup->get('menu.menu_user_favorite_label', 'Bookmarked')) ?>
                                                <span class="badge bg-light text-muted border"><?php echo $counts->favorited; ?></span>
                                            </a></li>
									<?php endif; ?>
									<?php if ($markup->get('menu.menu_user_rated') && $counts->rated): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo JRoute::_(Url::user('rated')); ?>">
												<?php if ($markup->get('menu.menu_user_rated_icon')): ?>
                                                    <img src="<?php echo JURI::root(true); ?>/media/com_joomcck/icons/16/star.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo JText::_($markup->get('menu.menu_user_rated_label', 'Rated')) ?>
                                                <span class="badge bg-light text-muted border"><?php echo $counts->rated; ?></span>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_commented') && $counts->commented): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo JRoute::_(Url::user('commented')); ?>">
												<?php if ($markup->get('menu.menu_user_commented_icon')): ?>
                                                    <img src="<?php echo JURI::root(true); ?>/media/com_joomcck/icons/16/balloon-left.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo JText::_($markup->get('menu.menu_user_commented_label', 'Commented')) ?>
                                                <span class="badge bg-light text-muted border"><?php echo $counts->commented; ?></span>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_visited') && $counts->visited): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo JRoute::_(Url::user('visited')); ?>">
												<?php if ($markup->get('menu.menu_user_visited_icon')): ?>
                                                    <img src="<?php echo JURI::root(true); ?>/media/com_joomcck/icons/16/hand-point-090.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo JText::_($markup->get('menu.menu_user_visited_label', 'Visited')) ?>
                                                <span class="badge bg-light text-muted border"><?php echo $counts->visited; ?></span>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_expire') && $counts->expired): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo JRoute::_(Url::user('expired')); ?>">
												<?php if ($markup->get('menu.menu_user_expire_icon')): ?>
                                                    <img src="<?php echo JURI::root(true); ?>/media/com_joomcck/icons/16/clock--exclamation.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo JText::_($markup->get('menu.menu_user_expire_label', 'Expired')) ?>
                                                <span class="badge bg-light text-muted border"><?php echo $counts->expired; ?></span>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_hidden') && $counts->hidden): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo JRoute::_(Url::user('hidden')); ?>">
												<?php if ($markup->get('menu.menu_user_hidden_icon')): ?>
                                                    <img src="<?php echo JURI::root(true); ?>/media/com_joomcck/icons/16/eye-half.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo JText::_($markup->get('menu.menu_user_hidden_label', 'Hidden')) ?>
                                                <span class="badge bg-light text-muted border"><?php echo $counts->hidden; ?></span>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_feature') && $counts->featured): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo JRoute::_(Url::user('featured')); ?>">
												<?php if ($markup->get('menu.menu_user_feature_icon')): ?>
                                                    <img src="<?php echo JURI::root(true); ?>/media/com_joomcck/icons/16/arrow-curve-090-left.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo JText::_($markup->get('menu.menu_user_feature_label', 'Fetured')) ?>
                                                <span class="badge bg-light text-muted border"><?php echo $counts->featured; ?></span>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_unpublished') && $counts->unpublished): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo JRoute::_(Url::user('unpublished')); ?>">
												<?php if ($markup->get('menu.menu_user_unpublished_icon')): ?>
                                                    <img src="<?php echo JURI::root(true); ?>/media/com_joomcck/icons/16/minus-circle.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo JText::_($markup->get('menu.menu_user_unpublished_label', 'On Approval')) ?>
                                                <span class="badge bg-light text-muted border"><?php echo $counts->unpublished; ?></span>
                                            </a></li>
									<?php endif; ?>


									<?php if ($markup->get('menu.menu_user_moder') && MECAccess::allowModerate(null, null, $this->section)): ?>
                                        <li class="divider"></li>
                                        <li><a class="dropdown-item"
                                               href="<?php echo JRoute::_('index.php?option=com_joomcck&view=moderators&filter_section=' . $this->section->id . '&return=' . Url::back()); ?>">
												<?php if ($markup->get('menu.menu_user_moder_icon')): ?>
                                                    <img src="<?php echo JURI::root(true); ?>/media/com_joomcck/icons/16/user-share.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo JText::_($markup->get('menu.menu_user_moder_label', 'Moderators')) ?>
                                            </a></li>
									<?php endif; ?>

									<?php if ($this->section->params->get('personalize.personalize') && $this->section->params->get('personalize.allow_section_set')): ?>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="<?php echo JRoute::_('index.php?option=com_joomcck&view=options&layout=section&section_id=' . $this->section->id . '&return=' . Url::back()); ?>">
												<?php if ($markup->get('menu.menu_user_subscribe_icon')): ?>
                                                    <img src="<?php echo JURI::root(true); ?>/media/com_joomcck/icons/16/gear.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo JText::_($markup->get('menu.menu_user_subscribe_label', 'Options')) ?>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_cat_manage') && in_array($this->section->params->get('personalize.pcat_submit'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li class="divider"></li>
                                        <li class="dropdown-submenu">
                                            <a tabindex="-1"
                                               href="<?php echo JRoute::_(Url::_('categories') . '&return=' . Url::back()) ?>">
												<?php if ($markup->get('menu.menu_user_cat_manage_icon')): ?>
                                                    <img src="<?php echo JURI::root(true); ?>/media/com_joomcck/icons/16/category.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo JText::_($markup->get('menu.menu_user_cat_manage_label', 'Categories')) ?>
                                                <span class="badge bg-light text-muted border"><?php echo $counts->categories; ?></span>
                                            </a>
											<?php if ($markup->get('menu.menu_user_cat_add')): ?>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" tabindex="-1"
                                                           href="<?php echo JRoute::_(Url::_('category')) ?>">
															<?php if ($markup->get('menu.menu_user_cat_add_icon')): ?>
                                                                <img src="<?php echo JURI::root(true); ?>/media/com_joomcck/icons/16/plus.png"
                                                                     align="absmiddle"/>
															<?php endif; ?>
															<?php echo JText::_($markup->get('menu.menu_user_cat_add_label', 'Add new category')) ?>
                                                        </a>
                                                    </li>
                                                </ul>
											<?php endif; ?>
                                        </li>
									<?php endif; ?>
                                </ul>
                            </li>
						<?php endif; ?>
                    </ul>
                    <div class="clearfix"></div>
				<?php endif; ?>
            </div>
        </div>
        <script>
            (function ($) {
                if (!$('#cnav .navbar-inner').text().trim()) {
                    $('#adminForm').hide();
                }

                var el = $('#joomcck-user-menu');
                var list = $('ul.dropdown-menu li', el);
                if (!list || list.length == 0) {
                    el.hide();
                }
            }(jQuery))
        </script>


	<?php if (in_array($markup->get('filters.show_more'), $this->user->getAuthorisedViewLevels()) && $markup->get('filters.filters')): ?>
        <div class="fade collapse separator-box" id="filter-collapse">
            <div class="btn-group float-end">
                <button class="btn btn-sm btn-primary" onclick="Joomla.submitbutton('records.filters')">
                    <img src="<?php echo JURI::root(true) ?>/media/com_joomcck/icons/16/tick-button.png" align="absmiddle"
                         alt="<?php echo JText::_('CAPPLY'); ?>"/>
					<?php echo JText::_('CAPPLY'); ?></button>
				<?php if (count($this->worns)): ?>
                    <button class="btn btn-light btn-sm border" type="button"
                            onclick="Joomla.submitbutton('records.cleanall')">
                        <img src="<?php echo JURI::root(true) ?>/media/com_joomcck/icons/16/cross-button.png" align="absmiddle"
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
					<?php if (in_array($markup->get('filters.filter_type'), $this->user->getAuthorisedViewLevels()) && (count($this->submission_types) > 1)): ?>
                        <li class="nav-item"><a class="nav-link active" href="#tab-types"
                                                data-bs-toggle="tab"><?php echo ($markup->get('filters.filter_type_icon') ? HTMLFormatHelper::icon('block.png') : null) . JText::_($markup->get('filters.type_label', 'Content Type')) ?></a>
                        </li>
					<?php endif; ?>

					<?php if (in_array($markup->get('filters.filter_tags'), $this->user->getAuthorisedViewLevels())): ?>
                        <li class="nav-item"><a class="nav-link" href="#tab-tags"
                                                data-bs-toggle="tab"><?php echo ($markup->get('filters.filter_tag_icon') ? HTMLFormatHelper::icon('price-tag.png') : null) . JText::_($markup->get('filters.tag_label', 'CTAGS')) ?></a>
                        </li>
					<?php endif; ?>

					<?php if (in_array($markup->get('filters.filter_user'), $this->user->getAuthorisedViewLevels())): ?>
                        <li class="nav-item"><a class="nav-link" href="#tab-users"
                                                data-bs-toggle="tab"><?php echo ($markup->get('filters.filter_user_icon') ? HTMLFormatHelper::icon('user.png') : null) . JText::_($markup->get('filters.user_label', 'CAUTHOR')) ?></a>
                        </li>
					<?php endif; ?>

					<?php if (in_array($markup->get('filters.filter_cat'), $this->user->getAuthorisedViewLevels()) && $this->section->categories && ($this->section->params->get('general.filter_mode') == 0)): ?>
                        <li class="nav-item"><a class="nav-link" href="#tab-cats"
                                                data-bs-toggle="tab"><?php echo ($markup->get('filters.filter_category_icon') ? HTMLFormatHelper::icon('category.png') : null) . JText::_($markup->get('filters.category_label', 'CCATEGORY')) ?></a>
                        </li>
					<?php endif; ?>

					<?php if (count($this->filters) && $markup->get('filters.filter_fields')): ?>
						<?php foreach ($this->filters as $filter): ?>
							<?php if ($filter->params->get('params.filter_hide')) continue; ?>
                            <li class="nav-item"><a class="nav-link" href="#tab-<?php echo $filter->key ?>"
                                                    id="<?php echo $filter->key ?>"
                                                    data-bs-toggle="tab"><?php echo ($markup->get('filters.filter_tag_icon') && $filter->params->get('core.icon') ? HTMLFormatHelper::icon($filter->params->get('core.icon')) : null) . ' ' . $filter->label ?></a>
                            </li>
						<?php endforeach; ?>
					<?php endif; ?>
                </ul>
                <div class="tab-content flex-grow-1 align-self-stretch" id="vtabs-content">
					<?php if (in_array($markup->get('filters.filter_type'), $this->user->getAuthorisedViewLevels()) && (count($this->submission_types) > 1)): ?>
                        <div class="tab-pane fade show active" id="tab-types">
							<?php if ($markup->get('filters.filter_type_type') == 1): ?>
								<?php echo JHtml::_('types.checkbox', $this->total_types, $this->submission_types, $this->state->get('records.type')); ?>
							<?php elseif ($markup->get('filters.filter_type_type') == 3): ?>
								<?php echo JHtml::_('types.toggle', $this->total_types, $this->submission_types, $this->state->get('records.type')); ?>
							<?php else : ?>
								<?php echo JHtml::_('types.select', $this->total_types_option, $this->state->get('records.type')); ?>
							<?php endif; ?>
                        </div>
					<?php endif; ?>


					<?php if (in_array($markup->get('filters.filter_tags'), $this->user->getAuthorisedViewLevels())): ?>
                        <div class="tab-pane fade" id="tab-tags">
							<?php if ($markup->get('filters.filter_tags_type') == 1): ?>
								<?php echo JHtml::_('tags.tagform', $this->section, $this->state->get('records.tag')); ?>
							<?php elseif ($markup->get('filters.filter_tags_type') == 2): ?>
								<?php echo JHtml::_('tags.tagcheckboxes', $this->section, $this->state->get('records.tag')); ?>
							<?php elseif ($markup->get('filters.filter_tags_type') == 3): ?>
								<?php echo JHtml::_('tags.tagselect', $this->section, $this->state->get('records.tag')); ?>
							<?php elseif ($markup->get('filters.filter_tags_type') == 4): ?>
								<?php echo JHtml::_('tags.tagpills', $this->section, $this->state->get('records.tag')); ?>
							<?php endif; ?>
                        </div>
					<?php endif; ?>

					<?php if (in_array($markup->get('filters.filter_user'), $this->user->getAuthorisedViewLevels())): ?>
                        <div class="tab-pane fade" id="tab-users">
							<?php if ($markup->get('filters.filter_users_type') == 1): ?>
								<?php echo JHtml::_('cusers.form', $this->section, $this->state->get('records.user')); ?>
							<?php elseif ($markup->get('filters.filter_users_type') == 2): ?>
								<?php echo JHtml::_('cusers.checkboxes', $this->section, $this->state->get('records.user')); ?>
							<?php elseif ($markup->get('filters.filter_users_type') == 3): ?>
								<?php echo JHtml::_('cusers.select', $this->section, $this->state->get('records.user')); ?>
							<?php endif; ?>
                        </div>
					<?php endif; ?>

					<?php if (in_array($markup->get('filters.filter_cat'), $this->user->getAuthorisedViewLevels()) && $this->section->categories && ($this->section->params->get('general.filter_mode') == 0)): ?>
                        <div class="tab-pane fade" id="tab-cats">
							<?php if ($markup->get('filters.filter_category_type') == 1): ?>
								<?php echo JHtml::_('categories.form', $this->section, $this->state->get('records.category')); ?>
							<?php elseif ($markup->get('filters.filter_category_type') == 2): ?>
								<?php echo JHtml::_('categories.checkboxes', $this->section, $this->state->get('records.category'), array('columns' => 3)); ?>
							<?php elseif ($markup->get('filters.filter_category_type') == 3): ?>
								<?php echo JHtml::_('categories.select', $this->section, $this->state->get('records.category'), array('multiple' => 0)); ?>
							<?php elseif ($markup->get('filters.filter_category_type') == 4): ?>
								<?php echo JHtml::_('categories.select', $this->section, $this->state->get('records.category'), array('multiple' => 1, 'size' => 25)); ?>
							<?php elseif ($markup->get('filters.filter_category_type') == 5): ?>
								<?php echo JHtml::_('mrelements.catselector', "filters[cats][]", $this->section->id, $this->state->get('records.category')); ?>
							<?php endif; ?>
                        </div>
					<?php endif; ?>
					<?php if (count($this->filters) && $markup->get('filters.filter_fields')): ?>
						<?php foreach ($this->filters as $filter): ?>
							<?php if ($filter->params->get('params.filter_hide')) continue; ?>
                            <div class="tab-pane fade" id="tab-<?php echo $filter->key ?>">
								<?php if ($filter->params->get('params.filter_descr') && $markup->get('filters.filter_descr')): ?>
                                    <p>
                                        <small><?php echo JText::_($filter->params->get('params.filter_descr')); ?></small>
                                    </p>
								<?php endif; ?>
								<?php echo $filter->onRenderFilter($this->section); ?>
                            </div>
						<?php endforeach; ?>
					<?php endif; ?>
                </div><!--  tab-content -->
            </div><!--  tabable -->
            <br>
        </div><!--  collapse -->
	<?php endif; ?>
	<?php endif; ?>

    <input type="hidden" name="section_id" value="<?php echo $this->state->get('records.section_id') ?>">
    <input type="hidden" name="cat_id" value="<?php echo $app->input->getInt('cat_id'); ?>">
    <input type="hidden" name="option" value="com_joomcck">
    <input type="hidden" name="task" value="">
    <input type="hidden" name="limitstart" value="0">
    <input type="hidden" name="filter_order" value="<?php //echo $this->ordering; ?>">
    <input type="hidden" name="filter_order_Dir" value="<?php //echo $this->ordering_dir; ?>">
	<?php echo JHtml::_('form.token'); ?>
	<?php if ($this->worns): ?>
		<?php foreach ($this->worns as $worn): ?>
            <input type="hidden" name="clean[<?php echo $worn->name; ?>]" id="<?php echo $worn->name; ?>" value="">
		<?php endforeach; ?>
	<?php endif; ?>
</form>

<!-- --------------  Show category index ---------------------- -->
<?php if ($this->show_category_index): ?>
    <DIV class="clearfix"></DIV>
	<?php echo $this->loadTemplate('cindex_' . $this->section->params->get('general.tmpl_category')); ?>
<?php endif; ?>

<?php if ($markup->get('main.alpha') && $this->alpha && $this->alpha_list && $this->items): ?>
    <div class="alpha-index">
		<?php foreach ($this->alpha as $set): ?>
            <div class="alpha-set">
				<?php foreach ($set as $alpha): ?>
					<?php if (in_array($alpha, $this->alpha_list)): ?>
                        <span class="badge bg-warning"
                              onclick="Joomcck.applyFilter('filter_alpha', '<?php echo $alpha ?>')"
							<?php echo $markup->get('main.alpha_num') ? 'rel="tooltip" data-bs-title="' . JText::plural('CXNRECFOUND',
									@$this->alpha_totals[$alpha]) . '"' : null; ?>><?php echo $alpha; ?></span>
					<?php else: ?>
                        <span class="badge bg-light text-muted border"><?php echo $alpha; ?></span>
					<?php endif; ?>
				<?php endforeach; ?>
            </div>
		<?php endforeach; ?>
    </div>
    <br>
<?php endif; ?>




<?php if ($markup->get('filters.worns') && count($this->worns)): ?>
    <div class="filter-worns">
		<?php foreach ($this->worns as $worn): ?>
            <div class="alert alert-info alert-dismissible fade show float-start" role="alert">

                <div><i class="fas fa-filter"></i> <?php echo $worn->label ?></div>
				<?php echo $worn->text ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"  aria-label="Close"
                        onclick="Joomcck.cleanFilter('<?php echo $worn->name ?>')" rel="tooltip"
                        data-bs-title="<?php echo JText::_('CDELETEFILTER') ?>">
                </button>
            </div>
		<?php endforeach; ?>
		<?php if (count($this->worns) > 1): ?>
            <button onclick="Joomla.submitbutton('records.cleanall');" class="alert alert-danger  float-start">
                <div><?php echo JText::_('CORESET'); ?></div>
				<?php echo JText::_('CODELETEALLFILTERS'); ?>
            </button>
		<?php endif; ?>

        <div class="clearfix"></div>
    </div>
    <br>
<?php endif; ?>




<?php if ($this->items): ?>

	<?php echo $this->loadTemplate('list_' . $this->list_template); ?>

	<?php if ($this->tmpl_params['list']->def('tmpl_core.item_pagination', 1)) : ?>
        <form method="post">
            <div style="text-align: center;">
                <small>
					<?php if ($this->pagination->getPagesCounter()): ?>
						<?php echo $this->pagination->getPagesCounter(); ?>
					<?php endif; ?>
					<?php if ($this->tmpl_params['list']->def('tmpl_core.item_limit_box', 0)) : ?>
						<?php echo str_replace('<option value="0">' . JText::_('JALL') . '</option>', '', $this->pagination->getLimitBox()); ?>
					<?php endif; ?>
					<?php echo $this->pagination->getResultsCounter(); ?>
                </small>
            </div>
			<?php if ($this->pagination->getPagesLinks()): ?>
                <div style="text-align: center;" class="pagination">
					<?php echo str_replace('<ul>', '<ul class="pagination-list">', $this->pagination->getPagesLinks()); ?>
                </div>
                <div class="clearfix"></div>
			<?php endif; ?>
        </form>
	<?php endif; ?>

<?php elseif ($this->worns): ?>
    <h4 align="center"><?php echo JText::_('CNORECFOUNDSEARCH'); ?></h4>
<?php else: ?>
	<?php if (((!empty($this->category->id) && $this->category->params->get('submission')) || (empty($this->category->id) && $this->section->params->get('general.section_home_items'))) && !$this->input->get('view_what')): ?>
        <h4 align="center" class="no-records"
            id="no-records<?php echo $this->section->id; ?>"><?php echo JText::_('CNOARTICLESHERE'); ?></h4>
	<?php endif; ?>
<?php endif; ?>
