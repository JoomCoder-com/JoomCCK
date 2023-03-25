<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
jimport('mint.mvc.model.list');

class JoomcckModelProducts extends MModelList
{
    public $worns = array();
    
	public function getListQuery()
	{
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$orders_model = MModelBase::getInstance('Orders', 'JoomcckModel');
		
		$db->setQuery('SELECT t.* FROM #__js_res_types AS t WHERE t.published = 1 AND t.id IN(SELECT type_id FROM #__js_res_fields WHERE published = 1 AND field_type LIKE "pay%")');
		$types = $db->loadObjectList();
		$all_types = array(0);
		
		foreach ($types AS $type)
		{
			$type->params = new JRegistry($type->params);
			if(!in_array($type->params->get('submission.submission'), $user->getAuthorisedViewLevels()) && !$orders_model->isSuperUser($user->get('id')))
			{
				continue;
			}
			
			$all_types[] = $type->id;
			@$this->types[$type->id]->id = $type->id;
			@$this->types[$type->id]->name = $type->name;
		}
		
		$query = $db->getQuery(true);
		
		$query->select('r.*');
		$query->from('#__js_res_record AS r');
		$query->where('r.published = 1');
		$query->where('r.type_id IN ('.implode(',', $all_types).')');
		if(!$orders_model->isSuperUser($user->get('id')))
		{
			if($secmod = MECAccess::allowNewSales($user))
			{
				$query->where('(r.user_id = '.$user->get('id').' OR r.section_id IN('.implode(',', $secmod).'))');
			}
			else
			{
				$query->where('r.user_id = '.$user->get('id'));
			}
		}

		$search = $this->getState ( 'filter.search', 0 );
		if ($search) {
			$search = $db->Quote ( '%' . $db->escape ( $search, true ) . '%' );
			$query->where ( '(r.title LIKE ' . $search . ')' );
		}
		
		$filter_type = $this->getState ( 'filter.type', 0 );
		if ($filter_type) {
			$query->where ('r.type_id = ' . $filter_type);
		}
		
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		
		return $query;
	}
	
	public function getTable($name = '', $prefix = 'Table', $options = array()){
		return JTable::getInstance('Sales', 'JoomcckTable');
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
		
		$search = $app->getUserStateFromRequest ( $this->context . '.filter.search', 'filter_search' );
		$this->setState ( 'filter.search', $search );
		$type = $app->getUserStateFromRequest ( $this->context . '.filter.type', 'filter_type' );
		$this->setState ( 'filter.type', $type );
		
		parent::populateState('ctime', 'desc');
	}
	
	public function getWorns()
	{
		return $this->worns;
	}
}
