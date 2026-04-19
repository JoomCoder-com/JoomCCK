<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');


$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>

<script type="text/javascript">

	var showedForm = 0;
	var linkView = '';

	function showForm(id) {
		if(showedForm == 0) {

			showedForm = id;

			var container = document.getElementById("tag_container_" + id);
			var link = document.getElementById("tag_" + id);
			linkView = container.innerHTML;
			var tag = link.innerHTML;

			container.innerHTML = '<div class="input-group">' +
				'<input type="text" class="form-control form-control-sm" style="margin-left: 5px;" name="tag" value="' + tag + '" />' +
				'<button rel="tooltip" class="btn btn-outline-success btn-sm" type="button" onclick="Joomla.submitbutton(\'tags.save\');"><i class="icon-save"></i></button> ' +
				'<button rel="tooltip" class="btn btn-outline-danger btn-sm" type="button" onclick="cancelForm();"><i class="icon-cancel"></i></button>' +
				'<input type="hidden" name="id" value="' + id + '" />' +
				'</div>';
		} else {
			cancelForm(showedForm);
			showForm(id);
		}
	}

	function cancelForm() {
		var container = document.getElementById("tag_container_" + showedForm);
		container.innerHTML = linkView;
		showedForm = 0;
	}
</script>

<?php echo HTMLFormatHelper::layout('navbar'); ?>


<form action="<?php echo Uri::getInstance()->toString(); ?>" method="post" name="adminForm" id="adminForm" class="cck-list-shell">

    <div class="cck-list-titlebar mb-4">
        <h2 class="cck-list-title">
            <img src="<?php echo Uri::root(true); ?>/components/com_joomcck/images/icons/tags.png" alt="">
            <span><?php echo \Joomla\CMS\Language\Text::_('CTAGS'); ?></span>
        </h2>
        <div class="cck-list-title-actions">
            <?php echo HTMLFormatHelper::layout('search', $this); ?>
        </div>
    </div>

    <div class="cck-list-action-bar">
        <?php echo HTMLFormatHelper::layout('items', $this); ?>
        <?php echo Layout::render('admin.list.ordering', $this); ?>
    </div>

    <?php echo HTMLFormatHelper::layout('filters', $this); ?>

    <div class="card cck-list-card">
        <div class="card-body">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <th width="1%">
                    <input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
                </th>
                <th class="title">
			        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'CTAGNAME', 't.tag', $listDirn, $listOrder); ?>
                </th>
                <th width="10%" class="nawrap center">
			        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'CCREATED', 't.ctime', $listDirn, $listOrder); ?>
                </th>
                <th width="15%" class="nowrap center">
			        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CLANGUAGE', 't.language', $listDirn, $listOrder); ?>
                </th>
                <th width="1%" class="nowrap">
			        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort',  'ID', 't.id', $listDirn, $listOrder); ?>
                </th>
                </thead>
                <tbody>
		        <?php $k=1; foreach ($this->items as $i => $row) :?>

                    <tr>
                        <td class="cck-col-check">
					        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.id', $i, $row->id); ?>
                        </td>
                        <td id="tag_container_<?php echo $row->id; ?>">
                            <a class="cck-item-title" href="javascript: void(0); showForm(<?php echo $row->id; ?>)" id="tag_<?php echo $row->id; ?>"><?php echo $row->tag; ?></a>
                        </td>
                        <td class="text-center small text-muted d-none d-md-table-cell">
					        <?php $data = new \Joomla\CMS\Date\Date($row->ctime); echo $data->format(\Joomla\CMS\Language\Text::_('CDATE1')); ?>
                        </td>
                        <td class="text-center d-none d-md-table-cell">
					        <?php echo $row->language; ?>
                        </td>
                        <td class="text-end">
                            <span class="cck-id"><?php echo (int) $row->id; ?></span>
                        </td>
                    </tr>
		        <?php endforeach;?>
                </tbody>
            </table>

            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
            <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	        <?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
        </div>
        <div class="card-footer">
		    <?php echo Layout::render('admin.list.pagination', ['pagination' => $this->pagination]) ?>
        </div>
    </div>

</form>