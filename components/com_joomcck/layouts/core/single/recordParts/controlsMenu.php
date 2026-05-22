<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla 4 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2026 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 *
 * Settings/controls dropdown for a single record view. Override this single file to restructure the
 * menu without touching the surrounding button group. $controls is the per-record control array
 * built by the record model; $record is the full item for additional context.
 */

defined('_JEXEC') or die();

extract($displayData);

if (empty($controls)) {
	return;
}
?>
<button type="button" data-bs-toggle="dropdown" class="dropdown-toggle btn btn-sm btn-light border">
	<?php echo HTMLFormatHelper::icon('gear.png'); ?></button>
<ul class="dropdown-menu">
	<?php echo list_controls($controls); ?>
</ul>
