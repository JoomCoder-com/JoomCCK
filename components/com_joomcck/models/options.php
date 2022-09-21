<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
jimport('mint.mvc.model.admin');

class JoomcckModelOptions extends MModelAdmin
{
	public function getForm($data = array(), $loadData = TRUE)
	{
		$form = $this->loadForm('com_joomcck.options', 'options', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form))
		{
			return FALSE;
		}

		return $form;
	}

	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_joomcck.edit.options.data', array());

		if(empty($data))
		{
			$data = $this->getOptions();
		}

		return $data;
	}

	public function getSub()
	{
		$user = JFactory::getUser();

		$db = JFactory::getDbo();

		$db->setQuery("SELECT
			  `s`.`id`,
			  `s`.`name`,
			  `s`.`params`
			FROM `#__js_res_sections` AS s
			ORDER BY `s`.`name`");

		$list = $db->loadObjectList();

		foreach($list AS $k => &$section)
		{
			$params = new JRegistry($section->params);
			if(!in_array($params->get('events.subscribe_category'), $user->getAuthorisedViewLevels())
				&& !in_array($params->get('events.subscribe_user'), $user->getAuthorisedViewLevels())
				&& !in_array($params->get('events.subscribe_category'), $user->getAuthorisedViewLevels())
				&& !in_array($params->get('events.subscribe_section'), $user->getAuthorisedViewLevels())
			)
			{
				unset($list[$k]);
				continue;
			}
		}

		return $list;
	}


	public function getOptions()
	{
		static $out = NULL;

		if($out)
		{
			return $out;
		}

		$me = JFactory::getUser();

		$table = JTable::getInstance('Useropt', 'JoomcckTable');
		$table->load(array('user_id' => $me->get('id')));

		$out = json_decode($table->params, TRUE);

		return $out;
	}
}