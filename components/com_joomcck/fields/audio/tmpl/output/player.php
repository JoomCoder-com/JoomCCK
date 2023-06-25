<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

foreach ($this->tracks as $k => $file):

	$tracks[] = "{sources:[{'file':'" . $this->getFileUrl($file) . "', 'title':'" . ($file->title ? $file->title : $file->realname) . "'}]}";

endforeach;

?>

<div style="display: block"><div id="mediaplayer<?php echo $record->id;?>"></div></div>
<div class="clearfix"></div>

<script type="text/javascript">
	var player<?php echo $record->id;?> = jwplayer("mediaplayer<?php echo $record->id;?>").setup({
		"width": "<?php echo $this->params->get('tmpl_full.width');?>",
        "height": "<?php echo $this->params->get('tmpl_full.height','27');?>",
		"controlbar": "top",
		"repeat": "true",
		"playlist": [<?php echo implode(',', $tracks)?>]
		<?php if ($this->params->get('tmpl_full.listbar', false)):?>
			,
			"listbar": {
		        "position": 'top',
		        "size": '200'
		    }
		<?php endif;?>
	});

	function stop<?php echo $record->id;?>(idx)
	{
		$("#file_stop_<?php echo $record->id;?>_" + idx).css("display", "none");
		$("#file_play_<?php echo $record->id;?>_" + idx).css("display", "inline-block");
		player<?php echo $record->id;?>.stop();
	}
	function play<?php echo $record->id;?>(index)
	{
		player<?php echo $record->id;?>.playlistItem(index);
		$('#playlist_table').find("a[id^=file_play_<?php echo $record->id;?>_]").css("display", "inline-block");
		$('#playlist_table').find("a[id^=file_stop_<?php echo $record->id;?>_]").css("display", "none");
		$("#file_play_<?php echo $record->id;?>_" + index).css("display", "none");
		$("#file_stop_<?php echo $record->id;?>_" + index).css("display", "inline-block");
	}
</script>
<?php
