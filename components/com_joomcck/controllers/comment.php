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
class JoomcckControllerComment extends MControllerForm
{

	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = \Joomla\CMS\Factory::getApplication()->input;
		}
	}
	public function save($key = null, $urlVar = null)
	{

		$this->view_list = 'record';
		$this->view_item = 'record';

		$form = $this->input->get('jform', array(), 'array');

		$user = \Joomla\CMS\Factory::getUser();
		$record = MModelBase::getInstance('Record', 'JoomcckModel')->getItem($form['record_id']);
		$section = MModelBase::getInstance('Section', 'JoomcckModel')->getItem($record->section_id);
		$type = MModelBase::getInstance('Form', 'JoomcckModel')->getRecordType($record->type_id);

		CEmeraldHelper::allowType('comment', $type, $user->id, $section, true, '', $record->user_id);

		if (!empty($form['id']) || $this->input->getCmd('view') == 'comment')
		{
			$this->view_list = 'comment';
			$this->view_item = 'comment';
			$this->input->set('id', $form['id']);
		}
		else
			$this->input->set('id', 0);

		parent::save($key, $urlVar);
	}

	public function postSaveHook(MModelBase $model, $validData = array())
	{

		$record = \Joomla\CMS\Table\Table::getInstance('Record', 'JoomcckTable');
		$record->load($validData['record_id']);
		$validData['record'] = $record->getProperties();

		if ($this->input->getInt('is_new'))
		{
			CEventsHelper::notify('record', CEventsHelper::_COMMENT_NEW, $validData['record_id'], $this->input->getInt('section_id'), $this->input->getInt('cat_id'), $model->getState('comment.id'), 0, $validData);
			ATlog::log($record, ATlog::COM_NEW, $model->getState('comment.id'));

			$type = ItemsStore::getType($record->type_id);
			if($model->getState()->get('comment.id') && $type->params->get('comments.comments_approve_public', 0) == 1)
			{
				$table = $model->getTable();
				$table->load($model->getState()->get('comment.id'));
				$table->published = 0;
				$table->store();
			}

			//CEmeraldHelper::countLimit('type', 'comment', $type, \Joomla\CMS\Factory::getUser()->get('id'));
		}
		else if ($this->input->getInt('is_edited'))
		{
			CEventsHelper::notify('record', CEventsHelper::_COMMENT_EDITED, $validData['record_id'], $this->input->getInt('section_id'), $this->input->getInt('cat_id'), $model->getState('comment.id'), 0, $validData);
			ATlog::log($record, ATlog::COM_EDIT, $model->getState('comment.id'));
		}
		else
		{
			if ($validData['parent_id'])
			{
				$comment = \Joomla\CMS\Table\Table::getInstance('Cobcomments', 'JoomcckTable');
				$comment->load($validData['parent_id']);

				if ($comment->user_id)
				{
					CEventsHelper::notify('record', CEventsHelper::_COMMENT_REPLY, $validData['record_id'], $this->input->getInt('section_id'), $this->input->getInt('cat_id'), $validData['parent_id'], 0, $validData, 2, $comment->user_id);
				}
			}
			ATlog::log($record, ATlog::COM_NEW, $model->getState('comment.id'));
		}

		if (isset($validData['subscribe']))
		{
			CSubscriptionsHelper::subscribe_record($validData['record_id']);
		}


		$model_record = MModelBase::getInstance('Record', 'JoomcckModel');
		$model_record->onComment($validData['record_id'], $validData);

		// close popup and reoad page after reply/edit comment
		if($this->input->getCmd('view') == 'comment')
		{
			echo '<script type="text/javascript">parent.window.jQuery("#commentmodal").modal("toggle");parent.window.location.reload();</script>';
			\Joomla\CMS\Factory::getApplication()->close();
		}
	}

	public function cancel($key = null)
	{
		$this->view_list = 'records';
		parent::cancel($key);
	}

	protected function allowSave($data = array(), $key = 'id')
	{
		return TRUE;
	}

	protected function allowAdd($data = array())
	{
		$user = \Joomla\CMS\Factory::getUser();
		$allow = $user->authorise('core.create', 'com_joomcck.comment');

		if ($allow === null)
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
		return \Joomla\CMS\Factory::getUser()->authorise('core.edit', 'com_joomcck.comment');
	}

	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		return $this->getRedirectToListAppend($recordId = null, $urlVar = 'id');
	}

	protected function getRedirectToListAppend($recordId = null, $urlVar = 'id')
	{

		$tmpl = $this->input->getCmd('tmpl');
		$post_jform = $this->input->get('jform', array(), 'array');
		$record_id = $post_jform['record_id'];
		//		$secton_id		= $this->input->getCmd('section_id');
		$itemId = $this->input->getInt('Itemid');
		$append = '';

		if ($this->view_item == 'comment')
		{
			$append .= '&id=' . $post_jform['id'];
			$append .= '&record_id=' . $post_jform['record_id'];
		}
		else
		{
			$append .= '&id=' . $post_jform['record_id'];
		}

		if ($tmpl)
		{
			$append .= '&tmpl=' . $tmpl;
		}

		//		if ($type_id) {
		//			$append .= '&type_id='.$type_id;
		//		}
		//
		//		if ($secton_id) {
		//			$append .= '&section_id='.$secton_id;
		//		}


// 		if ($record_id)
// 		{
// 			$append .= '&id=' . $record_id;
// 		}

		if ($itemId)
		{
			$append .= '&Itemid=' . $itemId;
		}

		return $append;
	}

}
