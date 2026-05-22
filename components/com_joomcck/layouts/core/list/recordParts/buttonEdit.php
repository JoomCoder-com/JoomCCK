<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla 4 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2026 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

extract($displayData);

// Raw values (href, label, icon) and the edit-permission gate live in HTMLFormatHelper::editData().
// Render this anywhere to expose the edit action on its own, independent of the settings dropdown.
$data = HTMLFormatHelper::editData($record, $type, $section);
if (!$data) {
	return;
}
?>
<a class="btn btn-sm btn-light border joomcck-edit-link" href="<?php echo $data['href']; ?>">
	<?php echo HTMLFormatHelper::icon('pencil.png', $data['label']); ?>
</a>
