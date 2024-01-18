<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Html\Helpers\Dropdown;

defined('_JEXEC') or die('Restricted access');
?>
<?php
$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
$userId = $user->get('id');
\Joomla\CMS\HTML\HTMLHelper::_('dropdown.init');
\Joomla\CMS\HTML\HTMLHelper::_('formbehavior.chosen', '.select');
\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<form action="<?php echo $this->action ?>" method="post" name="adminForm" id="adminForm">

	<?php echo HTMLFormatHelper::layout('search', $this); ?>

	<div class="page-header">
		<h1>
			<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE); ?>/components/com_joomcck/images/icons/sections.png">
			<?php echo \Joomla\CMS\Language\Text::_('COB_SECTIONMANAGER'); ?>
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
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
			</th>
			<th>
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'CSECTIONNAME', 'a.name', $listDirn, $listOrder); ?>
			</th>
			<th width="1%"></th>
			<th width="5%">
				<?php echo \Joomla\CMS\Language\Text::_('CRECORDS'); ?>
			</th>
			<th width="10%">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
			</th>
			<th width="1%" class="nowrap">
				<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.sort', 'ID', 'a.id', $listDirn, $listOrder); ?>
			</th>
		</tr>
		</thead>
		<?php echo HTMLFormatHelper::layout('pagenav', $this); ?>
		<tbody>
		<?php foreach($this->items as $i => $item) :
			$ordering   = ($listOrder == 'f.ordering');
			$canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
			$canChange  = TRUE;
			$item->params = new \Joomla\Registry\Registry($item->params);
			?>
			<tr>
				<td class="center">
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('grid.id', $i, $item->id); ?>
				</td>
				<td class="center">
					<?php echo \Joomla\CMS\HTML\HTMLHelper::_('jgrid.published', $item->published, $i, 'sections.', $canChange); ?>
				</td>
				<td class="has-context">
					<div class="float-start">
						<?php if($item->checked_out) : ?>
							<?php echo \Joomla\CMS\HTML\HTMLHelper::_('jgrid.checkedout', $i, $item->checked_out, $item->checked_out_time, 'sections.', $canCheckin); ?>
						<?php endif; ?>
						<a href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&task=section.edit&id=' . (int)$item->id); ?>">
							<?php echo $this->escape($item->name); ?>
						</a>
					</div>
					<div class="float-start">
						<?php
						// Create dropdown items
                        Dropdown::edit($item->id, 'section.');

                        Dropdown::addCustomItem(
	                        '<i class="fas fa-trash text-danger"></i> '.\Joomla\CMS\Language\Text::_('C_TOOLBAR_DELETE'), 'javascript:void(0)',
                                'onclick="if(!confirm(\'' . \Joomla\CMS\Language\Text::_('C_TOOLBAR_CONFIRMDELET') . '\')){return;}Joomla.listItemTask(\'cb' . $i . '\',\'sections.delete\')"'
                        );

						if($item->published) :
                            Dropdown::unpublish('cb' . $i, 'sections.');

						else :
							Dropdown::publish('cb' . $i, 'sections.');
						endif;

						if($item->checked_out) :
                            Dropdown::divider();
						    Dropdown::checkin('cb' . $i, 'sections.');
						endif;

						Dropdown::divider();
                        Dropdown::addCustomItem('<i class="fas fa-eye"></i> '.\Joomla\CMS\Language\Text::_('C_OPENSECTION'), \Joomla\CMS\Router\Route::_(Url::records($item)));
						Dropdown::divider();
                        Dropdown::addCustomItem(
                                '<i class="fas fa-folder-open"></i> '.\Joomla\CMS\Language\Text::_('C_MANAGE_CATS') . ' <span class="badge' . ($item->categories ? ' bg-success' : ' bg-light text-dark border') . '">' . $item->categories . '</span>',
                                \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=cats&section_id=' . $item->id)
                        );

						echo Dropdown::render();
						?>
					</div>
				</td>
				<td nowrap="nowrap">
					<a rel="tooltip" data-bs-toggle="tooltip" title="<?php echo \Joomla\CMS\Language\Text::_('CCATEGOY_MANAGE'); ?>" href="<?php echo \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=cats&section_id=' . $item->id) ?>">
						<?php echo \Joomla\CMS\Language\Text::_('CCATEGORIES'); ?>
					</a>
					<span class="badge<?php echo($item->fieldnum ? ' bg-success' : ' bg-light text-dark border') ?>"><?php echo $item->fieldnum; ?></span>
				</td>
				<td class="center">
					<span class="badge <?php echo($item->records ? ' bg-info' : ' bg-light text-dark border') ?>"><?php echo $item->records ?></span>
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
    <input type="hidden" name="id" value="0"/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo \Joomla\CMS\HTML\HTMLHelper::_('form.token'); ?>
</form>