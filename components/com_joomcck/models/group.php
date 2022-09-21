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

class JoomcckModelGroup extends MModelAdmin
{
	public function __construct($config = array())
	{
		$this->option = 'com_joomcck';
		parent::__construct($config);
	}

	public function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');

		$type = $app->getUserStateFromRequest('com_joomcck.groups.groups.type', 'type_id', 0, 'int');
		$this->setState('groups.type', $type);

		$r = $app->getUserStateFromRequest('com_joomcck.groups.groups.return', 'return', '', 'string');
		$this->setState('groups.return', $r);

		parent::populateState($ordering = null, $direction = null);
	}

	public function getTable($type = 'Group', $prefix = 'JoomcckTable', $config = array())
	{
		$db = JFactory::getDbo();
		include_once __DIR__.'/../tables/group.php';
		return new JoomcckTableGroup($db);
	}

	public function getForm($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();

		$form = $this->loadForm('com_joomcck.group', 'group', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form))
		{
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_joomcck.edit.group.data', array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}


	protected function getReorderConditions($table)
	{
		return array('type_id = ' . $table->type_id);
	}

	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.delete', 'com_joomcck.group.'.(int) $record->id);
	}

	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.edit.state', 'com_joomcck.group.'.(int) $record->id);
	}
}