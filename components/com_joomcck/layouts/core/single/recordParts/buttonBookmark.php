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
if (!$user->get('id') || is_null($type)) {
	return;
}
if (!in_array($type->params->get('properties.item_can_favorite'), $user->getAuthorisedViewLevels())) {
	return;
}
if (!in_array($record->access, $user->getAuthorisedViewLevels())) {
	return;
}

$pack      = $params->get('tmpl_core.bookmark_icons', 'star');
$state     = (int) ($record->bookmarked > 0);
$file      = Uri::root() . 'media/com_joomcck/icons/bookmarks/' . $pack . '/state' . $state . '.png';
$alt       = $record->bookmarked
	? Mint::_('CMSG_REMOVEBOOKMARK_' . $type->id, Text::_('CMSG_REMOVEBOOKMARK'))
	: Mint::_('CMSG_ADDBOOKMARK_' . $type->id, Text::_('CMSG_ADDBOOKMARK'));
$sectionId = Factory::getApplication()->input->getInt('section_id');
$img       = HTMLHelper::image($file, $alt, [
	'data-bs-original-title' => $alt,
	'rel'                    => 'tooltip',
	'id'                     => 'bookmark_' . $record->id,
]);
?>
<button class="btn border btn-light btn-sm" type="button" onclick="Joomcck.bookmarkRecord(<?php echo (int) $record->id; ?>, '<?php echo htmlspecialchars($pack, ENT_QUOTES, 'UTF-8'); ?>', <?php echo (int) $sectionId; ?>);">
	<?php echo $img; ?>
</button>
