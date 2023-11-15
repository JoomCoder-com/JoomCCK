<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

jimport('mint.mvc.view.base');

class JoomcckViewCpanel extends MViewBase
{

	public $hasExtendedVersion = false;


	function display($tpl = null)
	{

		// check if has extended version
		$this->hasExtendedVersion();

		parent::display($tpl);
	}

	/*
	 * Check if extended version installed
	 */
	public function hasExtendedVersion(){

		$file = JPATH_ROOT.'/modules/mod_joomcck_ifollow/mod_joomcck_ifollow.xml';

		if(is_file($file))
			$this->hasExtendedVersion = true;


	}

}
