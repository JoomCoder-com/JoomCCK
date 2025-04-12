<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

// Extract variables
$field = $displayData['field'];

// Get width
$width = $field->params->get('width');

// Loop through embeds
foreach($field->embed as $embed) {
	if(empty($embed)) continue;
	?>
	<div class="video-block embed-video">
		<?php echo CVideoAdapterHelper::constrain($embed, $width); ?>
	</div>
	<?php
}