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

$user = Factory::getApplication()->getIdentity();
if (!$user->get('id')) {
	return;
}
if (!in_array($section->params->get('events.subscribe_record'), $user->getAuthorisedViewLevels())) {
	return;
}
if (!in_array($record->access, $user->getAuthorisedViewLevels())) {
	return;
}

$state = (int) ($record->subscribed > 0);

if (!empty($params) && is_object($params) && method_exists($params, 'get')) {
	$pack = $params->get('tmpl_core.follow_icons', 'default');
	$file = Uri::root() . 'media/com_joomcck/icons/follow/' . $pack . '/state' . $state . '.png';
} else {
	$file = Uri::root() . 'media/com_joomcck/icons/16/follow' . $state . '.png';
}

$alt       = $record->subscribed ? Text::_('CMSG_CLICKTOUNFOLLOW') : Text::_('CMSG_CLICKTOFOLLOW');
$sectionId = Factory::getApplication()->input->getInt('section_id');
$img       = HTMLHelper::image($file, $alt, [
	'data-bs-original-title' => $alt,
	'rel'                    => 'tooltip',
	'id'                     => 'follow_record_' . $record->id,
]);
?>
<button class="btn btn-sm btn-light border" type="button" onclick="Joomcck.followRecord(<?php echo (int) $record->id; ?>, <?php echo (int) $sectionId; ?>);">
	<?php echo $img; ?>
</button>
