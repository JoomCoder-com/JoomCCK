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

defined('_JEXEC') or die();
jimport('mint.mvc.controller.admin');
class JoomcckControllerImport extends MControllerAdmin
{

	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = \Joomla\CMS\Factory::getApplication()->input;
		}

		// Security: require authenticated admin for all import operations
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		if (!$user->get('id'))
		{
			throw new \Exception(\Joomla\CMS\Language\Text::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'), 403);
		}
		if (!MECAccess::isAdmin())
		{
			throw new \Exception(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}
	}


	public function import()
	{
		\Joomla\CMS\Session\Session::checkToken('request') or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		$user   = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$app    = \Joomla\CMS\Factory::getApplication();

		$imports = \Joomla\CMS\Table\Table::getInstance('Import', 'JoomcckTable');

		// Coming from preview step - just load existing preset (params already saved)
		$imports->load($this->input->get('preset'));

		if (!$imports->id)
		{
			$app->enqueueMessage(\Joomla\CMS\Language\Text::_('CIMPORTPRESETNOTFOUND'), 'error');
			$app->redirect(\Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=import&step=1&section_id=' . $this->input->get('section_id'), false));
			return;
		}

		$db = \Joomla\CMS\Factory::getDbo();
		$start_time = microtime(true);

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
			$crossids_data = $imports->crossids ?: '{}';
			$conversion = new Registry($crossids_data);
			$crossids   = json_decode($crossids_data, TRUE) ?: array();
			$import_key = \Joomla\CMS\Factory::getSession()->get('key', NULL, 'import');
			$type       = ItemsStore::getType($this->input->getInt('type_id'));
			$section    = ItemsStore::getSection($this->input->getInt('section_id'));


			$db->setQuery("SELECT * FROM #__js_res_import_rows WHERE `import` = " . $db->quote($import_key));
			$list = $db->loadObjectList();

			if(!$list)
			{
				throw new Exception(\Joomla\CMS\Language\Text::_('CIMPORTROWSARENOTFOUND'));
			}

			require_once JPATH_ROOT . '/components/com_joomcck/models/fields.php';
			$fields_model = new JoomcckModelFields();
			$record_table = \Joomla\CMS\Table\Table::getInstance('Record', 'JoomcckTable');
			$cat_table    = \Joomla\CMS\Table\Table::getInstance('Record_category', 'JoomcckTable');

			$stat = array(
				'new' => 0,
				'old' => 0,
				'skipped' => 0
			);
			$field_ids = array();
			$import_method = $params->get('method', 'update'); // update, skip, duplicate

			foreach($list AS $record)
			{
				$row = new Registry($record->text);

				// Only require title when type has standard titles enabled
				if($type->params->get('properties.item_title') == 1 && !$row->get($params->get('field.title')))
				{
					continue;
				}

				$id_original = $row->get($params->get('field.id'));

				$id = $conversion->get($id_original, NULL);

				$data = array();

				// Handle import methods
				if($import_method == 'duplicate')
				{
					// Force add: Always create new records, never update
					$record_table->id = NULL;
					$data['id']       = NULL;
					$isNew = TRUE;
					$stat['new']++;
				}
				elseif($id)
				{
					$record_table->load($id);

					if($record_table->id)
					{
						if($import_method == 'skip')
						{
							// Skip: Don't import if record already exists
							$stat['skipped']++;
							$record_table->reset();
							$record_table->id = NULL;
							continue;
						}

						// Update: Update existing record (default behavior)
						$isNew = FALSE;
						$stat['old']++;
					}
					else
					{
						$record_table->id = NULL;
						$data['id']       = NULL;
						$isNew = TRUE;
						$stat['new']++;
					}
				}
				else
				{
					$record_table->id = NULL;
					$data['id']       = NULL;
					$isNew = TRUE;
					$stat['new']++;
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

				if($row->get($params->get('field.ctime')) || $isNew) $data['ctime']   = \Joomla\CMS\Date\Date::getInstance($row->get($params->get('field.ctime', 'now')))->toSql();
				if($row->get($params->get('field.mtime')) || $isNew) $data['mtime']   = \Joomla\CMS\Date\Date::getInstance($row->get($params->get('field.mtime', 'now')))->toSql();
				if($row->get($params->get('field.user_id')) || $isNew) $data['user_id'] = $row->get($params->get('field.user_id'), \Joomla\CMS\Factory::getApplication()->getIdentity()->get('id'));
				if($row->get($params->get('field.access')) || $isNew) $data['access']  = $row->get($params->get('field.access'));
				if($row->get($params->get('field.extime')) || $isNew) $data['extime']  = $row->get($params->get('field.extime'));

				$record_table->bind($data);
				$record_fields = json_decode($record_table->fields, TRUE);

				if(!$record_table->check_cli())
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
			$crossids = \Joomla\Utilities\ArrayHelper::toInteger($crossids);
			$sql = "SELECT id, fields, title, type_id, section_id, user_id FROM #__js_res_record WHERE id IN(" . implode(',', $crossids) . ")";
			$db->setQuery($sql);
			$list = $db->loadObjectList();

			$record_values = \Joomla\CMS\Table\Table::getInstance('Record_values', 'JoomcckTable');

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

				$user = \Joomla\CMS\Factory::getUser($item->user_id);

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
					$query = $db->getQuery(true)
						->update($db->quoteName('#__js_res_record'))
						->set($db->quoteName('fieldsdata') . ' = ' . $db->quote(strip_tags(implode(', ', $fulltext))))
						->where($db->quoteName('id') . ' = ' . (int)$item->id);
					$db->setQuery($query);
					$db->execute();
				}

				unset($fulltext, $user);
			}

			\Joomla\CMS\Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CIMPORTSUCCESS'));

		}
		catch(Exception $e)
		{
			Factory::getApplication()->enqueueMessage( $e->getMessage(), 'warning');
		}

		// Add extra statistics
		$params = new Registry($imports->params);
		$stat['preset_name'] = $imports->name;
		$stat['import_method'] = $params->get('method', 'update');
		$stat['duration'] = round(microtime(true) - $start_time, 2);

		\Joomla\CMS\Factory::getSession()->set('importstat', $stat);
		\Joomla\CMS\Factory::getApplication()->redirect(\Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=import&step=4&section_id=' . $imports->section_id, FALSE));
	}

	private function _get_cat($id)
	{
		static $categories = array();

		$id = (int)$id;
		if(!array_key_exists($id, $categories))
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName(['id', 'title']))
				->from($db->quoteName('#__js_res_categories'))
				->where($db->quoteName('id') . ' = ' . $id);
			$db->setQuery($query);

			$categories[$id] = json_encode($db->loadAssocList('id', 'title'));
		}

		return @$categories[$id];
	}

	public function analize()
	{
		\Joomla\CMS\Session\Session::checkToken('request') or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		$this->key = $this->input->get('json');
		$file      = $this->input->getString('file');

		$ext        = pathinfo($file,PATHINFO_EXTENSION);
		$this->json = JPATH_ROOT . '/tmp/' . $this->key . '.json';

		$upload = JPATH_ROOT . '/tmp/import_uploads/' . urldecode($file);

		if(!is_file($upload))
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

			if(!is_dir($dir))
			{
				\Joomla\Filesystem\Folder::create($dir);
			}

			if(!JArchive::extract($upload, $dir))
			{
				$this->_error('CIMPORTCANNOTEXTRACT');
			}

			$files = \Joomla\Filesystem\Folder::files($dir, '\.(csv|json)$', TRUE, TRUE);
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
		if(!is_file($upload))
		{
			$this->_error('CIMPORTCANNOTFINDFILE');
		}

		$this->db    = \Joomla\CMS\Factory::getDbo();
		$this->heads = array();

		$this->db->setQuery("DELETE FROM `#__js_res_import_rows` WHERE `ctime` < NOW() - INTERVAL 1 DAY OR `import` = " . $this->db->quote($this->key));
		$this->db->execute();

		$ext = strtolower(pathinfo($upload,PATHINFO_EXTENSION));
		if($ext == 'csv')
		{
			$delimiter = $this->input->get('delimiter', 'auto');
			if ($delimiter === 'auto')
			{
				$delimiter = $this->_detect_delimiter($upload);
			}
			$this->_load_csv($upload, $delimiter);
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

		\Joomla\CMS\Factory::getSession()->set('headers', $this->heads, 'import');
		\Joomla\CMS\Factory::getSession()->set('key', $this->key, 'import');

		$this->_msg(100, 'CIMPORTPARCE');

		\Joomla\CMS\Factory::getApplication()->close();
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
					$headerCount = count($header);
					$rowCount = count($row);

					if ($rowCount < $headerCount)
					{
						$row = array_pad($row, $headerCount, '');
					}
					elseif ($rowCount > $headerCount)
					{
						$row = array_slice($row, 0, $headerCount);
					}

					$data = array_combine($header, $row);
					$this->_row($data);
				}
			}
			fclose($handle);
		}
	}

	/**
	 * Auto-detect CSV delimiter by analyzing file content
	 *
	 * @param   string  $filename  Path to the CSV file
	 * @return  string  The detected delimiter character
	 */
	private function _detect_delimiter($filename)
	{
		$delimiters = [',', ';', "\t", '|'];
		$results = [];

		// Read first 5 lines for analysis
		$handle = fopen($filename, 'r');
		if ($handle === false)
		{
			return ','; // Default fallback
		}

		$lines = [];
		for ($i = 0; $i < 5 && ($line = fgets($handle)) !== false; $i++)
		{
			$lines[] = $line;
		}
		fclose($handle);

		if (empty($lines))
		{
			return ','; // Default fallback
		}

		// Count occurrences of each delimiter per line
		foreach ($delimiters as $delimiter)
		{
			$counts = [];
			foreach ($lines as $line)
			{
				$counts[] = substr_count($line, $delimiter);
			}

			// Check consistency (same count on each line) and count > 0
			$uniqueCounts = array_unique($counts);
			$consistent = count($uniqueCounts) === 1 && $counts[0] > 0;

			$results[$delimiter] = [
				'count' => array_sum($counts),
				'consistent' => $consistent,
				'avg' => $counts[0] ?? 0
			];
		}

		// Prefer consistent delimiters, then by count
		$best = ','; // Default fallback
		$bestScore = 0;

		foreach ($results as $delim => $data)
		{
			// Give much higher score to consistent delimiters
			$score = $data['consistent'] ? ($data['avg'] * 1000) : $data['count'];

			if ($score > $bestScore)
			{
				$bestScore = $score;
				$best = $delim;
			}
		}

		return $best;
	}

	private function _row($data)
	{
		if(!$data)
		{
			return;
		}

		$query = $this->db->getQuery(true)
			->insert($this->db->quoteName('#__js_res_import_rows'))
			->columns($this->db->quoteName(['import', 'text', 'ctime']))
			->values($this->db->quote($this->key) . ', ' . $this->db->quote(json_encode($data)) . ', NOW()');
		$this->db->setQuery($query);
		$this->db->execute();

		$columns     = array_keys($data);
		$this->heads = array_unique(array_merge($this->heads, $columns));
	}

	public function upload()
	{
		\Joomla\CMS\Session\Session::checkToken('request') or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		require_once JPATH_ROOT . '/components/com_joomcck/library/php/UploadHandler.php';

		$options = array(
			'accept_file_types' => '/\.(zip|json|csv)$/i',
			'upload_dir'        => JPATH_ROOT . '/tmp/import_uploads/'
		);

		\Joomla\CMS\Factory::getSession()->set('importprocess', 0);

		if(!is_dir($options['upload_dir']))
		{
			\Joomla\Filesystem\Folder::create($options['upload_dir']);
		}

		$upload = new UploadHandler($options);

		\Joomla\CMS\Factory::getApplication()->close();
	}

	private function _error($msg)
	{
		if(!empty($this->dir))
		{
			\Joomla\Filesystem\Folder::delete($this->dir);
		}
		\Joomla\Filesystem\File::write($this->json, json_encode(array(
			'error' => \Joomla\CMS\Language\Text::_($msg)
		)));
		\Joomla\CMS\Factory::getApplication()->close();
	}

	private function _msg($stat, $msg)
	{
		\Joomla\Filesystem\File::write($this->json, json_encode(array(
			'status' => $stat,
			'msg'    => \Joomla\CMS\Language\Text::_($msg)
		)));
	}

	public function preview()
	{
		\Joomla\CMS\Session\Session::checkToken('request') or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		$user   = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$app    = \Joomla\CMS\Factory::getApplication();

		// Get form data - use raw POST to ensure nested arrays are captured correctly
		$params = $app->input->post->getArray(array('import' => 'array'));
		$params = isset($params['import']) ? $params['import'] : array();

		// Fallback to direct POST if empty
		if (empty($params) && !empty($_POST['import']))
		{
			$params = $_POST['import'];
		}

		$imports = \Joomla\CMS\Table\Table::getInstance('Import', 'JoomcckTable');

		if($this->input->get('preset') == 'new')
		{
			$save = array(
				'user_id'    => $user->get('id'),
				'section_id' => $this->input->get('section_id'),
				'params'     => json_encode($params),
				'name'       => $params['name'] ?? '',
				'crossids'   => '[]'
			);
			$imports->save($save);
		}
		else
		{
			$imports->load($this->input->get('preset'));
			$imports->params = json_encode($params);
			$imports->name   = $params['name'] ?? $imports->name;
			$imports->store();
		}

		// Redirect to preview step
		$app->redirect(\Joomla\CMS\Router\Route::_(
			'index.php?option=com_joomcck&view=import&step=3' .
			'&section_id=' . $this->input->get('section_id') .
			'&type_id=' . $this->input->get('type_id') .
			'&preset=' . $imports->id,
			false
		));
	}

	public function getTypes()
	{
		\Joomla\CMS\Session\Session::checkToken('get') or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		$section_id = $this->input->getInt('section_id', 0);

		if (!$section_id)
		{
			echo json_encode([]);
			\Joomla\CMS\Factory::getApplication()->close();
		}

		$section = ItemsStore::getSection($section_id);
		$params = new Registry($section->params);
		$typeIds = $params->get('general.type', []);

		if (empty($typeIds))
		{
			echo json_encode([]);
			\Joomla\CMS\Factory::getApplication()->close();
		}

		$db = \Joomla\CMS\Factory::getDbo();
		$query = $db->getQuery(true)
			->select('id AS value, name AS text')
			->from('#__js_res_types')
			->where('published = 1')
			->where('id IN (' . implode(',', array_map('intval', $typeIds)) . ')');

		$db->setQuery($query);
		$types = $db->loadObjectList();

		echo json_encode($types ?: []);
		\Joomla\CMS\Factory::getApplication()->close();
	}

	public function deletePreset()
	{
		\Joomla\CMS\Session\Session::checkToken('get') or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		$preset_id = $this->input->getInt('preset_id', 0);
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		if (!$preset_id)
		{
			echo json_encode(['success' => false, 'message' => 'Invalid preset ID']);
			\Joomla\CMS\Factory::getApplication()->close();
		}

		$db = \Joomla\CMS\Factory::getDbo();

		// Check if preset belongs to current user
		$query = $db->getQuery(true)
			->select('id')
			->from('#__js_res_import')
			->where('id = ' . $preset_id)
			->where('user_id = ' . (int) $user->get('id'));

		$db->setQuery($query);
		$exists = $db->loadResult();

		if (!$exists)
		{
			echo json_encode(['success' => false, 'message' => 'Preset not found or access denied']);
			\Joomla\CMS\Factory::getApplication()->close();
		}

		// Delete the preset
		$query = $db->getQuery(true)
			->delete('#__js_res_import')
			->where('id = ' . $preset_id);

		$db->setQuery($query);

		if ($db->execute())
		{
			echo json_encode(['success' => true]);
		}
		else
		{
			echo json_encode(['success' => false, 'message' => 'Database error']);
		}

		\Joomla\CMS\Factory::getApplication()->close();
	}
}
