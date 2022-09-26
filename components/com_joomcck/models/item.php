<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('mint.mvc.model.admin');

class JoomcckModelItem extends MModelAdmin
{

	public function getTable($type = 'Record', $prefix = 'JoomcckTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}


	public function getForm($data = array(), $loadData = true) {
		return false;
	}

	public function getFormChCo($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();

		$form = $this->loadForm('com_joomcck.itemchco', 'itemchco', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form))
		{
			return FALSE;
		}

		return $form;
	}

	public function reset($pks, $value = 0)
	{
		if(!$value)
		{
			$this->setError(JText::_('C_MSG_NOTASK'));
			return false;
		}
		// Sanitize the ids.
		$pks = (array) $pks;
		\Joomla\Utilities\ArrayHelper::toInteger($pks);

		if (empty($pks)) {
			$this->setError(JText::_('C_MSG_SELECTITEM'));
			return false;
		}

		try {
			$db = $this->getDbo();

			$ids = implode(",", $pks);
			$now = JFactory::getDate()->toSql();
			$sql = array();
			switch ($value)
			{
				case 'reset_hits':
					$sql[] = "DELETE FROM #__js_res_hits WHERE record_id IN ($ids)";
					$sql[] = "UPDATE #__js_res_record SET hits = '0'  WHERE id IN ($ids)";
					break;
				case 'reset_com':
					$sql[] = "DELETE FROM #__js_res_comments WHERE record_id IN ($ids)";
					$sql[] = "UPDATE #__js_res_record SET comments = '0'  WHERE id IN ($ids)";
					break;
				case 'reset_vote':
					$sql[] = "DELETE FROM #__js_res_vote WHERE ref_type = 'record' AND ref_id IN ($ids)";
					$sql[] = "UPDATE #__js_res_record SET votes = 0, multirating = '', votes_result = 0  WHERE id IN ($ids)";
					foreach ($pks AS $id)
					{
						$ses = JFactory::getSession();
						$ses->set("record_rate_{$id}_0", 0);
						$ses->set("record_rate_{$id}_1", 0);
						$ses->set("record_rate_{$id}_2", 0);
						$ses->set("record_rate_{$id}_3", 0);
						$ses->set("record_rate_{$id}_4", 0);
						$ses->set("record_rate_{$id}_5", 0);
						$ses->set("record_rate_{$id}_6", 0);
						$ses->set("record_rate_{$id}_7", 0);
						$ses->set("record_rate_{$id}_8", 0);

						$config = JFactory::getConfig();
						$cookie_domain = $config->get('cookie_domain', '');
						$cookie_path = $config->get('cookie_path', '/');
						setcookie("record_rate_{$id}_0", 0, time() + 365 * 86400, $cookie_path, $cookie_domain);
						setcookie("record_rate_{$id}_1", 0, time() + 365 * 86400, $cookie_path, $cookie_domain);
						setcookie("record_rate_{$id}_2", 0, time() + 365 * 86400, $cookie_path, $cookie_domain);
						setcookie("record_rate_{$id}_3", 0, time() + 365 * 86400, $cookie_path, $cookie_domain);
						setcookie("record_rate_{$id}_4", 0, time() + 365 * 86400, $cookie_path, $cookie_domain);
						setcookie("record_rate_{$id}_5", 0, time() + 365 * 86400, $cookie_path, $cookie_domain);
						setcookie("record_rate_{$id}_6", 0, time() + 365 * 86400, $cookie_path, $cookie_domain);
						setcookie("record_rate_{$id}_7", 0, time() + 365 * 86400, $cookie_path, $cookie_domain);
						setcookie("record_rate_{$id}_8", 0, time() + 365 * 86400, $cookie_path, $cookie_domain);
					}
					break;
				case 'reset_fav':
					$sql[] = "DELETE FROM #__js_res_favorite WHERE record_id IN ($ids)";
					$sql[] = "UPDATE #__js_res_record SET favorite_num = 0 WHERE id IN ($ids)";
					break;
				case 'reset_ctime':
					$sql[] = "UPDATE #__js_res_record SET ctime = '{$now}' WHERE id IN ($ids)";
					break;
				case 'reset_mtime':
					$sql[] = "UPDATE #__js_res_record SET mtime = '{$now}' WHERE id IN ($ids)";
					break;
				case 'reset_extime':
					$sql[] = "UPDATE #__js_res_record SET extime = '0000-00-00 00:00:00' WHERE id IN ($ids)";
					break;
			}

			foreach ($sql AS $s)
			{
				$db->setQuery($s);
				$db->query();
			}

			if (!$db->query()) {
				throw new Exception($db->getErrorMsg());
			}

		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}

		return true;
	}

	public function copy($pks)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		\Joomla\Utilities\ArrayHelper::toInteger($pks);

