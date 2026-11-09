<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckupload.php';

class JFormFieldCAudio extends CFormFieldUpload
{


	public $tracks;
	public $el;



	public function getInput()
	{
		$js = "function afterupload(w, s){
			$$('#'+s.id+' div.result').set('html', '" . addslashes(\Joomla\CMS\Language\Text::_('P_CONVERTING')) . "').setStyle('background', 'url(\"" . \Joomla\CMS\Uri\Uri::root(TRUE) . "/media/com_joomcck/js/mooupload/imgs/load_bg_green.gif\")');
			new Request.JSON({
				url: Joomcck.field_call_url,
	    		method:'post',
	    		async: 'false',
	    		data:{
	    			field_id: {$this->id},
					func:'onConvert',
					field:'audio',
					record_id: 0,
					file:s
				},
				onComplete: function(json) {
	    			if(!json)
	    			{
	    				return;
	    			}
	    			if(!json.success)
	    			{
	    				alert(json.error);
	    				return;
	    			}
	    			$$('#'+s.id+' div.result').set('html', '" . addslashes(\Joomla\CMS\Language\Text::_('P_FINISHED')) . "').setStyle('background', 'none');
	    		}
			}).send();
		}";

		\Joomla\CMS\Factory::getDocument()->addScriptDeclaration($js);

		$params['width']  = $this->params->get('params.width', 0);
		$params['height'] = $this->params->get('params.height', 0);

		$params['max_size']         = ($this->params->get('params.max_size', 10000) * 1024);
		$params['method']           = $this->params->get('params.method', 'auto');
		$params['max_count']        = $this->params->get('params.max_count', 0);
		$params['file_formats']     = $this->params->get('params.file_formats', 'mp3, ogg, wav');
		$params['allow_edit_title'] = $this->params->get('params.allow_edit_title', 1);
		$params['allow_add_descr']  = $this->params->get('params.allow_add_descr', 1);

		if($this->params->get('params.convert'))
		{
			$params['callback'] = 'afterupload';
		}
		$this->options = $params;
		$this->upload  = parent::getInput();

