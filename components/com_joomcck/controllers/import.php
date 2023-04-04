<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use uploader\UploadHandler;

defined('_JEXEC') or die();
jimport('mint.mvc.controller.admin');
class JoomcckControllerImport extends MControllerAdmin
{

	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = JFactory::getApplication()->input;
		}
	}


	public function import()
	{
		$user   = JFactory::getUser();
		$app    = JFactory::getApplication();
		$params = $this->input->get('import', array(), 'array');

		$imports = JTable::getInstance('Import', 'JoomcckTable');

		if($this->input->get('preset') == 'new')
		{
			$save = array(
				'user_id'    => $user->get('id'),
				'section_id' => $this->input->get('section_id'),
				'params'     => json_encode($params),
				'name'       => $params['name'],
				'crossids'   => '[]'
			);
			$imports->save($save);
		}
		else
		{
			$imports->load($this->input->get('preset'));
			$imports->params = json_encode($params);
			$imports->name   = $params['name'];
			$imports->store();
		}

		$db = JFactory::getDbo();


		try
		{
			if(function_exists('set_time_limit'))
			{
				set_time_limit(0);
			}
			if(function_exists('ini_set'))
			{
				ini_set('max_execution_time', 0);
			}


			$params     = new Registry($imports->params);
			$conversion = new Registry($imports->crossids);
			$crossids   = json_decode($imports->crossids, TRUE);
			$import_key = JFactory::getSession()->get('key', NULL, 'import');
			$type       = ItemsStore::getType($this->input->getInt('type_id'));
			$section    = ItemsStore::getSection($this->input->getInt('section_id'));


			$db->setQuery("SELECT * FROM #__js_res_import_rows WHERE `import` = '{$import_key}'");
			$list = $db->loadObjectList();

			if(!$list)
			{
				throw new Exception(JText::_('CIMPORTROWSARENOTFOUND'));
			}

			require_once JPATH_ROOT . '/components/com_joomcck/models/fields.php';
			$fields_model = new JoomcckModelFields();
			$record_table = JTable::getInstance('Record', 'JoomcckTable');
			$cat_table    = JTable::getInstance('Record_category', 'JoomcckTable');

			$stat = array();

			foreach($list AS $record)
			{
				$row = new Registry($record->text);

				if(!$row->get($params->get('field.title')))
				{
					continue;
				}

				$id_original = $row->get($params->get('field.id'));

				$id = $conversion->get($id_original, NULL);

				$data = array();
				if($id)
				{
					$record_table->load($id);
				}

				if($record_table->id)
				{
					$isNew = FALSE;
					@$stat['old']++;
				}
				else
				{
					$record_table->id = NULL;
					$data['id']       = NULL;
					$isNew = TRUE;
					@$stat['new']++;
				}

				switch($type->params->get('properties.item_title'))
				{
					case 0:
						$data['title'] = 'NO: '.time();
						break;
					case 1: // standard
						$data['title'] = $row->get($params->get('field.title'));
						break;
					case 2: // Composite
						$data['title'] = 'No support for composite title on import';
						break;
				}

				$data['type_id']    = $this->input->getInt('type_id');
				$data['section_id'] = $this->input->getInt('section_id');
				$data['published']  = 1;

				$data['categories'] = '[]';
				$catid              = $params->get('category.' . $row->get($params->get('field.category')));
				if($catid && $this->_get_cat($catid))
				{
					$data['categories'] = $this->_get_cat($catid);
					$app->input->set('cat_id', $data['categories']);
					$_REQUEST['jform']['category'][] = $data['categories'];
				}

				if($row->get($params->get('field.ctime')) || $isNew) $data['ctime']   = JDate::getInstance($row->get($params->get('field.ctime', 'now')))->toSql();
				if($row->get($params->get('field.mtime')) || $isNew) $data['mtime']   = JDate::getInstance($row->get($params->get('field.mtime', 'now')))->toSql();
				if($row->get($params->get('field.user_id')) || $isNew) $data['user_id'] = $row->get($params->get('field.user_id'), JFactory::getUser()->get('id'));
				if($row->get($params->get('field.access')) || $isNew) $data['access']  = $row->get($params->get('field.access'));
				if($row->get($params->get('field.extime')) || $isNew) $data['extime']  = $row->get($params->get('field.extime'));

				$record_table->bind($data);
				$record_fields = json_decode($record_table->fields, TRUE);

				if(!$record_table->check())
				{
					throw new Exception($record_table->getError());
				}

				$record_table->fields = json_encode($record_fields);
				$record_table->store();

				$crossids[$id_original] = $record_table->id;

				if($data['categories'] != '[]')
				{
					$categories = json_decode($data['categories'], TRUE);
					foreach($categories AS $catid => $catname)
					{
						$cat_data = array(
							'record_id'  => $record_table->id, 'catid' => $catid,
							'section_id' => $record_table->section_id, 'ordering' => 0
						);
						$cat_table->load($cat_data);

						if(!$cat_table->id)
						{
							$cat_table->save($cat_data);
						}

						$cat_table->reset();
						$cat_table->id = NULL;
					}
				}

				$fields_list = $fields_model->getFormFields($this->input->get('type_id'), $record_table->id, FALSE);

				foreach($fields_list AS $field)
				{
					$field_data = $field->onImportData($row, $params);
					if($field_data === NULL || $field_data === FALSE || $field_data === '')
					{
						continue;
					}
					$field_data = $field->onImport($field_data, $params, $record_table);
					if($field_data === NULL || $field_data === FALSE || $field_data === '')
					{
						continue;
					}

					$record_fields[$field->id] = $field_data;
					$field_ids[]               = $field->id;
				}

				if($isNew)
				{
					ATlog::log($record_table, ATlog::REC_IMPORT);
				}
				else
				{
					ATlog::log($record_table, ATlog::REC_IMPORTUPDATE);
				}

				$record_table->fields = json_encode($record_fields);
				$record_table->store();
				$record_table->reset();
				$record_table->id = NULL;
			}

			$imports->crossids = json_encode($crossids);
			$imports->store();


			// REINDEX ----------------------
			// ==============================

			$crossids[] = 0;
			\Joomla\Utilities\ArrayHelper::toInteger($crossids);
			$sql = "SELECT id, fields, title, type_id, section_id, user_id FROM #__js_res_record WHERE id IN(" . implode(',', $crossids) . ")";
			$db->setQuery($sql);
			$list = $db->loadObjectList();

			$record_values = JTable::getInstance('Record_values', 'JoomcckTable');

			foreach($list AS $item)
			{
				$fulltext    = array();
				$fields_list = $fields_model->getFormFields($item->type_id, $item->id, FALSE, json_decode($item->fields, TRUE));
				$record_values->clean($item->id, $field_ids);

				foreach($fields_list as $field)
				{
					if($field->params->get('core.searchable'))
					{
						$data = $field->onPrepareFullTextSearch($field->value, $item, $type, $section);
						if(is_array($data))
						{
							$data = implode(', ', $data);
						}
						$fulltext[$field->id] = $data;
					}

					if(!in_array($field->id, $field_ids))
					{
						continue;
					}

					$values = $field->onStoreValues(get_object_vars($item), $item);
					if(empty($values))
					{
						continue;
					}

					settype($values, 'array');
					foreach($values as $key => $value)
					{
						$record_values->store_value($value, $key, $item, $field);
						$record_values->reset();
						$record_values->id = NULL;
					}
				}

				$user = JFactory::getUser($item->user_id);

				if($section->params->get('more.search_title'))
				{
					$fulltext[] = $item->title;
				}
				if($section->params->get('more.search_name'))
				{
					$fulltext[] = $user->get('name');
					$fulltext[] = $user->get('username');
				}
				if($section->params->get('more.search_email'))
				{
					$fulltext[] = $user->get('email');
				}
				if($section->params->get('more.search_category') && $item->categories != '[]')
				{
					$cats       = json_decode($item->categories, TRUE);
					$fulltext[] = implode(', ', array_values($cats));
				}

				if(!empty($fulltext))
				{
					$db->setQuery("UPDATE `#__js_res_record` SET fieldsdata = '" . $db->escape(strip_tags(implode(', ', $fulltext))) . "' WHERE id = $item->id");
					$db->execute();
				}

				unset($fulltext, $user);
			}

			JFactory::getApplication()->enqueueMessage(JText::_('CIMPORTSUCCESS'));

		}
		catch(Exception $e)
		{

			Factory::getApplication()->enqueueMessage( $e->getMessage(), 'warning');
		}

		JFactory::getSession()->set('importstat', $stat);
		JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_joomcck&view=import&step=3&section_id=' . $imports->section_id, FALSE));
	}

	private function _get_cat($id)
	{
		static $categories = array();

		if(!array_key_exists($id, $categories))
		{
			$db = JFactory::getDbo();
			$db->setQuery("SELECT id, title FROM #__js_res_categories WHERE id = {$id}");

			$categories[$id] = json_encode($db->loadAssocList('id', 'title'));
		}

		return @$categories[$id];
	}

	public function analize()
	{
		$this->key = $this->input->get('json');
		$file      = $this->input->getString('file');

		$ext        = strtolower(JFile::getExt($file));
		$this->json = JPATH_ROOT . '/tmp/' . $this->key . '.json';

		$upload = JPATH_ROOT . '/tmp/import_uploads/' . urldecode($file);

		if(!JFile::exists($upload))
		{
			$this->_error('CIMPORTCANNOTFINDFILE');
		}

		if(!in_array($ext,
			array(
				'zip',
				'csv',
				'json'
			))
		)
		{
			$this->_error('CIMPORTWRONGEXT');
		}

		if($ext == 'zip')
		{
			$this->_msg(10, 'CIMPORTEXTRCT');

			$dir       = JPATH_ROOT . '/tmp/import_extract/' . $this->key;
			$this->dir = $dir;

			if(!JFolder::exists($dir))
			{
				JFolder::create($dir);
			}

			if(!JArchive::extract($upload, $dir))
			{
				$this->_error('CIMPORTCANNOTEXTRACT');
			}

			$files = JFolder::files($dir, '\.(csv|json)$', TRUE, TRUE);
			if(count($files) == 0)
			{
				$this->_error('CIMPORTNOFOUND');
			}
			if(count($files) > 1)
			{
				$this->_error('CIMPORTMORETHANONE');
			}

			$upload = $files[0];
		}

		$this->_msg(20, 'CIMPORTPARCE');
		if(!JFile::exists($upload))
		{
			$this->_error('CIMPORTCANNOTFINDFILE');
		}

		$this->db    = JFactory::getDbo();
		$this->heads = array();

		$this->db->setQuery("DELETE FROM `#__js_res_import_rows` WHERE `ctime` < NOW() - INTERVAL 1 DAY OR `import` = {$this->key}");
		$this->db->execute();

		$ext = strtolower(JFile::getExt($upload));
		if($ext == 'csv')
		{
			$this->_load_csv($upload, $this->input->get('delimiter', ','));
		}
		elseif($ext == 'json')
		{
			$this->_load_json($upload);
        }
        
		foreach($this->heads AS $h)
		{
			if(strpos($h, '.') !== FALSE)
			{
				$this->_error('CIMPORTHEADERNAME');
			}
		}

		JFactory::getSession()->set('headers', $this->heads, 'import');
		JFactory::getSession()->set('key', $this->key, 'import');

		$this->_msg(100, 'CIMPORTPARCE');

		JFactory::getApplication()->close();
	}

	private function _load_json($file)
	{
		$body = json_decode(file_get_contents($file));
		foreach($body AS $row)
		{
			$this->_row(json_decode(json_encode($row), TRUE));
		}
	}

	private function _load_csv($filename = '', $delimiter = ',')
	{
		$header = [];
		if(($handle = fopen($filename, 'r')) !== FALSE)
		{
			while(($row = fgetcsv($handle, 2048, $delimiter)) !== FALSE)
			{
				if(!$header)
				{
					$header = $row;
				}
				else
				{
					$data = array_combine($header, $row);
					//$data = array_map("utf8_encode", $data);
					$this->_row($data);
				}
			}
			fclose($handle);
		}
	}

	private function _row($data)
	{
		if(!$data)
		{
			return;
		}

		$sql = "INSERT INTO #__js_res_import_rows (id, `import`,`text`,`ctime`) VALUES (null, %d, '%s', NOW())";
		$sql = sprintf($sql, $this->key, $this->db->escape(json_encode($data)));
		$this->db->setQuery($sql);
		$this->db->execute();

		$columns     = array_keys($data);
		$this->heads = array_unique(array_merge($this->heads, $columns));
	}

	public function upload()
	{
		require_once JPATH_ROOT . '/media/com_joomcck/vendors/blueimp-file-upload/php/UploadHandler.php';

		$options = array(
			'accept_file_types' => '/\.(zip|json|csv)$/i',
			'upload_dir'        => JPATH_ROOT . '/tmp/import_uploads/'
		);

		JFactory::getSession()->set('importprocess', 0);

		if(!JFolder::exists($options['upload_dir']))
		{
			JFolder::create($options['upload_dir']);
		}

		$upload = new UploadHandler($options);

		JFactory::getApplication()->close();
	}

	private function _error($msg)
	{
		if(!empty($this->dir))
		{
			JFolder::delete($this->dir);
		}
		JFile::write($this->json, json_encode(array(
			'error' => JText::_($msg)
		)));
		JFactory::getApplication()->close();
	}

	private function _msg($stat, $msg)
	{
		JFile::write($this->json, json_encode(array(
			'status' => $stat,
			'msg'    => JText::_($msg)
		)));
	}
}
