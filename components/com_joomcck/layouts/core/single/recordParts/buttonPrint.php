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

// Raw values (print url, onclick) and the item_print toggle live in HTMLFormatHelper::printData().
$data = HTMLFormatHelper::printData($record, $params ?? null);
if (!$data) {
	return;
}
?>
<a class="btn btn-sm btn-light border" onclick="<?php echo $data['onclick']; ?>">
	<?php echo HTMLFormatHelper::icon('printer.png', $data['tip']); ?>
</a>
