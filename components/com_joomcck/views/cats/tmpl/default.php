<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;

defined('_JEXEC') or die;

// Include the component HTML helpers.
\Joomla\CMS\HTML\HTMLHelper::addIncludePath(JPATH_COMPONENT.'/helpers/html');
\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip');
\Joomla\CMS\HTML\HTMLHelper::_('behavior.multiselect');
\Joomla\CMS\HTML\HTMLHelper::_('dropdown.init');
\Joomla\CMS\HTML\HTMLHelper::_('formbehavior.chosen', '.select');

$user		= \Joomla\CMS\Factory::getApplication()->getIdentity();
$userId		= $user->get('id');
$section	= $this->state->get('filter.section') ? $this->escape($this->state->get('filter.section')) : $this->section;
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$ordering 	= ($listOrder == 'a.lft');
$saveOrder 	= ($listOrder == 'a.lft' && $listDirn == 'asc');


if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_joomcck&task=cats.saveOrderAjax&tmpl=component';
	\Joomla\CMS\HTML\HTMLHelper::_('sortablelist.sortable', 'categoryList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<div class="page-header">
    <h1>
        <img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE); ?>/components/com_joomcck/images/icons/sections.png">
		<?php echo \Joomla\CMS\Language\Text::sprintf('COB_CATSOF', $this->section->text); ?>
    </h1>
</div>


<?php echo HTMLFormatHelper::layout('items'); ?>
<div class="clearfix"></div>

<form action="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=cats&section_id='.$section);?>" method="post" name="adminForm" id="adminForm">


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
            <table class="table table-striped" id="categoryList">
                <thead>
                <tr>
                    <th width="1%">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo \Joomla\CMS\Language\Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                    <th width="1%" class="nowrap center hidden-phone">
				        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                    </th>
                    <th width="5%">
				        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
                    </th>
                    <th>
				        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                    </th>
                    <th width="10%">
				        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
                    </th>
                    <th width="5%" class="nowrap hidden-phone">
				        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'language', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
                    </th>
                    <th width="1%" class="nowrap">
				        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
                </thead>
                <tbody <?php if ($saveOrder) : ?>
                            class="js-draggable"
                            data-url="<?php echo $saveOrderingUrl; ?>"
                            data-direction="<?php echo strtolower($listDirn); ?>"
                            data-nested="false"
                        <?php endif; ?>
                >
		        <?php
		        $originalOrders = array();
		        foreach ($this->items as $i => $item) :
			        $orderkey   = array_search($item->id, $this->ordering[$item->parent_id]);
			        $canEdit    = $user->authorise('core.edit',       $section . '.category.' . $item->id);
			        $canCheckin = $user->authorise('core.admin',      'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			        $canEditOwn = $user->authorise('core.edit.own',   $section . '.category.' . $item->id) && $item->created_user_id == $userId;
			        $canChange  = $user->authorise('core.edit.state', $section . '.category.' . $item->id) && $canCheckin;

			        // Get the parents of item for sorting
			        if ($item->level > 1)
			        {
				        $parentsStr = "";
				        $_currentParentId = $item->parent_id;
				        $parentsStr = " ".$_currentParentId;
				        for ($j = 0; $j < $item->level; $j++)
				        {
					        foreach ($this->ordering as $k => $v)
					        {
						        $v = implode("-", $v);
						        $v = "-".$v."-";
						        if (strpos($v, "-" . $_currentParentId . "-") !== false)
						        {
							        $parentsStr .= " ".$k;
							        $_currentParentId = $k;
							        break;
						        }
					        }
				        }
			        }
			        else
			        {
				        $parentsStr = "";
			        }			?>
                    <tr
                            data-draggable-group="<?php echo $item->parent_id; ?>"
                            data-item-id="<?php echo $item->id ?>"
                            data-parents="<?php echo $parentsStr ?>"
                            data-level="<?php echo $item->level ?>"


                            class="row<?php echo $i % 2; ?>">
                        <td class="center">
					        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td class="order nowrap center hidden-phone">
					        <?php if ($canChange) :
						        $disableClassName = '';
						        $disabledLabel    = '';
						        if (!$saveOrder) :
							        $disabledLabel    = \Joomla\CMS\Language\Text::_('JORDERINGDISABLED');
							        $disableClassName = 'inactive tip-top';
						        endif; ?>
                                <span class="sortable-handler <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>" rel="tooltip">
							<i class="icon-menu"></i>
						</span>

					        <?php else : ?>
                                <span class="sortable-handler inactive" >
							<i class="icon-menu"></i>
						</span>
					        <?php endif; ?>
                            <input type="text" style="display:none"  name="order[]" size="5" value="<?php echo $orderkey + 1;?>" />
                        </td>
                        <td class="center">
					        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('jgrid.published', $item->published, $i, 'cats.', $canChange);?>
                        </td>
                        <td>
					        <?php echo str_repeat('<span class="gi">&mdash;</span>', $item->level-1) ?>
					        <?php if ($item->checked_out) : ?>
						        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'cats.', $canCheckin); ?>
					        <?php endif; ?>
					        <?php if ($canEdit || $canEditOwn) : ?>
                                <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=category&task=cat.edit&id='.$item->id.'&section_id='.$section);?>">
							        <?php echo $this->escape($item->title); ?></a>
					        <?php else : ?>
						        <?php echo $this->escape($item->title); ?>
					        <?php endif; ?>
                            <span class="small" title="<?php echo $this->escape($item->path);?>">
							<?php if (empty($item->note)) : ?>
								<?php echo \Joomla\CMS\Language\Text::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
							<?php else : ?>
								<?php echo \Joomla\CMS\Language\Text::sprintf('JGLOBAL_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->note));?>
							<?php endif; ?>
						</span>
                        </td>

                        <td class="small hidden-phone">
					        <?php echo $this->escape($item->access_level); ?>
                        </td>
                        <td class="small nowrap hidden-phone">
					        <?php if ($item->language == '*'):?>
						        <?php echo \Joomla\CMS\Language\Text::alt('JALL', 'language'); ?>
					        <?php else:?>
						        <?php echo $item->language_title ? $this->escape($item->language_title) : \Joomla\CMS\Language\Text::_('JUNDEFINED'); ?>
					        <?php endif;?>
                        </td>
                        <td class="center hidden-phone">
						<span title="<?php echo sprintf('%d-%d', $item->lft, $item->rgt);?>">
							<?php echo (int) $item->id; ?></span>
                        </td>
                    </tr>
		        <?php endforeach; ?>
                </tbody>
            </table>
            <input type="hidden" name="section_id" value="<?php echo $section;?>" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
            <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
            <input type="hidden" name="original_order_values" value="<?php echo implode(',',$originalOrders); ?>" />
	        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
        </div>
        <div class="card-footer">
			<?php echo Layout::render('admin.list.pagination', ['pagination' => $this->pagination]) ?>
        </div>
    </div>
</form>
