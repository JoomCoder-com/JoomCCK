<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
jimport('mint.mvc.model.admin');

class JoomcckModelOptions extends MModelAdmin
{
	public function getForm($data = array(), $loadData = TRUE)
	{
		$form = $this->loadForm('com_joomcck.options', 'options', array('control' => 'jform', 'load_data' => false));
		if(empty($form))
		{
			return FALSE;
		}

		return $form;
	}

	protected function loadFormData()
	{
		$data = \Joomla\CMS\Factory::getApplication()->getUserState('com_joomcck.edit.options.data', array());

		if(empty($data))
		{
			$data = $this->getOptions();
		}

		return $data;
	}

	public function getSub()
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		$db = \Joomla\CMS\Factory::getDbo();

		$db->setQuery("SELECT
			  `s`.`id`,
			  `s`.`name`,
			  `s`.`params`
			FROM `#__js_res_sections` AS s
			ORDER BY `s`.`name`");

		$list = $db->loadObjectList();

		foreach($list AS $k => &$section)
		{
			$params = new \Joomla\Registry\Registry($section->params);
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

		$me = \Joomla\CMS\Factory::getApplication()->getIdentity();

		$table = \Joomla\CMS\Table\Table::getInstance('Useropt', 'JoomcckTable');
		$table->load(array('user_id' => $me->get('id')));

		$out = json_decode((string)$table->params, TRUE);

		return $out;
	}
}