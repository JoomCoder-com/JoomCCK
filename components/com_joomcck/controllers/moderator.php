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

class JoomcckControllerModerator extends MControllerForm
{

	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = \Joomla\CMS\Factory::getApplication()->input;
		}
	}

	protected function allowSave($data, $key = 'id')
	{
		return TRUE;
	}

	protected function allowAdd($data = array())
	{
		return TRUE;
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$allow = $user->authorise('core.create', 'com_joomcck.moderator');

		if($allow === NULL)
		{
			return parent::allowAdd($data);
		} else
		{
			return $allow;
		}
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		return TRUE;
		$recordId = (int)isset($data[$key]) ? $data[$key] : 0;
		$asset = 'com_joomcck.moderator.' . $recordId;

		// Check general edit permission first.
		if(\Joomla\CMS\Factory::getApplication()->getIdentity()->authorise('core.edit', $asset))
		{
			return TRUE;
		}
	}

	protected function getRedirectToItemAppend($recordId = NULL, $urlVar = 'id')
	{

		$tmpl = $this->input->getCmd('tmpl');
		$secton_id = $this->input->getCmd('section_id');
		$itemId = $this->input->getInt('Itemid');
		$return = $this->getReturnPage();

		$append = '';

		if($tmpl)
		{
			$append .= '&tmpl=' . $tmpl;
		}

		if($secton_id)
		{
			$append .= '&section_id=' . $secton_id;
		}

		if($recordId)
		{
			$append .= '&' . $urlVar . '=' . $recordId;
		}

		if($itemId)
		{
			$append .= '&Itemid=' . $itemId;
		}

		if($return)
		{
			$append .= '&return=' . base64_encode($return);
		}

		return $append;
	}

	protected function getRedirectToListAppend($recordId = NULL, $urlVar = 'id')
	{

		$tmpl = $this->input->getCmd('tmpl');
		$secton_id = $this->input->getCmd('section_id');
		$itemId = $this->input->getInt('Itemid');

		$append = '';

		if($tmpl)
		{
			$append .= '&tmpl=' . $tmpl;
		}

		if($secton_id)
		{
			$append .= '&section_id=' . $secton_id;
		}

		if($itemId)
		{
			$append .= '&Itemid=' . $itemId;
		}

		return $append;
	}

	protected function getReturnPage()
	{
		$return = $this->input->getBase64('return');

		if(!empty($return) || \Joomla\CMS\Uri\Uri::isInternal(JoomcckFilter::base64($return)))
		{
			return JoomcckFilter::base64($return);
		}

		return FALSE;
	}

	protected function postSaveHook(MModelBase $model, $validData = array())
	{

		$return =  trim(\Joomla\CMS\Factory::getApplication()->input->getString('return'));
		if($this->input->getCmd('task') == 'save' && $return)
		{
			$return = Url::get_back('return');
			if(!JURI::isInternal($return))
			{
				$return = '';
			}

			if($return)
			{
				$this->setRedirect($return);
				return TRUE;
			}
		}
	}
}