		if (empty($pks)) {
			$this->setError(JText::_('COM_JOOMCCK_NO_ITEM_SELECTED'));
			return false;
		}

		try {
			$db = $this->getDbo();
			$record_table = JTable::getInstance('Record', 'JoomcckTable');
			foreach($pks AS $pk)
			{
				$record_table->load($pk);

				$record_table->id = null;
				$record_table->title .= ' - Copy';
				$record_table->version = 1;
				$record_table->hits = 0;
				$record_table->votes = 0;
				$record_table->votes_result = 0;
				$record_table->favorite_num = 0;
				$record_table->comments = 0;
				$record_table->multirating = null;
				$record_table->subscriptions_num = 0;

				if($record_table->store())
				{
					//copy record values
					$sql = "SELECT * FROM #__js_res_record_values WHERE record_id = {$pk}";
					$db->setQuery($sql);
					$record_values = $db->loadAssocList();
					if($record_values)
					{
						$table = JTable::getInstance('Record_values', 'JoomcckTable');
						$this->_copyRecordDetails($record_values, $table, $record_table->id);
					}

					// copy record category
					$sql = "SELECT * FROM #__js_res_record_category WHERE record_id = {$pk}";
					$db->setQuery($sql);
					$record_values = $db->loadAssocList();
					if($record_values)
					{
						$table = JTable::getInstance('Record_category', 'JoomcckTable');
						$this->_copyRecordDetails($record_values, $table, $record_table->id);
					}

					// copy tags
					$sql = "SELECT * FROM #__js_res_tags_history WHERE record_id = {$pk}";
					$db->setQuery($sql);
					$record_values = $db->loadAssocList();
					if($record_values)
					{
						$table = JTable::getInstance('Taghistory', 'JoomcckTable');
						$this->_copyRecordDetails($record_values, $table, $record_table->id);
					}

					//copy files
					$sql = "SELECT * FROM #__js_res_files WHERE record_id = {$pk}";
					$db->setQuery($sql);
					$files = $db->loadAssocList();
					if(!empty($files))
					{
						$params = JComponentHelper::getParams('com_joomcck');
						$table = JTable::getInstance('Files', 'JoomcckTable');
						$field_table = JTable::getInstance('Field', 'JoomcckTable');
						foreach ($files as $file)
						{

							$field_table->load($file['field_id']);
							$field_params = new JRegistry($field_table->params);
							$subfolder = $field_params->get('params.subfolder', $field_table->field_type);

// 							$table->load($rv);
							$time = time();
							$date = date($params->get('folder_format'), $time);
							$dest = JPATH_ROOT. DIRECTORY_SEPARATOR .$params->get('general_upload'). DIRECTORY_SEPARATOR .$subfolder. DIRECTORY_SEPARATOR .$date.DIRECTORY_SEPARATOR;

							if(!JFolder::exists($dest))
							{
								JFolder::create($dest, 755);
							}
							$file['id'] = null;
							$parts = explode('_', $file['filename']);
							$file['filename'] = $time.'_'.$parts[1];

							$copied = JFile::copy(JPATH_ROOT. DIRECTORY_SEPARATOR .$params->get('general_upload'). DIRECTORY_SEPARATOR .$subfolder. DIRECTORY_SEPARATOR .$file['fullpath'], $dest.$file['filename']);

							$root = JPath::clean(JPATH_ROOT. DIRECTORY_SEPARATOR .$params->get('general_upload'));
							$url = str_replace(JPATH_ROOT, '', $root);
							$url = str_replace("\\", '/', $url);
							$file['fullpath'] = JPath::clean($subfolder. DIRECTORY_SEPARATOR .$date. DIRECTORY_SEPARATOR .$file['filename']);
							$metadata = '';
							if(function_exists('exif_read_data'))
							{
								$metadata = exif_read_data(JPATH_ROOT. DIRECTORY_SEPARATOR .$params->get('general_upload'). DIRECTORY_SEPARATOR .$subfolder. DIRECTORY_SEPARATOR .$file['fullpath']);
							}
							$file['params'] = json_encode($metadata);
							$file['record_id'] = $record_table->id;
							$file['hits'] = 0;
							$table->bind($file);
							$table->store();
							$table->reset();
							$table->id = null;
						}
					}
				}
			}

		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}

		return true;
	}

	private function _copyRecordDetails($record_values, $table, $new_record_id)
	{
		foreach ($record_values AS $rv)
		{
			foreach ($rv AS $key => $value)
			{
				if($key == 'id')
				{
					$data[$key] = null;
					continue;
				}
				if($key == 'record_id')
				{
					$data[$key] = $new_record_id;
					continue;
				}
				$data[$key] = $value;
			}
			$table->bind($data);
			$table->store();
			$table->reset();
			$table->id = null;
		}
	}

}