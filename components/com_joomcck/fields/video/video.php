<?php
defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckupload.php';

class JFormFieldCVideo extends CFormFieldUpload
{
	public $link;
	public $embed;
	public $videos;
	public $only_one;

	// Flag to track if system requirements are met
	protected $system_ready = true;

	public function __construct($field, $default)
	{
		parent::__construct($field, $default);
		require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helper.php';

		// register layouts folder
		\Joomcck\Layout\Helpers\Layout::$defaultBasePath = JPATH_ROOT.'/components/com_joomcck/fields/video/layouts';

		// Check system requirements of ffmpeg and other things
		$this->checkSystemRequirements();
	}

	/**
	 * Check if system meets requirements for video processing
	 * Verifies exec is available and ffmpeg is installed
	 */
	protected function checkSystemRequirements()
	{

		$ffmpeg_enabled = (int) $this->params->get('params.enable_ffmpeg', 0);

		// Check if ffmpeg processing is enabled in parameters
		if (!$ffmpeg_enabled) {
			$this->system_ready = false;
			return false;
		}

		// Check if exec function is available
		if (!function_exists('exec') || in_array('exec', array_map('trim', explode(',', ini_get('disable_functions'))))) {
			$this->system_ready = false;
			\Joomla\CMS\Factory::getApplication()->enqueueMessage('Video field requires exec function to be enabled', 'warning');
			return false;
		}

		// Check if ffmpeg is installed by running version command
		$ffmpeg_command = $this->params->get('params.command', 'ffmpeg');
		$return_var = null;
		$output = array();

		// Check ffmpeg by running the version command
		exec(escapeshellcmd($ffmpeg_command) . " -version 2>&1", $output, $return_var);

		if ($return_var !== 0) {
			$this->system_ready = false;
			\Joomla\CMS\Factory::getApplication()->enqueueMessage('Video field requires FFmpeg to be installed and accessible', 'warning');
			return false;
		}

		return true;
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
		$width = (int) $this->params->get('params.default_width', 640);
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

		// no need to continue if no ffmpeg or exec disabled ...
		if (!$this->system_ready) {
			return 0;
		}

		$duration = 0;
		$output = array();
		$return_var = null;

		$file_path = JPATH_ROOT . DIRECTORY_SEPARATOR .
			$params->get('general_upload') . DIRECTORY_SEPARATOR .
			$this->params->get('params.subfolder') . DIRECTORY_SEPARATOR . $val->fullpath;

		// Secure the command to prevent command injection
		$ffmpeg_command = escapeshellcmd($this->params->get('params.command', 'ffmpeg'));
		$file_path = escapeshellarg($file_path);

		// Execute the command
		exec("$ffmpeg_command -i $file_path 2>&1", $output, $return_var);

		// Join output and search for duration
		$output_str = implode("\n", $output);

		preg_match('/Duration: (.*?),/', $output_str, $matches);
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


		// No need to continue if no ffmpeg or exec disabled ...
		if (!$this->system_ready) {
			return false;
		}

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

		// Get thumbnail settings
		$thumbnail_quality = $this->params->get('params.thumbnail_quality', 'medium');
		$thumbnail_width = intval($this->params->get('params.thumbnail_width', 320));

		// Set quality parameter based on selected quality
		$quality_value = 2; // Default medium quality
		switch ($thumbnail_quality) {
			case 'low':
				$quality_value = 5;
				break;
			case 'medium':
				$quality_value = 2;
				break;
			case 'high':
				$quality_value = 1;
				break;
		}

		// Secure the command to prevent command injection
		$ffmpeg_command = escapeshellcmd($this->params->get('params.command', 'ffmpeg'));
		$input_file = escapeshellarg(JPATH_ROOT . DIRECTORY_SEPARATOR .
			$params->get('general_upload') . DIRECTORY_SEPARATOR .
			$this->params->get('params.subfolder') . DIRECTORY_SEPARATOR . $file->fullpath);
		$output_file = escapeshellarg($thumb_path . DIRECTORY_SEPARATOR . $file->filename . '.jpg');

		// Generate a higher quality thumbnail
		// -ss: seek to position (3 seconds into video)
		// -frames:v 1: extract just 1 video frame
		// -q:v: quality (lower number = higher quality, range 1-31)
		// -vf scale: resize to specified width and maintain aspect ratio with height=-1
		$command = "$ffmpeg_command -i $input_file -ss 00:00:03 -frames:v 1 -q:v $quality_value -vf scale=$thumbnail_width:-1 $output_file 2>&1";

		// Execute the command
		$output = array();
		$return_var = null;
		exec($command, $output, $return_var);

		if(is_file($thumb_path . DIRECTORY_SEPARATOR . $file->filename . '.jpg')) {
			return $url . '/thumbs_cache/' . $this->params->get('params.subfolder') . '/' . $date . '/' . $file->filename . '.jpg';
		}

		return FALSE;
	}
}