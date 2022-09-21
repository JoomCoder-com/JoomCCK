<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>

<div class="btn-toolbar" id="toolbar">
	<button onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('templates.uninstall')}" class="btn btn-small btn-danger pull-right">
		Uninstall
	</button>
	<button type="button" class="btn btn-small" data-toggle="collapse" data-target="#ins_form" rel="{onClose: function() {}}">
		Install
	</button>
	<button type="button" class="btn btn-small" data-toggle="collapse" data-target="#cr_form" rel="{onClose: function() {}}">
		Copy/Rename
	</button>
</div>