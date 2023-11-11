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

class JoomcckControllerTfield extends MControllerForm
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

	public function getModel($name = '', $prefix = 'JoomcckModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{

		$app = \Joomla\CMS\Factory::getApplication();

		$append = '';
		if($t = $app->input->getInt('type_id'))
		$append = '&type_id='.$t;
		$append .= parent::getRedirectToItemAppend($recordId, $urlVar);
		return $append;

	}
	protected function getRedirectToListAppend()
	{
		$app = \Joomla\CMS\Factory::getApplication();
		$append = '';
		if($t = $app->input->getInt('type_id'))
		$append = '&type_id='.$t;

		return $append.parent::getRedirectToListAppend();
	}

	public function postSaveHook(MModelBase $model, $data = array())
	{
		$table = \Joomla\CMS\Table\Table::getInstance('Field', 'JoomcckTable');
		$table->reorder('type_id ='. $data['type_id']);


		$db = \Joomla\CMS\Factory::getDbo();
		$key = 'k'.md5($data['label'].'-'.$data['field_type']);

		$db->setQuery("UPDATE #__js_res_record_values SET field_key = '{$key}', field_type = '{$data['field_type']}', field_label = '". $db->escape($data['label']) ."' WHERE field_id = ".$model->getState('tfield.id'));
		$db->execute();
	}

	protected function allowAdd($data = array())
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$allow = $user->authorise('core.create', 'com_joomcck.tfields');

		if($allow === null)
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
		return \Joomla\CMS\Factory::getApplication()->getIdentity()->authorise('core.edit', 'com_joomcck.tfields');
	}
}