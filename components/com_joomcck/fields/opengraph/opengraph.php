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
	public function getInput()
	{
		return;
	}	

	public function onRenderFull($record, $type, $section)
	{		

		//init vars
		$fupload = ComponentHelper::getParams('com_joomcck')->get('general_upload');
		$default_pic = $this->params->get('params.default_pic');				
		$ids = $this->_getFieldId();		
		$og_type = $this->params->get('params.og_type');
		$app_id = $this->params->get('params.app_id');
		$site_name = $this->sitename();
		$item_title = $record->title;
		$item_link 	= Route::_($record->href);
		$out = '';
		
		// get gallery field path
		if(!empty($ids['pic'])){
			$fparams = $this->_getFieldParamsFromDB($ids['pic']);
			$fparams = json_decode($fparams, true);
			$fparams = $fparams['params'];
			$subf = $fparams['subfolder']; //subfolder value
			
			if(!isset($fparams['params']['select_type'])){ // is gallery field				
								
				$purl = $this->routeurl().$fupload.'/'.$subf.'/';				
			}
			
		}else
			$purl = '';
		
		
		// get image url
		if(!empty($ids['pic'])){ 
			
			$pfiles = $this->_getFieldInfo($ids['pic'], $record, true);		
			
			
			if(!empty($pfiles)){
				
				if(is_array($pfiles)){ // if gallery field					
					
					foreach ($pfiles as $ppfile){
						$item_pic_url[] = $purl.$ppfile;
					}				
				
				}else{ // if simple image field
					$item_pic_url = $this->routeurl().$pfiles;
				}
				
			}else{
				if(!empty($default_pic)){ // default image						
					$item_pic_url = $this->routeurl().$default_pic;
				}else
					$item_pic_url = '';
			}			
			
			
		}	
		elseif(!empty($default_pic)){ // default image
			
			$item_pic_url = $this->routeurl().$default_pic;
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
	
	private function routeurl(){
		return Factory::getURI()->base();
	}
	
	// return ids
	private function _getFieldId(){
	
		$id = array();
			
		if($this->params->get('params.field_type'))
				
			$id = array('pic' => $this->params->get('params.pic_id'), 'desc' => $this->params->get('params.desc_id'));
	
		else
				
			$id = array('pic' => $this->_getFieldIdFromDB($this->params->get('params.pic_key')), 'desc' => $this->_getFieldIdFromDB($this->params->get('params.desc_key')));
			
		return $id ;
	
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