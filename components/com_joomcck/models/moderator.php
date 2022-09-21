<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
jimport('mint.mvc.model.admin');

class JoomcckModelModerator extends MModelAdmin
{

	function getTable($name = 'Moderators', $prefix = 'JoomcckTable', $options = array())
	{
		return JTable::getInstance($name, $prefix, $options);

	}

	public function getForm($data = array(), $loadData = TRUE)
	{
		$app = JFactory::getApplication();

		$form = $this->loadForm('com_joomcck.moderator', 'moderator', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form))
		{
			return FALSE;
		}

		return $form;
	}

	public function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_joomcck.edit.moderator.data', array());
		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	protected function populateState($ordering = NULL, $direction = NULL)
	{
		// Load state from the request.
		$pk = JFactory::getApplication()->input->getInt('id');
		JFactory::getApplication()->setUserState('com_joomcck.edit.moderator.id', $pk);
		$this->setState('com_joomcck.edit.moderator.id', $pk);
	}

	public function getItem($id = NULL)
	{
		$id  = JFactory::getApplication()->input->getInt('id', FALSE);
		$row = NULL;
		if($id)
		{
			$row = parent::getItem($id);

			$row->allow               = isset($row->params['allow']) ? $row->params['allow'] : NULL;
			$row->category_limit_mode = isset($row->params['category_limit_mode']) ? $row->params['category_limit_mode'] : NULL;
			$row->category            = isset($row->params['category']) ? $row->params['category'] : array();
		}

		if(!$row)
		{
			$row = $this->getTable();
			$row->load(array('user_id' => JFactory::getApplication()->input->getInt('user_id'), 'section_id' => JFactory::getApplication()->input->getInt('section_id')));
			if($row->id)
			{
				$registry = new JRegistry;
				$registry->loadString($row->params);
				$row->params = $registry->toArray();
			}
		}
		if(!$row->id)
		{
			$row             = new stdClass();
			$row->id         = NULL;
			$row->user       = JFactory::getUser(JFactory::getApplication()->input->getInt('user_id'));
			$row->user_id    = $row->user->get('id');
			$row->section_id = JFactory::getApplication()->input->getInt('section_id');
			$row->params     = array();
		}
		else
		{
			$row->user = JFactory::getUser($row->user_id);
		}

		if(!$id)
		{
			JFactory::getApplication()->input->set('id', $row->id);
			$this->populateState($ordering = NULL, $direction = NULL);
		}

		return $row;
	}

	public function save($data)
	{
		$data['is_moderator'] = isset($data['params']['allow_moderators']) ? 1 : 0;
		if(empty($data['params']))
		{
			$data['params'] = '';
		}

		return parent::save($data);
	}
}