<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('dropdown.init');
JHtml::_('bootstrap.modal', 'a.modal');
JHtml::_('formbehavior.chosen', '.select');

$user = JFactory::getUser();
$userId = $user->get('id');

$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<form action="<?php echo JUri::getInstance()->toString() ?>" method="post" id="adminForm" name="adminForm">
<?php echo HTMLFormatHelper::layout('search', $this); ?>

<div class="page-header">
	<h1>
		<img src="<?php echo JUri::root(TRUE); ?>/components/com_joomcck/images/icons/items.png">
		<?php echo JText::_('XML_TOOLBAR_TITLE_RECORDS'); ?>
	</h1>
</div>

<?php echo HTMLFormatHelper::layout('filters', $this); ?>

<?php echo HTMLFormatHelper::layout('items', $this); ?>

<table class="table table-hover" id="articleList">
	<thead>
	<th width="1%">
		<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
	</th>
	<th class="nowrap">
		<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
	</th>
	<th>
		<?php echo JHtml::_('grid.sort', 'CTITLE', 'a.title', $listDirn, $listOrder); ?>
	</th>
	<th width="1%" class="nowrap">
		<?php echo JHtml::_('grid.sort', 'CCREATED', 'a.ctime', $listDirn, $listOrder); ?><br/>
		<?php echo JHtml::_('grid.sort', 'CEXPIRE', 'a.extime', $listDirn, $listOrder); ?>
	</th>
	<th width="1%" class="nowrap">
		<?php echo JHtml::_('grid.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
	</th>
	</thead>
	<?php echo HTMLFormatHelper::layout('pagenav', $this); ?>
	<tbody>
	<?php foreach($this->items as $i => $item) :
		$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
		$canChange  = TRUE;
		?>
		<tr class="row<?php echo $i % 2; ?>">
			<td class="center">
				<?php echo JHtml::_('grid.id', $i, $item->id); ?>
			</td>
			<td class="center">
				<div class="btn-group">
					<a class="btn btn-mini" rel="tooltip" data-original-title="<?php echo $item->published ? JText::_('CUNPUB') : JText::_('CPUB'); ?>"
					   href="<?php echo Url::task('records.' . ($item->published ? 'sunpub' : 'spub'), $item->id); ?>">
						<?php echo HTMLFormatHelper::icon(!$item->published ? 'cross-circle.png' : 'tick.png'); ?>
					</a>
					<a class="btn btn-mini" rel="tooltip" data-original-title="<?php echo $item->featured ? JText::_('CMAKEUNFEATURE') : JText::_('CMAKEFEATURE'); ?>"
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
						<?php echo JHtml::_('ip.country', $item->ip); ?>
						<?php echo JHTML::link('javascript:void(0);', $item->ip, array('rel' => "tooltip", 'data-original-title' => JText::_('CFILTERBYIP'), 'onclick' => 'Joomcck.setAndSubmit(\'filter_search\', \'ip:' . $item->ip . '\');')); ?>
					</small>
					</div>
				<?php endif; ?>
			</td>
			<td class="has-context" style="position: relative">
				<div style="position: absolute; top: 10px; right: 10px;">
					<?php
					// Create dropdown items
					JHtml::_('dropdown.addCustomItem', JText::_('CCOPY'), 'javascript:void(0)', 'onclick="listItemTask(\'cb' . $i . '\',\'records.copy\')"');
					JHtml::_('dropdown.addCustomItem', $item->featured ? JText::_('CMAKEUNFEATURE') : JText::_('CMAKEFEATURE'), Url::task('records.' . ($item->featured ? 'sunfeatured' : 'sfeatured'), $item->id));
					JHtml::_('dropdown.addCustomItem', JText::_('CDELETE'), Url::task('records.delete', $item->id));

					JHtml::_('dropdown.divider');

					if($item->published) :
						JHtml::_('dropdown.unpublish', 'cb' . $i, 'items.');
					else :
						JHtml::_('dropdown.publish', 'cb' . $i, 'items.');
					endif;

					if($item->checked_out) :
						JHtml::_('dropdown.divider');
						JHtml::_('dropdown.checkin', 'cb' . $i, 'records.');
					endif;

					JHtml::_('dropdown.divider');

					JHtml::_('dropdown.addCustomItem', JText::_('C_TOOLBAR_RESET_CTIME'), 'javascript:void(0)', 'onclick="listItemTask(\'cb' . $i . '\',\'records.reset_ctime\')"');
					JHtml::_('dropdown.addCustomItem', JText::_('C_TOOLBAR_RESET_MTIME'), 'javascript:void(0)', 'onclick="listItemTask(\'cb' . $i . '\',\'records.reset_mtime\')"');
					JHtml::_('dropdown.addCustomItem', JText::_('C_TOOLBAR_RESET_EXTIME'), 'javascript:void(0)', 'onclick="listItemTask(\'cb' . $i . '\',\'records.reset_extime\')"');

					JHtml::_('dropdown.divider');
					if($item->hits):
						JHtml::_('dropdown.addCustomItem', JText::_('C_TOOLBAR_RESET_HITS') . " <span class=\"badgebg-info\">{$item->hits}</span>", 'javascript:void(0)', 'onclick="listItemTask(\'cb' . $i . '\',\'records.reset_hits\')"');
					endif;
					if($item->comments):
						JHtml::_('dropdown.addCustomItem', JText::_('C_TOOLBAR_RESET_COOMENT') . " <span class=\"badgebg-info\">{$item->comments}</span>", 'javascript:void(0)', 'onclick="listItemTask(\'cb' . $i . '\',\'records.reset_com\')"');
					endif;
					if($item->votes):
						JHtml::_('dropdown.addCustomItem', JText::_('C_TOOLBAR_RESET_RATING') . " <span class=\"badgebg-info\">{$item->votes}</span>", 'javascript:void(0)', 'onclick="listItemTask(\'cb' . $i . '\',\'records.reset_vote\')"');
					endif;
					if($item->favorite_num):
						JHtml::_('dropdown.addCustomItem', JText::_('C_TOOLBAR_RESET_FAVORIT') . " <span class=\"badgebg-info\">{$item->favorite_num}</span>", 'javascript:void(0)', 'onclick="listItemTask(\'cb' . $i . '\',\'records.reset_fav\')"');
					endif;

					echo JHtml::_('dropdown.render');
					?>
				</div>
				<div class="float-start">
					<?php if($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'records.', $canCheckin); ?>
					<?php endif; ?>
					<a title="<?php echo JText::_('CEDITRECORD'); ?>" href="<?php echo Url::edit((int)$item->id); ?>">
						<big><?php echo strip_tags($item->title); ?></big>
					</a>
					<br/>
					<small>
						<?php echo JText::_('CTYPE'); ?>:
						<a href="#" rel="tooltip" data-original-title="<?php echo JText::_('CFILTERBYTYPE'); ?>" onclick="Joomcck.setAndSubmit('filter_type', <?php echo $item->type_id ?>)">
							<?php echo $this->escape($item->type_name); ?>
						</a>
						<span style="color: lightgray">|</span>

						<?php echo JText::_('CSECTION'); ?>:
						<a href="#" rel="tooltip" data-original-title="<?php echo JText::_('CFILTERBYSECTION'); ?>" onclick="Joomcck.setAndSubmit('filter_section', <?php echo $item->section_id ?>)">
							<?php echo $this->escape($item->section_name); ?>
						</a>

						<?php if($item->categories): ?>
							<span style="color: lightgray">|</span>
							<?php echo JText::_('CCATEGORY'); ?>:
							<?php foreach($item->categories AS $key => $category): ?>
								<a rel="tooltip" data-original-title="<?php echo JText::_('CFILTERBYCATEGORY'); ?>" href="#" onclick="Joomcck.setAndSubmit('filter_category', <?php echo $key; ?>);"><?php echo $category; ?></a>
							<?php endforeach; ?>
						<?php endif; ?>

						<span style="color: lightgray">|</span>
						<?php echo JText::_('CAUTHOR'); ?>:
						<small>
							<?php echo JHtml::link('javascript:void(0);', ($item->userlogin ? $item->userlogin : Jtext::_('CANONYMOUS')), array(
								'rel' => "tooltip", 'data-original-title' => JText::_('CFILTERBYUSER'), 'onclick' => 'Joomcck.setAndSubmit(\'filter_search\', \'user:' . $item->user_id . '\');'
							)) ?>

						</small>
					</small>
					<br/>
					<small>
						<?php echo JHtml::_('grid.sort', 'CHITS', 'a.hits', $listDirn, $listOrder); ?>
						<span class="badge"><small><?php echo $this->escape($item->hits); ?></small></span>

						<?php echo JHtml::_('grid.sort', 'CCOMMENTS', 'a.comments', $listDirn, $listOrder); ?>
						<a rel="tooltip" data-original-title="<?php echo JText::_('CSHOWRECORDCOMMENTS'); ?>" href="<?php echo JRoute::_('index.php?option=com_joomcck&view=comms&filter_search=record:' . $item->id); ?>" class="badgebg-info">
							<small><?php echo $this->escape($item->comments); ?></small>
						</a>

						<?php echo JHtml::_('grid.sort', 'CVOTES', 'a.votes', $listDirn, $listOrder); ?>
						<a rel="tooltip" data-original-title="<?php echo JText::_('CSHOWRECORDVOTES'); ?>" href="<?php echo JRoute::_('index.php?option=com_joomcck&view=votes&filter_search=record:' . $item->id); ?>" class="badgebg-info">
							<small><?php echo $this->escape($item->votes); ?></small>
						</a>

						<?php echo JHtml::_('grid.sort', 'CFAVORITED', 'a.favorite_num', $listDirn, $listOrder); ?>
						<span class="badge"><small><?php echo $this->escape($item->favorite_num); ?></small></span>
					</small>
				</div>
			</td>
			<td nowrap="nowrap">
				<small>
					<?php $data = new JDate($item->ctime);
					echo $data->format(JText::_('CDATE1')); ?><br/>
					<?php if($item->extime == '0000-00-00 00:00:00'): ?>
						<span style="color: green"><?php echo JText::_('CNEVER') ?></span>
					<?php else: ?>
						<?php $extime = new JDate($item->extime); ?>
						<span style="color: <?php echo($extime->toUnix() <= time() ? 'red' : 'green') ?>">
							<?php echo $extime->format(JText::_('CDATE1')); ?>
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
<?php echo JHtml::_('form.token'); ?>
</form>