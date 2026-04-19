<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla 4 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2026 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die();

extract($displayData);

if (!empty($params) && is_object($params) && method_exists($params, 'get')) {
	if (!$params->get('tmpl_core.item_print')) {
		return;
	}
}

$printUrl = Route::_($record->url . '&tmpl=component&print=1');
?>
<a class="btn btn-sm btn-light border" onclick="window.open('<?php echo htmlspecialchars($printUrl, ENT_QUOTES, 'UTF-8'); ?>','win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;">
	<?php echo HTMLFormatHelper::icon('printer.png', Text::_('CPRINT')); ?>
</a>
