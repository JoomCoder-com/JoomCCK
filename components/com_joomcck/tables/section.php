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

class JoomcckTableSection extends JTable
{

	public function __construct(&$_db)
	{
		parent::__construct('#__js_res_sections', 'id', $_db);
		$this->_option = 'com_joomcck';
	}

	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return $this->_option . '.section.' . (int)$this->$k;
	}

	protected function _getAssetTitle()
	{
		return $this->title;
	}

	protected function _getAssetParentId(JTable $table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;
		$db = $this->getDbo();

		if($assetId === null)
		{
			// Build the query to get the asset id for the parent category.
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from('#__assets');
			$query->where('name = ' . $db->quote('com_joomcck.' . $this->id));

			// Get the asset id from the database.
			$db->setQuery($query);
			if($result = $db->loadResult())
			{
				$assetId = (int)$result;
			}
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

	public function bind($array, $ignore = '')
	{
		$params = JFactory::getApplication()->input->get('params', array(), 'array');
		if($params)
		{
			$registry = new JRegistry();
			$registry->loadArray($params);
			$array['params'] = (string)$registry;
		}

		// Bind the rules.
		if(isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}
		return parent::bind($array, $ignore);
	}

	public function store($updateNulls = false)
	{
		parent::store($updateNulls);
		$this->reorder();
		return count($this->getErrors()) == 0;
	}

	public function check()
	{
		if(trim($this->name) == '')
		{
			$this->setError(JText::_('C_MSG_NONAME'));
			return false;
		}
		$this->alias = trim($this->alias);
		if(empty($this->alias))
		{
			$this->alias = $this->title ? $this->title : $this->name;
		}

		$this->alias = \Joomla\CMS\Application\ApplicationHelper::stringURLSafe($this->alias);
		if(trim(str_replace('-', '', $this->alias)) == '')
		{
			$this->alias = JFactory::getDate()->format('Y-m-d-H-i-s');
		}
		return true;
	}

	public function delete($pk = null)
	{
		$res = parent::delete($pk);
		if($res)
		{
			$db = $this->getDbo();
			$query = 'SELECT id FROM #__js_res_categories WHERE section_id = ' . $pk;
			$db->setQuery($query);
			$result = $db->loadObjectList();
			if($result)
			{
				$cat_table = JTable::getInstance('CobCategory', 'JoomcckTable');
				foreach ($result as $catid)
				{
					$cat_table->load($catid->id);
					$cat_table->delete($catid->id);
				}
			}
		}

		return $res;
	}
}
?>
