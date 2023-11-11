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
class JoomcckControllerOptions extends MControllerAdmin
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
	public function savesectionoptionsclose()
	{
		$this->savesectionoptions();
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

		$table = JTable::getInstance('Useropt', 'JoomcckTable');

		$table->load(array(
			'user_id' => $me->get('id')
		));

		$section_id = $this->input->getInt('section_id');

		if($table->params)
		{
			$params = json_decode($table->params, TRUE);

			$params['schedule'] = $data['schedule'];
			$params['language'] = $data['language'];

			if($section_id)
			{
				$params['autofollow'][$section_id] = $data['autofollow'][$section_id];
				$params['notification'][$section_id] = $data['notification'][$section_id];
			}

			$data = $params;
		}

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
				if(!$sid) continue;
				$sql = "INSERT INTO #__js_res_user_options_autofollow VALUES (NULL, {$me->id}, {$sid})";
				$db->setQuery($sql);
				$db->execute();
			}
		}

		$app->enqueueMessage(\Joomla\CMS\Language\Text::_('CMSGOPTIONSSAVED'));
		$this->setRedirect(\Joomla\CMS\Uri\Uri::getInstance()->toString());
	}

	public function savesectionoptions()
	{
		$me = \Joomla\CMS\Factory::getUser();
		

		if(! $me->id)
		{
			return;
		}
		$section_id = $this->input->getInt('section_id');

		if(!$section_id)
			return;

		$app = \Joomla\CMS\Factory::getApplication();
		$data = $this->input->get('jform', array(), 'array');

		$table = JTable::getInstance('Useropt', 'JoomcckTable');

		$table->load(array(
			'user_id' => $me->get('id')
		));


		if($table->params)
		{
			$params = json_decode($table->params, TRUE);

			$data['title'] = JFilterInput::getInstance()->clean($data['title']);
			$data['description'] = JFilterInput::getInstance()->clean($data['description']);

			$params['sections'][$section_id] = $data;

			$data = $params;
		}

		$table->params = json_encode($data);

		$table->user_id = $me->get('id');

		if(empty($table->lastsend))
		{
			$table->lastsend = \Joomla\CMS\Factory::getDate()->toSql();
		}
		$table->store();

		$data = array('section_id' => $section_id, 'user_id' => $me->get('id'));

		$map = JTable::getInstance('Userpostmap', 'JoomcckTable');
		$map->load($data);
		if(!$map->id)
		{
			$map->bind($data);
		}
		$map->whopost = (int)@$params['sections'][$section_id]['who_post'];
		$map->store();



		$app->enqueueMessage(\Joomla\CMS\Language\Text::_('CMSGSECTIONOPTIONSSAVED'));
		$this->setRedirect(\Joomla\CMS\Uri\Uri::getInstance()->toString());
	}

}
