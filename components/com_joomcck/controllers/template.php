<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();

jimport('mint.mvc.controller.form');

class JoomcckControllerTemplate extends MControllerForm
{

	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = JFactory::getApplication()->input;
		}
	}

	public function getModel($name = 'Template', $prefix = 'JoomcckModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}

	protected function allowAdd($data = array())
	{
		$user  = JFactory::getUser();
		$allow = $user->authorise('core.create', 'com_joomcck.template');

		if($allow === NULL)
		{
			return parent::allowAdd($data);
		}
		else
		{
			return $allow;
		}
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		return JFactory::getUser()->authorise('core.edit', 'com_joomcck.template');
	}

	public function postSaveHook(MModelBase $model, $validData = array())
	{

	}

	public function save($key = NULL, $urlVar = NULL)
	{
		$data = $this->input->post->get('jform', array(), 'array');

		$file     = explode('.', $data['ident']);
		$filename = JoomcckTmplHelper::getTmplFile($file[1], $file[0]) . '.' . $this->input->get('ext');

		if(!JFile::write($filename, $data['source']))
		{

			Factory::getApplication()->enqueueMessage(JText::_('CCOULDNOTSAVEFILE'),'warning');
		}

		$this->setRedirect(Url::view('templates', FALSE));
		if($this->getTask() == 'apply')
		{
			$this->setRedirect(JUri::getInstance()->toString());
		}
	}

	public function edit_php($key = NULL, $urlVar = NULL)
	{
		self::editFile('php');
	}

	public function edit_xml($key = NULL, $urlVar = NULL)
	{
		self::editFile('xml');
	}

	public function edit_css($key = NULL, $urlVar = NULL)
	{
		self::editFile('css');
	}

	public function edit_js($key = NULL, $urlVar = NULL)
	{
		self::editFile('js');
	}

	public function editFile($ext)
	{
		$app     = JFactory::getApplication();
		$tmpl    = str_replace(array('[', ']', ','), array('', '', '.'), $app->input->getString('id'));
		$context = "$this->option.edit{$ext}.$this->context";

		$this->holdEditId($context, $tmpl);
		$app->setUserState($context . '.data', NULL);

		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_item
				. '&file=' . $tmpl . '&ext=' . $ext, FALSE
			)
		);

		return TRUE;
	}
}