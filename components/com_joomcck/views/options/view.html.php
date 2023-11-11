<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();

class JoomcckViewOptions extends MViewBase
{

	function display($tpl = null)
	{
		$app = \Joomla\CMS\Factory::getApplication();
		$user = \Joomla\CMS\Factory::getUser();

		if(!$user->id)
		{
			throw new Exception( \Joomla\CMS\Language\Text::_('CNOUSERID'),500);

		}

		if($this->getLayout() != 'default')
		{
			$this->{'display_' . $this->getLayout()}($tpl);
			return;
		}

		$this->data = $this->get('Options');
		$this->form = $this->get('Form');
		$this->usub = $this->get('Sub');
		$this->section = null;

		if($app->input->getInt('section_id'))
		{

			$this->section = ItemsStore::getSection($app->input->getInt('section_id'));
			$this->sec_follow = HTMLFormatHelper::followsection($this->section);

			$folmodel = MModelBase::getInstance('Follows', 'JoomcckModel');
			$this->articles = $folmodel->getItems();
			$this->pag = $folmodel->getPagination();

			$this->section->records_total = $folmodel->getTotalRecords($this->section->id);
			$this->section->records = $folmodel->getSubRecords($this->section->id);

			$records = array('record_new', 'record_view', 'record_approved', 'record_wait_approve', 'record_edited',
				'record_deleted', 'record_rated', 'record_expired', 'record_featured_expired',
				'record_bookmarked', 'record_tagged', 'record_unpublished', 'record_featured',
				'record_extended');
			$comments = array('comment_new', 'comment_edited','comment_rated', 'comment_deleted',
				'comment_approved', 'comment_reply', 'comment_unpublished');

			$this->notyfications = array();
			if($this->section->params->get('events.user_manage'))
			{
				foreach ($records AS $event)
				{
					if($this->section->params->get('events.event.'.$event.'.notif', 0) == -1 || in_array($this->section->params->get('events.event.'.$event.'.notif', 0), $user->getAuthorisedViewLevels()))
					{
						$this->notyfications['r'][] = $event;
					}
				}
				foreach ($comments AS $event)
				{
					if($this->section->params->get('events.event.'.$event.'.notif', 0) == -1 || in_array($this->section->params->get('events.event.'.$event.'.notif', 0), $user->getAuthorisedViewLevels()))
					{
						$this->notyfications['c'][] = $event;
					}
				}
			}

			if(in_array($this->section->params->get('events.subscribe_user'), $user->getAuthorisedViewLevels()) && $this->section->params->get('personalize.personalize'))
			{
				$this->users = $folmodel->getUsers($this->section);
			}

			if(in_array($this->section->params->get('events.subscribe_category'), $user->getAuthorisedViewLevels()) && $this->section->categories)
			{
				$this->cats = $folmodel->getCats($this->section);
			}
		}

		parent::display($tpl);
	}

	public function display_section($tpl)
	{
		$section_id = \Joomla\CMS\Factory::getApplication()->input->getInt('section_id');
		if(!$section_id)
		{

			Factory::getApplication()->enqueueMessage( \Joomla\CMS\Language\Text::_('CERRNOSECTION'),'warning');
			return;
		}

		$this->section = ItemsStore::getSection($section_id);

		if(!$this->section->params->get('personalize.personalize'))
		{

			Factory::getApplication()->enqueueMessage( \Joomla\CMS\Language\Text::_('CERRSECTIONNOTPERSONALIZED'),'warning');
			return;
		}
		\Joomla\CMS\Factory::getDocument()->setTitle(\Joomla\CMS\Language\Text::sprintf('CUSERSECTIONSETTINGS', $this->section->name));

		$data = $this->get('Options');

		if(!isset($data['sections'][$section_id]))
		$options = new \Joomla\Registry\Registry();
		else
		{
			$options = new \Joomla\Registry\Registry($data['sections'][$section_id]);
		}
		$this->options = $options;
		parent::display($tpl);
	}
}