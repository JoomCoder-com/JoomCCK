<?php
defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckupload.php';

class JFormFieldCVideo extends CFormFieldUpload
{

    public $link;
    public $embed;
    public $only_one;

	public function __construct($field, $default)
	{
		parent::__construct($field, $default);
		require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helper.php';
	}

	public function getInput()
	{
		$this->link  = @$this->value['link'];
		$this->embed = @$this->value['embed'];
		$this->value = @$this->value['files'];

		if(!$this->link)
		{
			$this->link = array('');
		}
		if(!$this->embed)
		{
			$this->embed = array('');
		}

		$this->only_one = $this->params->get('params.only_one', 0);

		if(in_array($this->params->get('params.upload', 1), $this->user->getAuthorisedViewLevels()))
		{
			$params['max_size']         = ($this->params->get('params.max_size', 2000) * 1024);
			$params['method']           = $this->params->get('params.method', 'auto');
			$params['max_count']        = $this->only_one ? 1 : $this->params->get('params.max_count', 0);
			$params['file_formats']     = $this->params->get('params.file_formats', 'avi, mp4, mpeg, flv, ogv');
			$params['allow_edit_title'] = $this->params->get('params.allow_edit_title', 1);
			$params['allow_add_descr']  = $this->params->get('params.allow_add_descr', 1);

			$this->options   = $params;
			$this->fieldname = '[files]';

			$this->upload = parent::getInput();
		}

		return $this->_display_input();
	}


	public function onCopy($value, $record, $type, $section, $field)
	{
		if(!empty($value))
		{
			foreach($value['files'] as $key => $file)
			{
				$value['files'][$key] = $this->copyFile($file, $field);
			}
		}

		return $value;
	}

	public function onRenderFull($record, $type, $section)
	{
		return $this->onRenderList($record, $type, $section, 'full');
	}

	public function onRenderList($record, $type, $section, $client = 'list')
	{
		ArrayHelper::clean_r($this->value);
		if(!$this->value)
		{
			return;
		}
		if(!empty($this->value['files']))
		{
			JFactory::getDocument()->addScript(JURI::root(TRUE) . '/media/com_joomcck/vendors/jwplayer/jwplayer.js');
		}

		return $this->_display_output($client, $record, $type, $section);
	}

	public function loadVideo($post)
	{
		$title  = $this->params->get('params.allow_edit_title', 0);
		$record = ItemsStore::getRecord($this->request->getInt('record_id'));

		$client = $this->request->get('client');
		$width  = $this->request->getInt('width');
		$key    = $client . $this->id . $record->id;

		$this->link  = @$this->value['link'];
		$this->embed = @$this->value['embed'];
		$this->value = @$this->value['files'];

		//$width -= (4 - ($width % 4));
		//$width -= ($width % 4);
		$height = round(($width / 16) * 9, 0);

		$this->params->set('width', $width);
		$this->params->set('height', $height);

		$out = array();

		if($this->value)
		{
			$videos = $this->getFiles($record);
			ob_start();
			?>
			jwplayer("mediaplayer<?php echo $key ?>").setup({
			"width": "<?php echo $this->params->get('width'); ?>",
			"height": "<?php echo $this->params->get('height'); ?>",
			<?php
			if(count($videos) > 1)
			{
				?>
				"playlist": [
				<?php
				$v        = array();
				$duration = 10;
				foreach($videos as $key_v => $video)
				{
					$v[$key_v] = '{sources:[{ file: "' . $this->getFileUrl($video) . '", label:"' . ($title && $video->title ? $video->title : $video->realname) . '" }],';

					$image = $this->_getVideoThumb($video);

					$v[$key_v] .= 'image: "' . $image . '",';
					$v[$key_v] .= 'duration: "' . $video->duration . '",';
					$v[$key_v] .= 'description: "' . $video->description . '",';
					$v[$key_v] .= 'title:"' . ($title && $video->title ? $video->title : $video->realname) . '"}';
				}
				echo implode(',', $v);
				?>

				]
				<?php if($this->params->get('params.listbar', TRUE)): ?>
				,
				listbar: {
				position: '<?php echo $this->params->get('params.listbar_position', 'right'); ?>',
				size: <?php echo $this->params->get('params.listbar_width', 200); ?>
				}
			<?php endif; ?>
				<?php
			}
			else
			{
				$video = array_pop($videos);
				?>
				"file": "<?php echo $this->getFileUrl($video); ?>",
				"duration": "<?php echo $video->duration; ?>",
				"title": "<?php echo($title && $video->title ? $video->title : $video->realname); ?>",
				"description": "<?php echo $video->description; ?>"
				<?php
			}
			?>
			});


			<?php
			$temp = ob_get_contents();
			ob_end_clean();
			$out['js'] = str_replace(array("\n", "\r", "\t"), '', $temp);
		}

		$blocks = array();
		if(is_array($this->embed))
		{
			foreach($this->embed AS $embed)
				$blocks[] = CVideoAdapterHelper::constrain($embed, $this->params->get('width'));
		}
		if(is_array($this->link))
		{

			foreach($this->link as $link)
			{
				if(in_array(strtolower(pathinfo($link, PATHINFO_EXTENSION)), array('mp4', 'flv', 'webm')))
				{
					$blocks[]  = '<div id="mediaplayer' . md5($link) . '"></div>';
					$out['js'] = <<<JWP
	jwplayer("mediaplayer{$key}").setup({
		"width": "{$this->params->get('width')}",
		"height": "{$this->params->get('height')}",
		"file": "{$link}"
	});
JWP;
				}
				else
				{
					$block = CVideoAdapterHelper::getVideoCode($this->params, $link);
					if($block)
					{
						$blocks[] = $block;
					}
				}
			}
		}

		if($blocks)
		{
			$out['html'] = '<div class="video-block">' . implode('</div><div class="video-block">', $blocks) . '</div>';
		}

		return $out;
	}

