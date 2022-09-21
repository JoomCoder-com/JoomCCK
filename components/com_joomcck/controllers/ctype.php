<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('mint.mvc.controller.form');

class JoomcckControllerCType extends MControllerForm
{

	public $model_prefix = 'JoomcckBModel';

	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = JFactory::getApplication()->input;
		}
	}

	public function getModel($name = 'Ctype', $prefix = 'JoomcckModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}

	protected function allowAdd($data = array())
	{
		$user  = JFactory::getUser();
		$allow = $user->authorise('core.create', 'com_joomcck.ctypes');

		if($allow === NULL)
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
		return JFactory::getUser()->authorise('core.edit', 'com_joomcck.ctypes');
	}

	public function postSaveHook(MModelBase $model, $validData = array())
	{
		$task = $this->getTask();

		if($task == 'save2copy')
		{
			$old_id = $this->input->getInt('id', 0);
			$new_id = $model->getState('ctype.id', 0);

			if(!$old_id || !$new_id)
			{
				return;
			}

			$old = JTable::getInstance('Type', 'JoomcckTable');
			$old->load($old_id);

			$new = JTable::getInstance('Type', 'JoomcckTable');
			$new->load($new_id);

			$params = new JRegistry($new->params);
			$key    = md5(time() . '-' . $new_id);

			$this->_moveTmpl($params, 'article', 'record', $key);
			$this->_moveTmpl($params, 'articleform', 'form', $key);
			$this->_moveTmpl($params, 'comment', 'comments', $key);

			$params->set('properties.tmpl_rating', 'default.' . $key);
			$new->params = $params->toString();
			$new->store();

			$db = JFactory::getDbo();
			$db->setQuery("INSERT INTO #__js_res_fields (`key`, label, type_id, field_type, params, published, ordering, access, group_id, asset_id, filter, user_id)
					SELECT `key`, label, $new_id, field_type, params, published, ordering, access, group_id, asset_id, filter, user_id
					FROM #__js_res_fields
					WHERE type_id = " . $old_id
			);
			$db->execute();

			$db->setQuery("SELECT * FROM #__js_res_fields_group WHERE type_id = " . $old_id);
			$groups = $db->loadObjectList();

			$db = JFactory::getDbo();
			include_once __DIR__ . '/../tables/group.php';
			$gt = new JoomcckTableGroup($db);
			foreach($groups AS $group)
			{
				$ogid           = $group->id;
				$group->id      = NULL;
				$group->type_id = $new_id;

				$gt->save($group);

				$db->setQuery("UPDATE #__js_res_fields SET group_id = $gt->id WHERE group_id = $ogid AND type_id = $new_id");
				$db->execute();

				$gt->reset();
				$gt->id = NULL;
			}
		}
	}

	private function _moveTmpl(&$params, $param, $name, $key)
	{
		$tmpl_name = $params->get('properties.tmpl_' . $param);

		$file = JPATH_ROOT . "/components/com_joomcck/configs/default_{$name}_{$tmpl_name}.json";

		if(JFile::exists($file))
		{
			$tmpl = explode('.', $tmpl_name);
			$dest = JPATH_ROOT . "/components/com_joomcck/configs/default_{$name}_{$tmpl[0]}.{$key}.json";
			JFile::copy($file, $dest);

			$params->set('properties.tmpl_' . $name, $tmpl[0] . '.' . $key);
		}
		else
		{
			$params->set('properties.tmpl_' . $name, 'default.' . $key);
		}
	}
}