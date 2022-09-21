<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access'); ?>
<?php
$user = JFactory::getUser();
$userId = $user->get('id');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', '.select');

$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<form action="<?php echo $this->action ?>" method="post" name="adminForm" id="adminForm">

	<?php echo HTMLFormatHelper::layout('search', $this); ?>

	<div class="page-header">
		<h1>
			<img src="<?php echo JUri::root(TRUE); ?>/components/com_joomcck/images/icons/sections.png">
			<?php echo JText::_('COB_SECTIONMANAGER'); ?>
		</h1>
	</div>

	<?php echo HTMLFormatHelper::layout('filters', $this); ?>

	<?php echo HTMLFormatHelper::layout('items'); ?>

	<table class="table table-striped">
		<thead>
		<tr>
			<th width="1%">
				<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/>
			</th>
			<th width="1%" class="nowrap">
				<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
			</th>
			<th>
				<?php echo JHtml::_('grid.sort', 'CSECTIONNAME', 'a.name', $listDirn, $listOrder); ?>
			</th>
			<th width="1%"></th>
			<th width="5%">
				<?php echo JText::_('CRECORDS'); ?>
			</th>
			<th width="10%">
				<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" class="nowrap">
				<?php echo JHtml::_('grid.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
			</th>
		</tr>
		</thead>
		<?php echo HTMLFormatHelper::layout('pagenav', $this); ?>
		<tbody>
		<?php foreach($this->items as $i => $item) :
			$ordering   = ($listOrder == 'f.ordering');
			$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
			$canChange  = TRUE;
			$item->params = new JRegistry($item->params);
			?>
			<tr>
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'sections.', $canChange); ?>
				</td>
				<td class="has-context">
					<div class="pull-left">
						<?php if($item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'sections.', $canCheckin); ?>
						<?php endif; ?>
						<a href="<?php echo JRoute::_('index.php?option=com_joomcck&task=section.edit&id=' . (int)$item->id); ?>">
							<?php echo $this->escape($item->name); ?>
						</a>
					</div>
					<div class="pull-left">
						<?php
						// Create dropdown items
						JHtml::_('dropdown.edit', $item->id, 'section.');
						JHtml::_('dropdown.addCustomItem', JText::_('C_TOOLBAR_DELETE'), 'javascript:void(0)', 'onclick="if(!confirm(\'' . JText::_('C_TOOLBAR_CONFIRMDELET') . '\')){return;}listItemTask(\'cb' . $i . '\',\'sections.delete\')"');
						if($item->published) :
							JHtml::_('dropdown.unpublish', 'cb' . $i, 'sections.');
						else :
							JHtml::_('dropdown.publish', 'cb' . $i, 'sections.');
						endif;

						if($item->checked_out) :
							JHtml::_('dropdown.divider');
							JHtml::_('dropdown.checkin', 'cb' . $i, 'sections.');
						endif;

						JHtml::_('dropdown.divider');
						JHtml::_('dropdown.addCustomItem', JText::_('C_OPENSECTION'), JRoute::_(Url::records($item)));
						JHtml::_('dropdown.divider');
						JHtml::_('dropdown.addCustomItem', JText::_('C_MANAGE_CATS') . ' <span class="badge' . ($item->categories ? ' badge-success' : NULL) . '">' . $item->categories . '</span>', JRoute::_('index.php?option=com_joomcck&view=cats&section_id=' . $item->id));

						echo JHtml::_('dropdown.render');
						?>
					</div>
				</td>
				<td nowrap="nowrap">
					<a rel="tooltip" data-original-title="<?php echo JText::_('CCATEGOY_MANAGE'); ?>" href="<?php echo JRoute::_('index.php?option=com_joomcck&view=cats&section_id=' . $item->id) ?>">
						<?php echo JText::_('CCATEGORIES'); ?>
					</a>
					<span class="badge<?php echo($item->fieldnum ? ' badge-success' : NULL) ?>"><?php echo $item->fieldnum; ?></span>
				</td>
				<td class="center">
					<span class="badge <?php echo($item->records ? ' badge-info' : NULL) ?>"><?php echo $item->records ?></span>
				</td>
				<td class="center">
					<?php echo $item->language; ?>
				</td>
				<td class="center">
					<?php echo (int)$item->id; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>