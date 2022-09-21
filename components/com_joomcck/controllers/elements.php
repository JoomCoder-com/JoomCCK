<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
jimport('mint.mvc.controller.admin');
class JoomcckControllerElements extends MControllerAdmin
{

	public function __construct($config = array())
	{
		parent::__construct($config);
		
		if(!$this->input)
		{
			$this->input = JFactory::getApplication()->input;
		}
	}
	public function saveoptionsclose()
	{
		$this->saveoptions();
		$return = Url::get_back('return');
		if(!$return)
		{
			$return = Url::records($this->section);
		}

		$this->setRedirect($return);
	}
	public function saveoptions()
	{
		$me = JFactory::getUser();
		

		if(! $me->id)
		{
			return;
		}

		$app = JFactory::getApplication();
		$data = $this->input->get('jform', array(), 'array');

		$table = JTable::getInstance('Useropt', 'JoomcckTable');

		$table->load(array(
			'user_id' => $me->get('id')
		));

		$table->params = json_encode($data);
		$table->user_id = $me->get('id');
		$table->schedule = (int)@$data['schedule'];

		if(empty($table->lastsend))
		{
			$table->lastsend = JFactory::getDate()->toSql();
		}
		$table->store();

		$db = JFactory::getDbo();
		$sql = "DELETE FROM #__js_res_user_options_autofollow WHERE user_id = ".$me->get('id');
		$db->setQuery($sql);
		$db->query();

		if(is_array($data['autofollow']))
		{
			foreach ($data['autofollow'] AS $sid)
			{
				$sql = "INSERT INTO #__js_res_user_options_autofollow VALUES (NULL, {$me->id}, {$sid})";
				$db->setQuery($sql);
				$db->query();
			}
		}

		$app->enqueueMessage('CMSGOPTIONSSAVED');
		$this->setRedirect(JFactory::getURI()->toString());
	}

}
