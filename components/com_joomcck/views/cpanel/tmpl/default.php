<?php
/**
 * by JoomBoost
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2007-2014 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<?php echo HTMLFormatHelper::layout('navbar'); ?>

<div class="page-header">
	<h1><?php echo JText::_('C_CPANEL') ?></h1>
</div>

<h2><?php echo JText::_('CAPPBUILD'); ?></h2>
<ul class="unstyled cpanel-list">
	<?php cp_item_el('XML_SUBMENU_QUICKSTART', 'section&layout=fast', 'fast'); ?>
	<?php cp_item_el('XML_SUBMENU_SECTIONS', 'sections', 'sections'); ?>
	<?php cp_item_el('XML_SUBMENU_TYPES', 'ctypes', 'types'); ?>
	<?php cp_item_el('XML_SUBMENU_TEMPLATES', 'templates', 'tmpl'); ?>
</ul>
<div class="clearfix"></div>

<h2><?php echo JText::_('CCONTENT'); ?></h2>
<ul class="unstyled cpanel-list">
	<?php cp_item_el('XML_SUBMENU_RECORDS', 'items', 'items'); ?>
	<?php cp_item_el('XML_SUBMENU_VOTES', 'votes', 'votes'); ?>
	<?php cp_item_el('XML_SUBMENU_COMMENTS', 'comms', 'comments'); ?>
	<?php cp_item_el('XML_SUBMENU_TAGS', 'tags', 'tags'); ?>
</ul>
<div class="clearfix"></div>
<h2><?php echo JText::_('CCONTENTTOOLS'); ?></h2>
<ul class="unstyled cpanel-list">
	<?php cp_item_el('XML_SUBMENU_PACK', 'packs', 'packs'); ?>
	<?php cp_item_el('CMODERATORS', 'moderators', 'moders'); ?>
	<?php cp_item_el('XML_SUBMENU_TOOLS', 'tools', 'tools'); ?>
	<?php cp_item_el('XML_SUBMENU_AUDIT', 'auditlog', 'audit'); ?>
	<?php cp_item_el('XML_SUBMENU_NOTIFY', 'notifications', 'bell'); ?>
	<?php cp_item_el('XML_SUBMENU_IMPORT', 'import', 'import'); ?>

</ul>

<div class="clearfix"></div>
<div class="well alert-info">What else could we place here?</div>

<?php
function cp_item_el($title, $link, $img)
{
	?>
	<li>
		<a href="<?php echo Url::view($link) ?>" style="background-image: url(<?php echo JUri::root(TRUE); ?>/components/com_joomcck/images/icons/<?php echo $img ?>.png)">
			<?php echo JText::_($title); ?>
		</a>
	</li>
<?php
}
?>