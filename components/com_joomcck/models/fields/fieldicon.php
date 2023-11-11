<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport( 'joomla.filesystem.folder' );
jimport( 'joomla.filesystem.file'   );

class JFormFieldFieldicon extends JFormField
{
	public $type = 'Fieldicon';
	
	public function getInput()
	{
		$dir = JPATH_ROOT. '/media/com_joomcck/icons/16';
		$path = 'media/com_joomcck/icons/16/';
		if($this->element['directory'])
		{
			$dir = JPATH_ROOT. DIRECTORY_SEPARATOR .$this->element['directory'];
			$path = $this->element['directory'].'/';
		}

		$html = '<div class="border p-3 rounded">';
		$html  .= '<input type="hidden" name="'.$this->name.'" id="icon_param'.$this->fieldname.'" value="'.$this->value.'">';
		$html .= '<div class="input-group mb-3">
            <span class="input-group-text"><i class="fas fa-filter"></i> '.\Joomla\CMS\Language\Text::_('LTFILTERS').'</span>
              <input id="icon_param_search" class="form-control form-control-sm" type="text" value="" >
            </div>';
		$html .= '<script type="text/javascript">
         (function($){$(document).ready(function() {
           $("#icon_param_search").on("input", function (){
             var imgSearch = $(this).val();
             var imgBox = $(this).parent().next().next().next().next().next().next().next();
             imgBox.children().each(function( index ) {
               var ico_name = String($(this).attr("src")).split("/").pop();
               if(ico_name.indexOf(imgSearch) >= 0){
                 $(this).show();
               }else{
                 $(this).hide();
               }
             });
           });
         });})(jQuery);
		</script>';
		$html .= '<img id="icon_img'.$this->fieldname.'" align="absmiddle" src="'.JURI::root().
			($this->value ? $path.$this->value : 'media/com_joomcck/blank.png').'"> <span id="icon_name'.$this->fieldname.'" class="icon_name">'.$this->value.'</span>';
		$html .= ' <a class="btn btn-sm border bg-light" href="javascript:void(0)" title="Delete curent icon" onclick="mrSetIcon'.$this->fieldname.'(\'\')"><i class="fas fa-times"></i></a>';
		$html .= '<div class="mt-3" style="clear:both"></div><div style="height:58px;width:100%;overflow-x:hidden;overflow-y:scroll">';
		$html .= "<script type=\"text/javascript\">function mrSetIcon{$this->fieldname}(file){document.getElementById('icon_img".$this->fieldname."').src = '".JURI::root().$path."' + file;	document.getElementById('icon_name".$this->fieldname."').innerHTML = file;	document.getElementById('icon_param".$this->fieldname."').value = file;}</script>";
		
		$atr = array(
			'border' 	=> 0,
			'align'		=> 'absmiddle',
			'style'   	=> 'float:left;padding:2px;margin:0;'
		);
		echo "<style>.jsicon {margin:2px;}.icon_name{line-height:26px;}</style>";
		if (is_dir($dir)) {
		    if ($dh = opendir($dir)) {
		        while (($file = readdir($dh)) !== false) {
		            $ext = strtolower(substr($file, strrpos($file, '.')+1));
		        	if($ext == 'png' || $ext == 'gif')
		        	{
		        		$atr['onclick'] = "mrSetIcon{$this->fieldname}('$file')";
		        		$html .= ' '.\Joomla\CMS\HTML\HTMLHelper::image(JURI::root().$path.$file, \Joomla\CMS\Language\Text::_('CICONCLICKINSERT'), $atr);
		        	}
		            
		        }
		        closedir($dh);
		    }
		}
		
		$html .= '</div>';
		$html .= '</div>';
		return $html;
	}
}