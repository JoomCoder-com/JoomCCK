<?php
/**
 * JW Player sublayout
 */
defined('_JEXEC') or die();

// Extract variables
extract($displayData);

// Add JW Player script
\Joomla\CMS\Factory::getDocument()->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/vendors/jwplayer/jwplayer.js');
?>

<div class="video-block local-videos">
	<script type="text/javascript">
        jQuery(document).ready(function($) {
            jwplayer("mediaplayer<?php echo $key ?>").setup({
                "width": "100%",
                "aspectratio": "16:9",
				<?php
				if(count($videos) > 1) {
				?>
                "playlist": [
					<?php
					$v = array();
					foreach($videos as $key_v => $video) {
						$v[$key_v] = '{sources:[{ file: "' . $video->url . '", label:"' . $video->display_title . '" }],';

						$v[$key_v] .= 'image: "' . $video->thumbnail . '",';

						if(isset($video->duration) && $video->duration > 0)
							$v[$key_v] .= 'duration: "' . $video->duration . '",';

						if(isset($video->description) && !empty($video->description))
							$v[$key_v] .= 'description: "' . $video->description . '",';

						$v[$key_v] .= 'title:"' . $video->display_title . '"}';
					}
					echo implode(',', $v);
					?>
                ]
				<?php if($field->params->get('params.listbar', TRUE)): ?>
                ,
                listbar: {
                    position: '<?php echo $field->params->get('params.listbar_position', 'right'); ?>',
                    size: <?php echo $field->params->get('params.listbar_width', 200); ?>
                }
				<?php endif; ?>
				<?php
				} else {
				$video = reset($videos); // safer than array_pop which modifies the array
				?>
                "file": "<?php echo $video->url; ?>",
				<?php if(isset($video->duration)): ?>
                "duration": "<?php echo $video->duration; ?>",
				<?php endif; ?>
                "title": "<?php echo $video->display_title; ?>",
                "description": "<?php echo $video->description; ?>"
				<?php
				}
				?>
            });
        });
	</script>
	<div id="mediaplayer<?php echo $key; ?>"></div>
</div>