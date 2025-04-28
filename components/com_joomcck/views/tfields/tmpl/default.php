<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die('Restricted access');

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');

$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
$userId = $user->get('id');

$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');

\Joomla\CMS\HTML\HTMLHelper::_('dropdown.init');
\Joomla\CMS\HTML\HTMLHelper::_('formbehavior.chosen', '.select');

$canOrder = $user->authorise('core.edit.state', 'com_joomcck.tfields');
$saveOrder = $listOrder == 'f.ordering';

if($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_joomcck&task=tfields.ordersave&tmpl=component';
	\Joomla\CMS\HTML\HTMLHelper::_('sortablelist.sortable', 'fieldsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<div class="page-header">
    <h1>
        <img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE); ?>/components/com_joomcck/images/icons/sections.png">
		<?php echo \Joomla\CMS\Language\Text::sprintf('COB_FIELDSOF', $this->type->name); ?>
    </h1>
</div>
<?php echo HTMLFormatHelper::layout('items', $this); ?>

<div class="clearfix"></div>

<form action="<?php echo Route::_('index.php?option=com_joomcck&view=tfields'); ?>" method="post" name="adminForm" id="adminForm">

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
            <table class="table table-hover" id="fieldsList">
                <thead>
                <tr>
                    <th width="1%">
                        <input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)"/>
                    </th>
                    <th width="1%" class="nowrap center hidden-phone">
				        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', '<i class="icon-menu-2"></i>', 'f.ordering', $listDirn, $listOrder, NULL, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                    </th>

                    <th width="1%">
				        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JSTATUS', 'f.published', $listDirn, $listOrder); ?>
                    </th>
                    <th class="title">
				        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CFIELDLABEL', 'f.label', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%">
		                <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CTYPE', 'f.field_type', $listDirn, $listOrder); ?>
                    </th>
                    <th width="5%" nowrap="nowrap">
				        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CGROUPNAME', 'g.title', $listDirn, $listOrder); ?>
                    </th>
                    <th width="1%"><span rel="tooltip" data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('JGLOBAL_FIELD_KEY_LABEL'); ?>"><?php echo \Joomla\String\StringHelper::substr(\Joomla\CMS\Language\Text::_('JGLOBAL_FIELD_KEY_LABEL'), 0, 1) ?></span></th>
                    <th width="1%"><span rel="tooltip" data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('XML_LABEL_F_REQ'); ?>"><?php echo \Joomla\String\StringHelper::substr(\Joomla\CMS\Language\Text::_('XML_LABEL_F_REQ'), 0, 1) ?></span></th>
                    <th width="1%"><span rel="tooltip" data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('XML_LABEL_F_SEARCHABLE'); ?>"><?php echo \Joomla\String\StringHelper::substr(\Joomla\CMS\Language\Text::_('XML_LABEL_F_SEARCHABLE'), 0, 1) ?></span></th>
                    <th width="1%"><span rel="tooltip" data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('INTRO'); ?>"><?php echo \Joomla\String\StringHelper::substr(\Joomla\CMS\Language\Text::_('XML_LABEL_F_SHOW_INTRO'), 0, 1) ?></span></th>
                    <th width="1%"><span rel="tooltip" data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('FULL'); ?>"><?php echo \Joomla\String\StringHelper::substr(\Joomla\CMS\Language\Text::_('XML_LABEL_F_SHOW_FULL'), 0, 1) ?></span></th>
                    <th width="1%" class="nowrap">
				        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'ID', 'f.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
                </thead>
                <tbody>
		        <?php
		        foreach($this->items as $i => $item):
			        $item->max_ordering = 0; //??
			        $ordering           = ($listOrder == 'f.ordering');
			        $canCreate          = $user->authorise('core.create', 'com_joomcck.type.' . $item->type_id);
			        $canEdit            = $user->authorise('core.edit', 'com_joomcck.type.' . $item->type_id);
			        $canCheckin         = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			        $canEditOwn         = $user->authorise('core.edit.own', 'com_joomcck.field.' . $item->id) && $item->user_id == $userId;
			        $canChange          = $user->authorise('core.edit.state', 'com_joomcck.field.' . $item->id) && $canCheckin;

			        $params = new \Joomla\Registry\Registry();
			        $params->loadString((string)$item->params);
			        ?>
                    <tr sortable-group-id="<?php echo $item->group_id ?>">
                        <td class="center">
					        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td class="order nowrap center hidden-phone">
					        <?php if($canChange) :
						        $disableClassName = '';
						        $disabledLabel    = '';

						        if(!$saveOrder) :
							        $disabledLabel    = \Joomla\CMS\Language\Text::_('JORDERINGDISABLED');
							        $disableClassName = 'inactive tip-top';
						        endif; ?>
                                <span class="sortable-handler <?php echo $disableClassName ?>" title="<?php echo $disabledLabel ?>" rel="tooltip">
							<i class="icon-menu"></i>
						</span>
                                <input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order "/>
					        <?php else : ?>
                                <span class="sortable-handler inactive">
							<i class="icon-menu"></i>
						</span>
					        <?php endif; ?>
                        </td>

                        <td class="center">
					        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('field.state', $item->published, $i, 'tfields.', $canChange); ?>
                        </td>
                        <td>
                            <div class="float-start">
						        <?php if($item->checked_out) : ?>
							        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'tfields.', $canCheckin); ?>
						        <?php endif; ?>

						        <?php if($params->get('core.icon')): ?>
                                    <img alt="Icon" src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE) ?>/media/com_joomcck/icons/16/<?php echo $params->get('core.icon'); ?>" align="absmiddle">
						        <?php endif; ?>

                                <a href="<?php echo Route::_('index.php?option=com_joomcck&task=tfield.edit&id=' . (int)$item->id); ?>">
							        <?php echo $this->escape($item->label); ?>
                                </a>
                            </div>
                        </td>
                        <td nowrap="nowrap">
		                    <?php
		                    $icon = \Joomla\CMS\Uri\Uri::root() . 'components/com_joomcck/fields/';
		                    if(is_file(JPATH_ROOT . '/components/com_joomcck/fields' . DIRECTORY_SEPARATOR . $item->field_type . DIRECTORY_SEPARATOR . $item->field_type . '.png'))
		                    {
			                    $icon .= "{$item->field_type}/{$item->field_type}.png";
		                    }
		                    else
		                    {
			                    $icon .= "text/text.png";
		                    }
		                    echo \Joomla\CMS\HTML\HTMLHelper::image($icon, $item->field_type, array(
			                    'align' => 'absmiddle',
                                'class' => 'rounded border shadow-sm',
                                'width' => '24px',
                                'height' => '24px',
                                'style' => 'padding: 4px'
		                    ));
		                    ?>
                            <small><a href="#" rel="tooltip" data-bs-original-title="<?php echo \Joomla\CMS\Language\Text::_('CFILTERFIELDTYPE') ?>" onclick="Joomcck.setAndSubmit('filter_ftype', '<?php echo $item->field_type ?>')"><?php echo $item->field_type ?></a></small>
                        </td>
                        <td nowrap="nowrap">
					        <?php if($item->icon): ?>
						        <?php echo HTMLFormatHelper::icon($item->icon); ?>
					        <?php endif; ?>

					        <?php echo $item->group_field_title; ?>
                        </td>
                        <td>
                            <img style="max-width: 16px;" rel="tooltip" data-bs-original-title="<?php echo $item->key; ?>" src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE) ?>/media/com_joomcck/icons/16/key.png"/>
                        </td>
                        <td><?php echo \Joomla\CMS\HTML\HTMLHelper::_('field.required', $params->get('core.required', 0), $i, 'tfields.', $canChange) ?></td>
                        <td><?php echo \Joomla\CMS\HTML\HTMLHelper::_('field.searchable', $params->get('core.searchable', 0), $i, 'tfields.', $canChange) ?></td>
                        <td><?php echo \Joomla\CMS\HTML\HTMLHelper::_('field.show_intro', $params->get('core.show_intro', 0), $i, 'tfields.', $canChange) ?></td>
                        <td><?php echo \Joomla\CMS\HTML\HTMLHelper::_('field.show_full', $params->get('core.show_full', 0), $i, 'tfields.', $canChange) ?></td>
                        <td class="center">
                            <small><?php echo (int)$item->id; ?></small>
                        </td>
                    </tr>
		        <?php endforeach; ?>
                </tbody>
            </table>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="type_id" value="<?php echo $this->type->id; ?>"/>
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