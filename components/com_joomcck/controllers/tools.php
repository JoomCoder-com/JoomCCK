<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('mint.mvc.controller.form');

class JoomcckControllerTools extends MControllerForm
{
	public $model_prefix = 'JoomcckBModel';

	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = \Joomla\CMS\Factory::getApplication()->input;
		}

		// Security: require authenticated admin for all tool operations
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		if (!$user->get('id'))
		{
			throw new \Exception(\Joomla\CMS\Language\Text::_('JGLOBAL_YOU_MUST_LOGIN_FIRST'), 403);
		}
		if (!MECAccess::isAdmin())
		{
			throw new \Exception(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}
	}

	public function save($key = NULL, $urlVar = NULL)
	{
		\Joomla\CMS\Session\Session::checkToken('request') or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		$name = preg_replace('/[^a-zA-Z0-9_]/', '', $this->input->getCmd('name'));
		$uri  = \Joomla\CMS\Uri\Uri::getInstance();

		$params = new \Joomla\Registry\Registry('');
		$jform = $this->input->post->get('jform', [], 'array');
		if(!empty($jform))
		{
			$params->loadArray($jform);
		}



        $file = JPATH_ROOT . '/components/com_joomcck/library/php/tools/' . $name . '/exec.php';

		if(is_file($file))
		{
			include $file;
		}
		else
		{
			$dispatcher = \Joomla\CMS\Factory::getApplication();
			\Joomla\CMS\Plugin\PluginHelper::importPlugin('mint');
			$dispatcher->triggerEvent('onToolExecute', array(
				$name,
				$params
			));
		}


        $form_data = JPATH_ROOT . '/components/com_joomcck/library/php/tools/' . $name . '/data.json';
		$content = $params->toString();
		\Joomla\Filesystem\File::write($form_data, $content);
		
		$this->setRedirect($uri->toString());
		$this->redirect();
	}
}