<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla 4 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2026 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die();

extract($displayData);

if (!$type->params->get('properties.item_compare')) {
	return;
}

$app = Factory::getApplication();
if ($app->input->get('api') == 1) {
	return;
}

$list = $app->getUserState("compare.set{$section->id}");
ArrayHelper::clean_r($list);

$hide = in_array($record->id, $list) ? ' hide' : '';
$file = Uri::root() . 'media/com_joomcck/icons/16/edit-diff.png';
$img  = HTMLHelper::image($file, Text::_('Compare'), [
	'data-bs-original-title' => Text::_('CMSG_COMPARE'),
	'rel'                    => 'tooltip',
]);
$sectionId = $app->input->getInt('section_id');
?>
<button class="btn border btn-sm btn-light<?php echo $hide; ?>" id="compare_<?php echo (int) $record->id; ?>" type="button" onclick="Joomcck.CompareRecord(<?php echo (int) $record->id; ?>, <?php echo (int) $sectionId; ?>);">
	<?php echo $img; ?>
</button>
