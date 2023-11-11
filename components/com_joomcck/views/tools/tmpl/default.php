<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<div class="page-header">
	<h1>
		<img src="<?php echo \Joomla\CMS\Uri\Uri::root(TRUE); ?>/components/com_joomcck/images/icons/tools.png">
		<?php echo \Joomla\CMS\Language\Text::_('XML_SUBMENU_TOOLS'); ?>
	</h1>
</div>
<table class="table table-hover" id="fieldsList">
	<thead>
	<tr>
		<th width="50px"><?php echo \Joomla\CMS\Language\Text::_(''); ?></th>
		<th><?php echo \Joomla\CMS\Language\Text::_('CNAME'); ?></th>
		<th><?php echo \Joomla\CMS\Language\Text::_('CDESCR'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach($this->tools AS $name => $row): ?>
		<?php
		$link = 'index.php?option=com_joomcck&layout=form&view=tools&name=' . $name;
		$h    = (@$row->height ? @$row->height : '380');
		$w    = (@$row->width ? @$row->width : '570');
		?>
		<tr>
			<td><img src="<?php echo JURI::root(TRUE); ?>/components/com_joomcck/library/php/tools/<?php echo $name; ?>/icon.png"/>
			</td>
			<td><a href="<?php echo \Joomla\CMS\Router\Route::_($link); ?>"
				   title="<?php echo \Joomla\CMS\Language\Text::_($row->label); ?>::<?php echo htmlspecialchars(\Joomla\CMS\Language\Text::_($row->description)); ?>">
					<?php echo \Joomla\CMS\Language\Text::_($row->label); ?></a></td>
			<td><small><?php echo \Joomla\CMS\Language\Text::_($row->description); ?></small></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>