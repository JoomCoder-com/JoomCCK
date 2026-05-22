<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla 4 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2026 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();

extract($displayData);

// Raw values and permission gating live in HTMLFormatHelper::compareData().
$data = HTMLFormatHelper::compareData($record, $type, $section);
if (!$data) {
	return;
}

$img = HTMLHelper::image($data['icon'], $data['alt'], [
	'data-bs-original-title' => $data['tip'],
	'rel'                    => 'tooltip',
]);
?>
<button class="btn border btn-sm btn-light<?php echo $data['hidden'] ? ' hide' : ''; ?>" id="<?php echo $data['id']; ?>" type="button" onclick="<?php echo $data['onclick']; ?>">
	<?php echo $img; ?>
</button>
