<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla 4 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2026 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 *
 * Inline "funnel" filter link shown next to a record value (author, type, …). Override this single
 * file to restructure every inline filter funnel at once. The icon is chosen per template via the
 * matching *_filter_icon parameter and passed in as $icon.
 *
 * Expected keys: name, value, tip, section. Optional: type, icon (default funnel-small.png).
 */

defined('_JEXEC') or die();

extract($displayData);

if (!isset($name, $value, $tip, $section)) {
	return;
}

echo FilterHelper::filterButton($name, $value, $type ?? null, $tip, $section, $icon ?? 'funnel-small.png');
