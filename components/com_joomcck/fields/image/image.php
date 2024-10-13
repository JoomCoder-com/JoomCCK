<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckfield.php';

class JFormFieldCImage extends CFormField
{
	public static $loaded = FALSE;
	public $directory;
	public $script_loaded;

	public function getInput()
	{
		\Joomla\CMS\Factory::getDocument()->addScript(\Joomla\CMS\Uri\Uri::root(TRUE) . '/components/com_joomcck/fields/image/assets/input.js');
		$params = $this->params;
		$user   = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$doc    = \Joomla\CMS\Factory::getDocument();
		\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.modal');
		$this->directory = str_replace('\\', '/', $params->get('params.directory', 'images')) . '/';

		if(is_string($this->value))
		{
			$this->value = json_decode($this->value, TRUE);
			if(!is_array($this->value))
			{
				settype($this->value, 'array');
			}
		}
		if(!empty($this->value['image']))
		{
			$this->value['image'] = ltrim($this->value['image'], '/');
		}

		if(!self::$loaded)
		{
			$this->script_loaded = FALSE;
			self::$loaded        = TRUE;
		}
		else
		{
			$this->script_loaded = TRUE;
		}

		return $this->_display_input();
	}

	public function onJSValidate()
	{
		if($this->required)
		{


			// joomla core media field
			if($this->params->get('params.select_type', 2) == 1){

				return "\n\t\tif(!jQuery('#jform_fields__{$this->id}__image').val()){ hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(\Joomla\CMS\Language\Text::sprintf('CFIELDREQUIRED', $this->label)) . "');}";

			}


			return "\n\t\tif(!jQuery('#jformfields{$this->id}image').val() && !jQuery('#jformfields{$this->id}hiddenimage').val()){ hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(\Joomla\CMS\Language\Text::sprintf('CFIELDREQUIRED', $this->label)) . "');}";
		}
	}

	public function validateField($value, $record, $type, $section)
	{
		if($this->params->get('params.select_type', 2) == 2) // upload mode
		{
			$file  = new \Joomla\CMS\Input\Files();
			$file  = $file->get('fields' . $this->id . 'image', FALSE);
			$check = $file['name'];
			if(!$check)
			{
				$check = $value['image'];
			}

		}
		elseif($this->params->get('params.select_type', 2) == 1){ // joomla media field
			$check = \Joomla\CMS\HTML\HTMLHelper::cleanImageURL($value['image'])->url;
		}
		else // other modes
		{
			$check = $value['image'];
		}

		$ext = \Joomla\CMS\Filesystem\File::getExt($check);

		$formats = explode(',', strtolower(str_replace(array(' '), '', $this->params->get('params.formats', 'png,jpg,gif,jpeg,webp,avif'))));


		if($check && FALSE === array_search(strtolower($ext), $formats))
		{
			$this->setError(\Joomla\CMS\Language\Text::sprintf('F_NOTALLOWEDEXT', $this->label, str_replace(',', ', ', $this->params->get('params.formats', 'png,jpg,gif,jpeg,webp,avif'))));

			return FALSE;
		}

		return parent::validateField($check, $record, $type, $section);
	}

	public function onStoreValues($validData, $record)
	{
		if($this->params->get('params.select_type', 2) == 2) // upload mode
		{
			$table = \Joomla\CMS\Table\Table::getInstance('Files', 'JoomcckTable');
			$table->load(array('record_id' => $record->id, 'field_id' => $this->id));


			if(empty($this->value['image']))
			{
				if($table->id)
				{
					\Joomla\Filesystem\File::delete(JPATH_ROOT . DIRECTORY_SEPARATOR . $table->fullpath);
					$table->delete();
				}
			}
			else
			{
				if(!empty($this->value['filename']) && !empty($this->value['realname']))
				{
					$data = array(
						'id'         => NULL,
						'filename'   => $this->value['filename'],
						'realname'   => urldecode($this->value['realname']),
						'section_id' => $record->section_id,
						'record_id'  => $record->id,
						'type_id'    => $record->type_id,
						'field_id'   => $this->id,
						'saved'      => 1,
						'fullpath'   => $this->value['image'],
						'ext'        => \Joomla\CMS\Filesystem\File::getExt($this->value['filename'])
					);

					if($table->id)
					{
						if(strtolower($this->value['filename']) != strtolower($table->filename))
						{
							\Joomla\Filesystem\File::delete(JPATH_ROOT . DIRECTORY_SEPARATOR . $table->fullpath);
							$table->delete();
							$table->reset();
							$table->id = NULL;
							$table->save($data);
						}
					}
					else
					{
						$table->save($data);
					}
				}
			}
		}

		return $this->value['image'];
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		// upload mode
		if($this->params->get('params.select_type', 2) == 2)
		{
			$file = new \Joomla\CMS\Input\Files();
			$file = $file->get('fields' . $this->id . 'image', FALSE);

			if(!$file['error'] && !empty($file['name']))
			{
				$time   = time();
				$params = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');
				$date   = date($params->get('folder_format', 'Y-m'), $time);
				$ext    = \Joomla\String\StringHelper::strtolower(\Joomla\CMS\Filesystem\File::getExt($file['name']));

				$subfolder = $this->params->get('params.subfolder');


				$dest  = $params->get('general_upload') . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR;
				$index = '<html><body></body></html>';
				if(!is_dir($dest))
				{
					\Joomla\Filesystem\Folder::create(JPATH_ROOT . DIRECTORY_SEPARATOR .$dest, 0755);
					\Joomla\Filesystem\File::write(JPATH_ROOT . DIRECTORY_SEPARATOR .$dest . DIRECTORY_SEPARATOR . 'index.html', $index);
				}

				$dest .= $date . DIRECTORY_SEPARATOR;
				if(!is_dir($dest))
				{
					\Joomla\Filesystem\Folder::create(JPATH_ROOT . DIRECTORY_SEPARATOR .$dest, 0755);
					\Joomla\Filesystem\File::write(JPATH_ROOT . DIRECTORY_SEPARATOR .$dest . DIRECTORY_SEPARATOR . 'index.html', $index);
				}

				$filename = $time . '_' . md5($file['name']);
				$dest .= \Joomla\Filesystem\File::stripExt($filename) . '.' . $ext;

				if(\Joomla\Filesystem\File::upload($file['tmp_name'], JPATH_ROOT . DIRECTORY_SEPARATOR . $dest))
				{
					$value['image']    = str_replace('\\', '/', $dest);
					$value['realname'] = $file['name'];
					$value['filename'] = $filename . '.' . $ext;
				}
			}
		}

		if(!empty($value['image']))
		{
			$value['image'] = ltrim($value['image'], '/');

			return $value;
		}

		return FALSE;
	}

