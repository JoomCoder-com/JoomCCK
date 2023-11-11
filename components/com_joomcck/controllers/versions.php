<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

class JoomcckControllerVersions extends MControllerAdmin
{

	public function __construct($config = array())
	{
		parent::__construct($config);
		
		if(!$this->input)
		{
			$this->input = \Joomla\CMS\Factory::getApplication()->input;
		}
        $this->view_list = 'versions&record_id='.$this->input->getInt('record_id', null).'&return='.$this->input->getString('return', null);
	}
    
    public function &getModel($name = 'Auditversion', $prefix = 'JoomcckModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
}