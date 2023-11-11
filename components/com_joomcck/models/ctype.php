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

class JoomcckModelCType extends MModelAdmin
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

	/*
	public function getFields()
	{
		$fileds = JPATH_ROOT . '/components/com_joomcck/fields' ;
		$folders = \Joomla\Filesystem\Folder::folders($fileds);
		$out = array();
		foreach($folders as $folder)
		{
			$xml = \Joomla\CMS\Factory::getXMLParser('Simple');
			if(! $xml->loadFile($fileds . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $folder . '.xml'))
			{
				JError::raiseWarning(100, \Joomla\CMS\Language\Text::sprintf('C_MSG_CANNOTLOADFILE'));
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
			$field->description_full .= sprintf('<table><tr><td><b>%s</b></td><td>%s</td></tr> <tr><td><b>%s</b></td><td>%s</td></tr> <tr><td><b>%s</b></td><td>%s</td></tr> <tr><td><b>%s</b></td><td>%s</td></tr></table>', \Joomla\CMS\Language\Text::_('JAUTHOR'), $field->author, \Joomla\CMS\Language\Text::_('JSITE'), $field->url,
				\Joomla\CMS\Language\Text::_('JGLOBAL_EMAIL'), $field->email, \Joomla\CMS\Language\Text::_('CLICENSE'), $field->license);

			$field->icon = \Joomla\CMS\Uri\Uri::root() . 'libraries/mint/forms/fields/joomcck';
			if(is_file($fileds . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $folder . '.png'))
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
		$app = \Joomla\CMS\Factory::getApplication();

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
		$data = \Joomla\CMS\Factory::getApplication()->getUserState('com_joomcck.edit.ctype.data', array());

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

		return $user->authorise('core.delete', 'com_joomcck.ctype.' . (int)$record->id);
	}

	protected function canEditState($record)
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		return $user->authorise('core.edit.state', 'com_joomcck.ctype.' . (int)$record->id);
	}
}