	public function onRenderFull($record, $type, $section, $client = 'full')
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		if(empty($this->value['image']) && $this->params->get('params.default_img', 0))
		{
			$this->value['image'] = str_replace('\\', '/', $this->params->get('params.default_img'));
		}

		if(!empty($this->value['image']))
		{
			return $this->_display_output($client, $record, $type, $section);
		}

		return;
	}

	public function onRenderList($record, $type, $section)
	{
		return $this->onRenderFull($record, $type, $section, 'list');
	}

	public function onImportData($row, $params)
	{
		return $row->get($params->get('field.' . $this->id . '.fname'));
	}

	public function onImport($value, $params, $record = NULL)
	{
		$file = str_replace('\\', '/', $value);
		$file = explode('/', $file);
		$file = $file[count($file) - 1];


		if(!$file)
		{
			return;
		}

		$file = $this->_find_import_file($params->get('field.' . $this->id . '.path'), $file);

		if(!$file)
		{
			return;
		}


		$upload_path = DIRECTORY_SEPARATOR . \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck')->get('general_upload') . DIRECTORY_SEPARATOR . $this->params->get('params.subfolder');
		if(!is_dir(JPATH_ROOT . $upload_path))
		{
			\Joomla\Filesystem\Folder::create(JPATH_ROOT . $upload_path, 0755);
			\Joomla\Filesystem\File::write(JPATH_ROOT . $upload_path . DIRECTORY_SEPARATOR . 'index.html', $index = '<html><body></body></html>');
		}
		$upload_path .= DIRECTORY_SEPARATOR . time() . '_' . md5($file) . '.' . strtolower(\Joomla\CMS\Filesystem\File::getExt(basename($file)));

		if(\Joomla\Filesystem\File::copy($file, JPATH_ROOT . $upload_path))
		{
			$upload_path = str_replace('\\', '/', $upload_path);

			return array('image' => $upload_path);
		}

		return;
	}

	public function onImportForm($heads, $defaults)
	{
		$out = $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id . '.fname'), 'fname');
		$out .= sprintf('<div><small>%s</small></div><input type="text" name="import[field][%d][path]" value="%s" class="col-md-12" >',
			\Joomla\CMS\Language\Text::_('IIMPPATH'), $this->id, $defaults->get('field.' . $this->id . '.path', 'images'));

		return $out;
	}

	public function deleteImage()
	{
		$input = \Joomla\CMS\Factory::getApplication()->input;
		$file  = $input->getString('file');

		$files_table  = \Joomla\CMS\Table\Table::getInstance('Files', 'JoomcckTable');
		$record_table = \Joomla\CMS\Table\Table::getInstance('Record', 'JoomcckTable');

		$files_table->load(array('fullpath' => $file));

		$record_table->load($files_table->record_id);
		$type   = ItemsStore::getType($files_table->type_id);
		$params = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');

		$subfolder = $this->params->get('params.subfolder', 'image');

		$exist = FALSE;

		$full_file_path = JPATH_ROOT . DIRECTORY_SEPARATOR . $file;

		if(is_file($full_file_path))
		{
			$out = array(
				'success' => 1
			);

			if(!$files_table->record_id || !$files_table->saved || !($type->params->get('audit.audit_log') && $type->params->get('audit.al27.on')))
			{
				\Joomla\Filesystem\File::delete($full_file_path);
				$files_table->delete();
			}
			else
			{
				$files_table->saved = 2;
				$files_table->store();
				$data             = $record_table->id ? $record_table->getProperties() : array();
				$data['file']     = $files_table->getProperties();
				$data['field']    = $this->label;
				$data['field_id'] = $this->id;
				ATlog::log($data, ATlog::REC_FILE_DELETED, 0, $files_table->field_id);
			}

		}
		else
		{
			$out = array(
				'success' => 2
			);
		}

		if($out['success'] > 0 && $record_table->id && $files_table->saved)
		{
			$fields = json_decode($record_table->fields, TRUE);

			if(isset($fields[$this->id]))
			{
				unset($fields[$this->id]);
				$record_table->fields = json_encode($fields);
				$record_table->store();
			}
		}

		echo json_encode($out);
		\Joomla\CMS\Factory::getApplication()->close();
	}

	public function getCompatabilityPath($path)
	{
		$params    = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');
		$subfolder = $this->params->get('params.subfolder', 'image');

		if(is_file(JPATH_ROOT . DIRECTORY_SEPARATOR . $params->get('general_upload') . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR . $path))
		{
			return $params->get('general_upload') . '/' . $subfolder . '/' . $path;
		}

		return $path;
	}
}