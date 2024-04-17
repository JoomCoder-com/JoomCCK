<?php
/**
 * Open Graph Field for JoomCCK CCK
 * Author Website: http://www.joomboost.com/
 * @copyright Copyright (C) 2012 TADAMONET Web Solutions (http://www.joomboost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

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
		$this->pictureFieldId = $this->params->get('params.pic_id',0);
		$this->descriptionFieldId = $this->params->get('params.desc_id',0);

		// get general upload directory
		$fupload = ComponentHelper::getParams('com_joomcck')->get('general_upload');

		// get default pictures
		$default_pic = $this->params->get('params.default_pic','');
		$default_pic = !empty($default_pic) ? \Joomla\CMS\HTML\HTMLHelper::cleanImageURL($default_pic)->url : '';


		// get og type
		$og_type = $this->params->get('params.og_type');

		$site_name = $this->sitename();
		$item_title = $record->title;
		$item_link 	= Route::_($record->href);
		$out = '';
		$purl = '';


		
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
						$item_pic_url[] = $purl.$ppfile;
					}				
				
				}else{ // if simple image field
					$item_pic_url = \Joomla\CMS\Uri\Uri::root().$pfiles;
				}
				
			}else{
				if(!empty($default_pic)){ // default image						
					$item_pic_url = \Joomla\CMS\Uri\Uri::root().$default_pic;
				}else
					$item_pic_url = '';
			}			
			
			
		}	
		elseif(!empty($default_pic)){ // default image
			
			$item_pic_url = \Joomla\CMS\Uri\Uri::root().$default_pic;
		}	
		else{ // nothing found
			
			$item_pic_url = '';
		}			
		
		// get description
		if(!empty($ids['desc'])) // field content
			$item_desc = $this->_getFieldInfo($ids['desc'], $record);
		elseif(!empty($record->meta_descr)) // record meta desc
			$item_desc = $record->meta_descr;
		else // website global desc
			$item_desc = $this->sitedesc();
		
		// build open graph tags
		$out .= '<meta property="og:title" content="'.$item_title.'">';
		$out .= '<meta property="og:site_name" content="'.$site_name.'">';
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

	public function setGalleryFieldPath(){
		
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

	private function _getFieldInfo($id, $record, $isPIC = null){
		$fields =json_decode($record->fields, true);
		if($isPIC)
		{
			if(isset($fields[$id]['image']))
				$pic = $fields[$id]['image'];
				
			elseif($fields[$id]){
				$pic = array();
				foreach ($fields[$id] as $v){
					
					$pic[] = $v['fullpath'];
				}
				
			}
			
			return $pic;
		}
		else 
		{
			$desc = substr(strip_tags($fields[$id]), 0, 300);
			// strip bbcodes
			$desc = $this->_stripBBCode($desc);
			return $desc;
		}
	}
	
	private function sitename(){
		return Factory::getApplication()->getCfg('sitename');
	}
	
	private function sitedesc(){
		return Factory::getDocument()->getMetaData('description');
	}

	
	// strip bbcode in desc field
	private function _stripBBCode($desc) {
    	$pattern = '|[[\/\!]*?[^\[\]]*?]|si';
    	$replace = '';
    	return preg_replace($pattern, $replace, $desc);
    }	
	
	// return field params of given id
	private function _getFieldParamsFromDB($id){		
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
	private function _getFieldIdFromDB($key){			
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