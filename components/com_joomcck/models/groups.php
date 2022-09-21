<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport('mint.mvc.model.list');

class JoomcckModelGroups extends MModelList
{

	public function __construct($config = array()) {
		if (empty ( $config ['filter_fields'] )) {
			$config ['filter_fields'] =
			array ( 'g.ordering');
		}
		$this->option = 'com_joomcck';
		parent::__construct ( $config );
	}

	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');

		$type = $app->getUserStateFromRequest($this->context.'.groups.type', 'type_id', JRequest::getInt('type_id', 0), 'int');
		$this->setState('groups.type', $type);


		parent::populateState('g.ordering', 'asc');
	}

	protected function getStoreId($id = '')
	{
		$id .= ':' . $this->getState('groups.type');

		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('g.*');
		$query->from('#__js_res_fields_group AS g');

		$query->where('g.type_id = '.(int)$this->getState('groups.type'));

		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
		//$query->group('t.id');

//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}


}
