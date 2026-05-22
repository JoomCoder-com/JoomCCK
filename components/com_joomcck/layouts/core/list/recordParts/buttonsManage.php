<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Factory;

defined('_JEXEC') or die();


extract($displayData);

$user = Factory::getApplication()->getIdentity();
$disabled = !isset($disabled) ? [] : $disabled;

if (!$user->id) {
	return;
}

$type = $submissionTypes[$item->type_id];
?>

<div class="user-ctrls">
	<div class="btn-group" role="group" style="display: none;">
		<?php echo Layout::render('core.list.recordParts.buttonBookmark', ['record' => $item, 'type' => $type, 'params' => $params]); ?>
		<?php echo Layout::render('core.list.recordParts.buttonFollow',   ['record' => $item, 'section' => $section, 'params' => $params]); ?>
		<?php echo Layout::render('core.list.recordParts.buttonRepost',   ['record' => $item, 'section' => $section]); ?>
		<?php if (!in_array('compare', $disabled)): ?>
			<?php echo Layout::render('core.list.recordParts.buttonCompare', ['record' => $item, 'type' => $type, 'section' => $section]); ?>
		<?php endif; ?>
		<?php if (isset($params) && is_object($params) && $params->get('tmpl_core.item_edit_button')): ?>
			<?php echo Layout::render('core.list.recordParts.buttonEdit', ['record' => $item, 'type' => $type, 'section' => $section]); ?>
		<?php endif; ?>
		<?php echo Layout::render('core.list.recordParts.controlsMenu', ['controls' => $item->controls, 'record' => $item]); ?>
	</div>
</div>
