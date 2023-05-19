<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

JHTML::_('bootstrap.tooltip', '*[rel^="tooltip"]');

$user = JFactory::getUser();
$userId = $user->get('id');

$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');

JHtml::_('formbehavior.chosen', '.select');
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>


<form action="<?php echo $this->action ?>" method="post" name="adminForm" id="adminForm">
	<?php echo HTMLFormatHelper::layout('search', $this); ?>

	<div class="page-header">
		<h1>
			<img src="<?php echo JUri::root(TRUE); ?>/components/com_joomcck/images/icons/comments.png">
			<?php echo JText::_('CCOMMENTS'); ?>
		</h1>
	</div>

	<?php echo HTMLFormatHelper::layout('filters', $this); ?>

	<?php echo HTMLFormatHelper::layout('items', $this); ?>

	<table class="table table-hover" id="articleList">
		<thead>
		<th width="1%">
			<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
		</th>
		<th class="">
			<?php echo JHTML::_('grid.sort', 'CSUBJECT', 'a.comment', $listDirn, $listOrder); ?>
		</th>
		<th width="10%" class="nowrap">
			<?php echo JHTML::_('grid.sort', 'CUSER', 'u.username', $listDirn, $listOrder); ?>
		</th>
		<th width="1%" class="nowrap">
			<?php echo JHTML::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
		</th>
		<th width="8%" class="nowrap">
			<?php echo JHTML::_('grid.sort', 'CCREATED', 'a.ctime', $listDirn, $listOrder); ?>
		</th>
		<th width="1%">
			<?php echo JHTML::_('grid.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
		</th>
		</thead>
		<?php echo HTMLFormatHelper::layout('pagenav', $this); ?>
		<tbody>
		<?php foreach($this->items as $i => $item) : ?>
			<?php
			$canCheckin = TRUE;
			$canChange  = TRUE;
			$body       = substr(strip_tags($item->comment), 0, 100);
			?>

			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<?php echo JHTML::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<a href="javascript:void(0);" rel="tooltip" data-bs-title="<?php echo JText::_('CFILTERRECORD'); ?>"
					   onclick="document.getElementById('filter_search').value='record:<?php echo $item->record_id; ?>'; document.adminForm.submit();">
						<?php echo $item->record ?>
					</a>
					[<a href="#" rel="tooltip" data-bs-title="<?php echo JText::_('CFILTERBYTYPE'); ?>" onclick="Joomcck.setAndSubmit('filter_type', <?php echo $item->type_id ?>)"><?php echo $this->escape($item->type); ?></a>]
					<br/>
					<small>
						<a href="index.php?option=com_joomcck&task=comm.edit&id=<?php echo (int)$item->id; ?>" rel="tooltip" data-bs-title="<?php echo JText::_('CEDITCOMMENT'); ?>">
							<?php echo $body; ?>
						</a>
					</small>
				</td>
				<td width="5%" nowrap="nowrap">
					<small>
						<?php
						$user = JFactory::getUser($item->user_id);
						$link = 'index.php?option=com_users&task=edit&cid[]=' . $user->get('id');
         
						if(\Joomla\CMS\Filesystem\Folder::exists(JPATH_ADMINISTRATOR . '/components/com_juser'))
						{
							$link = 'index.php?option=com_juser&task=edit&cid[]=' . $user->get('id');
						}
						?>
						<?php if($user->get('username')): ?>
							<?php echo JHtml::link('javascript:void(0);', $user->get('username'), array(
								'rel' => "tooltip", 'data-bs-title' => JText::_('CFILTERUSER'), 'onclick' => 'document.getElementById(\'filter_search\').value=\'user:' . $item->user_id . '\'; document.adminForm.submit();'
							)) ?>
							<?php //echo JHtml::_('ip.block_user', $item->user_id, $item->id);?>
						<?php else: ?>
							<?php echo $item->name ? $item->name . " (<a href=\"javascript:void(0);\" rel=\"tooltip\" data-bs-title=\"" . JText::_('CFILTEREMAIL') . "\" onclick=\"document.getElementById('filter_search').value='email:{$item->email}'; document.adminForm.submit();\">{$item->email}</a>) " : Jtext::_('CANONYMOUS') ?>
						<?php endif; ?>

						<?php if($item->ip): ?>
							<div>
								<?php echo JHtml::_('ip.country', $item->ip); ?>
								<?php echo JHTML::link('javascript:void(0);', $item->ip, array(
									'rel' => "tooltip", 'data-bs-title' => JText::_('CFILTERIP'), 'onclick' => 'document.getElementById(\'filter_search\').value=\'ip:' . $item->ip . '\'; document.adminForm.submit();'
								)); ?>
								<?php //echo JHtml::_('ip.block_ip', $item->ip, $item->id);?>
							</div>
						<?php endif; ?>
					</small>
				</td>

				<td align="center"><?php echo JHtml::_('jgrid.published', $item->published, $i, 'comments.', $canChange); ?></td>

				<td align="center" class="nowrap">
					<small>
						<?php $data = new JDate($item->ctime);
						echo $data->format(JText::_('CDATE1')); ?>
					</small>
				</td>

				<td align="center">
					<small><?php echo $item->id; ?></small>
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