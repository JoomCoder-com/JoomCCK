<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Html\Helpers\Dropdown;
use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');

\Joomla\CMS\HTML\HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
\Joomla\CMS\HTML\HTMLHelper::_('dropdown.init');
\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.modal', 'a.modal');
\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');
\Joomla\CMS\HTML\HTMLHelper::_('formbehavior.chosen', '.select');

$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
$userId = $user->get('id');

$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<div class="page-header">
    <h1>
        <img src="<?php echo Uri::root(TRUE); ?>/components/com_joomcck/images/icons/items.png">
		<?php echo Text::_('XML_TOOLBAR_TITLE_RECORDS'); ?>
    </h1>
</div>

<?php echo HTMLFormatHelper::layout('items', $this); ?>

<div class="clearfix"></div>

<form action="<?php echo Uri::getInstance()->toString() ?>" method="post" id="adminForm" name="adminForm">

    <div class="card shadow-sm mb-5">

        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
			        <?php echo HTMLFormatHelper::layout('search', $this); ?>
                </div>
		        <?php echo Layout::render('admin.list.ordering', $this) ?>
            </div>

            <div class="my-2">
		        <?php echo HTMLFormatHelper::layout('filters', $this); ?>
            </div>
        </div>

        <div class="card-body">
            <table class="table table-hover" id="articleList">
                <thead>
                <th width="1%">
                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
                </th>
                <th class="nowrap">
			        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
                </th>
                <th>
			        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CTITLE', 'a.title', $listDirn, $listOrder); ?>
                </th>
                <th width="1%" class="nowrap">
			        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CCREATED', 'a.ctime', $listDirn, $listOrder); ?><br/>
			        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CEXPIRE', 'a.extime', $listDirn, $listOrder); ?>
                </th>
                <th width="1%" class="nowrap">
			        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
                </thead>

                <tbody>
		        <?php foreach($this->items as $i => $item) :
			        $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
			        $canChange  = TRUE;
			        ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td class="center">
					        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td class="center">
                            <div class="btn-group">
                                <a class="btn btn-sm btn-light border" rel="tooltip" data-bs-toggle="tooltip" title="<?php echo $item->published ? Text::_('CUNPUB') : Text::_('CPUB'); ?>"
                                   href="<?php echo Url::task('records.' . ($item->published ? 'sunpub' : 'spub'), $item->id); ?>">
							        <?php echo HTMLFormatHelper::icon(!$item->published ? 'cross-circle.png' : 'tick.png'); ?>
                                </a>
                                <a class="btn btn-sm btn-light border" rel="tooltip" data-bs-toggle="tooltip" title="<?php echo $item->featured ? Text::_('CMAKEUNFEATURE') : Text::_('CMAKEFEATURE'); ?>"
                                   href="<?php echo Url::task('records.' . ($item->featured ? 'sunfeatured' : 'sfeatured'), $item->id); ?>">
							        <?php echo HTMLFormatHelper::icon(!$item->featured ? 'crown-silver.png' : 'crown.png'); ?>
                                </a>
                            </div>
                            <div>
                                <small>
							        <?php echo $this->escape($item->access_title); ?>
                                </small>
                            </div>
					        <?php if($item->ip): ?>
                                <div>
                                    <small>
								        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('ip.country', $item->ip); ?>
								        <?php echo \Joomla\CMS\HTML\HTMLHelper::link('javascript:void(0);', $item->ip, array('rel' => "tooltip", 'title' => Text::_('CFILTERBYIP'), 'onclick' => 'Joomcck.setAndSubmit(\'filter_search\', \'ip:' . $item->ip . '\');')); ?>
                                    </small>
                                </div>
					        <?php endif; ?>
                        </td>
                        <td class="has-context" style="position: relative">
                            <div style="position: absolute; top: 10px; right: 10px;">
						        <?php
						        // Create dropdown items
						        Dropdown::addCustomItem('<i class="fas fa-copy"></i> '. Text::_('CCOPY'), 'javascript:void(0)', 'onclick="Joomla.listItemTask(\'cb' . $i . '\',\'records.copy\')"');
						        Dropdown::addCustomItem($item->featured ? '<i class="fas fa-star text-muted"></i> '. Text::_('CMAKEUNFEATURE') : '<i class="fas fa-star text-warning"></i> '. Text::_('CMAKEFEATURED'), Url::task('records.' . ($item->featured ? 'sunfeatured' : 'sfeatured'), $item->id));
						        Dropdown::addCustomItem('<i class="fas fa-trash text-danger"></i> '. Text::_('CDELETE'), Url::task('records.delete', $item->id));

						        Dropdown::divider();

						        if($item->published) :
							        Dropdown::unpublish( 'cb' . $i, 'items.');
						        else :
							        Dropdown::publish(  'cb' . $i, 'items.');
						        endif;

						        if($item->checked_out) :
							        Dropdown::divider();
							        Dropdown::checkin( 'cb' . $i, 'records.');
						        endif;

						        Dropdown::divider();

						        Dropdown::addCustomItem( Text::_('C_TOOLBAR_RESET_CTIME'), 'javascript:void(0)', 'onclick="Joomla.Joomla.listItemTask(\'cb' . $i . '\',\'records.reset_ctime\')"');
						        Dropdown::addCustomItem( Text::_('C_TOOLBAR_RESET_MTIME'), 'javascript:void(0)', 'onclick="Joomla.listItemTask(\'cb' . $i . '\',\'records.reset_mtime\')"');
						        Dropdown::addCustomItem( Text::_('C_TOOLBAR_RESET_EXTIME'), 'javascript:void(0)', 'onclick="Joomla.listItemTask(\'cb' . $i . '\',\'records.reset_extime\')"');

						        Dropdown::divider();

						        if($item->hits):
							        Dropdown::addCustomItem( Text::_('C_TOOLBAR_RESET_HITS') . " <span class=\"badge bg-info\">{$item->hits}</span>", 'javascript:void(0)', 'onclick="Joomla.listItemTask(\'cb' . $i . '\',\'records.reset_hits\')"');
						        endif;
						        if($item->comments):
							        Dropdown::addCustomItem( Text::_('C_TOOLBAR_RESET_COOMENT') . " <span class=\"badge bg-info\">{$item->comments}</span>", 'javascript:void(0)', 'onclick="Joomla.listItemTask(\'cb' . $i . '\',\'records.reset_com\')"');
						        endif;
						        if($item->votes):
							        Dropdown::addCustomItem( Text::_('C_TOOLBAR_RESET_RATING') . " <span class=\"badge bg-info\">{$item->votes}</span>", 'javascript:void(0)', 'onclick="Joomla.listItemTask(\'cb' . $i . '\',\'records.reset_vote\')"');
						        endif;
						        if($item->favorite_num):
							        Dropdown::addCustomItem( Text::_('C_TOOLBAR_RESET_FAVORIT') . " <span class=\"badge bg-info\">{$item->favorite_num}</span>", 'javascript:void(0)', 'onclick="Joomla.listItemTask(\'cb' . $i . '\',\'records.reset_fav\')"');
						        endif;

						        echo Dropdown::render();
						        ?>
                            </div>
                            <div class="float-start">
						        <?php if($item->checked_out) : ?>
							        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'records.', $canCheckin); ?>
						        <?php endif; ?>
                                <a title="<?php echo Text::_('CEDITRECORD'); ?>" href="<?php echo Url::edit((int)$item->id); ?>">
                                    <big><?php echo strip_tags($item->title); ?></big>
                                </a>
                                <br/>
                                <small>
							        <?php echo Text::_('CTYPE'); ?>:
                                    <a href="#" rel="tooltip" data-bs-toggle="tooltip" title="<?php echo Text::_('CFILTERBYTYPE'); ?>" onclick="Joomcck.setAndSubmit('filter_type', <?php echo $item->type_id ?>)">
								        <?php echo $this->escape($item->type_name); ?>
                                    </a>
                                    <span style="color: lightgray">|</span>

							        <?php echo Text::_('CSECTION'); ?>:
                                    <a href="#" rel="tooltip" data-bs-toggle="tooltip" title="<?php echo Text::_('CFILTERBYSECTION'); ?>" onclick="Joomcck.setAndSubmit('filter_section', <?php echo $item->section_id ?>)">
								        <?php echo $this->escape($item->section_name); ?>
                                    </a>

							        <?php if($item->categories): ?>
                                        <span style="color: lightgray">|</span>
								        <?php echo Text::_('CCATEGORY'); ?>:
								        <?php foreach($item->categories AS $key => $category): ?>
                                            <a rel="tooltip" data-bs-toggle="tooltip" title="<?php echo Text::_('CFILTERBYCATEGORY'); ?>" href="#" onclick="Joomcck.setAndSubmit('filter_category', <?php echo $key; ?>);"><?php echo $category; ?></a>
								        <?php endforeach; ?>
							        <?php endif; ?>

                                    <span style="color: lightgray">|</span>
							        <?php echo Text::_('CAUTHOR'); ?>:
                                    <small>
								        <?php echo \Joomla\CMS\HTML\HTMLHelper::link('javascript:void(0);', ($item->userlogin ? $item->userlogin : Text::_('CANONYMOUS')), array(
									        'rel' => "tooltip", 'title' => Text::_('CFILTERBYUSER'), 'onclick' => 'Joomcck.setAndSubmit(\'filter_search\', \'user:' . $item->user_id . '\');'
								        )) ?>

                                    </small>
                                </small>
                                <br/>
                                <small>
							        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CHITS', 'a.hits', $listDirn, $listOrder); ?>
                                    <span class="badge text-bg-light shadow-sm px-2 py-1"><small><?php echo $this->escape($item->hits); ?></small></span>

							        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CCOMMENTS', 'a.comments', $listDirn, $listOrder); ?>
                                    <a rel="tooltip" data-bs-toggle="tooltip" title="<?php echo Text::_('CSHOWRECORDCOMMENTS'); ?>" href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=comms&filter_search=record:' . $item->id); ?>" class="badge bg-info">
                                        <small><?php echo $this->escape($item->comments); ?></small>
                                    </a>

							        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CVOTES', 'a.votes', $listDirn, $listOrder); ?>
                                    <a rel="tooltip" data-bs-toggle="tooltip" title="<?php echo Text::_('CSHOWRECORDVOTES'); ?>" href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=votes&filter_search=record:' . $item->id); ?>" class="badge bg-info">
                                        <small><?php echo $this->escape($item->votes); ?></small>
                                    </a>

							        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CFAVORITED', 'a.favorite_num', $listDirn, $listOrder); ?>
                                    <span class="badge text-bg-light shadow-sm px-2 py-1"><small><?php echo $this->escape($item->favorite_num); ?></small></span>
                                </small>
                            </div>
                        </td>
                        <td nowrap="nowrap">
                            <small>
						        <?php $data = new \Joomla\CMS\Date\Date($item->ctime);
						        echo $data->format(Text::_('CDATE1')); ?><br/>
						        <?php if($item->extime == '0000-00-00 00:00:00' || is_null($item->extime)): ?>
                                    <span style="color: green"><?php echo Text::_('CNEVER') ?></span>
						        <?php else: ?>
							        <?php $extime = new \Joomla\CMS\Date\Date($item->extime); ?>
                                    <span style="color: <?php echo($extime->toUnix() <= time() ? 'red' : 'green') ?>">
							<?php echo $extime->format(Text::_('CDATE1')); ?>
							</span>
						        <?php endif; ?>
                            </small>
                        </td>
                        <td class="center">
                            <small>
						        <?php echo (int)$item->id; ?>
                            </small>
                        </td>
                    </tr>
		        <?php endforeach; ?>
                </tbody>
            </table>

            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
            <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
            <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
        </div>

        <div class="card-footer">
		    <?php echo Layout::render('admin.list.pagination', ['pagination' => $this->pagination]) ?>
        </div>

    </div>

</form>