		return $this->_display_input();
	}

	public function onConvert($params)
	{
		$name   = $params['file']['upload_name'];
		$ext = pathinfo($name,PATHINFO_EXTENSION);
		$parts  = explode("_", $name);
		$params = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');
		$root   =\Joomla\Filesystem\Path::clean(JPATH_ROOT . DIRECTORY_SEPARATOR . $params->get('general_upload'));

		$dir  = $root . DIRECTORY_SEPARATOR . $ext . DIRECTORY_SEPARATOR . date($params->get('folder_format'), $parts[0]) . DIRECTORY_SEPARATOR;
		$from = $dir . $name;

		switch(strtolower($ext))
		{
			case 'mp3':
				$this->_convert($from, '-acodec libvorbis', 'ogg');
				break;
			case 'ogg':
			case 'wav':
				$this->_convert($from, '-acodec libmp3lame', 'mp3');
				break;
		}

		return 1;
	}

	private function _convert($src, $codec, $ext)
	{
		$descriptorspec = array(0 => array("file", JPATH_ROOT . '/logs/convertion_in.txt', "a"), 1 => array("file", JPATH_ROOT . '/logs/convertion_out.txt', "a"), 2 => array("file", JPATH_ROOT . '/logs/convertion_err.txt', "a"));

		$_ext    = pathinfo($src,PATHINFO_EXTENSION);
		$command = sprintf('%s -i "%s" %s "%s"',
			$this->params->get('params.command', 'ffmpeg'), $src, $codec, str_replace('.' . $_ext, '.' . $ext, $src));

		$p = proc_open($command, $descriptorspec, $pipes,\Joomla\Filesystem\Path::clean(JPATH_ROOT . DIRECTORY_SEPARATOR . \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck')->get('general_upload')));
		proc_close($p);
	}

	/**
	 * MIME type for an audio file, by extension.
	 */
	public static function audioMime($filename)
	{
		$map = [
			'mp3'  => 'audio/mpeg',
			'wav'  => 'audio/wav',
			'ogg'  => 'audio/ogg',
			'oga'  => 'audio/ogg',
			'opus' => 'audio/ogg',
			'm4a'  => 'audio/mp4',
			'aac'  => 'audio/mp4',
			'mp4'  => 'audio/mp4',
			'flac' => 'audio/flac',
			'webm' => 'audio/webm',
		];

		$ext = strtolower(pathinfo((string) $filename, PATHINFO_EXTENSION));

		return $map[$ext] ?? 'audio/mpeg';
	}

	/**
	 * Render a MediaElement.js audio player for the field's tracks.
	 * Exposes window.JcckAudio[<playerId>] with play(index) / stop() for external track buttons.
	 *
	 * @param   object  $record    Record
	 * @param   string  $client    tmpl_full | tmpl_list
	 * @param   bool    $listbar   Render a clickable track list under the player
	 *
	 * @return string
	 */
	public function renderPlayer($record, $client = 'tmpl_full', $listbar = false)
	{
		$playerId = 'jcck-audio-' . (int) $this->id . '-' . (int) $record->id;
		$tracks   = [];

		foreach ($this->tracks as $file)
		{
			$tracks[] = [
				'src'   => $this->getFileUrl($file),
				'type'  => self::audioMime($file->fullpath ?: $file->filename),
				'title' => $file->title ?: $file->realname,
			];
		}

		if (!$tracks)
		{
			return '';
		}

		$width = trim((string) $this->params->get($client . '.width', ''));
		$style = $width !== '' ? 'max-width:' . (int) $width . 'px;' : '';

		ob_start();
		?>
		<div class="jcck-audio-player mb-3" style="<?php echo $style; ?>">
			<audio id="<?php echo $playerId; ?>" preload="metadata" controls style="width:100%">
				<source src="<?php echo htmlspecialchars($tracks[0]['src'], ENT_QUOTES, 'UTF-8'); ?>"
						type="<?php echo $tracks[0]['type']; ?>">
			</audio>
			<?php if ($listbar && count($tracks) > 1): ?>
				<ol class="list-group list-group-numbered mt-2" id="<?php echo $playerId; ?>-list">
					<?php foreach ($tracks as $i => $track): ?>
						<li class="list-group-item list-group-item-action<?php echo $i === 0 ? ' active' : ''; ?>"
							role="button" data-index="<?php echo $i; ?>">
							<?php echo htmlspecialchars($track['title'], ENT_QUOTES, 'UTF-8'); ?>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>
		</div>
		<script>
			(function () {
				var id = <?php echo json_encode($playerId); ?>,
					tracks = <?php echo json_encode($tracks, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>,
					current = 0;

				window.JcckAudio = window.JcckAudio || {};

				function init() {
					var el = document.getElementById(id);
					if (!el || typeof MediaElementPlayer === 'undefined') return;

					var list = document.getElementById(id + '-list');
					var api = window.JcckAudio[id] = {
						player: null,
						media: null,
						tracks: tracks,
						current: function () { return current; },
						load: function (i) {
							if (!tracks[i]) return;
							current = i;
							api.player.setSrc([{src: tracks[i].src, type: tracks[i].type}]);
							api.player.load();
							if (list) {
								list.querySelectorAll('[data-index]').forEach(function (li) {
									li.classList.toggle('active', +li.dataset.index === i);
								});
							}
						},
						play: function (i) {
							if (typeof i === 'number' && i !== current) api.load(i);
							api.player.play();
						},
						stop: function () {
							api.player.pause();
							try { api.player.setCurrentTime(0); } catch (e) {}
						}
					};

					api.player = new MediaElementPlayer(el, {
						features: ['playpause', 'current', 'progress', 'duration', 'volume'],
						audioWidth: '100%',
						stretching: 'responsive',
						success: function (media) {
							api.media = media;
							media.dispatchEvent(new CustomEvent('jcck:ready'));
							document.dispatchEvent(new CustomEvent('jcck:audio-ready', {detail: id}));
							media.addEventListener('ended', function () {
								if (current + 1 < tracks.length) api.play(current + 1);
							});
						}
					});

					if (list) {
						list.addEventListener('click', function (e) {
							var li = e.target.closest('[data-index]');
							if (li) api.play(+li.dataset.index);
						});
					}
				}

				if (document.readyState === 'loading') {
					document.addEventListener('DOMContentLoaded', init);
				} else {
					init();
				}
			})();
		</script>
		<?php

		return ob_get_clean();
	}

	public function onRenderFull($record, $type, $section)
	{
		return $this->_render('full', $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		return $this->_render('list', $record, $type, $section);
	}

	private function _render($client, $record, $type, $section)
	{
		$doc = \Joomla\CMS\Factory::getDocument();
		$doc->addStyleSheet(\Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/css/mediaelement/mediaelementplayer.min.css');
		$doc->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/media/com_joomcck/js/mediaelement/mediaelement-and-player.min.js');

		$browser             = \Joomla\CMS\Environment\Browser::getInstance();
		$this->user          = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$this->descr         = $this->params->get('params.allow_add_descr', 0);
		$this->download_type = '[{type: "download"}]';

		if($this->params->get('params.convert'))
		{
			$this->download_type = '[{type: "html5"}]';
		}

		$this->tracks = $this->getFiles($record, TRUE);
		$this->el     = array();
		$r            = 0;

		if(count($this->tracks) <= 0)
			return;

		return $this->_display_output($client, $record, $type, $section);
	}
}
