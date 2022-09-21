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

class JoomcckModelUsercategory extends MModelAdmin
{
	
	function getTable($name = 'Usercategory', $prefix = 'JoomcckTable', $options = array())
	{
		return JTable::getInstance($name, $prefix, $options);
	
	}
	
	public function getForm($data = array(), $loadData = true)
	{
	    $app = JFactory::getApplication();
		
		$form = $this->loadForm('com_joomcck.usercategory', 'usercategory', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form))
		{
			return false;
		}
		return $form;
	}
	
	public function loadFormData()
	{
	    $data = JFactory::getApplication()->getUserState('com_joomcck.edit.usercategory.data', array());
		if(empty($data))
		{
			$data = $this->getItem();
			if(isset($data->params))
			{
				$data->params = $data->params->toArray();
			}
		}
		
		return $data;
	}
	
    protected function populateState($ordering = null, $direction = null)
	{
		// Load state from the request.
		$pk = JFactory::getApplication()->input->getInt('id');
		JFactory::getApplication()->setUserState('com_joomcck.edit.usercategory.id',  $pk);
		$this->setState('com_joomcck.edit.usercategory.id', $pk);
	}
	
	public function getItem($id = NULL)
	{
		static $cache = array();
		
	    $id = (int) ($id ? $id : $this->getState('com_joomcck.edit.usercategory.id'));
		
	    if(isset($cache[$id]))
	    {
	    	return $cache[$id];
	    }
	    
		if($id)
		{
    		$cache[$id] = parent::getItem($id);
    		if($cache[$id])
    		$cache[$id]->params = new JRegistry($cache[$id]->params);
		}
		else
		{
		    $user_category = new stdClass();
		    $user_category->section_id = JFactory::getApplication()->input->getInt('section_id');
		    return $user_category;
		}
		return $cache[$id];
	}
	
	public function saveorder($pks = null, $order = null)
	{
		$table = $this->getTable();
		$app = JFactory::getApplication();
		$section_id = $app->getUserStateFromRequest('com_joomcck.usercategories.section_id', 'section_id', null, 'int');
		$user = JFactory::getUser();
		
		$table->reorder('section_id ='. $section_id . ' AND user_id ='. $user->get('id'));
	}
	
}