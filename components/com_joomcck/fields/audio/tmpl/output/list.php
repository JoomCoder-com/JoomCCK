<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
?>

<span style="display: block"><div id="mediaplayer<?php echo $record->id;?>"></div></span>
<table class="table table-condensed" id="playlist_table">
	<thead>
		<tr>
			<th><?php echo JText::_('CNAME');?></th>

			<?php if($this->params->get('tmpl_list.show_year')):?>
			<th width="1%"><?php echo JText::_('CYEAR')?></th>
			<?php endif;?>

			<?php if($this->params->get('tmpl_list.show_genre')):?>
			<th width="1%"><?php echo JText::_('P_GENRE')?></th>
			<?php endif;?>

			<?php if($this->params->get('tmpl_list.show_album')):?>
			<th width="8%" style="max-width: 120px;"><?php echo JText::_('P_ALNUM')?></th>
			<?php endif;?>

			<?php if($this->params->get('tmpl_list.show_artist')):?>
			<th width="1%"><?php echo JText::_('P_ARTIST')?></th>
			<?php endif;?>

			<?php if($this->descr):?>
			<th width="1%"><?php echo JText::_('P_LYRIC');?></th>
			<?php endif;?>
			<?php if(in_array($this->params->get('tmpl_list.show_download', 0), $this->user->getAuthorisedViewLevels())):?>
			<th width="1%"><?php echo JText::_('CSAVE');?></th>
			<?php endif;?>
		</tr>
	</thead>
	<tbody>
		<?php foreach($this->tracks as $k => $file):?>

		<?php
			$tracks[] = "{sources:[{'file':'".$this->getFileUrl($file)."', 'title':'".($file->title ? $file->title : $file->realname)."'}]}";
			$data = new JRegistry($file->params);
		?>
		<tr valign="middle">
			<td>
				<a href="javascript:void(0)" id="file_play_<?php echo $record->id;?>_<?php echo $k;?>"
					onclick="play<?php echo $record->id;?>(<?php echo $k;?>)">
					<img src="<?php echo JURI::root(TRUE)?>/media/mint/icons/16/control.png" alt="<?php echo JText::_('P_PLAY')?>" align="absmiddle"></a>
				<a href="javascript:void(0)" id="file_stop_<?php echo $record->id;?>_<?php echo $k;?>" onclick="stop<?php echo $record->id;?>(<?php echo $k;?>)" style="display:none">
					<img src="<?php echo JURI::root(TRUE)?>/media/mint/icons/16/control-stop-square.png" alt="<?php echo JText::_('P_STOP')?>" align="absmiddle"></a>
				<?php echo $file->title ? $file->title : $file->realname;?>
				<?php if($data->get('comment') && $this->params->get('tmpl_list.show_comment')):?>
					<?php echo $data->get('comment');?>
				<?php endif;?>
			</td>

			<?php if($this->params->get('tmpl_list.show_year')):?>
				<td nowrap="nowrap"><?php echo $data->get('year')?></td>
			<?php endif;?>

			<?php if($this->params->get('tmpl_list.show_genre')):?>
				<td nowrap="nowrap"><?php echo $data->get('genre')?></td>
			<?php endif;?>

			<?php if($this->params->get('tmpl_list.show_album')):?>
				<td><?php echo $data->get('album')?></td>
			<?php endif;?>

			<?php if($this->params->get('tmpl_list.show_artist')):?>
				<td nowrap="nowrap"><?php echo $data->get('artist')?></td>
			<?php endif;?>

			<?php if($this->descr):?>
				<td nowrap="nowrap">
					<?php if(!empty($file->description)): ?>
					<center>
						<a href="#lyric<?php echo $file->id;?>" class="modal" rel="{handler:'adopt', size:{x:400,y:500}}">
							<img src="<?php echo JURI::root(TRUE)?>/media/mint/icons/16/clipboard-list.png" alt="<?php echo JText::_('P_LYRIC')?>" align="absmiddle">
						</a>
						<div  style="display: none;">
							<div id="lyric<?php echo $file->id;?>"><h3><?php echo JText::_('P_LYRIC')?></h3><br /><?php echo nl2br($file->description);?></div>
						</div>
					</center>
					<?php endif; ?>
				</td>
			<?php endif;?>
			<?php if(in_array($this->params->get('tmpl_list.show_download', 0), $this->user->getAuthorisedViewLevels())):?>
				<td nowrap="nowrap">
					<?php if(in_array($this->params->get('params.allow_download', 0), $this->user->getAuthorisedViewLevels())):?>
					<center>
						<a href="<?php echo $file->url?>">
							<img src="<?php echo JURI::root(TRUE)?>/media/mint/icons/16/disk.png" alt="<?php echo JText::_('CDOWNLOAD')?>" align="absmiddle"></a>
						<?php if ($this->params->get('tmpl_list.hits', 0)):?>
							[<?php echo $file->hits?>]
						<?php endif;?>
					</center>
					<?php endif;?>
				</td>
			<?php endif;?>
		</tr>
		<?php endforeach;?>
	</tbody>
</table>

<script type="text/javascript">
	var player<?php echo $record->id;?> = jwplayer("mediaplayer<?php echo $record->id;?>").setup({
		"width": "<?php echo $this->params->get('tmpl_list.width');?>",
		"height": "<?php echo $this->params->get('tmpl_list.height');?>",
		"controlbar": "bottom",
		"repeat": "true",
		"playlist": [<?php echo implode(',', $tracks)?>]
		<?php if ($this->params->get('tmpl_list.listbar', false)):?>
			,
			"listbar": {
		        "position": 'bottom',
		        "size": '200'
		    }
		<?php endif;?>
	});

	function stop<?php echo $record->id;?>(idx)
	{
		$("#file_stop_<?php echo $record->id;?>_" + idx).setStyle("display", "none");
		$("#file_play_<?php echo $record->id;?>_" + idx).setStyle("display", "inline-block");
		player<?php echo $record->id;?>.stop();
	}
	function play<?php echo $record->id;?>(index)
	{
		player<?php echo $record->id;?>.playlistItem(index);
		$('#playlist_table').getElements("a[id^=file_play_<?php echo $record->id;?>_]").setStyle("display", "inline-block");
		$('#playlist_table').getElements("a[id^=file_stop_<?php echo $record->id;?>_]").setStyle("display", "none");
		$("#file_play_<?php echo $record->id;?>_" + index).setStyle("display", "none");
		$("#file_stop_<?php echo $record->id;?>_" + index).setStyle("display", "inline-block");
	}
</script>
<?php
