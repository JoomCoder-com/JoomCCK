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

class JoomcckModelCType extends MModelAdmin
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

	/*
	public function getFields()
	{
		$fileds = JPATH_ROOT . '/components/com_joomcck/fields' ;
		$folders = JFolder::folders($fileds);
		$out = array();
		foreach($folders as $folder)
		{
			$xml = JFactory::getXMLParser('Simple');
			if(! $xml->loadFile($fileds . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $folder . '.xml'))
			{
				JError::raiseWarning(100, JText::sprintf('C_MSG_CANNOTLOADFILE'));
				continue;
			}
			$group = $xml->document->group[0]->data();
			$name = $xml->document->name[0]->data();

			$field = new stdClass();
			$field->name = $name;
			$field->file_name = $folder;
			$field->license = $xml->document->license[0]->data();
			$field->author = $xml->document->author[0]->data();
			$field->email = $xml->document->email[0]->data();
			$field->url = $xml->document->url[0]->data();
			$field->description = $xml->document->description[0]->data();
			$field->description_full = $field->description;
			$field->description_full .= sprintf('<table><tr><td><b>%s</b></td><td>%s</td></tr> <tr><td><b>%s</b></td><td>%s</td></tr> <tr><td><b>%s</b></td><td>%s</td></tr> <tr><td><b>%s</b></td><td>%s</td></tr></table>', JText::_('JAUTHOR'), $field->author, JText::_('JSITE'), $field->url,
				JText::_('JGLOBAL_EMAIL'), $field->email, JText::_('CLICENSE'), $field->license);

			$field->icon = JURI::root() . 'libraries/mint/forms/fields/joomcck';
			if(JFile::exists($fileds . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $folder . '.png'))
			{
				$field->icon .= "/{$folder}/{$folder}.png";
			}
			else
			{
				$field->icon .= "/rtext/rtext.png";
			}

			$out[$group][$folder] = $field;
			unset($field, $xml);
		}
		return $out;
	}
	*/

	public function getForm($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();

		$form = $this->loadForm('com_joomcck.ctype', 'type', array(
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
		$data = JFactory::getApplication()->getUserState('com_joomcck.edit.ctype.data', array());

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

		return $user->authorise('core.delete', 'com_joomcck.ctype.' . (int)$record->id);
	}

	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.edit.state', 'com_joomcck.ctype.' . (int)$record->id);
	}
}