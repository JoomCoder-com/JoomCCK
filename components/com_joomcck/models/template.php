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

class JoomcckModelTemplate extends MModelAdmin
{

	public function __construct($config = array())
	{
		$this->option = 'com_joomcck';
		parent::__construct($config);
	}

	public function getTable($type = 'Type', $prefix = 'JoomcckTable', $config = array())
	{
		return \Joomla\CMS\Table\Table::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = TRUE)
	{
		$app = \Joomla\CMS\Factory::getApplication();

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
		$ext  = \Joomla\CMS\Factory::getApplication()->input->get('ext');
		$data = \Joomla\CMS\Factory::getApplication()->getUserState('com_joomcck.edit' . $ext . '.template.data', array());

		if(empty($data))
		{
			$file           = explode('.', \Joomla\CMS\Factory::getApplication()->input->get('file'));
			$data['ident']  = \Joomla\CMS\Factory::getApplication()->input->get('file');
			$file_name = JoomcckTmplHelper::getTmplFile($file[1], $file[0]) . '.' . $ext;
			$data['source'] = '';
			if(\Joomla\CMS\Filesystem\File::exists($file_name))
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
		$user = \Joomla\CMS\Factory::getUser();

		return $user->authorise('core.delete', 'com_joomcck.template.' . (int)$record->id);
	}

	protected function canEditState($record)
	{
		$user = \Joomla\CMS\Factory::getUser();

		return $user->authorise('core.edit.state', 'com_joomcck.template.' . (int)$record->id);
	}
}