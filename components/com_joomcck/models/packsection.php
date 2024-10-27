<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
jimport('mint.mvc.model.admin');

class JoomcckModelPacksection extends MModelAdmin
{
	public function __construct($config = array())
	{
		$this->option = 'com_joomcck';
		parent::__construct($config);
	}

	public function getTable($type = 'Packs_sections', $prefix = 'JoomcckTable', $config = array())
	{
		return \Joomla\CMS\Table\Table::getInstance($type, $prefix, $config);
	}


	public function getForm($data = array(), $loadData = TRUE)
	{
		$app = \Joomla\CMS\Factory::getApplication();

		$form = $this->loadForm('com_joomcck.packsection', 'packsection', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form))
		{
			return FALSE;
		}

		return $form;
	}

	protected function loadFormData()
	{
		$data = \Joomla\CMS\Factory::getApplication()->getUserState('com_joomcck.edit.' . $this->getName() . '.data', array());

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
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		return $user->authorise('core.delete', 'com_joomcck.packsection.' . (int)$record->id);
	}

	protected function canEditState($record)
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		return $user->authorise('core.edit.state', 'com_joomcck.packsection.' . (int)$record->id);
	}

	public function populateState($ordering = NULL, $direction = NULL)
	{
		$app = \Joomla\CMS\Factory::getApplication('administrator');

		$pack = $app->getUserStateFromRequest('com_joomcck.packsections.pack', 'pack_id', 0, 'int');
		$this->setState('pack', $pack);

		parent::populateState();
	}

	public function getSectionForm($section_id, $default = array())
	{
		MModelBase::addIncludePath(JPATH_ROOT . '/components/com_joomcck/models');
		$file = JPATH_COMPONENT. '/models/forms/packtype.xml';
		if(!is_file($file))
		{
			echo "File not found: {$file}";
		}
		$section = ItemsStore::getSection($section_id);

		if(!is_object($section->params))
		{
			$section->params = new \Joomla\Registry\Registry($section->params);
		}

		$types = $section->params->get('general.type');
		settype($types, 'array');

		if(!count($types))
		{
			return \Joomla\CMS\Language\Text::_('CNOTYPES');
		}


		return \Joomcck\Layout\Helpers\Layout::render('admin.packer.sectionForm', ['types' => $types, 'default' => $default,'file' => $file]);
	}

	private function _getTypeFieldNames($type_id)
	{
		if(!$type_id)
		{
			return array();
		}

		$db    = \Joomla\CMS\Factory::getDbo();
		$query = 'SELECT label FROM #__js_res_fields WHERE type_id = ' . $type_id;
		$db->setQuery($query);

		return $db->loadRowList();
	}
}