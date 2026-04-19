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
if (!$record->user_id) {
	return;
}
if ($user->get('id') == $record->user_id) {
	return;
}
if (!$section->params->get('personalize.personalize')) {
	return;
}
if (!$section->params->get('personalize.post_anywhere')) {
	return;
}
if (in_array($user->get('id'), $record->repostedby)) {
	return;
}
if ($record->whorepost == 0 && ($record->user_id != $user->get('id'))) {
	return;
}
if ($record->whorepost == 1 && ($record->user_id != $user->get('id')) && !CUsrHelper::is_follower($record->user_id, $user->get('id'), $section)) {
	return;
}

$file      = Uri::root() . 'media/com_joomcck/icons/16/arrow-retweet.png';
$alt       = Text::_('CMSG_REPOST');
$sectionId = Factory::getApplication()->input->getInt('section_id');
$img       = HTMLHelper::image($file, $alt, [
	'data-bs-original-title' => $alt,
	'rel'                    => 'tooltip',
]);
?>
<button class="btn btn-co-control btn-sm" id="repost_<?php echo (int) $record->id; ?>" type="button" onclick="Joomcck.RepostRecord(<?php echo (int) $record->id; ?>, <?php echo (int) $sectionId; ?>);">
	<?php echo $img; ?>
</button>
