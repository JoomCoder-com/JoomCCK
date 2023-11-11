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
	}

	public function save($key = NULL, $urlVar = NULL)
	{

		$name = $this->input->get('name');
		$uri  = \Joomla\CMS\Uri\Uri::getInstance();

		$params = new \Joomla\Registry\Registry('');
		if(@$_POST['jform'])
		{
			$params->loadArray(@$_POST['jform']);
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