<?php
/**
 * MediaElement.js player sublayout
 */
defined('_JEXEC') or die();

// Extract variables
extract($displayData);

// Add MediaElement.js CSS and JS
\Joomla\CMS\Factory::getDocument()->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/mediaelement/4.2.16/mediaelementplayer.min.css');
\Joomla\CMS\Factory::getDocument()->addScript('https://cdnjs.cloudflare.com/ajax/libs/mediaelement/4.2.16/mediaelement-and-player.min.js');

// Add MediaElement.js Playlist plugin
\Joomla\CMS\Factory::getDocument()->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/mediaelement-plugins/2.5.0/playlist/playlist.min.css');
\Joomla\CMS\Factory::getDocument()->addScript('https://cdnjs.cloudflare.com/ajax/libs/mediaelement-plugins/2.5.0/playlist/playlist.min.js');
?>

<div class="jcck-video-player-me mb-3">
	<video id="mediaelement-player-<?php echo $key; ?>" width="100%" height="auto" style="max-width:100%;" preload="none" controls playsinline>
		<?php foreach($videos as $index => $video): ?>
			<source src="<?php echo $video->url; ?>" type="video/mp4" title="<?php echo $video->display_title; ?>">
		<?php endforeach; ?>
	</video>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        var player = new MediaElementPlayer('mediaelement-player-<?php echo $key; ?>', {
            features: ['playpause', 'current', 'progress', 'duration', 'volume', 'playlist', 'fullscreen'],
            // Available features: 'playpause', 'current', 'progress', 'duration', 'tracks', 'volume', 'fullscreen', 'playlist'

            // Playlist plugin settings
            playlist: {
				<?php if(count($videos) > 1): ?>
                // Enable playlist
                playlist: [
					<?php foreach($videos as $index => $video): ?>
                    {
                        src: '<?php echo $video->url; ?>',
                        type: 'video/mp4',
                        title: '<?php echo addslashes($video->display_title); ?>',
						<?php if($video->thumbnail): ?>
                        poster: '<?php echo $video->thumbnail; ?>',
						<?php endif; ?>
                        description: '<?php echo addslashes(isset($video->description) ? \Joomla\CMS\HTML\HTMLHelper::_('string.truncate', $video->description, 100) : ''); ?>'
                    }<?php echo ($index < count($videos) - 1) ? ',' : ''; ?>
					<?php endforeach; ?>
                ],
				<?php endif; ?>

                // Playlist options
                loop: false,
                autoplay: false,
                currentIndex: 0,
                nextText: 'Next',
                prevText: 'Previous',
                loopText: 'Loop',
                shuffleText: 'Shuffle',
                playlistTitle: 'Playlist',
                removeText: 'Remove'
            },

            // General player options
            alwaysShowControls: true,
            enableAutosize: true,
            stretching: 'fill',
            iconSprite: '', // Default sprite

            // Enable responsive behavior
            videoWidth: '100%',
            videoHeight: 'auto',

            // Set initial poster if first video has thumbnail
			<?php if(reset($videos)->thumbnail): ?>
            poster: '<?php echo reset($videos)->thumbnail; ?>',
			<?php endif; ?>

            // Success callback
            success: function(mediaElement, originalNode, instance) {
                // Player is ready
                mediaElement.addEventListener('ended', function() {
                    // Automatically move to next video when current one ends
                    var playlist = instance.layers.querySelector('.mejs__playlist');
                    if (playlist) {
                        var current = playlist.querySelector('.current');
                        if (current && current.nextElementSibling) {
                            current.nextElementSibling.click();
                        } else if (playlist.querySelector('li')) {
                            // Loop back to first item if at end of playlist
                            playlist.querySelector('li').click();
                        }
                    }
                });
            }
        });
    });
</script>