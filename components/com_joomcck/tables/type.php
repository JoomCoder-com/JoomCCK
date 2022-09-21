<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.table.table');
jimport('legacy.access.rules');

/**
 * @package JCommerce
 */

class JoomcckTableType extends JTable
{

	public $_trackAssets = 1;

	public function __construct(&$_db)
	{
		parent::__construct('#__js_res_types', 'id', $_db);
	}

	public function bind($array, $ignore = '')
	{
		$params = JRequest::getVar('params', array(), 'post', 'array');
		if($params)
		{
			$registry = new JRegistry();
			$registry->loadArray($params);
			$array['params'] = (string)$registry;
		}

		if(isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}

	public function delete($pk = null)
	{
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		if($pk)
		{
			$this->_db->setQuery("DELETE FROM #__js_res_fields WHERE type_id = $pk");
			$this->_db->execute();

			$this->_db->setQuery("DELETE FROM #__js_res_fields_group WHERE type_id = $pk");
			$this->_db->execute();

			$this->_db->setQuery("select id FROM #__js_res_record WHERE type_id = $pk");
			$records = $this->_db->loadColumn();

			ArrayHelper::clean_r($records);
			JArrayHelper::toInteger($records);

			if($records)
			{
				$this->_db->setQuery("DELETE FROM #__js_res_record WHERE type_id = $pk");
				$this->_db->execute();

				$this->_db->setQuery("DELETE FROM #__js_res_favorite WHERE type_id = $pk");
				$this->_db->execute();

				$this->_db->setQuery("DELETE FROM #__js_res_record_values WHERE type_id = $pk");
				$this->_db->execute();

				$this->_db->setQuery("DELETE FROM #__js_res_favorite WHERE type_id = $pk");
				$this->_db->execute();

				$this->_db->setQuery("DELETE FROM #__js_res_audit_log WHERE type_id = $pk");
				$this->_db->execute();

				$this->_db->setQuery("DELETE FROM #__js_res_audit_restore WHERE type_id = $pk");
				$this->_db->execute();

				$this->_db->setQuery("DELETE FROM #__js_res_audit_versions WHERE type_id = $pk");
				$this->_db->execute();


				//$this->_db->setQuery("DELETE FROM #__js_res_hits WHERE type_id = $pk");
				//$this->_db->execute();

				$this->_db->setQuery("DELETE FROM #__js_res_sales WHERE type_id = $pk");
				$this->_db->execute();

				$this->_db->setQuery("DELETE FROM #__js_res_comments WHERE type_id = $pk");
				$this->_db->execute();

				$this->_db->setQuery("DELETE FROM #__js_res_subscribe WHERE type = 'record' AND ref_id IN(".implode(',', $records).")");
				$this->_db->execute();

				$this->_db->setQuery("DELETE FROM #__js_res_tags_history WHERE record_id IN(".implode(',', $records).")");
				$this->_db->execute();

				$this->_db->setQuery("DELETE FROM #__js_res_notifications WHERE ref_1 IN(".implode(',', $records).")");
				$this->_db->execute();

				$this->_db->setQuery("SELECT * FROM #__js_res_files WHERE type_id = $pk");
				$files = $this->_db->loadObjectList();
				if(!empty($files))
				{
					$field_table = JTable::getInstance('Field', 'JoomcckTable');
					$params = JComponentHelper::getParams('com_joomcck');
					foreach ($files as $file)
					{
						$field_table->load($file->field_id);
						$field_params = new JRegistry($field_table->params);
						$subfolder = $field_params->get('params.subfolder', $field_table->field_type);
						$dest = JPATH_ROOT. DIRECTORY_SEPARATOR .$params->get('general_upload'). DIRECTORY_SEPARATOR .$subfolder. DIRECTORY_SEPARATOR . $file->fullpath;

						if(JFile::exists($dest))
						{
							JFile::delete($dest);
						}
					}

					$this->_db->setQuery("DELETE FROM #__js_res_files WHERE type_id = $pk");
					$this->_db->execute();
				}

			}
		}

		return parent::delete($pk);
	}

	public function check()
	{
		if(trim($this->name) == '')
		{
			$this->setError(JText::_('C_MSG_NONAME'));
			return false;
		}

		if(trim($this->form) == '')
		{
			$this->form = 'form-' . md5($this->name) . time();
		}

		if(trim($this->user_id) == '')
		{
			$this->user_id = (int)JFactory::getUser()->get('id');
		}

		return true;
	}

	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_joomcck.type.' . (int)$this->$k;
	}

	protected function _getAssetTitle()
	{
		return 'Joomcck Content Type: ' . $this->name;
	}

	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;

		$db = $this->getDbo();

		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__assets');
		$query->where("name = 'com_joomcck'");

		// Get the asset id from the database.
		$db->setQuery($query);
		if($result = $db->loadResult())
		{
			$assetId = (int)$result;
		}

		// Return the asset id.
		if($assetId)
		{
			return $assetId;
		}
		else
		{
			return parent::_getAssetParentId($table, $id);
		}
	}
}
?>
