<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

JHtml::_('bootstrap.tooltip');

$field		= JFactory::getApplication()->input->getCmd('field');
$function	= 'jSelectUser_'.$field;
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>
<br />
<form action="<?php echo JUri::getInstance()->toString();?>" method="post" name="adminForm" id="adminForm">
	<div class="controls">
		<div class="input-append">
			<input class="col-md-3" type="text" name="filter_search"	id="filter_search" value="<?php echo $this->state->get('filter.search'); ?>"/>
			<button class="btn" type="submit" rel="tooltip" data-original-title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
				<?php echo HTMLFormatHelper::icon('magnifier.png');  ?>
			</button>
			<?php if($this->state->get('filter.search')) :?>
			<button class="btn<?php echo ($this->state->get('filter.search') ? ' btn-warning' : NULL); ?>" type="button"
				onclick="Joomcck.setAndSubmit('filter_search', '');" rel="tooltip" data-original-title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>">
				<?php echo HTMLFormatHelper::icon('eraser.png');  ?>
			</button>
			<?php endif; ?>
			<button class="btn<?php if($this->state->get('filter.group_id')) echo ' btn-warning'; ?>" type="button" data-toggle="collapse" data-target="#filters-block" >
				<?php echo HTMLFormatHelper::icon('funnel.png');  ?>
			</button>
		</div>
	</div>
	
	<div class="controls controls-row collapse btn-toolbar" id="filters-block">
		<div class="well well-small">
			<?php echo JHtml::_('access.usergroup', 'filter_group_id', $this->state->get('filter.group_id'), 'onchange="this.form.submit()"'); ?>
		</div>
	</div>
	<div class="clearfix"> </div>

	<table class="table table-hover">
		<thead>
			<tr>
				<th>
					<?php echo JHtml::_('grid.sort', 'CNAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="25%">
					<?php echo JHtml::_('grid.sort', 'CUSERNAME', 'a.username', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="25%">
					<?php echo JHtml::_('grid.sort', 'CGROUPS', 'group_names', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach ($this->items as $item) : ?>
			<tr>
				<td>
					<a class="pointer" onclick="if (parent) parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->name)); ?>');">
						<?php echo $item->name; ?></a>
				</td>
				<td align="center">
					<?php echo $item->username; ?>
				</td>
				<td align="left">
					<?php echo nl2br($item->group_names); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<div style="text-align: center;">
		<small>
			<?php if($this->pagination->getPagesCounter()):?>
				<?php echo $this->pagination->getPagesCounter(); ?>
			<?php endif;?>
			<?php echo str_replace('<option value="0">'.JText::_('JALL').'</option>', '', $this->pagination->getLimitBox());?>
			<?php echo $this->pagination->getResultsCounter(); ?>
		</small>
	</div>
	<div style="text-align: center;" class="pagination">
		<?php echo str_replace('<ul>', '<ul class="pagination-list">', $this->pagination->getPagesLinks()); ?>
	</div>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="field" value="<?php echo $this->escape($field); ?>" />
	<input type="hidden" name="type_id" value="<?php echo $this->escape($field); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>

</form>
