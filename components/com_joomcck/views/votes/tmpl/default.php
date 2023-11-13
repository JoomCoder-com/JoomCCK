<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');

$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
$userId = $user->get('id');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<form action="<?php echo \Joomla\CMS\Uri\Uri::getInstance()->toString() ?>" method="post" name="adminForm" id="adminForm">
	<?php echo HTMLFormatHelper::layout('search', $this); ?>

	<div class="page-header">
		<h1>
			<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE); ?>/components/com_joomcck/images/icons/votes.png">
			<?php echo \Joomla\CMS\Language\Text::_('CVOTES'); ?>
		</h1>
	</div>

	<?php echo HTMLFormatHelper::layout('filters', $this); ?>

	<?php echo HTMLFormatHelper::layout('items', $this); ?>

	<table class="table table-hover" id="articleList">
		<thead>
		<tr>
			<th width="1%">
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo \Joomla\CMS\Language\Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
			</th>
			<th width="1%" nowrap="nowrap">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CVOTE', 'a.vote', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" nowrap="nowrap">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CTYPE', 'a.ref_type', $listDirn, $listOrder); ?>
			</th>
			<th class="title">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CARTICLE', 'r.title', $listDirn, $listOrder); ?>
			</th>
			<th width="10%">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CUSER', 'u.username', $listDirn, $listOrder); ?>
			</th>
			<th width="8%" nowrap="nowrap">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CVOTED', 'a.ctime', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" nowrap="nowrap">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
			</th>
		</tr>
		</thead>
		<?php echo HTMLFormatHelper::layout('pagenav', $this); ?>
		<tbody>
		<?php foreach($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td><?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.id', $i, $item->id); ?></td>
				<td>
					<small>
						<a href="#" rel="tooltip" data-bs-title="<?php echo \Joomla\CMS\Language\Text::_('CFILTERBYVOTE'); ?>" onclick="Joomcck.setAndSubmit('filter_votes', '<?php echo $item->vote ?>')">
							<?php echo $this->escape($item->vote); ?>
						</a>
					</small>
				</td>
				<td>
					<small>
						<a href="#" rel="tooltip" data-bs-title="<?php echo \Joomla\CMS\Language\Text::_('CFILTERBYVOTETYPE'); ?>" onclick="Joomcck.setAndSubmit('filter_type', '<?php echo $item->ref_type ?>')">
							<?php echo $this->escape($item->ref_type); ?>
						</a>
					</small>
				</td>
				<td>
					<a href="javascript:void(0);" rel="tooltip" data-bs-title="<?php echo \Joomla\CMS\Language\Text::_('CFILTERBYRECORD'); ?>"
					   onclick="document.getElementById('filter_search').value='record:<?php echo $item->record_id; ?>'; document.adminForm.submit();">
						<?php echo $item->record ?>
					</a>
				</td>
				<td width="5%" class="nowrap">
					<small>
						<?php
						$user = \Joomla\CMS\Factory::getUser($item->user_id);
						$link = 'index.php?option=com_users&task=edit&cid[]=' . $user->get('id');
						if(is_dir(JPATH_ADMINISTRATOR .  '/components/com_juser'))
						{
							$link = 'index.php?option=com_juser&task=edit&cid[]=' . $user->get('id');
						}
						?>
						<?php if($user->get('username')): ?>
							<?php echo \Joomla\CMS\HTML\HTMLHelper::link('javascript:void(0);', $user->get('username'), array(
								'rel' => "tooltip", 'data-bs-title' => \Joomla\CMS\Language\Text::_('CFILTERBYUSER'), 'onclick' => 'document.getElementById(\'filter_search\').value=\'user:' . $item->user_id . '\'; document.adminForm.submit();'
							)) ?>
							<?php //echo \Joomla\CMS\HTML\HTMLHelper::_('ip.block_user', $item->user_id, $item->id);?>
						<?php else: ?>
							<?php echo $item->username ? $item->username . " (<a href=\"javascript:void(0);\" rel=\"tooltip\" data-bs-title=\"" . \Joomla\CMS\Language\Text::_('CFILTEREMAIL') . "\" onclick=\"document.getElementById('filter_search').value='email:{$item->useremail}'; document.adminForm.submit();\">{$item->useremail}</a>) " : \Joomla\CMS\Language\Text::_('CANONYMOUS') ?>
						<?php endif; ?>

						<?php if($item->ip): ?>
							<div>
								<?php echo \Joomla\CMS\HTML\HTMLHelper::_('ip.country', $item->ip); ?>
								<?php echo \Joomla\CMS\HTML\HTMLHelper::link('javascript:void(0);', $item->ip, array(
									'rel' => "tooltip", 'data-bs-title' => \Joomla\CMS\Language\Text::_('CFILTERBYIP'), 'onclick' => 'document.getElementById(\'filter_search\').value=\'ip:' . $item->ip . '\'; document.adminForm.submit();'
								)); ?>
								<?php //echo \Joomla\CMS\HTML\HTMLHelper::_('ip.block_ip', $item->ip, $item->id);?>
							</div>
						<?php endif; ?>
					</small>
				</td>
				<td align="center" class="nowrap">
					<small>
						<?php $data = new \Joomla\CMS\Date\Date($item->ctime);
						echo $data->format(\Joomla\CMS\Language\Text::_('CDATE1')); ?>
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
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
</form>