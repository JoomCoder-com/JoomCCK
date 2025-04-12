<?php
defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckupload.php';

class JFormFieldCVideo extends CFormFieldUpload
{
	public $link;
	public $embed;
	public $videos;
	public $only_one;

	public function __construct($field, $default)
	{
		parent::__construct($field, $default);
		require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helper.php';

		// register layouts folder
		\Joomcck\Layout\Helpers\Layout::$defaultBasePath = JPATH_ROOT.'/components/com_joomcck/fields/video/layouts';

	}

	public function getInput()
	{
		$this->link  = @$this->value['link'];
		$this->embed = @$this->value['embed'];
		$this->value = @$this->value['files'];

		if(!$this->link) {
			$this->link = array('');
		}
		if(!$this->embed) {
			$this->embed = array('');
		}

		$this->only_one = $this->params->get('params.only_one', 0);

		if(in_array($this->params->get('params.upload', 1), $this->user->getAuthorisedViewLevels())) {
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
		if(!empty($value)) {
			foreach($value['files'] as $key => $file) {
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
		if(!$this->value) {
			return;
		}


		$this->prepareVideoData($record, $client);

		return $this->_display_output($client, $record, $type, $section);
	}

	protected function prepareVideoData($record, $client)
	{
		// Set width and height
		$width = $this->params->get('params.default_width', 640);
		$height = round(($width / 16) * 9, 0);

		$this->params->set('width', $width);
		$this->params->set('height', $height);

		// Prepare videos and their URLs
		$this->videos = [];

		if (!empty($this->value)) {

			$files = $this->getFiles($record);

			foreach ($files as $key => $file) {
				$this->videos[$key] = $file;
				$this->videos[$key]->url = $this->getFileUrl($file);
				$this->videos[$key]->thumbnail = $this->_getVideoThumb($file);
				$this->videos[$key]->display_title = ($this->params->get('params.allow_edit_title', 1) &&
					isset($file->title) &&
					$file->title) ? $file->title : $file->realname;
			}
		}

	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		if(isset($value['files'])) {
			$value['files'] = $this->_getPrepared($value['files']);
			$params = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');

			foreach($value['files'] as $val) {
				$val['duration'] = $this->getVideoDuration($params, $val);
			}
		}

		if(isset($value['link'])) {
			ArrayHelper::clean_r($value['link']);
		}

		if(isset($value['embed'])) {
			foreach($value['embed'] as &$embed) {
				$embed = CVideoAdapterHelper::getVideoEmbed($embed);
			}
			ArrayHelper::clean_r($value['embed']);
		}

		return $value;
	}

	/**
	 * Get video duration using ffmpeg
	 *
	 * @param object $params Component parameters
	 * @param object $val Video file information
	 * @return int Duration in seconds
	 */
	protected function getVideoDuration($params, $val)
	{
		$val['duration'] = 0;
		ob_start();
		passthru($this->params->get('params.command', 'ffmpeg') . " -i \"" . JPATH_ROOT . DIRECTORY_SEPARATOR .
			$params->get('general_upload') . DIRECTORY_SEPARATOR .
			$this->params->get('params.subfolder') . DIRECTORY_SEPARATOR . $val->fullpath . "\" 2>&1");
		$duration = ob_get_contents();
		ob_end_clean();

		preg_match('/Duration: (.*?),/', $duration, $matches);
		if(isset($matches[1])) {
			$duration = $matches[1];
			$duration_array = explode(':', $duration);
			return $duration_array[0] * 3600 + $duration_array[1] * 60 + $duration_array[2];
		}

		return 0;
	}

	public function validateField($value, $record, $type, $section)
	{
		$total = 0;
		if(isset($value['files'])) {
			$total += count($value['files']);
		}

		if(isset($value['link'])) {
			ArrayHelper::clean_r($value['link']);
			$total += count($value['link']);

			if($this->params->get('params.link_max_count', 0) && (count($value['link']) > $this->params->get('params.link_max_count', 0))) {
				$this->setError(\Joomla\CMS\Language\Text::_('CERRORMAXLINKS'));
			}
		}

		if(isset($value['embed'])) {
			ArrayHelper::clean_r($value['embed']);
			$total += count($value['embed']);

			if($this->params->get('params.embed_max_count', 0) && (count($value['embed']) > $this->params->get('params.embed_max_count', 0))) {
				$this->setError(\Joomla\CMS\Language\Text::_('CERRORMAXEMBEDS'));
			}
		}

		if($this->params->get('params.only_one', 0) && $total > 1) {
			$this->setError(\Joomla\CMS\Language\Text::_('CERRORMORETHANONE'));
		}
	}

	public function onStoreValues($validData, $record)
	{
		$value = @$this->value['files'];
		settype($value, 'array');
		$out = $saved = array();

		foreach($value as $file) {
			$out[]   = $file['realname'];
			$saved[] = $file['filename'];
		}

		$files = \Joomla\CMS\Table\Table::getInstance('Files', 'JoomcckTable');
		$files->markSaved($saved, $validData, $this->id);

		return $out;
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		return parent::onPrepareFullTextSearch(@$value['files'], $record, $type, $section);
	}

	/**
	 * Public wrapper to access files
	 *
	 * @param object $record The record object
	 * @return array Array of files
	 */
	public function getVideoFiles($record)
	{
		return $this->getFiles($record);
	}

	/**
	 * Public wrapper to access file URL
	 *
	 * @param object $file The file object
	 * @return string URL to the file
	 */
	public function getVideoFileUrl($file)
	{
		return $this->getFileUrl($file);
	}

	public function _getVideoThumb($file)
	{
		$params = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');
		$root   = \Joomla\Filesystem\Path::clean($params->get('general_upload'));
		$url    = str_replace(JPATH_ROOT, '', $root);
		$url    = str_replace("\\", '/', $url);
		$url    = preg_replace('#^\/#iU', '', $url);
		$url    = \Joomla\CMS\Uri\Uri::root(TRUE) . '/' . str_replace("//", "/", $url);

		$parts = explode('_', $file->filename);
		$date  = date($params->get('folder_format', 'Y-m'), (int)$parts[0]);

		$thumb_path = JPATH_ROOT . DIRECTORY_SEPARATOR . $params->get('general_upload') . DIRECTORY_SEPARATOR .
			'thumbs_cache' . DIRECTORY_SEPARATOR . $this->params->get('params.subfolder') . DIRECTORY_SEPARATOR . $date;

		if(is_file($thumb_path . DIRECTORY_SEPARATOR . $file->filename . '.jpg')) {
			return $url . '/thumbs_cache/' . $this->params->get('params.subfolder') . '/' . $date . '/' . $file->filename . '.jpg';
		}

		if(!is_dir($thumb_path)) {
			\Joomla\Filesystem\Folder::create($thumb_path);
		}

		passthru($this->params->get('params.command', 'ffmpeg') . " -i \"" . JPATH_ROOT . DIRECTORY_SEPARATOR .
			$params->get('general_upload') . DIRECTORY_SEPARATOR .
			$this->params->get('params.subfolder') . DIRECTORY_SEPARATOR . $file->fullpath .
			"\" -ss  00:00:03 -s qcif  " . $thumb_path . DIRECTORY_SEPARATOR . $file->filename . '.jpg');

		if(is_file($thumb_path . DIRECTORY_SEPARATOR . $file->filename . '.jpg')) {
			return $url . '/thumbs_cache/' . $this->params->get('params.subfolder') . '/' . $date . '/' . $file->filename . '.jpg';
		}

		return FALSE;
	}
}