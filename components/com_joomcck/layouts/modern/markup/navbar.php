<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Navbar Layout
 *
 * Tailwind CSS toolbar with search, navigation, post buttons, ordering, user menu.
 * Replaces Bootstrap navbar/dropdown with flex layout + vanilla JS dropdowns.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

extract($displayData);

$app = \Joomla\CMS\Factory::getApplication();
$markup = $current->tmpl_params['markup'];
$listparams = $current->tmpl_params['list'];
$listOrder = @$current->ordering;
$listDirn = @$current->ordering_dir;

$back = null;
if ($current->input->getString('return')) {
	$back = Url::get_back('return');
}

if (!in_array($markup->get('menu.menu'), $current->user->getAuthorisedViewLevels()) && !in_array($markup->get('menu.menu'), $current->user->getAuthorisedViewLevels())) return;

?>

<div id="cnav" class="bg-white rounded-lg border border-gray-200 shadow-sm mb-4 p-3">
	<div class="flex flex-wrap items-center gap-2">

		<?php // Search + filter toggle ?>
		<?php if ($markup->get('filters.filters')): ?>
			<div class="ml-auto flex items-center gap-1 order-last">
				<?php if (in_array($markup->get('filters.show_search'), $current->user->getAuthorisedViewLevels())): ?>
					<input class="w-full border border-gray-300 rounded px-3 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary"
						   type="text"
						   style="max-width: 140px; min-width: 80px;"
						   placeholder="<?php echo Text::_('CSEARCHPLACEHOLDER'); ?>"
						   name="filter_search"
						   value="<?php echo htmlentities($current->state->get('records.search'), ENT_COMPAT, 'utf-8'); ?>"/>
				<?php endif; ?>
				<?php if (in_array($markup->get('filters.show_more'), $current->user->getAuthorisedViewLevels())): ?>
					<button type="button"
							class="bg-white border border-gray-300 text-gray-700 px-2 py-1.5 rounded text-sm hover:bg-gray-50 transition-colors"
							onclick="document.getElementById('filter-collapse').classList.toggle('hidden')"
							title="<?php echo Text::_('CMORESEARCHOPTIONS') ?>">
						<?php echo HTMLFormatHelper::icon('binocular.png'); ?>
					</button>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php // Navigation buttons ?>
		<?php if ($markup->get('menu.menu')): ?>
			<?php if (($app->input->getString('view_what') || $app->input->getInt('user_id') || $app->input->getInt('ucat_id') || $back) && $markup->get('menu.menu_all')): ?>
				<a class="bg-white border border-gray-300 text-gray-700 px-2 py-1 rounded text-sm hover:bg-gray-50 transition-colors no-underline"
				   href="<?php echo $back ? $back : \Joomla\CMS\Router\Route::_(Url::records($current->section)) ?>">
					<?php if ($markup->get('menu.menu_all_records_icon')): ?>
						<?php echo HTMLFormatHelper::icon('navigation-180.png'); ?>
					<?php endif; ?>
					<?php echo $back ? Text::_('CGOBACK') : Text::_($markup->get('menu.menu_all_records', 'All Records')); ?>
				</a>
			<?php endif; ?>

			<?php if ($app->input->getString('cat_id') && $markup->get('menu.menu_home_button')): ?>
				<a class="bg-white border border-gray-300 text-gray-700 px-2 py-1 rounded text-sm hover:bg-gray-50 transition-colors no-underline"
				   href="<?php echo Url::records($current->section) ?>">
					<?php if ($markup->get('menu.menu_home_icon')): ?><?php echo HTMLFormatHelper::icon($current->section->get('personalize.text_icon', 'home.png')); ?><?php endif; ?>
					<?php echo Text::_($markup->get('menu.menu_home_label', 'Home')); ?>
				</a>
			<?php endif; ?>

			<?php if (!empty($current->category->parent_id) && ($current->category->parent_id > 1) && $markup->get('menu.menu_up')): ?>
				<a class="bg-white border border-gray-300 text-gray-700 px-2 py-1 rounded text-sm hover:bg-gray-50 transition-colors no-underline"
				   href="<?php echo Url::records($current->section, $current->category->parent_id) ?>">
					<?php if ($markup->get('menu.menu_up_icon')): ?><?php echo HTMLFormatHelper::icon('arrow-curve-090-left.png'); ?><?php endif; ?>
					<?php echo Text::_($markup->get('menu.menu_up_label', 'Up')); ?>
				</a>
			<?php endif; ?>

			<?php // Post buttons ?>
			<?php if (!empty($current->postbuttons)): ?>
				<?php if (count($current->postbuttons) > 1): ?>
					<?php $l = array();
					foreach ($current->postbuttons as $type) {
						$o = array();
						if (in_array($type->params->get('submission.submission'), $current->user->getAuthorisedViewLevels()) || MECAccess::allowNew($type, $current->section)) {
							$o[] = '<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 no-underline" href="' . Url::add($current->section, $type, $current->category) . '">' . Text::_($type->name) . '</a>';
						} else {
							$o[] = '<span class="block px-4 py-2 text-sm text-gray-400 cursor-not-allowed" title="' . Text::sprintf($markup->get('menu.menu_user_register', 'Register or login to submit %s'), Text::_($type->name)) . '">' . Text::_($type->name) . '</span>';
						}
						if ($o) $l[] = implode('', $o);
					}
					?>
					<?php if ($l): ?>
						<div class="relative jcck-dd">
							<button type="button" class="bg-white border border-gray-300 text-gray-700 px-2 py-1 rounded text-sm hover:bg-gray-50 transition-colors flex items-center gap-1"
									onclick="this.nextElementSibling.classList.toggle('hidden')">
								<?php if ($markup->get('menu.menu_newrecord_icon')): ?><?php echo HTMLFormatHelper::icon('plus.png'); ?><?php endif; ?>
								<?php echo Text::_($markup->get('menu.menu_newrecord_label', 'Post here')) ?>
								<i class="fas fa-chevron-down text-xs ml-1"></i>
							</button>
							<div class="hidden absolute left-0 z-20 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 py-1 min-w-48">
								<?php echo implode("\n", $l); ?>
							</div>
						</div>
					<?php endif; ?>
				<?php elseif (count($current->postbuttons) == 1) : ?>
					<?php $submit = array_values($current->postbuttons); $submit = array_shift($submit); ?>
					<a <?php if (!(in_array($submit->params->get('submission.submission'), $current->user->getAuthorisedViewLevels()) || MECAccess::allowNew($submit, $current->section))): ?>
							class="bg-white border border-gray-300 text-gray-400 px-2 py-1 rounded text-sm cursor-not-allowed no-underline" href="#"
							title="<?php echo Text::sprintf($markup->get('menu.menu_user_register', 'Register or login to submit <b>%s</b>'), Text::_($submit->name)) ?>"
						<?php else: ?>
							class="bg-white border border-gray-300 text-gray-700 px-2 py-1 rounded text-sm hover:bg-gray-50 transition-colors no-underline" href="<?php echo Url::add($current->section, $submit, $current->category); ?>"
						<?php endif; ?>>
						<?php if ($markup->get('menu.menu_newrecord_icon')): ?><?php echo HTMLFormatHelper::icon('plus.png'); ?><?php endif; ?>
						<?php echo Text::sprintf($markup->get('menu.menu_user_single', 'Post %s here'), Text::_($submit->name)); ?>
					</a>
				<?php endif; ?>
			<?php endif; ?>

			<?php // Template switcher ?>
			<?php if (count($current->list_templates) > 1 && in_array($markup->get('menu.menu_templates'), $current->user->getAuthorisedViewLevels()) && $current->items): ?>
				<div class="relative jcck-dd">
					<button type="button" class="bg-white border border-gray-300 text-gray-700 px-2 py-1 rounded text-sm hover:bg-gray-50 transition-colors flex items-center gap-1"
							onclick="this.nextElementSibling.classList.toggle('hidden')">
						<?php if ($markup->get('menu.menu_templates_icon',0)): ?><?php echo HTMLFormatHelper::icon('zones.png'); ?><?php endif; ?>
						<?php echo Text::_($markup->get('menu.menu_templates_label', 'Switch view')) ?>
						<i class="fas fa-chevron-down text-xs ml-1"></i>
					</button>
					<div class="hidden absolute left-0 z-20 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 py-1 min-w-48">
						<?php foreach ($current->list_templates as $id => $template): ?>
							<?php $tmpl = explode('.', $id); $tmpl = $tmpl[0]; ?>
							<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 no-underline cursor-pointer"
							   onclick="Joomcck.applyFilter('filter_tpl', '<?php echo $id ?>')">
								<?php echo ($current->list_template == $tmpl) ? '<strong>' : ''; ?>
								<?php echo $template; ?>
								<?php echo ($current->list_template == $tmpl) ? '</strong>' : ''; ?>
							</a>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php // Ordering menu ?>
			<?php if (in_array($markup->get('menu.menu_ordering'), $current->user->getAuthorisedViewLevels()) && $current->items): ?>
				<div class="relative jcck-dd">
					<button type="button" class="bg-white border border-gray-300 text-gray-700 px-2 py-1 rounded text-sm hover:bg-gray-50 transition-colors flex items-center gap-1"
							onclick="this.nextElementSibling.classList.toggle('hidden')">
						<?php if ($markup->get('menu.menu_ordering_icon')): ?><?php echo HTMLFormatHelper::icon('sort.png'); ?><?php endif; ?>
						<?php echo Text::_($markup->get('menu.menu_ordering_label', 'Sort By')) ?>
						<i class="fas fa-chevron-down text-xs ml-1"></i>
					</button>
					<div class="hidden absolute left-0 z-20 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 py-1 min-w-48
								[&_a]:block [&_a]:px-4 [&_a]:py-2 [&_a]:text-sm [&_a]:text-gray-700 [&_a]:no-underline [&_a:hover]:bg-gray-50">
						<?php if (@$current->items[0]->searchresult): ?>
							<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_ctime_icon') ? HTMLFormatHelper::icon('document-search-result.png') : null) . ' ' . Text::_('CORDERRELEVANCE'), 'searchresult', $listDirn, $listOrder); ?>
						<?php endif; ?>
						<?php if (in_array($markup->get('menu.menu_order_ctime'), $current->user->getAuthorisedViewLevels())): ?>
							<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_ctime_icon') ? HTMLFormatHelper::icon('core-ctime.png') : null) . ' ' . Text::_($markup->get('menu.menu_order_ctime_label', 'Created')), 'r.ctime', $listDirn, $listOrder); ?>
						<?php endif; ?>
						<?php if (in_array($markup->get('menu.menu_order_mtime'), $current->user->getAuthorisedViewLevels())): ?>
							<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_mtime_icon') ? HTMLFormatHelper::icon('core-ctime.png') : null) . ' ' . Text::_($markup->get('menu.menu_order_mtime_label', 'Modified')), 'r.mtime', $listDirn, $listOrder); ?>
						<?php endif; ?>
						<?php if (in_array($markup->get('menu.menu_order_extime'), $current->user->getAuthorisedViewLevels())): ?>
							<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_extime_icon') ? HTMLFormatHelper::icon('core-ctime.png') : null) . ' ' . Text::_($markup->get('menu.menu_order_extime_label', 'Expire')), 'r.extime', $listDirn, $listOrder); ?>
						<?php endif; ?>
						<?php if (in_array($markup->get('menu.menu_order_title'), $current->user->getAuthorisedViewLevels())): ?>
							<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_title_icon') ? HTMLFormatHelper::icon('edit.png') : null) . ' ' . Text::_($markup->get('menu.menu_order_title_label', 'Title')), 'r.title', $listDirn, $listOrder); ?>
						<?php endif; ?>
						<?php if (in_array($markup->get('menu.menu_order_hits'), $current->user->getAuthorisedViewLevels())): ?>
							<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_hits_icon') ? HTMLFormatHelper::icon('hand-point-090.png') : null) . ' ' . Text::_($markup->get('menu.menu_order_hits_label', 'Hist')), 'r.hits', $listDirn, $listOrder); ?>
						<?php endif; ?>
						<?php if (in_array($markup->get('menu.menu_order_votes_result'), $current->user->getAuthorisedViewLevels())): ?>
							<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_votes_result_icon') ? HTMLFormatHelper::icon('star.png') : null) . ' ' . Text::_($markup->get('menu.menu_order_votes_result_label', 'Votes')), 'r.votes_result', $listDirn, $listOrder); ?>
						<?php endif; ?>
						<?php if (in_array($markup->get('menu.menu_order_comments'), $current->user->getAuthorisedViewLevels())): ?>
							<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_comments_icon') ? HTMLFormatHelper::icon('balloon-left.png') : null) . ' ' . Text::_($markup->get('menu.menu_order_comments_label', 'Comments')), 'r.comments', $listDirn, $listOrder); ?>
						<?php endif; ?>
						<?php if (in_array($markup->get('menu.menu_order_favorite_num'), $current->user->getAuthorisedViewLevels())): ?>
							<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_favorite_num_icon') ? '<img src="' . \Joomla\CMS\Uri\Uri::root(true) . '/media/com_joomcck/icons/bookmarks/' . $listparams->get('tmpl_core.bookmark_icons', 'star') . '/state1.png" > ' : null) . ' ' . Text::_($markup->get('menu.menu_order_favorite_num_label', 'Number of bookmarks')), 'r.favorite_num', $listDirn, $listOrder); ?>
						<?php endif; ?>
						<?php if (in_array($markup->get('menu.menu_order_username'), $current->user->getAuthorisedViewLevels())): ?>
							<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_username_icon') ? HTMLFormatHelper::icon('user.png') : null) . ' ' . Text::_($markup->get('menu.menu_order_username_label', 'user name')), $current->section->params->get('personalize.author_mode'), $listDirn, $listOrder); ?>
						<?php endif; ?>
						<?php if (in_array($markup->get('menu.menu_order_fields'), $current->user->getAuthorisedViewLevels())): ?>
							<?php foreach ($current->sortable as $field): ?>
								<?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.sort', ($markup->get('menu.menu_order_fields_icon') && ($icon = $field->params->get('core.icon')) ? HTMLFormatHelper::icon($icon) : null) . ' ' . Text::_($field->label), FieldHelper::sortName($field), $listDirn, $listOrder); ?>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>

			<?php // User menu ?>
			<?php if (in_array($markup->get('menu.menu_user'), $current->user->getAuthorisedViewLevels()) && $current->user->id && !$current->isMe): ?>
				<?php $counts = $current->_getUsermenuCounts($markup); ?>
				<div class="relative jcck-dd" id="joomcck-user-menu">
					<button type="button" class="bg-white border border-gray-300 text-gray-700 px-2 py-1 rounded text-sm hover:bg-gray-50 transition-colors flex items-center gap-1"
							onclick="this.nextElementSibling.classList.toggle('hidden')">
						<?php if ($markup->get('menu.menu_user_icon')): ?><?php echo HTMLFormatHelper::icon('user.png'); ?><?php endif; ?>
						<?php echo Text::_($markup->get('menu.menu_user_label', 'My Menu')) ?>
						<i class="fas fa-chevron-down text-xs ml-1"></i>
					</button>
					<div class="hidden absolute right-0 z-20 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 py-1 min-w-56">
						<?php if ($markup->get('menu.menu_user_my')): ?>
							<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 no-underline" href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('created')); ?>"><?php if ($markup->get('menu.menu_user_my_icon')): ?><?php echo HTMLFormatHelper::icon($current->section->params->get('personalize.text_icon', 'home.png')); ?> <?php endif; ?><?php echo Text::_($markup->get('menu.menu_user_my_label', 'My Homepage')) ?> <span class="bg-gray-100 text-gray-600 text-xs px-1.5 py-0.5 rounded-full"><?php echo $counts->created; ?></span></a>
						<?php endif; ?>
						<?php if ($markup->get('menu.menu_user_followed') && $counts->followed): ?>
							<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 no-underline" href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('follow')); ?>"><?php if ($markup->get('menu.menu_user_follow_icon')): ?><img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/follow1.png" align="absmiddle"/> <?php endif; ?><?php echo Text::_($markup->get('menu.menu_user_follow_label', 'Watched')) ?> <span class="bg-gray-100 text-gray-600 text-xs px-1.5 py-0.5 rounded-full"><?php echo $counts->followed; ?></span></a>
						<?php endif; ?>
						<?php if ($markup->get('menu.menu_user_evented') && CEventsHelper::getNum('section', $current->section->id)): ?>
							<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 no-underline" href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('events')); ?>"><?php if ($markup->get('menu.menu_user_events_icon')): ?><img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/bell.png" align="absmiddle"/> <?php endif; ?><?php echo Text::_($markup->get('menu.menu_user_events_label', 'With new events')) ?> <?php echo CEventsHelper::showNum('section', $current->section->id) ?></a>
						<?php endif; ?>
						<?php if ($markup->get('menu.menu_user_favorite') && $counts->favorited): ?>
							<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 no-underline" href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('favorited')); ?>"><?php if ($markup->get('menu.menu_user_favorite_icon')): ?><img src="<?php echo \Joomla\CMS\Uri\Uri::root(true) . '/media/com_joomcck/icons/bookmarks/' . $listparams->get('tmpl_core.bookmark_icons', 'star') . '/state1.png'; ?>" align="absmiddle"/> <?php endif; ?><?php echo Text::_($markup->get('menu.menu_user_favorite_label', 'Bookmarked')) ?> <span class="bg-gray-100 text-gray-600 text-xs px-1.5 py-0.5 rounded-full"><?php echo $counts->favorited; ?></span></a>
						<?php endif; ?>
						<?php if ($markup->get('menu.menu_user_rated') && $counts->rated): ?>
							<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 no-underline" href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('rated')); ?>"><?php if ($markup->get('menu.menu_user_rated_icon')): ?><img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/star.png" align="absmiddle"/> <?php endif; ?><?php echo Text::_($markup->get('menu.menu_user_rated_label', 'Rated')) ?> <span class="bg-gray-100 text-gray-600 text-xs px-1.5 py-0.5 rounded-full"><?php echo $counts->rated; ?></span></a>
						<?php endif; ?>
						<?php if ($markup->get('menu.menu_user_commented') && $counts->commented): ?>
							<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 no-underline" href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('commented')); ?>"><?php if ($markup->get('menu.menu_user_commented_icon')): ?><img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/balloon-left.png" align="absmiddle"/> <?php endif; ?><?php echo Text::_($markup->get('menu.menu_user_commented_label', 'Commented')) ?> <span class="bg-gray-100 text-gray-600 text-xs px-1.5 py-0.5 rounded-full"><?php echo $counts->commented; ?></span></a>
						<?php endif; ?>
						<?php if ($markup->get('menu.menu_user_visited') && $counts->visited): ?>
							<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 no-underline" href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('visited')); ?>"><?php if ($markup->get('menu.menu_user_visited_icon')): ?><img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/hand-point-090.png" align="absmiddle"/> <?php endif; ?><?php echo Text::_($markup->get('menu.menu_user_visited_label', 'Visited')) ?> <span class="bg-gray-100 text-gray-600 text-xs px-1.5 py-0.5 rounded-full"><?php echo $counts->visited; ?></span></a>
						<?php endif; ?>
						<?php if ($markup->get('menu.menu_user_expire') && $counts->expired): ?>
							<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 no-underline" href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('expired')); ?>"><?php if ($markup->get('menu.menu_user_expire_icon')): ?><img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/clock--exclamation.png" align="absmiddle"/> <?php endif; ?><?php echo Text::_($markup->get('menu.menu_user_expire_label', 'Expired')) ?> <span class="bg-gray-100 text-gray-600 text-xs px-1.5 py-0.5 rounded-full"><?php echo $counts->expired; ?></span></a>
						<?php endif; ?>
						<?php if ($markup->get('menu.menu_user_hidden') && $counts->hidden): ?>
							<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 no-underline" href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('hidden')); ?>"><?php if ($markup->get('menu.menu_user_hidden_icon')): ?><img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/eye-half.png" align="absmiddle"/> <?php endif; ?><?php echo Text::_($markup->get('menu.menu_user_hidden_label', 'Hidden')) ?> <span class="bg-gray-100 text-gray-600 text-xs px-1.5 py-0.5 rounded-full"><?php echo $counts->hidden; ?></span></a>
						<?php endif; ?>
						<?php if ($markup->get('menu.menu_user_feature') && $counts->featured): ?>
							<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 no-underline" href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('featured')); ?>"><?php if ($markup->get('menu.menu_user_feature_icon')): ?><img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/arrow-curve-090-left.png" align="absmiddle"/> <?php endif; ?><?php echo Text::_($markup->get('menu.menu_user_feature_label', 'Fetured')) ?> <span class="bg-gray-100 text-gray-600 text-xs px-1.5 py-0.5 rounded-full"><?php echo $counts->featured; ?></span></a>
						<?php endif; ?>
						<?php if ($markup->get('menu.menu_user_unpublished') && $counts->unpublished): ?>
							<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 no-underline" href="<?php echo \Joomla\CMS\Router\Route::_(Url::user('unpublished')); ?>"><?php if ($markup->get('menu.menu_user_unpublished_icon')): ?><img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/minus-circle.png" align="absmiddle"/> <?php endif; ?><?php echo Text::_($markup->get('menu.menu_user_unpublished_label', 'On Approval')) ?> <span class="bg-gray-100 text-gray-600 text-xs px-1.5 py-0.5 rounded-full"><?php echo $counts->unpublished; ?></span></a>
						<?php endif; ?>
						<?php if ($markup->get('menu.menu_user_moder') && MECAccess::allowModerate(null, null, $current->section)): ?>
							<div class="border-t border-gray-200 my-1"></div>
							<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 no-underline" href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=moderators&filter_section=' . $current->section->id . '&return=' . Url::back()); ?>"><?php if ($markup->get('menu.menu_user_moder_icon')): ?><img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/user-share.png" align="absmiddle"/> <?php endif; ?><?php echo Text::_($markup->get('menu.menu_user_moder_label', 'Moderators')) ?></a>
						<?php endif; ?>
						<?php if ($current->section->params->get('personalize.personalize') && $current->section->params->get('personalize.allow_section_set')): ?>
							<div class="border-t border-gray-200 my-1"></div>
							<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 no-underline" href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=options&layout=section&section_id=' . $current->section->id . '&return=' . Url::back()); ?>"><?php if ($markup->get('menu.menu_user_subscribe_icon')): ?><img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/gear.png" align="absmiddle"/> <?php endif; ?><?php echo Text::_($markup->get('menu.menu_user_subscribe_label', 'Options')) ?></a>
						<?php endif; ?>
						<?php if ($markup->get('menu.menu_user_cat_manage') && in_array($current->section->params->get('personalize.pcat_submit'), $current->user->getAuthorisedViewLevels())): ?>
							<div class="border-t border-gray-200 my-1"></div>
							<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 no-underline" href="<?php echo \Joomla\CMS\Router\Route::_(Url::_('categories') . '&return=' . Url::back()) ?>"><?php if ($markup->get('menu.menu_user_cat_manage_icon')): ?><img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/category.png" align="absmiddle"/> <?php endif; ?><?php echo Text::_($markup->get('menu.menu_user_cat_manage_label', 'Categories')) ?> <span class="bg-gray-100 text-gray-600 text-xs px-1.5 py-0.5 rounded-full"><?php echo $counts->categories; ?></span></a>
							<?php if ($markup->get('menu.menu_user_cat_add')): ?>
								<a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 no-underline pl-8" href="<?php echo \Joomla\CMS\Router\Route::_(Url::_('category')) ?>"><?php if ($markup->get('menu.menu_user_cat_add_icon')): ?><img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/media/com_joomcck/icons/16/plus.png" align="absmiddle"/> <?php endif; ?><?php echo Text::_($markup->get('menu.menu_user_cat_add_label', 'Add new category')) ?></a>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>

<script>
(function() {
	// Close all dropdowns when clicking outside
	document.addEventListener('click', function(e) {
		if (!e.target.closest('.jcck-dd')) {
			document.querySelectorAll('.jcck-dd > div').forEach(function(m) { m.classList.add('hidden'); });
		}
	});

	// Hide empty user menu
	var userMenu = document.getElementById('joomcck-user-menu');
	if (userMenu) {
		var menuItems = userMenu.querySelectorAll('a');
		if (!menuItems || menuItems.length === 0) userMenu.style.display = 'none';
	}

	// Hide form if navbar is empty
	var cnav = document.getElementById('cnav');
	if (cnav && !cnav.textContent.trim()) {
		var form = document.getElementById('adminForm');
		if (form) form.style.display = 'none';
	}
})();
</script>
