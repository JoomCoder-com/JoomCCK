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

// Loop through links
foreach($field->link as $link) {
	if(empty($link)) continue;

	if(in_array(strtolower(pathinfo($link, PATHINFO_EXTENSION)), array('mp4', 'flv', 'webm'))) {
		$md5link = md5($link);
		?>
		<div class="video-block remote-video">
			<script type="text/javascript">
                jQuery(document).ready(function($) {
                    jwplayer("remoteLink<?php echo $md5link; ?>").setup({
                        "width": "<?php echo $width; ?>",
                        "height": "<?php echo $height; ?>",
                        "file": "<?php echo $link; ?>"
                    });
                });
			</script>
			<div id="remoteLink<?php echo $md5link; ?>"></div>
		</div>
	<?php } else {
		$block = CVideoAdapterHelper::getVideoCode($field->params, $link);
		if($block) {
			?>
			<div class="video-block remote-video-service">
				<?php echo $block; ?>
			</div>
			<?php
		}
	}
}