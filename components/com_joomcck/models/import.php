<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
jimport('mint.mvc.model.list');

class JoomcckModelImport extends MModelList
{
	public function getPresets()
	{
		$this->_db->setQuery("SELECT id as value, name as text FROM #__js_res_import WHERE section_id = " .
			\Joomla\CMS\Factory::getApplication()->input->get('section_id') . " AND user_id = " . \Joomla\CMS\Factory::getApplication()->getIdentity()->get('id', 0));

		return $this->_db->loadObjectList();
	}

	public function getPreset()
	{
		$preset_id = (int)\Joomla\CMS\Factory::getApplication()->input->get('preset');

		if (!$preset_id)
		{
			return NULL;
		}

		$this->_db->setQuery("SELECT * FROM #__js_res_import WHERE id = " . $preset_id);
		$preset = $this->_db->loadObject();

		if(!$preset)
		{
			return NULL;
		}

		// Handle empty or invalid params
		$params_data = $preset->params;
		if (empty($params_data) || $params_data === '[]' || $params_data === '{}')
		{
			$params_data = '{}';
		}

		$preset->params = new \Joomla\Registry\Registry($params_data);

		// Ensure name is set from params if available
		if (!$preset->params->get('name') && $preset->name)
		{
			$preset->params->set('name', $preset->name);
		}

		return $preset;
	}
}
