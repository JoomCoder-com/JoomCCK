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

// Get width and height
$width = $field->params->get('width');
$height = $field->params->get('height');

// get remote links
$remoteLinks = isset($field->value['link']) && count($field->value['link']) > 0 ? $field->value['link'] : [];

// don't continue if not remote link
if(empty($remoteLinks))
    return;

// Loop through links
foreach($remoteLinks as $link) {

	if(empty($link)) continue;

	$block = CVideoAdapterHelper::getVideoCode($field->params, $link);
	if($block) {
		?>
        <div class="jcck-custom-responsive-embed mb-3">
			<?php echo $block; ?>
        </div>
		<?php
	}
}