<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomla\CMS\Layout\LayoutHelper;

defined('_JEXEC') or die();

class JHTMLMrelements
{
	public static function sort($title, $order, $direction = 'asc', $selected = 0, $task = NULL, $new_direction = 'asc', $tip = '')
	{
		$direction = strtolower($direction);
		$icon      = array('arrow-down', 'arrow-up');
		$index     = (int)($direction == 'desc');

		if($order != $selected)
		{
			$direction = $new_direction;
		}
		else
		{
			$direction = ($direction == 'desc') ? 'asc' : 'desc';
		}

		$html = '<a class="dropdown-item" href="javascript:void(0);" onclick="Joomla.tableOrdering(\'' . $order . '\',\'' . $direction . '\',\'' . $task . '\');">';
		$html .= JText::_($title);

		if($order == $selected)
		{
			$html .= ' <i class="icon-' . $icon[$index] . '"></i>';
		}

		$html .= '</a>';

		return $html;
	}

	/**
	 * AJAX Category selector
	 *
	 * @param string $name    form element name
	 * @param mixed  $section 0 start from al sections, array - start from sections in array
	 * @param array  $default deafult
	 * @param int    $limit   limit selector.
	 * @param array  $ignore  IDs of categories to ignore.
	 */
	public static function catselector($name, $section, $default, $limit = 0, $ignore = array())
	{

		$lang = JFactory::getLanguage();
		$lang->load('com_joomcck', JPATH_ROOT);

		$db = JFactory::getDbo();

		if(!$section)
		{
			$db->setQuery("SELECT id, name, categories FROM #__js_res_sections WHERE published = 1 AND categories > 0");
			$sections = $db->loadObjectList();
		}
		else
		{
			settype($section, 'array');

			$db->setQuery("SELECT c.id, c.title, c.path, CONCAT(s.name, '/', c.path), c.params, c.section_id, s.name AS section_name,
				(SELECT count(id) FROM #__js_res_categories WHERE parent_id = c.id)  as children
    			FROM #__js_res_categories AS c
				LEFT JOIN #__js_res_sections AS s ON s.id = c.section_id
				WHERE c.published = 1 AND c.section_id IN (" . implode(',', $section) . ") AND c.parent_id = 1  ORDER BY c.lft ASC");
			$categories = $db->loadObjectList();
			foreach($categories as &$category)
			{
				$category->params = new JRegistry($category->params);
				$category->title  = htmlentities($category->title, ENT_QUOTES, 'UTF-8');
				$category->path   = htmlentities($category->path, ENT_QUOTES, 'UTF-8');
			}
		}

		ArrayHelper::clean_r($default);
		\Joomla\Utilities\ArrayHelper::toInteger($default);

		if($default)
		{
			$db->setQuery("SELECT c.id, c.title, CONCAT(s.name, '/', c.path) AS path
			FROM #__js_res_categories AS c
			LEFT JOIN #__js_res_sections AS s ON s.id = c.section_id WHERE c.id IN (" . implode(',', $default) . ")");
			$defaults = $db->loadObjectList();
		}

		settype($ignore, 'array');

		ob_start();
		include_once 'mrelements/catselector.php';
		$out = ob_get_contents();
		ob_end_clean();
        
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root(TRUE) . '/components/com_joomcck/library/php/html/mrelements/catselector.css');
        
		return $out;
	}
	
	public static function flow($name = 'filecontrol', $files = NULL, $options = array(), $field = NULL)
    {
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/vendors/flow/flow.js');
		$doc->addStyleSheet(JURI::root(TRUE) . '/components/com_joomcck/library/php/html/mrelements/flow.css');
        
        $upload_url = JRoute::_("index.php?option=com_joomcck&task=files.upload&tmpl=component&section_id=" . $app->input->getInt('section_id') . "&record_id=" . $app->input->getInt('id') . "&type_id=" . $app->input->getInt('type_id') . "&field_id={$field->id}&key=" . md5($name) ."&iscomment=".(int)@$field->iscomment, false);
        
        ob_start();
		include 'mrelements/flow.php';
		$out = ob_get_contents();
		ob_end_clean();

		return $out;
    }
	/*public static function jqupload($name = 'filecontrol', $files = NULL, $options = array(), $field = NULL)
	{
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        $doc->addStyleSheet(JURI::root(TRUE) . '/media/com_joomcck/vendors/blueimp-file-upload/css/all.css');
        $doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/vendors/blueimp-file-upload/js/vendor/jquery.ui.widget.js');
        $doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/vendors/blueimp-file-upload/js/jquery.iframe-transport.js');
        $doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/vendors/blueimp-file-upload/js/jquery.fileupload.js');
        
        ob_start();
		include_once 'mrelements/upload.php';
		$out = ob_get_contents();
		ob_end_clean();

		return $out;

		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/js/jqupload/js/tmpl.min.js');

		require_once JPATH_ROOT . '/media/com_joomcck/js/jqupload/js/tmpl.php';

		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/js/jqupload/js/load-image.all.min.js');
		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/js/jqupload/js/canvas-to-blob.min.js');
		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/js/jqupload/js/jquery.blueimp-gallery.min.js');

		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/js/jqupload/js/jquery.iframe-transport.js');
		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/js/jqupload/js/jquery.fileupload.js');
		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/js/jqupload/js/jquery.fileupload-process.js');
		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/js/jqupload/js/jquery.fileupload-image.js');
		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/js/jqupload/js/jquery.fileupload-audio.js');
		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/js/jqupload/js/jquery.fileupload-video.js');
		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/js/jqupload/js/jquery.fileupload-validate.js');
		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/js/jqupload/js/jquery.fileupload-ui.js');
		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/js/jqupload/js/fileupload-cob.js');


		//$doc->addStyleSheet(JURI::root(TRUE) . '/media/com_joomcck/js/jqupload/css/blueimp-gallery.min.css');
		$doc->addStyleSheet(JURI::root(TRUE) . '/media/com_joomcck/js/jqupload/css/jquery.fileupload.css');
		//$doc->addStyleSheet(JURI::root(TRUE) . '/media/com_joomcck/js/jqupload/css/jquery.fileupload-ui.css');
		// 		$doc->addStyleSheet(JURI::root(TRUE) . '/media/com_joomcck/js/jqupload/css/jquery.fileupload-noscript.css');
		// 		$doc->addStyleSheet(JURI::root(TRUE) . '/media/com_joomcck/js/jqupload/css/jquery.fileupload-ui-noscript.css');

		$tempname = $options['tmpname'];

		$doc->addScriptDeclaration("
			(function ($) {
				$( document ).ready(function() {
					$('#fileupload').fileupload({
				        url: '" . JRoute::_("index.php?option=com_joomcck&task=files.upload&tmpl=component&section_id=" . $app->input->getInt('section_id') . "&record_id=" . $app->input->getInt('id') . "&type_id=" . $app->input->getInt('type_id') . "&field_id={$field->id}&key=" . md5($name), FALSE) . "',
				        maxChunkSize: 200000,
						paramName: '{$tempname}[]',
						formName: '{$name}',
				        autoUpload: " . $options['autostart'] . ",
				        formData: function (form) {
			                return [{name: 'filecontrol', value: '{$tempname}'}];
			            },
				        getFilesFromResponse: function (data){
							return data.result.{$tempname};
						},
						completed: function(e, data){
							addTitleInterface( data.result.{$tempname}[0]);
						}
					});
				});
			})(jQuery);
		");

		$out[] = '
			<div class="float-start">
				<input type="checkbox" class="toggle">
				<button type="button" class="btn btn-danger btn-sm delete">
					Delete
				</button>
			</div>
			<div class="float-end">
				<span class="btn btn-success btn-sm fileinput-button">
					<span>Add files...</span>
					<input type="file" name="tmpfileuploader[]" multiple>
				</span>' . ($options['autostart'] ? NULL : '
				<button type="button" class="btn btn-sm btn-primary start">
					<span>Start upload</span>
				</button>') . '
				<button type="reset" class="btn btn-sm btn-warning cancel">
					<span>Cancel upload</span>
				</button>
				<span class="fileupload-process"></span>
			</div>
			<div class="clearfix"></div>
			<!-- The global progress state -->
			<div class="col-lg-5 fileupload-progress fade">
				<!-- The global progress bar -->
				<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
					<div class="progress-bar progress-bar-success" style="width:0%;"></div>
				</div>
				<!-- The extended global progress state -->
				<div class="progress-extended">&nbsp;</div>
			</div>';

		$cob_params = JComponentHelper::getParams('com_joomcck');
		$path       = $cob_params->get('general_upload');
		$path .= '/' . $field->params->get('params.subfolder') . '/';
        $t = [];
		if($files)
		{
			foreach($files as $file)
			{
				$img = FALSE;
				$ext = JFile::getExt($file['filename']);
				if(in_array($ext, array('png', 'jpg', 'jpeg', 'gif', 'bmp')))
				{
					$img = CImgHelper::getThumb(JPATH_ROOT . '/' . $path . $file['fullpath'], 50, 50, 'uploader', JFactory::getUser()->get('id'), ['mode' => 1]);
				}
				$t[] = '
				<tr class="template-download fade in">
			        <td>
			            <input type="checkbox" name="delete" value="1" class="toggle">
			        </td>
			        <td width="1%">';
				$t[] = $img ? '<img src="' . $img . '">' : '';

				$t[] = '
			        </td>
			        <td>
			            <p class="name">
			            	<a href="' . JUri::root() . $path . $file['fullpath'] . '" target="blank"><span id="title' . $file['id'] . '">' . ($file['title'] ? $file['title'] : $file['realname']) . '</span></a>
			            	<button class="btn btn-sm btn-light border" type="button" onclick="addTitleInterface()">Edit title</button>
			            </p>

						<input type="hidden" name="' . $name . '[]" value="' . $file['filename'] . '">
			        </td>
			        <td width="1%">' . $file['size'] . '</td>
			        <td width="1%">
		                <button class="btn btn-sm btn-link btn-danger" data-type="DELETE" style="width:30px" data-url="' . JRoute::_("index.php?option=com_joomcck&task=files.uploadremove&tmpl=component&filename=" . $file['filename']) . '">
		                    ' . HTMLFormatHelper::icon('minus-circle.png', JText::_('CDELETE')) . '
		                </button>
			        </td>
			    </tr>';
			}
		}

		$out[] = '<table role="presentation" class="table table-striped table-condensed"><tbody class="files">' . implode("\n", $t) . '</tbody></table>';
		$out   = implode("\n", $out);
		$out   = '<div id="fileupload">' . $out . '</div>';

		return $out;

		//$out[] = '<input id="fileupload" type="file" name="files[]" data-url="'.JRoute::_("index.php?option=com_joomcck&task=files.upload&tmpl=component&section_id=" . $app->input->getInt('section_id') . "&record_id=" . $app->input->getInt('id') . "&type_id=" . $app->input->getInt('type_id') . "&field_id={$field_id}&key=" . md5($name), FALSE ).'" multiple>';
    }*/
    

	/**
	 *
	 * Enter description here ...
	 *
	 * @param unknown_type $name
	 * @param unknown_type $files
	 * @param array        $options width, height, max_size, file_formats, max_count, ,
	 */
	public static function mooupload($name = 'filecontrol', $files = NULL, $options = array(), $field = NULL)
	{
        if(!is_object($field)) {
            $field = json_decode(json_encode(["id" => $field]));
        }
        $app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/js/mooupload/MooUpload.js');
		$doc->addStyleSheet(JURI::root(TRUE) . '/media/com_joomcck/js/mooupload/style.css');

		$params = new JRegistry();
		$params->loadArray($options);
		$tempname  = $params->get('tmpname', substr(md5(time() . rand(1, 1000000)), 0, 5));
		$record_id = $app->input->getInt('id', 0);

		$exts    = explode(',', str_replace(' ', '', $params->get('file_formats', 'zip, jpg, png, jpeg, gif, txt, md, bmp')));
		$session = JFactory::getSession();
		$session->set('width', $params->get('width', 0), md5($name));
		$session->set('height', $params->get('height', 0), md5($name));
		$session->set('max_size', $params->get('max_size', 2097152), md5($name));
		$session->set('file_formats', $exts, md5($name));
		if(!empty($files) && is_array($files))
		{
			$files = json_encode($files);
		}
		else
		{
			$files = 0;
		}

		$out[] = "
		<script type=\"text/javascript\">
			window.addEvent('domready', function() {
				var myUpload = new MooUpload('{$tempname}', {
					action: '" . JRoute::_("index.php?option=com_joomcck&task=files.mooupload&tmpl=component&section_id=" . $app->input->getInt('section_id') . "&record_id=" . $app->input->getInt('id') . "&type_id=" . $app->input->getInt('type_id') . "&field_id={$field->id}&key=" . md5($name), FALSE) . "',
					action_remove_file: '" . JRoute::_("index.php?option=com_joomcck&task=files.uploadremove&tmpl=component") . "',
					method: '" . $params->get('method', 'auto') . "',
					tempname: '{$tempname}',
					files:" . $files . ",
					formname:'" . $name . "[]',
					autostart:" . $params->get('autostart', 0) . ",
					field_id:" . $field->id . ",
    	    		record_id:" . $record_id . ",
					maxfilesize: " . $params->get('max_size', 2097152) . ",
					exts: ['" . implode("','", $exts) . "'],
					maxfiles: " . $params->get('max_count', 1) . ",
					canDelete: " . $params->get('can_delete', 1) . ",
					allowEditTitle: " . $params->get('allow_edit_title', 1) . ",
					allowAddDescr: " . $params->get('allow_add_descr', 1) . ",
					url_root: '" . JURI::root(TRUE) . "',
					flash: {
				      movie: '" . JURI::root(TRUE) . "/media/com_joomcck/js/mooupload/Moo.Uploader.swf'
				    },
				    texts: {
					    error      : '" . JText::_('CERROR') . "',
					    file       : '" . JText::_('CFILE') . "',
					    filesize   : '" . JText::_('CFILESIZE') . "',
					    filetype   : '" . JText::_('CFILETYPE') . "',
					    nohtml5    : '" . JText::_('CNOHTMLSUPPORT') . "',
					    noflash    : '" . JText::_('CINSTALLFLASH') . "',
					    sel        : '" . JText::_('CACT') . "',
					    selectfile : '" . JText::_('CADDFILE') . "',
					    status     : '" . JText::_('CSTATUS') . "',
					    startupload: '" . JText::_('CCTARTUPLOAD') . "',
					    uploaded   : '" . JText::_('CUPLOADED') . "',
					    sure	   : '" . JText::_('CSURE') . "',
					    edit_descr : '" . JText::_('CEDITDESCR') . "',
					    edit_title : '" . JText::_('CEDITTITLE') . "',
					    deleting   : '" . JText::_('CDELETING') . "'
				    },

				    " . ($params->get('callback') ? "
				    onFileUpload:function(fileindex, response){
				    	" . $params->get('callback') . "(fileindex, response);
				    }," : NULL) . "
				    onFileDelete: function(error, filename){
						if(error == '1016')
						{
							msg = '" . JText::sprintf('CERR_FILEDOSENTDELETED', "' + filename + '", array('jsSafe' => TRUE)) . "';
						}
						if(error == '1017')
						{
							msg = '" . JText::sprintf('CERR_FILEDOSENTEXIST', "' + filename + '", array('jsSafe' => TRUE)) . "';
						}
						if(error)
						{
							Joomcck.fieldError(" . $field->id . ", msg);
				    	}
					},
					onSelectError: function(error, filename, filesize){
						var msg = error;
						if(error == '1012')
						{
							msg = '" . JText::sprintf('CERR_FILEUPLOADLIMITREACHED', $params->get('max_count', 1), array('jsSafe' => TRUE)) . "';
						}
						if(error == '1013')
						{
							msg = '" . JText::sprintf('CERR_EXTENSIONNOTALLOWED', "' + filename + '", array('jsSafe' => TRUE)) . "';
						}
						if(error == '1014')
						{
							msg = '" . JText::sprintf('CERR_UPLOADEDFILESIZESMALLER', "' + filename + '", array('jsSafe' => TRUE)) . "';
						}
						if(error == '1015')
						{
							msg = '" . JText::sprintf('CERR_UPLOADEDFILESIZEBIGGER', "' + filename + '", array('jsSafe' => TRUE)) . "';
						}
						Joomcck.fieldError(" . $field->id . ", msg);
					}
				});

			});
		</script>";

		$out[] = '<div id="' . $tempname . '" class="upload-element"></div>';

		if($exts)
		{
			$out[] = '<br/><span class="small">' . JText::_('CER_ONLYFORMATS') . ': <b>' . implode("</b>, <b>", $exts) . '</b></span>';
		}
		$out[] = '<br/><span class="small">' . JText::_('CNSG_MAXSIZEPERFILE') . ': <b>' . HTMLFormatHelper::formatSize($params->get('max_size', 2097152)) . '</b></span>';

		return implode("\n", $out);

	}

	public static function autocompleteitem($html, $id = NULL)
	{
		$o = new stdClass();

		$o->id    = ($id ? $id : strip_tags($html));
		$o->html  = $html;
		$o->plain = strip_tags($html);

		return $o;
	}

	public static function pills($name, $id, $default = array(), $list = array(), $options = array(),$useTomSelect = 0,$params = null)
	{


		if(is_null($params))
			$params = new \Joomla\Registry\Registry();


		// max items user can add
		if(isset($options['limit']))
			$options['maxItems'] = $options['limit'];
		else
			$options['maxItems'] = $params->get('params.max_items',5);

		// suggestion limit
		if(isset($options['limit']))
			$options['maxOptions'] = $options['suggestion_limit'];
		else
			$options['maxOptions'] = $params->get('params.max_result',10);

		// allow user to add
		if(isset($options['can_add']))
			$options['canAdd'] = $options['can_add'];
		else
			$options['canAdd'] = $params->get('params.only_values',0) ? 'false' : 'true';


		$options['canDelete'] = $options['can_delete'];




		// if list not used use default as list (to display selected items in dropdown
		if(empty($list)){

			foreach ($default as $iKey => $iValue){
				$iValue = (array) $iValue;
				$list[] = $iValue['id'];
			}
		}


		$data = [
			'options' => $options,
			'params' => $params,
			'default' => $default,
			'list' => $list,
			'name' => $name,
			'id' => $id
		];

		//use new layout tomSelect
		return LayoutHelper::render('core.fields.tomSelect',$data,null,['component' => 'com_joomcck','client' => 'site']);

	}
	public static function listautocomplete($name, $id, $default = array(), $list = array(), $options = array())
	{
		$params = new JRegistry();
		$params->loadArray($options);

		settype($default, 'array');

		if($params->get('only_values', 0) == 1 && !$list && !$params->get('ajax_url'))
		{
			return '<input type="hidden" name="' . $name . '" id="' . $id . '" value="" />';
		}

		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::root(TRUE) . '/media/com_joomcck/js/autocomplete/style.css');
		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/js/autocomplete/GrowingInput.js');
		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/js/autocomplete/TextboxList.js');
		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/js/autocomplete/TextboxList.Autocomplete.js');
		$doc->addScript(JURI::root(TRUE) . '/media/com_joomcck/js/autocomplete/TextboxList.Autocomplete.Binary.js');

		$el     = $add = $skip = $a = array();
		$script = NULL;
		$patern = '["%s", "%s", "%s"]';

		foreach($default as $key => &$def)
		{
			if(!is_object($def))
			{
				$def = self::autocompleteitem($def, $key);
			}

			if(!$def->id)
				continue;

			$add[]  = sprintf('add("%s", "%s", "%s")', str_replace('"', '\\"', stripslashes($def->plain)), str_replace('"', '\\"', stripslashes($def->id)), str_replace('"', '\\"', stripslashes($def->html)));
			$skip[] = $def->id;
		}

		settype($list, 'array');

		foreach($list as &$item)
		{
			if(!is_object($item))
			{
				$item = self::autocompleteitem($item);
			}

			if(in_array($item->id, $skip))
				continue;
			if(!trim($item->id))
				continue;

			$el[] = sprintf($patern, str_replace('"', '\\"', stripslashes($item->id)), str_replace('"', '\\"', stripslashes($item->plain)), str_replace('"', '\\"', stripslashes($item->html)));
		}

		$a[] = "\nplaceholder: '" . JText::_('CTYPETOSUGGEST') . "'";
		$a[] = "\nremote:{ emptyResultPlaceholder:'" . JText::_('CNOSUGGEST') . "', loadPlaceholder:'" . JText::_('CPLSWAIT') . "'}";
		$a[] = "\nwidth: '" . $params->get('min_width', 300) . "'";
		$a[] = "\nminLength: " . $params->get('min_length', 1);
		$a[] = "\nmaxResults: " . $params->get('max_result', 10);
		if($params->get('only_values', 0) == 1)
		{
			$a[] = "\nonlyFromValues: 1";
		}
		if($params->get('case_sensitive', 0))
		{
			$a[] = "\ninsensitive: false";
		}
		if($params->get('highlight', 0) == 0)
		{
			$a[] = "\nhighlight: false";
		}

		$additional[] = "\nplugins: {autocomplete: {" . implode(',', $a) . "}}";

		if($params->get('coma_separate', 0)) // && !count($el))
		{
			$additional[] = "\nbitsOptions : { editable : {addKeys:188}}";
		}
		if($params->get('max_items', 0))
		{
			$additional[] = "\nmax : " . $params->get('max_items', 0);
		}
		if($params->get('unique', 0))
		{
			$additional[] = "\nunique: true ";
		}
		if($params->get('separateby', 0))
		{
			$additional[] = "\n" . 'decode: function(o) {
				return o.split(\'' . $params->get('separateby') . '\');
			},
			encode: function(o) {
					return o.map(function(v) {
					v = ($chk(v[0]) ? v[0] : v[1]);
					return $chk(v) ? v : null;
				}).clean().join(\'' . $params->get('separateby') . '\');
			}';
		}

		$additional[] = "\ntexts:{ limit : '" . JText::_('C_JSLIMITOPTIONS') . "'	}";

		$uniq    = substr(md5(time() . '-' . rand(0, 1000)), 0, 5);
		$options = '{' . implode(',', $additional) . '}';

		$html[] = '<input type="text" name="' . $name . '" id="' . $id . '" value="" />';
		$html[] = "<script type=\"text/javascript\">";
		$html[] = "var default{$uniq} = ['" . (count($skip) ? implode("','", $skip) : '') . "'];\n";
		$html[] = "var t{$uniq} = new jQuery.TextboxList('#{$id}', {$options});\n";


		$html[] = "t{$uniq}.addEvent('bitBoxRemove', function(box) {";
		if($params->get('max_items', 0))
		{
			//$html[] = "if($('#hidden-{$uniq}')) $('#hidden-{$uniq}').show();";
		}

		if($params->get('onRemove', 0))
		{
			$html[] = "
				jQuery(box).css('background-image', 'url(\"" . JURI::root(TRUE) . "/media/com_joomcck/js/mooupload/imgs/load_bg_blue.gif\")');

				jQuery.ajax({
					url:'" . JRoute::_($params->get('onRemove'), FALSE) . "',
					type:'POST',
					dataType:'json',
					data:{
						rid: " . JFactory::getApplication()->input->getInt('id') . ",
						tid: box.value[0]
					}
				}).done(function(json) {
					jQuery(box).css('background-image', '');
					if(json.success)
					{
						jQuery.each(default{$uniq}, function(key, item){
							if(box.value[0] == item)
								default{$uniq}.splice(key);
						});
						//t{$uniq}.update();
					}
					else
					{
						alert(json.error);
					}
				});";
		}
		$html[] = "});";


		$html[] = "t{$uniq}.addEvent('bitBoxAdd', function(box){
				if(box.value[0])
				{
					if(default{$uniq}.contains(box.value[0].toString()))
					{
						return;
					}
				}
			";
		if($params->get('max_items', 0))
		{
			$html[] = "var parent = $(box).parents('ul.textboxlist-bits');
			if(parent.children('li.textboxlist-bit.textboxlist-bit-box').length >= " . $params->get('max_items', 0) . ")
			{
				//parent.children('li').last().hide().attr('id', 'hidden-{$uniq}');

			}";
		}

		if($params->get('onAdd', 0))
		{
			$html[] = ($params->get('max_items', 0) ? "if(default{$uniq}.length > " . $params->get('max_items', 0) . "){ alert('" . JText::_('CTAGLIMITREACHED') . "'); return;}" : "") . "

				jQuery(box).css('background-image', 'url(\"" . JURI::root(TRUE) . "/media/com_joomcck/js/mooupload/imgs/load_bg_blue.gif\")');

				jQuery.ajax({
					url:'" . JRoute::_($params->get('onAdd'), FALSE) . "',
					type:'POST',
					dataType:'json',
					data:{
						rid: " . $params->get('record_id', 0) . ",
						val: box.value,
						max: " . $params->get('max_items', 0) . "
					}
				}).done(function(json) {
					jQuery(box).css('background-image', '');
					if(!json)
					{
						return;
					}
					if(json.success)
					{
						box.value = json.result;
						default{$uniq}.push(json.result[0]);
						//t{$uniq}.update();
					}
					else
					{
						alert(json.error);
					}
			    });";
		}
		$html[] = "});";


		if($add)
		{
			$html[] = "t{$uniq}." . implode(".", $add) . ";\n";
		}
		if($el)
		{
			$html[] = "var r{$uniq} = [" . implode(",", $el) . "];\n";
			$html[] = "t{$uniq}.plugins['autocomplete'].setValues(r{$uniq});\n";
		}
		if($params->get('ajax_url'))
		{
			//$html[] = "t{$uniq}.container.addClass('textboxlist-loading');\n";
			$html[] = "jQuery.ajax({
				url:'" . JRoute::_($params->get('ajax_url'), FALSE) . "',
				type:'POST',
				dataType:'json',
				data:{" . $params->get('ajax_data') . "}
			}).done(function(json) {
				if(!json.success)
				{
					alert(json.error);
					return;
				}
				if(json.result)
				{
					//t{$uniq}.container.removeClass('textboxlist-loading');
					t{$uniq}.plugins['autocomplete'].setValues(json.result);
				}
		    });";
		}

		$html[] = "</script>\n";

		return implode("\n", $html);
	}
}