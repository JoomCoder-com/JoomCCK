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
class JoomcckControllerComms extends MControllerAdmin
{
	public $model_prefix = 'JoomcckBModel';

	public function &getModel($name = 'Comment', $prefix = 'JoomcckModel' , $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
    }
}