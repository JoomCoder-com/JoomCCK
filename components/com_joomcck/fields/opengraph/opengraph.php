<?php
/**
 * Open Graph Field for JoomCCK CCK
 * Author Website: http://www.joomboost.com/
 * @copyright Copyright (C) 2012 TADAMONET Web Solutions (http://www.joomboost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\Helpers\StringHelper;

defined('_JEXEC') or die();
require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_joomcck/library/php/fields/joomcckfield.php';

class JFormFieldCOpengraph extends CFormField
{

	public $pictureFieldId = 0;
	public $descriptionFieldId = 0;

	/*
	 * Doesn't need it
	 */
	public function getInput()
	{
		return;
	}

	public function onRenderFull($record, $type, $section)
	{
		// set fields ids
		$this->pictureFieldId = $this->params->get('params.pic_id', 0);
		$this->descriptionFieldId = $this->params->get('params.desc_id', 0);

		// Get description truncation settings
		$desc_length = (int)$this->params->get('params.desc_length', 200);
		$no_split = (bool)$this->params->get('params.no_split', 1);

		// get general upload directory
		$fupload = ComponentHelper::getParams('com_joomcck')->get('general_upload');

		// get default pictures
		$default_pic = $this->params->get('params.default_pic', '');
		$default_pic = !empty($default_pic) ? \Joomla\CMS\HTML\HTMLHelper::cleanImageURL($default_pic)->url : '';

		// get og type
		$og_type = $this->params->get('params.og_type');

		// Check for Facebook App ID
		$app_id = $this->params->get('params.fb_app_id', '');

		$site_name = $this->sitename();
		$item_title = $record->title;
		$item_link = Route::_($record->href);
		$out = '';
		$purl = '';

		// Prepare field IDs
		$ids = [
			'pic' => $this->pictureFieldId,
			'desc' => $this->descriptionFieldId
		];

		// get gallery field path
		if(!empty($ids['pic'])){
			$fparams = $this->_getFieldParamsFromDB($ids['pic']);
			$fparams = json_decode($fparams, true);
			$fparams = $fparams['params'];
			$subf = $fparams['subfolder']; //subfolder value

			if(!isset($fparams['params']['select_type'])){ // is gallery field

				$purl = \Joomla\CMS\Uri\Uri::root().$fupload.'/'.$subf.'/';
			}
		}

		// get image url
		if(!empty($ids['pic'])){

			$pfiles = $this->_getFieldInfo($ids['pic'], $record, true);

			if(!empty($pfiles)){

				if(is_array($pfiles)){ // if gallery field

					foreach ($pfiles as $ppfile){
						$cleanImage = HtmlHelper::cleanImageURL($ppfile);
						$item_pic_url[] = $purl.$cleanImage->url;
					}

				}else{ // if simple image field

					$cleanImage = HtmlHelper::cleanImageURL($pfiles);
					$item_pic_url = \Joomla\CMS\Uri\Uri::root().$cleanImage->url;
				}

			}else{
				if(!empty($default_pic)){ // default image

					$cleanImage = HtmlHelper::cleanImageURL($default_pic);

					$item_pic_url = \Joomla\CMS\Uri\Uri::root().$cleanImage->url;
				}else
					$item_pic_url = '';
			}

		}
		elseif(!empty($default_pic)){ // default image

			$cleanImage = HtmlHelper::cleanImageURL($default_pic);
			$item_pic_url = \Joomla\CMS\Uri\Uri::root().$cleanImage->url;
		}
		else{ // nothing found

			$item_pic_url = '';
		}

		// get description
		if(!empty($ids['desc'])) { // field content
			$item_desc = $this->_getFieldInfo($ids['desc'], $record, false, $desc_length, $no_split);
		}
		elseif(!empty($record->meta_descr)) { // record meta desc
			$item_desc = $this->truncateDescription($record->meta_descr, $desc_length, $no_split);
		}
		else { // website global desc
			$item_desc = $this->truncateDescription($this->sitedesc(), $desc_length, $no_split);
		}

		// build open graph tags
		$out .= '<meta property="og:title" content="'.$this->cleanString($item_title).'">';
		$out .= '<meta property="og:site_name" content="'.$this->cleanString($site_name).'">';
		$out .= '<meta property="og:url" content="'.$item_link.'">';

		if(!empty($item_desc))
			$out .= '<meta property="og:description" content="'.$item_desc.'">';

		if(!empty($item_pic_url)){
			if(is_array($item_pic_url)){
				foreach ($item_pic_url as $ipu){ // for gallery field
					$out .= '<meta property="og:image" content="'.$ipu.'" />';
				}
			}
			else // for simple image field
				$out .= '<meta property="og:image" content="'.$item_pic_url.'" />';
		}

		if(!empty($app_id))
			$out .= '<meta property="fb:app_id" content="'.$app_id.'">';

		$out .= '<meta property="og:type" content="'.$og_type.'">';

		// add open graph tags in head
		Factory::getDocument()->addCustomtag($out);
	}

	/**
	 * Truncate description and add ellipsis if needed
	 *
	 * @param string $text The description text to truncate
	 * @param int $length Maximum length of description
	 * @param bool $noSplit Whether to avoid splitting words
	 * @return string Truncated description
	 */
	private function truncateDescription($text, $length = 200, $noSplit = true)
	{
		// clear first
		$text = $this->cleanString($text);

		// return truncated
		return \Joomla\CMS\HTML\Helpers\StringHelper::truncate($text, $length, $noSplit, false);
	}

	/**
	 * Clean string for Open Graph meta tags
	 * Removes extra whitespace and special characters
	 *
	 * @param string $string Text to clean
	 * @return string Cleaned string
	 */
	private function cleanString($string)
	{

		// Strip BBCode first
		$string = $this->_stripBBCode($string);

		// Strip tags
		$string = strip_tags($string);

		// Remove line breaks and extra spaces
		$string = preg_replace('/\s+/', ' ', $string);

		// Escape quotes
		$string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');

		return trim($string);
	}

	public function onRenderList($record, $type, $section)
	{
		return;
	}

	public function onImport($value, $params, $record = null)
	{
		return;
	}

	public function onImportForm($heads, $defaults)
	{
		return $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id));
	}

	private function _getFieldInfo($id, $record, $isPIC = false, $desc_length = 200, $noSplit = true)
	{
		$fields = json_decode($record->fields, true);

		if($isPIC)
		{
			if(isset($fields[$id]['image']))
				$pic = $fields[$id]['image'];

			elseif(isset($fields[$id]) && is_array($fields[$id])){
				$pic = array();
				foreach ($fields[$id] as $v){
					if(isset($v['fullpath'])) {
						$pic[] = $v['fullpath'];
					}
				}
			} else {
				$pic = '';
			}

			return $pic;
		}
		else
		{
			if(!isset($fields[$id])) {
				return '';
			}

			// Use Joomla's StringHelper for truncation
			return $this->truncateDescription($fields[$id], $desc_length, $noSplit);
		}
	}

	private function sitename()
	{
		return Factory::getApplication()->getCfg('sitename');
	}

	private function sitedesc()
	{
		return Factory::getDocument()->getMetaData('description');
	}

	// strip bbcode in desc field
	private function _stripBBCode($desc)
	{
		$pattern = '|[[\/\!]*?[^\[\]]*?]|si';
		$replace = '';
		return preg_replace($pattern, $replace, $desc);
	}

	// return field params of given id
	private function _getFieldParamsFromDB($id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('params');
		$query->from($db->quoteName('#__js_res_fields'));
		$query->where($db->quoteName('id')." = ".$db->quote($id));

		$db->setQuery($query);
		$params = $db->loadResult();

		return $params;
	}

	// return field id of given field key
	private function _getFieldIdFromDB($key)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from($db->quoteName('#__js_res_fields'));
		$query->where($db->quoteName('key')." = ".$db->quote($key));

		$db->setQuery($query);
		$id = $db->loadResult();

		return $id;
	}
}