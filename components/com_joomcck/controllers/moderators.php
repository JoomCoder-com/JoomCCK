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
class JoomcckControllerModerators extends MControllerAdmin
{

	public function __construct($config = array())
	{
		parent::__construct($config);
		
		if(!$this->input)
		{
			$this->input = \Joomla\CMS\Factory::getApplication()->input;
		}
        $this->view_list = 'moderators';
	}
    
    public function getModel($name = '', $prefix = '', $config = [])
	{
		$name = $name ?: 'Moderator';
		$prefix = $prefix ?: 'JoomcckModel';
		$config = $config ?: ['ignore_request' => true];
		return parent::getModel($name, $prefix, $config);
	}
}