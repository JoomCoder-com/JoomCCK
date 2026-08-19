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
			$this->input = \Joomla\CMS\Factory::getApplication()->input;
		}
	}

	public function getModel($name = 'Template', $prefix = 'JoomcckModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}

	protected function allowAdd($data = array())
	{
		$user  = \Joomla\CMS\Factory::getApplication()->getIdentity();
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
		return \Joomla\CMS\Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_joomcck.template');
	}

	public function postSaveHook(MModelBase $model, $validData = array())
	{

	}

	public function save($key = NULL, $urlVar = NULL)
	{
		// Anti-CSRF: the legitimate editor form already emits the token
		// (views/template/tmpl/default.php). GHSA-whf2-8p4w-v6xv.
		\Joomla\CMS\Session\Session::checkToken() or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		// Writing a template file means writing server-executed code. Require a
		// Joomla Super User (global core.admin), never merely a JoomCCK moderator.
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		if(!$user->get('id') || !$user->authorise('core.admin'))
		{
			throw new \Exception(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$data = $this->input->post->get('jform', array(), 'array');

		// Extension allow-list: exactly the four the UI edits (edit_php/xml/css/js).
		$ext = strtolower((string) $this->input->get('ext'));
		if(!in_array($ext, array('php', 'xml', 'css', 'js'), true))
		{
			throw new \Exception(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		// ident is "<name>.<type>"; reject anything that could escape the tmpl dir.
		$ident = isset($data['ident']) ? (string) $data['ident'] : '';
		$file  = explode('.', $ident);
		if(count($file) !== 2 || $file[0] === '' || strpbrk($file[0], "/\\") !== FALSE || strpos($file[0], '..') !== FALSE)
		{
			throw new \Exception(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$base     = JoomcckTmplHelper::getTmplFile($file[1], $file[0]);
		$filename = $base . '.' . $ext;

		// Path containment: the resolved file must stay inside the template dir
		// that its type maps to. Blocks traversal even if a prefix absorbs a "..".
		$allowedDir = JoomcckTmplHelper::getTmplPath($file[1]);
		if($base === '' || !$allowedDir)
		{
			throw new \Exception(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}
		$allowedDir = \Joomla\Filesystem\Path::clean($allowedDir);
		if(strpos(\Joomla\Filesystem\Path::clean($filename), $allowedDir . DIRECTORY_SEPARATOR) !== 0)
		{
			throw new \Exception(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		if(!\Joomla\Filesystem\File::write($filename, $data['source']))
		{

			Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CCOULDNOTSAVEFILE'),'warning');
		}

		$this->setRedirect(Url::view('templates', FALSE));
		if($this->getTask() == 'apply')
		{
			$this->setRedirect(\Joomla\CMS\Uri\Uri::getInstance()->toString());
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

	protected function editFile($ext)
	{
		$app     = \Joomla\CMS\Factory::getApplication();
		$tmpl    = str_replace(array('[', ']', ','), array('', '', '.'), $app->input->getString('id'));
		$context = "$this->option.edit{$ext}.$this->context";

		$this->holdEditId($context, $tmpl);
		$app->setUserState($context . '.data', NULL);

		$this->setRedirect(
			\Joomla\CMS\Router\Route::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_item
				. '&file=' . $tmpl . '&ext=' . $ext, FALSE
			)
		);

		return TRUE;
	}
}