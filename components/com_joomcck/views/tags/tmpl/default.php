<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('formbehavior.chosen', '.select');

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

			container.innerHTML = '<div class="input-append float-start">' +
				'<input type="text" style="margin-left: 5px;" name="tag" value="' + tag + '" />' +
				'<button rel="tooltip" class="btn" type="button" onclick="submitbutton(\'tags.save\');"><i class="icon-save"></i></button> ' +
				'<button rel="tooltip" class="btn" type="button" onclick="cancelForm();"><i class="icon-cancel"></i></button>' +
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

<form action="<?php echo JUri::getInstance()->toString(); ?>" method="post" name="adminForm"  id="adminForm">
	<?php echo HTMLFormatHelper::layout('search', $this); ?>

	<div class="page-header">
		<h1>
			<img src="<?php echo JUri::root(TRUE); ?>/components/com_joomcck/images/icons/tags.png">
			<?php echo JText::_('CTAGS'); ?>
		</h1>
	</div>

	<?php echo HTMLFormatHelper::layout('filters', $this); ?>

	<?php echo HTMLFormatHelper::layout('items', $this); ?>

	<table class="table table-striped">
		<thead>
			<th width="1%">
				<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this)" />
			</th>
			<th class="title">
				<?php echo JHtml::_('grid.sort',  'CTAGNAME', 't.tag', $listDirn, $listOrder); ?>
			</th>
			<th width="10%" class="nawrap center">
				<?php echo JHtml::_('grid.sort',  'CCREATED', 't.ctime', $listDirn, $listOrder); ?>
			</th>
			<th width="15%" class="nowrap center">
				<?php echo JHtml::_('grid.sort', 'CLANGUAGE', 't.language', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" class="nowrap">
				<?php echo JHtml::_('grid.sort',  'ID', 't.id', $listDirn, $listOrder); ?>
			</th>
		</thead>
		<?php echo HTMLFormatHelper::layout('pagenav', $this); ?>
		<tbody>
		<?php $k=1; foreach ($this->items as $i => $row) :?>

			<tr class="<?php $k = 1 - $k; echo "row$k"; ?>">
				<td>
					<?php echo JHTML::_('grid.id', $i, $row->id ); ?>
				</td>
				<td id="tag_container_<?php echo $row->id; ?>">
					<a href="javascript: void(0); showForm(<?php echo $row->id; ?>)" id="tag_<?php echo $row->id; ?>"><?php echo $row->tag; ?></a>
				</td>
				<td class="nowrap center small">
					<?php $data = new JDate( $row->ctime ); echo $data->format( JText::_('CDATE1' ) ); ?>
				</td>
				<td class="center">
					<?php echo $row->language; ?>
				</td>
				<td align="center">
					<?php echo $row->id; ?>
				</td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>