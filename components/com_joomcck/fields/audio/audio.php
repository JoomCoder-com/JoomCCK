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

/**
 *
 * jwplayer("container").setup({
 * file: 'http://sute.com/sreamfile.php?id=12',
 * provider: 'sound'
 * });
 *
 */
class JFormFieldCAudio extends CFormFieldUpload
{


	public $tracks;
	public $el;



	public function getInput()
	{
		$js = "function afterupload(w, s){
			$$('#'+s.id+' div.result').set('html', '" . addslashes(\Joomla\CMS\Language\Text::_('P_CONVERTING')) . "').setStyle('background', 'url(\"" . JURI::root(TRUE) . "/media/com_joomcck/js/mooupload/imgs/load_bg_green.gif\")');
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
		$ext    = \Joomla\Filesystem\File::getExt($name);
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

		$_ext    = \Joomla\Filesystem\File::getExt($src);
		$command = sprintf('%s -i "%s" %s "%s"',
			$this->params->get('params.command', 'ffmpeg'), $src, $codec, str_replace('.' . $_ext, '.' . $ext, $src));

		$p = proc_open($command, $descriptorspec, $pipes,\Joomla\Filesystem\Path::clean(JPATH_ROOT . DIRECTORY_SEPARATOR . \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck')->get('general_upload')));
		proc_close($p);
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
		\Joomla\CMS\Factory::getDocument()->addScript(JURI::root(TRUE) . '/media/com_joomcck/vendors/jwplayer/jwplayer.js');

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
