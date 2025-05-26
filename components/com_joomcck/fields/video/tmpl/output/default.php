<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomcck\Layout\Helpers\Layout;

defined('_JEXEC') or die();

if($vw = $this->request->get('view_what')) {
	$client = $vw;
}

$key = $client . $this->id . $record->id;

// load web assets manager
$wa = Webassets::$wa;

// Add video JavaScript
\Joomla\CMS\Factory::getDocument()->addScript(\Joomla\CMS\Uri\Uri::root(true) . '/components/com_joomcck/fields/video/assets/video.js');

// make iframe video responsive
$wa->useStyle('com_joomcck.responsive-video');

?>


<div id="video-block-<?php echo $key; ?>" class="video-field-container">
	<?php
	// Use layouts for each video type
	if(isset($this->value['files']) && !empty($this->value['files'])) {
		echo Layout::render('output.type.local', array(
			'field' => $this,
			'record' => $record,
			'client' => $client,
			'key' => $key
		),$this->layoutFolder);
	}

	if(isset($this->value['embed']) && !empty($this->value['embed'])) {
		echo Layout::render('output.type.embed', array(
			'field' => $this,
			'record' => $record,
			'client' => $client
		),$this->layoutFolder);
	}

	if(isset($this->value['link']) && !empty($this->value['link'])) {
		echo Layout::render('output.type.remote', array(
			'field' => $this,
			'record' => $record,
			'client' => $client
		),$this->layoutFolder);
	}
	?>
</div>