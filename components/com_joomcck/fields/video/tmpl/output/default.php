<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;

defined('_JEXEC') or die();

if($vw = $this->request->get('view_what')) {
	$client = $vw;
}

$key = $client . $this->id . $record->id;

// Add video JavaScript
\Joomla\CMS\Factory::getDocument()->addScript(\Joomla\CMS\Uri\Uri::root(true) . '/components/com_joomcck/fields/video/assets/video.js');

?>

<style>
    .video-block {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        height: 0;
        overflow: hidden;
        display: block;
        width: 100%;
        margin-bottom: 20px;
    }
    .video-block iframe,
    .video-block object,
    .video-block embed,
    .video-block video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    /* Media query for tablets */
    @media (max-width: 768px) {
        .video-block iframe,
        .video-block object,
        .video-block embed,
        .video-block video {
            max-width: 100% !important;
        }
    }
</style>

<div id="video-block-<?php echo $key; ?>" class="video-field-container">
	<?php
	// Use layouts for each video type
	if(!empty($this->value['files'])) {
		echo Layout::render('output.type.local', array(
			'field' => $this,
			'record' => $record,
			'client' => $client,
			'key' => $key
		));
	}

	if(!empty($this->value['embed'])) {
		echo Layout::render('output.type.embed', array(
			'field' => $this,
			'record' => $record,
			'client' => $client
		));
	}

	if(!empty($this->value['link'])) {
		echo Layout::render('output.type.remote', array(
			'field' => $this,
			'record' => $record,
			'client' => $client
		));
	}
	?>
</div>