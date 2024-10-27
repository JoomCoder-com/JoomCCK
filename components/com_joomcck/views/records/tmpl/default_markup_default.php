<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

defined('_JEXEC') or die('Restricted access');

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

$user_id = $this->input->getInt('user_id', 0);
$app     = \Joomla\CMS\Factory::getApplication();

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
$current_user = \Joomla\CMS\Factory::getUser($this->input->getInt('user_id', $this->user->get('id')));

// required css file fix issues of UI/UX
\Joomla\CMS\Factory::getDocument()->addStyleSheet(\Joomla\CMS\Uri\Uri::root().'/media/com_joomcck/css/joomcck.css');

?>
<?php if ($markup->get('main.css')): ?>
    <style>
        <?php echo $markup->get('main.css');?>
    </style>
<?php endif; ?>

<?php echo Layout::render('core.markup.header',['current' => $this]) ?>

<div id="compare" <?php echo !$this->compare ? 'class="hide"' : ''; ?>>
    <div class="alert alert-info alert-block">
        <h4><?php echo Text::sprintf('CCOMPAREMSG', $this->compare) ?></h4>
        <br><a rel="nofollow"
               href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=compare&section_id=' . $this->section->id . '&return=' . Url::back()); ?>"
               class="btn btn-primary"><?php echo Text::_('CCOMPAREVIEW'); ?></a>
        <button onclick="Joomcck.CleanCompare(null, '<?php echo @$this->section->id ?>')"
                class="btn"><?php echo Text::_('CCLEANCOMPARE'); ?></button>
    </div>
</div>

