<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
jimport('mint.mvc.model.admin');


class JoomcckModelComm extends MModelAdmin
{
	public function __construct($config = array())
	{
		$this->option = 'com_joomcck';
		parent::__construct($config);
	}

	public function getTable($type = 'Cobcomments', $prefix = 'JoomcckTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();

		$form = $this->loadForm('com_joomcck.comment', 'comm', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form))
		{
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_joomcck.edit.comment.data', array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {
			if(!is_array($item->params))
			{
				$registry = new JRegistry;
				$registry->loadString((string)$item->params);
				$item->params = $registry->toArray();
			}
		}

		return $item;
	}

	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.delete', 'com_joomcck.filter.'.(int) $record->id);
	}

	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.edit.state', 'com_joomcck.filter.'.(int) $record->id);
	}
}