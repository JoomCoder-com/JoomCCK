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

<div class="my-3" id="toolbar">
	<button onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('templates.uninstall')}" class="btn btn-danger float-end">
		<i class="fas fa-times"></i> Uninstall
	</button>
	<button role="button" type="button" class="btn btn-light border" data-bs-toggle="collapse" data-bs-target="#ins_form">
        <i class="fas fa-plus"></i> Install
	</button>
	<button type="button" class="btn btn-light border" data-bs-toggle="collapse" data-bs-target="#cr_form">
        <i class="fas fa-copy"></i> Copy/Rename
	</button>
</div>