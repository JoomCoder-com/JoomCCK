<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
if($vw = $this->request->get('view_what'))
	$client = $vw;
$key = $client.$this->id.$record->id;
\Joomla\CMS\Factory::getDocument()->addScript(\Joomla\CMS\Uri\Uri::root(true).'/components/com_joomcck/fields/video/assets/video.js');
?>

<div id="video-block<?php echo $key;?>">
	<div id="destr<?php echo $key;?>" class="video-block" style="display: none;"><div id="mediaplayer<?php echo $key;?>"></div></div>
	<div id="htmlplayer<?php echo $key;?>">
		<div class="progress progress-success progress-striped">
			<div class="bar" style="width: 100%"><?php echo \Joomla\CMS\Language\Text::_('V_LOADING');?></div>
		</div>
	</div>
</div>

<script type="text/javascript">
jQuery(function(){
	Joomcck.loadvideo(<?php echo $this->id ?>, <?php echo $record->id ?>, '<?php echo $key ?>', '<?php echo $client ?>', '<?php echo $this->params->get('params.default_width', 640)?>');
});
</script>
