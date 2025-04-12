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
$record = $displayData['record'];
$key = $displayData['key'];

// Get parameters
$width = $field->params->get('width');
$height = $field->params->get('height');

// Videos should already be prepared in the field object
$videos = $field->videos;

// Add JW Player script
\Joomla\CMS\Factory::getDocument()->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/vendors/jwplayer/jwplayer.js');
?>

<?php if(!empty($videos)): ?>
    <div class="video-block local-videos">
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                var playerInstance = jwplayer("mediaplayer<?php echo $key ?>");
                playerInstance.setup({
                    "width": "100%",
                    "aspectratio": "16:9",
                    "responsive": true,
				    <?php
				    if(count($videos) > 1) {
				    ?>
                    "playlist": [
					    <?php
					    $v = array();
					    foreach($videos as $key_v => $video) {
						    $v[$key_v] = '{
                    "sources": [{ 
                        "file": "' . $video->url . '", 
                        "label": "' . $video->display_title . '" 
                    }],';

						    if($video->thumbnail) {
							    $v[$key_v] .= '"image": "' . $video->thumbnail . '",';
						    }

						    if(isset($video->duration) && $video->duration > 0) {
							    $v[$key_v] .= '"duration": "' . $video->duration . '",';
						    }

						    if(isset($video->description) && !empty($video->description)) {
							    $v[$key_v] .= '"description": "' . $video->description . '",';
						    }

						    $v[$key_v] .= '"title": "' . $video->display_title . '"
                }';
					    }
					    echo implode(',', $v);
					    ?>
                    ]
				    <?php
				    if($field->params->get('params.listbar', TRUE)):
				    ?>
                    ,
                    "listbar": {
                        "position": "<?php echo $field->params->get('params.listbar_position', 'right'); ?>",
                        "size": <?php echo $field->params->get('params.listbar_width', 200); ?>
                    }
				    <?php
				    endif;
				    } else {
				    $video = reset($videos); // Get first video without modifying array
				    ?>
                    "file": "<?php echo $video->url; ?>",
				    <?php if(isset($video->duration)): ?>
                    "duration": "<?php echo $video->duration; ?>",
				    <?php endif; ?>
                    "title": "<?php echo $video->display_title; ?>",
				    <?php if(isset($video->description) && !empty($video->description)): ?>
                    "description": "<?php echo $video->description; ?>",
				    <?php endif; ?>
				    <?php if($video->thumbnail): ?>
                    "image": "<?php echo $video->thumbnail; ?>"
				    <?php endif; ?>
				    <?php
				    }
				    ?>
                });
            });
        </script>
        <div id="mediaplayer<?php echo $key; ?>"></div>
    </div>
<?php endif; ?>