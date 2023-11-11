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
		$this->_db->setQuery("SELECT * FROM #__js_res_import WHERE id = " . (int)\Joomla\CMS\Factory::getApplication()->input->get('preset'));
		$preset = $this->_db->loadObject();

		if(!@preset)
		{
			return NULL;
		}

		@$preset->params = new \Joomla\Registry\Registry(@$preset->params);

		return $preset;
	}
}
