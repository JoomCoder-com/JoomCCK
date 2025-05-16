<?php
/**
 * MediaElement.js player sublayout
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die();

// Extract variables
extract($displayData);

// Add MediaElement.js Player CSS and JS
Factory::getDocument()->addStyleSheet(Uri::root(TRUE) . '/media/com_joomcck/css/mediaelement/mediaelementplayer.min.css');
Factory::getDocument()->addScript(Uri::root(TRUE) . '/media/com_joomcck/js/mediaelement/mediaelement-and-player.min.js');

// Add MediaElement.js Playlist plugin CSS and JS
if (count($videos) > 1):
Factory::getDocument()->addStyleSheet(Uri::root(TRUE) . '/media/com_joomcck/css/mediaelement/playlist.min.css');
Factory::getDocument()->addScript(Uri::root(TRUE) . '/media/com_joomcck/js/mediaelement/playlist.min.js');
endif;

?>

<div id="mediaelement-player-container-<?php echo $key; ?>">
    <div class="jcck-video-player-me mb-3 w-100">
        <video id="mediaelement-player-<?php echo $key; ?>" height="auto" preload="true" controls playsinline>
			<?php foreach($videos as $index => $video): ?>
                <source src="<?php echo $video->url; ?>" type="video/mp4" title="<?php echo $video->display_title; ?>">
			<?php endforeach; ?>
        </video>
    </div>
</div>


<script type="text/javascript">
    jQuery(document).ready(function($) {
        var player = new MediaElementPlayer('mediaelement-player-<?php echo $key; ?>', {
            features: ['playpause', 'current', 'progress', 'duration', 'volume', 'playlist', 'fullscreen'],
            // Available features: 'playpause', 'current', 'progress', 'duration', 'tracks', 'volume', 'fullscreen', 'playlist'

            // Aspect ratio handling
            stretching: 'responsive',  // Key setting for handling different aspect ratios

            // Playlist plugin settings
	        <?php if(count($videos) > 1): ?>
            playlist: {
                // Playlist options
                loop: false,
                autoplay: false,
                currentIndex: 0,
                nextText: 'Next',
                prevText: 'Previous',
                loopText: 'Loop',
                shuffleText: 'Shuffle',
                playlistTitle: 'Playlist',
                removeText: 'Remove',

            },
	        <?php endif; ?>

            // General player options
            alwaysShowControls: true,

            // Set initial poster if first video has thumbnail
			<?php if(reset($videos)->thumbnail): ?>
            poster: '<?php echo reset($videos)->thumbnail; ?>',
			<?php endif; ?>
        });
    });
</script>