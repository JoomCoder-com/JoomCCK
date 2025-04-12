<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

use Joomcck\Layout\Helpers\Layout;

// Extract variables
$field = $displayData['field'];
$record = $displayData['record'];
$key = $displayData['key'];

// Get parameters
$width = $field->params->get('width');
$height = $field->params->get('height');

// Get videos from field
$videos = $field->videos;

if (empty($videos)) {
	return;
}

// Get selected video player
$player = $field->params->get('params.video_player', 'videojs');

// Load player layout
echo Layout::render('output.type.localPlayers.' . $player, [
	'field' => $field,
	'record' => $record,
	'key' => $key,
	'videos' => $videos,
	'width' => $width,
	'height' => $height
]);