	private function constrain($html)
	{
		preg_match("/width=\"([0-9]{1,3})/iU", $html, $match);
		$width = $match[1];
		preg_match("/height=\"([0-9]{1,3})/iU", $html, $match);
		$height = $match[1];

		echo $height, $width;
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		if(isset($value['files']))
		{
			$value['files'] = $this->_getPrepared($value['files']);
			$params         = JComponentHelper::getParams('com_joomcck');

			foreach($value['files'] as $val)
			{
				$val['duration'] = 0;
				ob_start();
				passthru($this->params->get('params.command', 'ffmpeg') . " -i \"" . JPATH_ROOT . DIRECTORY_SEPARATOR . $params->get('general_upload') . DIRECTORY_SEPARATOR . $this->params->get('params.subfolder') . DIRECTORY_SEPARATOR . $val->fullpath . "\" 2>&1");
				$duration = ob_get_contents();
				ob_end_clean();
				preg_match('/Duration: (.*?),/', $duration, $matches);
				if(isset($matches[1]))
				{
					$duration       = $matches[1];
					$duration_array = explode(':', $duration);
					$duration       = $duration_array[0] * 3600 + $duration_array[1] * 60 + $duration_array[2];
					$val['duration']  = $duration;
				}
			}
		}
		if(isset($value['link']))
		{
			ArrayHelper::clean_r($value['link']);
		}
		if(isset($value['embed']))
		{
			foreach($value['embed'] AS &$embed)
			{
				$embed = CVideoAdapterHelper::getVideoEmbed($embed);

			}
			ArrayHelper::clean_r($value['embed']);
		}

		return $value;
	}

	public function validateField($value, $record, $type, $section)
	{
		$total = 0;
		if(isset($value['files']))
		{
			$total += count($value['files']);
		}
		if(isset($value['link']))
		{
			ArrayHelper::clean_r($value['link']);
			$total += count($value['link']);

			if($this->params->get('params.link_max_count', 0) && (count($value['link']) > $this->params->get('params.link_max_count', 0)))
			{
				$this->setError(JText::_('CERRORMAXLINKS'));
			}
		}
		if(isset($value['embed']))
		{
			ArrayHelper::clean_r($value['embed']);
			$total += count($value['embed']);

			if($this->params->get('params.embed_max_count', 0) && (count($value['embed']) > $this->params->get('params.embed_max_count', 0)))
			{
				$this->setError(JText::_('CERRORMAXEMBEDS'));
			}
		}

		if($this->params->get('params.only_one', 0) && $total > 1)
		{
			$this->setError(JText::_('CERRORMORETHANONE'));
		}
	}

	public function onStoreValues($validData, $record)
	{
		$value = @$this->value['files'];
		settype($value, 'array');
		$out = $saved = array();
		foreach($value as $file)
		{
			$out[]   = $file['realname'];
			$saved[] = $file['filename'];
		}

		$files = JTable::getInstance('Files', 'JoomcckTable');
		$files->markSaved($saved, $validData, $this->id);

		return $out;
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		return parent::onPrepareFullTextSearch(@$value['files'], $record, $type, $section);
	}

	public function _getVideoThumb($file)
	{
		$params = JComponentHelper::getParams('com_joomcck');
		$root   = JPath::clean($params->get('general_upload'));
		$url    = str_replace(JPATH_ROOT, '', $root);
		$url    = str_replace("\\", '/', $url);
		$url    = preg_replace('#^\/#iU', '', $url);
		$url    = JURI::root(TRUE) . '/' . str_replace("//", "/", $url);

		$parts = explode('_', $file->filename);
		$date  = date($params->get('folder_format', 'Y-m'), (int)$parts[0]);

		$thumb_path = JPATH_ROOT . DIRECTORY_SEPARATOR . $params->get('general_upload') . DIRECTORY_SEPARATOR . 'thumbs_cache' . DIRECTORY_SEPARATOR . $this->params->get('params.subfolder') . DIRECTORY_SEPARATOR . $date;
		if(JFile::exists($thumb_path . DIRECTORY_SEPARATOR . $file->filename . '.jpg'))
		{
			return $url . '/thumbs_cache/' . $this->params->get('params.subfolder') . '/' . $date . '/' . $file->filename . '.jpg';
		}

		if(!JFolder::exists($thumb_path))
		{
			JFolder::create($thumb_path);
		}

		passthru($this->params->get('params.command', 'ffmpeg') . " -i \"" . JPATH_ROOT . DIRECTORY_SEPARATOR . $params->get('general_upload') . DIRECTORY_SEPARATOR . $this->params->get('params.subfolder') . DIRECTORY_SEPARATOR . $file->fullpath . "\" -ss  00:00:03 -s qcif  " . $thumb_path . DIRECTORY_SEPARATOR . $file->filename . '.jpg');

		if(JFile::exists($thumb_path . DIRECTORY_SEPARATOR . $file->filename . '.jpg'))
		{
			return $url . '/thumbs_cache/' . $this->params->get('params.subfolder') . '/' . $date . '/' . $file->filename . '.jpg';
		}

		return FALSE;
	}

}
