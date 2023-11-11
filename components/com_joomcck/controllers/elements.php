<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
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
			$this->input = \Joomla\CMS\Factory::getApplication()->input;
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
		$me = \Joomla\CMS\Factory::getUser();
		

		if(! $me->id)
		{
			return;
		}

		$app = \Joomla\CMS\Factory::getApplication();
		$data = $this->input->get('jform', array(), 'array');

		$table = \Joomla\CMS\Table\Table::getInstance('Useropt', 'JoomcckTable');

		$table->load(array(
			'user_id' => $me->get('id')
		));

		$table->params = json_encode($data);
		$table->user_id = $me->get('id');
		$table->schedule = (int)@$data['schedule'];

		if(empty($table->lastsend))
		{
			$table->lastsend = \Joomla\CMS\Factory::getDate()->toSql();
		}
		$table->store();

		$db = \Joomla\CMS\Factory::getDbo();
		$sql = "DELETE FROM #__js_res_user_options_autofollow WHERE user_id = ".$me->get('id');
		$db->setQuery($sql);
		$db->execute();

		if(is_array($data['autofollow']))
		{
			foreach ($data['autofollow'] AS $sid)
			{
				$sql = "INSERT INTO #__js_res_user_options_autofollow VALUES (NULL, {$me->id}, {$sid})";
				$db->setQuery($sql);
				$db->execute();
			}
		}

		$app->enqueueMessage('CMSGOPTIONSSAVED');
		$this->setRedirect(\Joomla\CMS\Uri\Uri::getInstance()->toString());
	}

}
