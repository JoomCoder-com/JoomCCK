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

class JoomcckModelTemplate extends MModelAdmin
{

	public function __construct($config = array())
	{
		$this->option = 'com_joomcck';
		parent::__construct($config);
	}

	public function getTable($type = 'Type', $prefix = 'JoomcckTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = TRUE)
	{
		$app = JFactory::getApplication();

		$form = $this->loadForm('com_joomcck.template', 'template', array(
			'control'   => 'jform',
			'load_data' => $loadData
		));
		if(empty($form))
		{
			return FALSE;
		}

		return $form;
	}

	protected function loadFormData()
	{
		$ext  = JFactory::getApplication()->input->get('ext');
		$data = JFactory::getApplication()->getUserState('com_joomcck.edit' . $ext . '.template.data', array());

		if(empty($data))
		{
			$file           = explode('.', JFactory::getApplication()->input->get('file'));
			$data['ident']  = JFactory::getApplication()->input->get('file');
			$file_name = JoomcckTmplHelper::getTmplFile($file[1], $file[0]) . '.' . $ext;
			$data['source'] = '';
			if(JFile::exists($file_name))
			{
				$data['source'] = file_get_contents($file_name);
			}
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

		return $user->authorise('core.delete', 'com_joomcck.template.' . (int)$record->id);
	}

	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.edit.state', 'com_joomcck.template.' . (int)$record->id);
	}
}