<?php
/**
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.filesystem.folder');
JFormHelper::loadFieldClass('list');

class JFormFieldMjfolderList extends JFormFieldList
{

	public $type = 'MJFolderList';

	protected function getInput()
	{
		$id = str_replace(array('][', '[', ']'), '_', $this->name);
		$id = preg_replace("/_$/iU", '', $id);

		//var_dump(!empty($this->value->icon));

		$html = '<span style="float:left;">'.$this->element['directory'].'/</span><br>';
		$options = $this->getOptions();
		$html .= JHtml::_('select.genericlist', $options, $this->name.'[dir]', 'class="span5" onchange="updatedefaulticon(false)"', 'value', 'text', !empty($this->value->dir) ? $this->value->dir : 'google', $this->id);
		$html .= '&nbsp;<img src="'.JUri::root(TRUE).(!empty($this->value->icon) ? '/'.$this->element['directory'].'/'.$this->value->dir.'/'.$this->value->icon : '/media/com_joomcck/blank.png').'" id="icon'.$this->fieldname.'" > <span id="label'.$this->fieldname.'">'.@$this->value->icon.'</span>';
		$html .= '<input type="hidden" name="'.$this->name.'[icon]" readonly="readonly" id="icon_param'.$this->fieldname.'" value="'.@$this->value->icon.'">';
		$html .= '<p><small>'.JText::_('G_CLICKSELECTDEFAULT').'</small></p>';
		$html .= '<div id="icons'.$this->fieldname.'" style="width:250px;max-height:150px;overflow-x:hidden;overflow-y:scroll"></div>';
		$html .= "<script>
			(function($){
				window.updatedefaulticon = function(first)
				{
					var input = $('#icon_param{$this->fieldname}');
					var dir = '{$this->element['directory']}/';
					var sel = $('#{$id}');
					var stack = $('#icons{$this->fieldname}');

					if(!first)
					{
					 	input.val('');
					 	$('#icon{$this->fieldname}').attr('src', '".JUri::root(TRUE)."/media/com_joomcck/blank.png');
						$('#label{$this->fieldname}').html();
					}
					$.ajax({
						url: '".JURI::root(TRUE)."/index.php?option=com_joomcck&task=ajax.icons&tmpl=component',
						dataType: 'json',
						type: 'POST',
						data:{
							'dir': dir + sel.val()
						}
					}).done(function(json) {
						if(!json)
						{
							return;
						}
						if(!json.success)
						{
							alert(json.error);
							return;
						}
						stack.html('');
						$.each(json.result, function(k, v){
							$(document.createElement('img'))
								.attr({class:'float-start marker-icon', src: '".JUri::root(TRUE)."/{$this->element['directory']}/' + sel.val() + '/' + v})
								.click(function(){
										input.val(v);
										$('#icon{$this->fieldname}').attr('src', '".JUri::root(TRUE)."/{$this->element['directory']}/' + sel.val() + '/' + v);
										$('#label{$this->fieldname}').html(v);
									})
								.appendTo(stack);
						});
					});
				}
				window.updatedefaulticon(true);
			}(jQuery));

		</script>";

		return $html;

	}
	protected function getOptions()
	{
		// Initialize variables.
		$options = array();

		// Initialize some field attributes.
		$filter = (string) $this->element['filter'];
		$exclude = (string) $this->element['exclude'];
		$hideNone = (string) $this->element['hide_none'];
		$hideDefault = (string) $this->element['hide_default'];

		// Get the path in which to search for file options.
		$path = (string) $this->element['directory'];
		if (!is_dir($path))
		{
			$path = JPATH_ROOT . '/' . $path;
		}

		// Prepend some default options based on field attributes.
		if (!$hideNone)
		{
			$options[] = JHtml::_('select.option', '-1', JText::alt('JOPTION_DO_NOT_USE', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
		}
		if (!$hideDefault)
		{
			$options[] = JHtml::_('select.option', '', JText::alt('JOPTION_USE_DEFAULT', preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)));
		}

		// Get a list of folders in the search path with the given filter.
		$folders = JFolder::folders($path, $filter);

		// Build the options list from the list of folders.
		if (is_array($folders))
		{
			foreach ($folders as $folder)
			{

				// Check to see if the file is in the exclude mask.
				if ($exclude)
				{
					if (preg_match(chr(1) . $exclude . chr(1), $folder))
					{
						continue;
					}
				}

				$options[] = JHtml::_('select.option', $folder, $folder);
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
