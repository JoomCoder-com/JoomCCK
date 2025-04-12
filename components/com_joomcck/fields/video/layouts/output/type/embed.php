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

// get remote links
$embeds = isset($field->value['link']) && count($field->value['embed']) > 0 ? $field->value['embed'] : [];

// don't continue if not remote link
if(empty($embeds))
	return;

// Loop through embeds
foreach($embeds as $embed) {
	if(empty($embed)) continue;
	?>
	<div class="jcck-custom-responsive-embed mb-3">
		<?php echo CVideoAdapterHelper::constrain($embed, $width); ?>
	</div>
	<?php
}