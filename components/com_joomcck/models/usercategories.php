<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
jimport('mint.mvc.model.list');

class JoomcckModelUsercategories extends MModelList
{
    
    public function __construct($config = array()) {
		if (empty ( $config ['filter_fields'] )) {
			$config ['filter_fields'] = 
			array ('name', 'ordering', 'access_level', 'published', 'id');
		}
		
		parent::__construct ( $config );
	}
	    
	public function getList($user_id, $section_id)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		$query->select('*');
		$query->from('#__js_res_category_user ');
		$query->where('user_id = '.$user_id);
		$query->where('section_id = '.$section_id);
		
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->escape('ordering ASC'));
		
		$db->setQuery($query);
		return $db->loadObjectList('id');
		
	}
	public function getListQuery()
	{
		$section_id = $this->getState('usercategories.section_id');
		
		if(!$section_id)
		{
			$this->setError(JText::_('CNOSECTION'));
			return FALSE;
		}
		$user = JFactory::getUser();
		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		$query->select('cu.*');
		$query->from('#__js_res_category_user AS cu');
		$query->select('vl.title as access_level');
		$query->leftJoin('#__viewlevels AS vl ON cu.access = vl.id');
		$query->where('cu.user_id = '.$user->get('id'));
		$query->where('cu.section_id = '.$section_id);
		
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
//		$query->order($db->escape('cu.ordering ASC'));
		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
	
	public function getStoreId($id = NULL)
	{
		$id	.= ':'.$this->getState('list.start');
		$id	.= ':'.$this->getState('list.limit');
		$id	.= ':'.$this->getState('list.ordering');
		$id	.= ':'.$this->getState('list.direction');

		return md5($this->context.':'.$id);
	}
	
    protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();
		
		$section = $app->getUserStateFromRequest($this->context.'.usercategories.section_id', 'section_id', null, 'int');
		$this->setState('usercategories.section_id', $section);
		
		$return = $app->getUserStateFromRequest($this->context.'.usercategories.return', 'return', null, 'string');
		$this->setState('usercategories.return', $return);
		
		parent::populateState('cu.ordering', 'asc');
	}
	
}
