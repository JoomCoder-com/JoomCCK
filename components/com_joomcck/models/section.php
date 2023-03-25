<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
jimport('mint.mvc.model.admin');

class JoomcckModelSection extends MModelAdmin
{
	public function __construct($config = array())
	{
		$this->option = 'com_joomcck';
		parent::__construct($config);
	}

	public function getTable($type = 'Section', $prefix = 'JoomcckTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}


	public function getForm($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();

		$form = $this->loadForm('com_joomcck.section', 'section', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form))
		{
			return FALSE;
		}

		return $form;
	}

	public function getFormQs($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();

		$form = $this->loadForm('com_joomcck.section-qs', 'section-qs', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form))
		{
			return FALSE;
		}

		return $form;
	}

	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_joomcck.section.edit.'.$this->getName().'.data', array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	public function save($data)
	{
		return parent::save($data);
	}

	protected function canDelete($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.delete', 'com_joomcck.section.' . (int)$record->id);
	}

	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.edit.state', 'com_joomcck.section.' . (int)$record->id);
	}

	public function getItem($id = NULL)
	{
		static $cache = array();

		if(!$id)
		{
			$id = JFactory::getApplication()->input->getInt('section_id');
		}

		if(isset($cache[$id]))
		{
			return $cache[$id];
		}

		$section = parent::getItem($id);
		if($section)
		{
			$section->params = new JRegistry($section->params);

			if($section->params->get('personalize.personalize') && $section->params->get('events.subscribe_user'))
			{
				$section->params->set('events.subscribe_section', 0);
			}

			if(!$section->categories)
			{
				$section->params->set('events.subscribe_category', 0);
			}

			$descr                 = JText::_($section->description);
			$descr                 = (!empty($section->description) ? JHtml::_('content.prepare', $descr) : '');
			$descr                 = preg_split('#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i', $descr, 2);
			$section->descr_before = @$descr[0];
			$section->descr_after  = @$descr[1];
			$section->descr_full   = implode($descr);
			$section->link         = Url::records($section);
			$section->name         = JText::_($section->name);
		}
		$cache[$id] = $section;

		return $cache[$id];
	}

	public function countUserRecords($section_id, $type_id = NULL, $byday = FALSE)
	{
		$user = JFactory::getUser();

		$query = $this->_db->getQuery(TRUE);

		$query->select('count(*)');
		$query->from('#__js_res_record');
		$query->where('section_id = ' . $section_id);
		if($type_id)
		{
			$query->where('type_id = ' . $type_id);
		}
		if($byday)
		{
			$start = $this->_db->quote(date('Y-m-d 00:00:00'));
			$end   = $this->_db->quote(date('Y-m-d 23:59:59'));
			$query->where("ctime BETWEEN $start AND $end");
		}
		$query->where('user_id = ' . $user->get('id'));
		$this->_db->setQuery($query);

		return $this->_db->loadResult();

	}

	public function getSectionTypes($id)
	{
		$section = $this->getItem($id);

		$ids = implode(',', $section->params->get('general.type'));

		if(!$ids)
		{
			$this->setError('There is no type selected in section parameters');

			return FALSE;
		}

		$query = $this->_db->getQuery(TRUE);

		$query->select('id, name, description, params');
		$query->from('#__js_res_types');
		$query->where('published = 1');
		$query->where("id IN({$ids})");

		$this->_db->setQuery($query);

		$types = $this->_db->loadObjectList();

		if(!$types)
		{
			$this->setError('Types not found');

			return FALSE;
		}

		return $types;
	}
}