<!-- --------------  Show description of the current category or section ---------------------- -->
<?php if ($this->description): ?>
    <div id="jcck-description-block">
	    <?php echo $this->description; ?>
    </div>
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
                                       placeholder="<?php echo Text::_('CSEARCHPLACEHOLDER'); ?>" name="filter_search"
                                       value="<?php echo htmlentities($this->state->get('records.search'), ENT_COMPAT, 'utf-8'); ?>"/>
							<?php endif; ?>
							<?php if (in_array($markup->get('filters.show_more'), $this->user->getAuthorisedViewLevels())): ?>
                                <button type="button" class="btn btn-sm btn-light border hasTooltip" data-bs-toggle="collapse"
                                        data-bs-target="#filter-collapse"
                                        title="<?php echo Text::_('CMORESEARCHOPTIONS') ?>">
									<?php echo HTMLFormatHelper::icon('binocular.png'); ?>
                                </button>
							<?php endif; ?>
                        </div>
                    </div>
				<?php endif; ?>

				<?php if ($markup->get('menu.menu')): ?>
                    <ul class="nav mt-3">
						<?php if (($app->input->getString('view_what') || $app->input->getInt('user_id') || $app->input->getInt('ucat_id') || $back) && $markup->get('menu.menu_all')): ?>
                            <li class="dropdown me-2">
                                <a class="btn btn-light btn-sm border" href="<?php echo $back ? $back : \Joomla\CMS\Router\Route::_(Url::records($this->section)) ?>">
									<?php if ($markup->get('menu.menu_all_records_icon')): ?>
										<?php echo HTMLFormatHelper::icon('navigation-180.png'); ?>
									<?php endif; ?>
									<?php echo $back ? Text::_('CGOBACK') : Text::_($markup->get('menu.menu_all_records', 'All Records')); ?>
                                </a>
                            </li>
						<?php endif; ?>

						<?php if ($app->input->getString('cat_id') && $markup->get('menu.menu_home_button')): ?>
                            <li class="dropdown me-2">
                                <a class="btn btn-light btn-sm border" href="<?php echo Url::records($this->section) ?>">
									<?php if ($markup->get('menu.menu_home_icon')): ?>
										<?php echo HTMLFormatHelper::icon($this->section->get('personalize.text_icon', 'home.png')); ?>
									<?php endif; ?>
									<?php echo Text::_($markup->get('menu.menu_home_label', 'Home')); ?>
                                </a>
                            </li>
						<?php endif; ?>

						<?php if (!empty($this->category->parent_id) && ($this->category->parent_id > 1) && $markup->get('menu.menu_up')): ?>
                            <li  class="dropdown me-2">
                                <a class="btn btn-light btn-sm border" href="<?php echo Url::records($this->section, $this->category->parent_id) ?>">
									<?php if ($markup->get('menu.menu_up_icon')): ?>
										<?php echo HTMLFormatHelper::icon('arrow-curve-090-left.png'); ?>
									<?php endif; ?>
									<?php echo Text::_($markup->get('menu.menu_up_label', 'Up')); ?>
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
										$o[] = '<a class="dropdown-item" href="' . Url::add($this->section, $type, $this->category) . '">' . Text::_($type->name) . '</a>';
									}
									else
									{
										$o[] = '<a  class="dropdown-item" disabled class="disabled hasTooltip" title="' . Text::sprintf($markup->get('menu.menu_user_register', 'Register or login to submit %s'), Text::_($type->name)) . '">' . Text::_($type->name) . '</a>';
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
											<?php echo Text::_($markup->get('menu.menu_newrecord_label', 'Post here')) ?>
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
                                            disabled class="disabled btn btn-light border btn-sm hasTooltip" href="#"
                                            title="<?php echo Text::sprintf($markup->get('menu.menu_user_register', 'Register or login to submit <b>%s</b>'), Text::_($submit->name)) ?>"
										<?php else: ?>
                                            class="btn btn-light border btn-sm" href="<?php echo Url::add($this->section, $submit, $this->category); ?>"
										<?php endif; ?>
                                    >
										<?php if ($markup->get('menu.menu_newrecord_icon')): ?>
											<?php echo HTMLFormatHelper::icon('plus.png'); ?>
										<?php endif; ?>
										<?php echo Text::sprintf($markup->get('menu.menu_user_single', 'Post %s here'), Text::_($submit->name)); ?>
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
									<?php echo Text::_($markup->get('menu.menu_templates_label', 'Switch view')) ?>
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
									<?php echo Text::_($markup->get('menu.menu_ordering_label', 'Sort By')) ?>
                                    <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
									<?php if (@$this->items[0]->searchresult): ?>
                                        <li>
											<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_ctime_icon') ? HTMLFormatHelper::icon('document-search-result.png') : null) . ' ' . Text::_('CORDERRELEVANCE'), 'searchresult', $listDirn, $listOrder); ?></li>
									<?php endif; ?>

									<?php if (in_array($markup->get('menu.menu_order_ctime'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li>
											<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_ctime_icon') ? HTMLFormatHelper::icon('core-ctime.png') : null) . ' ' . Text::_($markup->get('menu.menu_order_ctime_label', 'Created')), 'r.ctime', $listDirn, $listOrder); ?></li>
									<?php endif; ?>

									<?php if (in_array($markup->get('menu.menu_order_mtime'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li>
											<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_mtime_icon') ? HTMLFormatHelper::icon('core-ctime.png') : null) . ' ' . Text::_($markup->get('menu.menu_order_mtime_label', 'Modified')), 'r.mtime', $listDirn, $listOrder); ?></li>
									<?php endif; ?>
									<?php if (in_array($markup->get('menu.menu_order_extime'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li>
											<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_extime_icon') ? HTMLFormatHelper::icon('core-ctime.png') : null) . ' ' . Text::_($markup->get('menu.menu_order_extime_label', 'Expire')), 'r.extime', $listDirn, $listOrder); ?></li>
									<?php endif; ?>

									<?php if (in_array($markup->get('menu.menu_order_title'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li>
											<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_title_icon') ? HTMLFormatHelper::icon('edit.png') : null) . ' ' . Text::_($markup->get('menu.menu_order_title_label', 'Title')), 'r.title', $listDirn, $listOrder); ?></li>
									<?php endif; ?>

									<?php if (in_array($markup->get('menu.menu_order_hits'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li>
											<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_hits_icon') ? HTMLFormatHelper::icon('hand-point-090.png') : null) . ' ' . Text::_($markup->get('menu.menu_order_hits_label', 'Hist')), 'r.hits', $listDirn, $listOrder); ?></li>
									<?php endif; ?>

									<?php if (in_array($markup->get('menu.menu_order_votes_result'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li>
											<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_votes_result_icon') ? HTMLFormatHelper::icon('star.png') : null) . ' ' . Text::_($markup->get('menu.menu_order_votes_result_label', 'Votes')), 'r.votes_result', $listDirn, $listOrder); ?></li>
									<?php endif; ?>

									<?php if (in_array($markup->get('menu.menu_order_comments'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li>
											<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_comments_icon') ? HTMLFormatHelper::icon('balloon-left.png') : null) . ' ' . Text::_($markup->get('menu.menu_order_comments_label', 'Comments')), 'r.comments', $listDirn, $listOrder); ?></li>
									<?php endif; ?>

									<?php if (in_array($markup->get('menu.menu_order_favorite_num'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li>
											<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_favorite_num_icon') ? '<img src="' . \Joomla\CMS\Uri\Uri::root(true) . '/media/com_joomcck/icons/bookmarks/' . $listparams->get('tmpl_core.bookmark_icons', 'star') . '/state1.png" > ' : null) . ' ' . Text::_($markup->get('menu.menu_order_favorite_num_label', 'Number of bookmarks')), 'r.favorite_num', $listDirn, $listOrder); ?></li>
									<?php endif; ?>

									<?php if (in_array($markup->get('menu.menu_order_username'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li>
											<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_username_icon') ? HTMLFormatHelper::icon('user.png') : null) . ' ' . Text::_($markup->get('menu.menu_order_username_label', 'user name')), $this->section->params->get('personalize.author_mode'), $listDirn, $listOrder); ?></li>
									<?php endif; ?>

									<?php if (in_array($markup->get('menu.menu_order_fields'), $this->user->getAuthorisedViewLevels())): ?>
										<?php foreach ($this->sortable as $field): ?>
                                            <li>
												<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_fields_icon') && ($icon = $field->params->get('core.icon')) ? HTMLFormatHelper::icon($icon) : null) . ' ' . Text::_($field->label), FieldHelper::sortName($field), $listDirn, $listOrder); ?></li>
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
									<?php echo Text::_($markup->get('menu.menu_user_label', 'My Menu')) ?>
                                    <b class="caret"></b>
                                </a>
                                <ul class="dropdown-menu">
									<?php if ($markup->get('menu.menu_user_my')): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('created')); ?>">
												<?php if ($markup->get('menu.menu_user_my_icon')): ?>
													<?php echo HTMLFormatHelper::icon($this->section->params->get('personalize.text_icon', 'home.png')); ?>
												<?php endif; ?>
												<?php echo Text::_($markup->get('menu.menu_user_my_label', 'My Homepage')) ?>
                                                <span class="badge text-bg-light shadow-sm px-2 py-1"><?php echo $counts->created; ?></span>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_followed') && $counts->followed): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('follow')); ?>">
												<?php if ($markup->get('menu.menu_user_follow_icon')): ?>
                                                    <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/follow1.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo Text::_($markup->get('menu.menu_user_follow_label', 'Watched')) ?>
                                                <span class="badge text-bg-light shadow-sm px-2 py-1"><?php echo $counts->followed; ?></span>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_evented') && CEventsHelper::getNum('section', $this->section->id)): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('events')); ?>">
												<?php if ($markup->get('menu.menu_user_events_icon')): ?>
                                                    <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/bell.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo Text::_($markup->get('menu.menu_user_events_label', 'With new events')) ?>
												<?php echo CEventsHelper::showNum('section', $this->section->id) ?>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_favorite') && $counts->favorited): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('favorited')); ?>">
												<?php if ($markup->get('menu.menu_user_favorite_icon')): ?>
                                                    <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true) . '/media/com_joomcck/icons/bookmarks/' . $listparams->get('tmpl_core.bookmark_icons', 'star') . '/state1.png'; ?>"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo Text::_($markup->get('menu.menu_user_favorite_label', 'Bookmarked')) ?>
                                                <span class="badge text-bg-light shadow-sm px-2 py-1"><?php echo $counts->favorited; ?></span>
                                            </a></li>
									<?php endif; ?>
									<?php if ($markup->get('menu.menu_user_rated') && $counts->rated): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('rated')); ?>">
												<?php if ($markup->get('menu.menu_user_rated_icon')): ?>
                                                    <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/star.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo Text::_($markup->get('menu.menu_user_rated_label', 'Rated')) ?>
                                                <span class="badge text-bg-light shadow-sm px-2 py-1"><?php echo $counts->rated; ?></span>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_commented') && $counts->commented): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('commented')); ?>">
												<?php if ($markup->get('menu.menu_user_commented_icon')): ?>
                                                    <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/balloon-left.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo Text::_($markup->get('menu.menu_user_commented_label', 'Commented')) ?>
                                                <span class="badge text-bg-light shadow-sm px-2 py-1"><?php echo $counts->commented; ?></span>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_visited') && $counts->visited): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('visited')); ?>">
												<?php if ($markup->get('menu.menu_user_visited_icon')): ?>
                                                    <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/hand-point-090.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo Text::_($markup->get('menu.menu_user_visited_label', 'Visited')) ?>
                                                <span class="badge text-bg-light shadow-sm px-2 py-1"><?php echo $counts->visited; ?></span>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_expire') && $counts->expired): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('expired')); ?>">
												<?php if ($markup->get('menu.menu_user_expire_icon')): ?>
                                                    <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/clock--exclamation.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo Text::_($markup->get('menu.menu_user_expire_label', 'Expired')) ?>
                                                <span class="badge text-bg-light shadow-sm px-2 py-1"><?php echo $counts->expired; ?></span>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_hidden') && $counts->hidden): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('hidden')); ?>">
												<?php if ($markup->get('menu.menu_user_hidden_icon')): ?>
                                                    <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/eye-half.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo Text::_($markup->get('menu.menu_user_hidden_label', 'Hidden')) ?>
                                                <span class="badge text-bg-light shadow-sm px-2 py-1"><?php echo $counts->hidden; ?></span>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_feature') && $counts->featured): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('featured')); ?>">
												<?php if ($markup->get('menu.menu_user_feature_icon')): ?>
                                                    <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/arrow-curve-090-left.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo Text::_($markup->get('menu.menu_user_feature_label', 'Fetured')) ?>
                                                <span class="badge text-bg-light shadow-sm px-2 py-1"><?php echo $counts->featured; ?></span>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_unpublished') && $counts->unpublished): ?>
                                        <li><a class="dropdown-item"
                                               href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('unpublished')); ?>">
												<?php if ($markup->get('menu.menu_user_unpublished_icon')): ?>
                                                    <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/minus-circle.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo Text::_($markup->get('menu.menu_user_unpublished_label', 'On Approval')) ?>
                                                <span class="badge text-bg-light shadow-sm px-2 py-1"><?php echo $counts->unpublished; ?></span>
                                            </a></li>
									<?php endif; ?>


									<?php if ($markup->get('menu.menu_user_moder') && MECAccess::allowModerate(null, null, $this->section)): ?>
                                        <li class="divider"></li>
                                        <li><a class="dropdown-item"
                                               href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=moderators&filter_section=' . $this->section->id . '&return=' . Url::back()); ?>">
												<?php if ($markup->get('menu.menu_user_moder_icon')): ?>
                                                    <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/user-share.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo Text::_($markup->get('menu.menu_user_moder_label', 'Moderators')) ?>
                                            </a></li>
									<?php endif; ?>

									<?php if ($this->section->params->get('personalize.personalize') && $this->section->params->get('personalize.allow_section_set')): ?>
                                        <li class="divider"></li>
                                        <li>
                                            <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=options&layout=section&section_id=' . $this->section->id . '&return=' . Url::back()); ?>">
												<?php if ($markup->get('menu.menu_user_subscribe_icon')): ?>
                                                    <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/gear.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo Text::_($markup->get('menu.menu_user_subscribe_label', 'Options')) ?>
                                            </a></li>
									<?php endif; ?>

									<?php if ($markup->get('menu.menu_user_cat_manage') && in_array($this->section->params->get('personalize.pcat_submit'), $this->user->getAuthorisedViewLevels())): ?>
                                        <li class="divider"></li>
                                        <li class="dropdown-submenu">
                                            <a tabindex="-1"
                                               href="<?php echo \Joomla\CMS\Router\Route::_(Url::_('categories') . '&return=' . Url::back()) ?>">
												<?php if ($markup->get('menu.menu_user_cat_manage_icon')): ?>
                                                    <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/category.png"
                                                         align="absmiddle"/>
												<?php endif; ?>
												<?php echo Text::_($markup->get('menu.menu_user_cat_manage_label', 'Categories')) ?>
                                                <span class="badge text-bg-light shadow-sm px-2 py-1"><?php echo $counts->categories; ?></span>
                                            </a>
											<?php if ($markup->get('menu.menu_user_cat_add')): ?>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" tabindex="-1"
                                                           href="<?php echo \Joomla\CMS\Router\Route::_(Url::_('category')) ?>">
															<?php if ($markup->get('menu.menu_user_cat_add_icon')): ?>
                                                                <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/plus.png"
                                                                     align="absmiddle"/>
															<?php endif; ?>
															<?php echo Text::_($markup->get('menu.menu_user_cat_add_label', 'Add new category')) ?>
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

    <!-- filters layout -->
	<?php echo Layout::render('core.markup.filters',['current' => $this], null, ['client' => 'site','component' => 'com_joomcck']) ?>

    <?php endif; ?>

    <input type="hidden" name="section_id" value="<?php echo $this->state->get('records.section_id') ?>">
    <input type="hidden" name="cat_id" value="<?php echo $app->input->getInt('cat_id'); ?>">
    <input type="hidden" name="option" value="com_joomcck">
    <input type="hidden" name="task" value="">
    <input type="hidden" name="limitstart" value="0">
    <input type="hidden" name="filter_order" value="<?php //echo $this->ordering; ?>">
    <input type="hidden" name="filter_order_Dir" value="<?php //echo $this->ordering_dir; ?>">
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
	<?php if ($this->worns): ?>
		<?php foreach ($this->worns as $worn): ?>
            <input type="hidden" name="clean[<?php echo $worn->name; ?>]" id="<?php echo $worn->name; ?>" value="">
		<?php endforeach; ?>
	<?php endif; ?>
</form>

<!-- Show category index -->
<?php if ($this->show_category_index): ?>
    <DIV class="clearfix"></DIV>
	<?php echo $this->loadTemplate('cindex_' . $this->section->params->get('general.tmpl_category')); ?>
<?php endif; ?>

<?php echo Layout::render('core.markup.alphaIndex',['current' => $this]); ?>

<!-- Filters worns -->
<?php if ($markup->get('filters.worns') && count($this->worns)): ?>
    <div class="filter-worns">
		<?php foreach ($this->worns as $worn): ?>
            <div class="alert alert-info alert-dismissible fade show float-start" role="alert">

                <div><i class="fas fa-filter"></i> <?php echo $worn->label ?></div>
				<?php echo $worn->text ?>
                <button type="button" class="btn-close hasTooltip" data-bs-dismiss="alert"  aria-label="Close"
                        onclick="Joomcck.cleanFilter('<?php echo $worn->name ?>')"
                        title="<?php echo Text::_('CDELETEFILTER') ?>">
                </button>
            </div>
		<?php endforeach; ?>
		<?php if (count($this->worns) > 1): ?>
            <button onclick="Joomla.submitbutton('records.cleanall');" class="alert alert-danger  float-start">
                <div><?php echo Text::_('CORESET'); ?></div>
				<?php echo Text::_('CODELETEALLFILTERS'); ?>
            </button>
		<?php endif; ?>

        <div class="clearfix"></div>
    </div>
    <br>
<?php endif; ?>


<?php if ($this->items): ?>

	<?php echo $this->loadTemplate('list_' . $this->list_template); ?>

	<?php echo Layout::render('core.list.pagination',['params' => $this->tmpl_params['list'],'pagination' => $this->pagination]) ?>

<?php elseif ($this->worns): ?>
    <h4 align="center"><?php echo Text::_('CNORECFOUNDSEARCH'); ?></h4>
<?php else: ?>
	<?php if (
            ((!empty($this->category->id) && $this->category->params->get('submission')) || (empty($this->category->id) && $this->section->params->get('general.section_home_items'))) && !$this->input->get('view_what') && $markup->get('main.display_no_records_warning',1)): ?>
        <p  class="jcck-no-records alert alert-warning" id="no-records<?php echo $this->section->id; ?>">
            <i class="fas fa-exclamation-triangle"></i> <?php echo Text::_('CNOARTICLESHERE'); ?>
        </p>
	<?php endif; ?>
<?php endif; ?>
