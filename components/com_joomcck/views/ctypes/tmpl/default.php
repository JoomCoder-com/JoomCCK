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

defined('_JEXEC') or die('Restricted access');

\Joomla\CMS\HTML\HTMLHelper::_('dropdown.init');
\Joomla\CMS\HTML\HTMLHelper::_('formbehavior.chosen', '.select');

$user   = \Joomla\CMS\Factory::getApplication()->getIdentity();
$userId = $user->get('id');

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>


<div class="page-header">
    <h1>
        <img src="<?php echo \Joomla\CMS\Uri\Uri::root(true); ?>/components/com_joomcck/images/icons/types.png">
		<?php echo \Joomla\CMS\Language\Text::_('COB_CONT_TYPES'); ?>
    </h1>
</div>

<?php echo HTMLFormatHelper::layout('items'); ?>

<div class="clearfix"></div>


<form action="<?php echo $this->action ?>" method="post" name="adminForm" id="adminForm">

    <div class="card shadow-sm-mb-5">
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
            <table class="table table-hover" id="articlelist">
                <thead>
                <tr>
                    <th width="1%">
                        <input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)"/>
                    </th>
                    <th width="1%">
					    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
                    </th>
                    <th>
					    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CNAME', 'a.name', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%">
					    <?php echo \Joomla\CMS\Language\Text::_('CFIELDS'); ?>
                    </th>
                    <th width="10%">
					    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%" class="nowrap">
					    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
                </thead>
                <tbody>
			    <?php foreach ($this->items as $i => $item) :
				    $ordering = ($listOrder == 'f.ordering');
				    $canCreate = $user->authorise('core.create', 'com_joomcck.type.' . $item->id);
				    $canDelete = $user->authorise('core.delete', 'com_joomcck.type.' . $item->id);
				    $canEdit = $user->authorise('core.edit', 'com_joomcck.type.' . $item->id);
				    $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
				    $canEditOwn = $user->authorise('core.edit.own', 'com_joomcck.type.' . $item->id) && $item->user_id == $userId;
				    $canChange = $user->authorise('core.edit.state', 'com_joomcck.type.' . $item->id) && $canCheckin;
				    $addFields = true;
				    /*(
						$user->authorise('core.field.create', 'com_joomcck.field.'.$item->id) ||
						$user->authorise('core.field.delete', 'com_joomcck.field.'.$item->id) ||
						$user->authorise('core.field.edit', 'com_joomcck.field.'.$item->id) ||
						$user->authorise('core.field.edit.state', 'com_joomcck.field.'.$item->id) ||
						$user->authorise('core.field.edit.own', 'com_joomcck.field.'.$item->id)
					);*/
				    ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td class="center">
						    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td class="center">
						    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('jgrid.published', $item->published, $i, 'ctypes.', $canChange); ?>
                        </td>
                        <td class="nowrap has-context">
                            <div class="float-start">
							    <?php if ($item->checked_out) : ?>
								    <?php echo \Joomla\CMS\HTML\HTMLHelper::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'ctypes.', $canCheckin); ?>
							    <?php endif; ?>

							    <?php if ($canEdit || $canEditOwn) : ?>
                                    <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&task=ctype.edit&id=' . (int) $item->id); ?>">
									    <?php echo $this->escape($item->name); ?></a>
							    <?php else: ?>
								    <?php echo $this->escape($item->name); ?>
							    <?php endif; ?>
                            </div>
                            <div class="float-start">
							    <?php
							    // Create dropdown items
							    Dropdown::edit($item->id, 'ctype.');
							    Dropdown::addCustomItem('<i class="fas fa-trash text-danger"></i> '.\Joomla\CMS\Language\Text::_('C_TOOLBAR_DELETE'), 'javascript:void(0)', 'onclick="if(!confirm(\'' . \Joomla\CMS\Language\Text::_('C_TOOLBAR_CONFIRMDELET') . '\')){return;}Joomla.listItemTask(\'cb' . $i . '\',\'types.delete\')"');
							    if ($item->published) :
								    Dropdown::unpublish('cb' . $i, 'ctypes.');
							    else :
								    Dropdown::publish('cb' . $i, 'ctypes.');
							    endif;

							    if ($item->checked_out) :
								    Dropdown::divider();
								    Dropdown::checkin('cb' . $i, 'ctypes.');
							    endif;


							    Dropdown::divider();
							    Dropdown::addCustomItem(\Joomla\CMS\Language\Text::_('C_MANAGE_FIELDS') . ' <span class="badge ' . ($item->fieldnum ? ' bg-success' : ' bg-light text-dark border') . '">' . $item->fieldnum . '</span>', \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=tfields&filter_type=' . $item->id));

							    echo Dropdown::render();
							    ?>
                            </div>
                        </td>
                        <td nowrap="nowrap">
						    <?php if ($addFields): ?>
                                <a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=tfields&filter_type=' . $item->id) ?>">
								    <?php echo \Joomla\CMS\Language\Text::_('CFIELDS'); ?></a>
						    <?php else: ?>
							    <?php echo \Joomla\CMS\Language\Text::_('CFIELDS'); ?>
						    <?php endif; ?>
                            <span class="badge <?php echo $item->fieldnum ? ' bg-success' : 'bg-light text-dark border' ?>"><?php echo $item->fieldnum ?></span>
                        </td>
                        <td class="center">
						    <?php echo $item->language; ?>
                        </td>
                        <td class="center">
						    <?php echo (int) $item->id; ?>
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