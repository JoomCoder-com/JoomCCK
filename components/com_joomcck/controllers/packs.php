<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

jimport('mint.mvc.controller.admin');
class JoomcckControllerPacks extends MControllerAdmin
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
	public function &getModel($name = 'Pack', $prefix = 'JoomcckModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	public function close()
	{
		$this->setRedirect('index.php?option=com_joomcck&view=packs');
		$this->redirect();
	}
}