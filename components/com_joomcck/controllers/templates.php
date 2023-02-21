<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();

jimport('mint.mvc.controller.form');
jimport('joomla.utilities.simplexml');

class JoomcckControllerTemplates extends MControllerForm
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = JFactory::getApplication()->input;
		}
		$this->registerTask('saveclose', 'save');
	}

	public function getModel($name = 'Templates', $prefix = 'JoomcckModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => TRUE));

		return $model;
	}

	public function install()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app = JFactory::getApplication();
		$uri = \Joomla\CMS\Uri\Uri::getInstance();
		$uri->setVar('tab', $this->input->get('tab'));

		$model = $this->getModel();

		if($model->install())
		{
			$cache = JFactory::getCache('mod_menu');
			$cache->clean();

			$app->enqueueMessage(JText::_('C_MSG_TMPLINSTALLOK'));
		}

		$app->redirect($uri->toString());
	}

	public function uninstall()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app = JFactory::getApplication();
		$uri = JUri::getInstance();
		$uri->setVar('tab', $this->input->get('tab'));

		$tmpls = $this->input->get('cid', array(), 'array');

		foreach($tmpls as $k => $tmpl)
		{
			$matches = Array();
			preg_match("/^\[(.*)\]\,\[(.*)\]$/i", $tmpl, $matches);

			if($matches[1] == 'default')
			{
				throw new GenericDataException(JText::_('C_MSG_DEAFULEUNINSTALL'), 500);
				unset($tmpls[$k]);
			}
		}
		if(!$tmpls)
		{

			Factory::getApplication()->enqueueMessage(JText::_('C_MSG_CHOSETEMPL'),'warning');
			$app->redirect($uri->toString());

			return;
		}

		$model = $this->getModel();
		$model->uninstall($tmpls);

		$this->setRedirect($uri->toString(), JText::_('C_MSG_TMPLUNINSTALLOK'));
		$this->redirect();
	}

	public function rename()
	{
		$this->copy('rename');
	}

	public function copy($func = 'copy')
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app = JFactory::getApplication();
		$uri = \Joomla\CMS\Uri\Uri::getInstance();
		$uri->setVar('tab', $this->input->get('tab'));

		$new_name = $this->input->get('tmplname');
		$tmpls    = $this->input->get('cid', array(), 'array');

		$model = $this->getModel();

		if(!$model->$func($tmpls[0], $new_name))
		{

			Factory::getApplication()->enqueueMessage(($func == 'copy' ? JText::_('C_MSG_TMPLCOPYFAIL') : JText::_('C_MSG_TMPLRENAMEFAIL')),'warning');
			$app->redirect($uri->toString());
		}

		$this->setRedirect($uri->toString(), ($func == 'copy' ? JText::_('C_MSG_TMPLCOPYOK') : JText::_('C_MSG_TMPLRENAMEOK')));
		$this->redirect();
	}


	public function change_label()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$tmpls = $this->input->get('cid', array(), 'array');
		$uri   = \Joomla\CMS\Uri\Uri::getInstance();
		$uri->setVar('tab', $this->input->get('tab'));
		$app = JFactory::getApplication();

		$matches = Array();
		preg_match("/^\[(.*)\]\,\[(.*)\]$/i", $tmpls[0], $matches);

		$new_name = $this->input->getString('tmpl_name');
		if(!$new_name)
		{

			Factory::getApplication()->enqueueMessage(JText::_('C_MSG_TMPLNO_NONAMEENTER'),'warning');
			$app->redirect($uri->toString());

			return;
		}

		$file = JPATH_ROOT . '/components/com_joomcck/views/records/tmpl/default_list_' . $matches[1] . '.xml';

		$model = $this->getModel();
		if(!$model->change_name($file, $new_name))
		{

			Factory::getApplication()->enqueueMessage(JText::_('C_MSG_TMPLSAVEFAIL'),'warning');
			$app->redirect($uri->toString());

			return;
		}
		$app->enqueueMessage(JText::_('C_MSG_TMPLLABELCHANGEOK'));
		$app->redirect($uri->toString());
	}

	public function save($key = NULL, $urlVar = NULL)
	{
		$uri    = \Joomla\CMS\Uri\Uri::getInstance();
		$app    = JFactory::getApplication();
		$task   = $this->input->getCmd('task');
		$type   = $this->input->get('type');
		$name   = $this->input->get('name');
		$config = $this->input->get('config');

		$regestry = new JRegistry();
		$regestry->loadArray($this->input->get('jform', array(), 'array'));

		$file = JoomcckTmplHelper::getTmplFile($type, $name, TRUE) . '.' . $config . '.json';

		$reg_string = $regestry->toString();
		JFile::write($file, $reg_string);

		$msg = JText::_('C_MSG_TMPLPARAMS_SAVEOK');
		switch($task)
		{
			case 'saveclose':
				if($this->input->get('return'))
				{
					$url = base64_decode($this->input->getBase64('return'));
				}
				else
				{
					$uri->setVar('close', 1);
					$url = $uri->toString();
				}
				break;
			case 'save':
			case 'apply':
			default:
				if($this->input->get('return'))
				{
					$uri->setVar('return', $this->input->getBase64('return'));
				}
				$url = $uri->toString();
				break;
		}

		$app->enqueueMessage($msg);
		$app->redirect($url);
	}

	public function cancel($key = NULL)
	{
		if($this->input->get('return'))
		{
			$url = base64_decode($this->input->getBase64('return'));
		}

		$this->setRedirect($url, JText::_('C_MSG_TMPLEDITCANCEL'));
		$this->redirect();
	}
}