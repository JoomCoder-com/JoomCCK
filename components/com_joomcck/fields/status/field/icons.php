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
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class JFormFieldIcons extends \Joomla\CMS\Form\FormField
{
	public $type = 'Icons';

	public function getInput()
	{

		$path = $this->getAttribute('directory','components/com_joomcck/fields/status/icons');

		if (!\Joomla\CMS\Filesystem\Folder::exists($path))
		{
			$path = JPATH_ROOT . '/' . $path;
		}
		$atr['onclick'] = "mrSetIcon{$this->fieldname}('')";
		$html = '<input type="hidden" name="' . $this->name . '" id="icon_param' . $this->fieldname . '" value="' . $this->value . '">';
		$html .= '<img id="icon_img' . $this->fieldname . '" align="absmiddle" src="' . ($this->value ? \Joomla\CMS\Uri\Uri::root(TRUE).'/'.$path.'/'.$this->value : '') . '"> <span id="icon_name' . $this->fieldname . '" class="icon_name">' . $this->value . '</span>';
		$html .= ' ' . \Joomla\CMS\HTML\HTMLHelper::link('javascript:void(0)', 'Delete curent icon', $atr);
		$html .= '<div style="height:60px;max-width:330px;overflow-x:hidden;overflow-y:scroll">';
		$html .= "<script type=\"text/javascript\">function mrSetIcon{$this->fieldname}(file){document.getElementById('icon_img" . $this->fieldname . "').src = (file != '') ? '" . \Joomla\CMS\Uri\Uri::root(TRUE) . '/' . $path."/' + file : '';	document.getElementById('icon_name" . $this->fieldname . "').innerHTML = file;	document.getElementById('icon_param" . $this->fieldname . "').value = file;}</script>";
		
		$atr = array('border' => 0, 'align' => 'absmiddle', 'style' => 'float:left;padding:2px;margin:0;');
		echo "<style>.jsicon {margin:2px;}.icon_name{line-height:26px;}</style>";
		if (Folder::exists($path))
		{
			if ($dh = opendir($path))
			{
				while ( ($file = readdir($dh)) !== false )
				{
					$ext = strtolower(substr($file, strrpos($file, '.') + 1));
					if ($ext == 'png' || $ext == 'gif')
					{
						$atr['onclick'] = "mrSetIcon{$this->fieldname}('{$file}')";
						$html .= ' ' . \Joomla\CMS\HTML\HTMLHelper::image( $path.'/'.$file, \Joomla\CMS\Language\Text::_('Click to insert'), $atr);
					}				
				}
				closedir($dh);
			}
		}
		
		$html .= '</div>';
		return $html;
	}
}
?>