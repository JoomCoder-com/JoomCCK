<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT. '/components/com_joomcck/library/php/fields/joomcckupload.php';

class JFormFieldCUploads extends CFormFieldUpload
{
	public function getInput()
	{
		$params['width'] = $this->params->get('params.width', 0);
		$params['height'] = $this->params->get('params.height', 0);

		$params['max_size'] = ($this->params->get('params.max_size', 2000) * 1024);
		$params['method'] = $this->params->get('params.method', 'auto');
		$params['max_count'] = $this->params->get('params.max_count', 0);
		$params['file_formats'] = $this->params->get('params.file_formats', 'zip, jpg, png, gif, bmp');
		$params['allow_edit_title'] = $this->params->get('params.allow_edit_title', 1);
		$params['allow_add_descr'] = $this->params->get('params.allow_add_descr', 1);

		$this->options = $params;
		$this->upload = parent::getInput();
		return $this->_display_input();
	}

	public function onRenderFull($record, $type, $section)
	{
		return $this->_render(2, 0, $record, $type, $section);
	}


	public function onRenderList($record, $type, $section)
	{
		return $this->_render(1, $this->params->get('params.list_limit', 5), $record, $type, $section);
	}

	private function _render($client, $limit = 0, $record, $type, $section)
	{
		$this->record = $record;
		$hits = in_array($this->params->get('params.show_hit'), array(3, $client));
		$size = in_array($this->params->get('params.show_size'), array(3, $client));
		$descr = in_array($this->params->get('params.show_descr'), array(3, $client));
		
		$files = $this->getFiles($this->record, $hits);
		
		if($limit)
		{
			$files = array_slice($files, 0, $limit);
		}
		if(!$files) return;
		
		$this->files = $files;
		$this->hits = $hits;
		$this->size = $size;
		$this->descr = $descr;
		
		$open_in_b = $this->params->get('params.show_in_browser');
		if($open_in_b && $this->params->get('params.show_target') == 1)
		{
			$doc = JFactory::getDocument();
		
			$doc->addScriptDeclaration("
				var x_win{$this->id} = 0; 
				function popUpFile{$this->id}(imgSrc)
				{
					if(x_win{$this->id})
					{
						if(!x_win{$this->id}.closed) x_win{$this->id}.close();
					}
					margin = 20;
					x_win{$this->id} = open('', 'x_win{$this->id}', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable,width=1,height=1,top=0,left=0');
					img = new Image();
					img.onload = function() {
						x_win{$this->id}.resizeTo(w = img.width + margin * 1.8, h = img.height + margin * 4);
						x_win{$this->id}.moveTo( 300, 300);	
						if( img.outerHTML ) 
							x_win{$this->id}.document.write( img.outerHTML );
						else
							x_win{$this->id}.document.body.appendChild(img);
					}
					img.src = imgSrc;
					x_win{$this->id}.focus();
				}
			");
		}
		
		return $this->_display_output(($client == 1 ? 'list' : 'full'), $record, $type, $section);
		
		
	}
}
