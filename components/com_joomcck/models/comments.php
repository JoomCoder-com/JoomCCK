<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
jimport('mint.mvc.model.list');

class JoomcckModelComments extends MModelList
{

	public function getTable($type = 'Comments', $prefix = 'JoomcckTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	protected function populateState($ordering = NULL, $direction = NULL)
	{
		$app = JFactory::getApplication();

		$file = JPATH_ROOT . '/components/com_joomcck/views/record/tmpl/default_comments_' . $this->type->params->get('properties.tmpl_comment', 'default') . '.json';
		if(JFile::exists($file))
		{
			$file = file_get_contents();
		}
		else
		{
			$file = array();
		}
		$tmpl_params = new JRegistry($file);

		$order = explode(' ', $tmpl_params->get('tmpl_core.comments_sort', 'ctime ASC'));
		$this->setState('list.ordering', 'c.' . $order[0]);
		$this->setState('list.direction', @$order[1]);

		parent::populateState('c.' . $order[0], @$order[1]);
	}

	public function getItems($record_id = NULL)
	{
		static $cache = array();

		$item      = $this->item;
		$record_id = $item->id;

		// Try to load the data from internal storage.
		if(!empty($cache[$record_id]))
		{
			return $cache[$record_id];
		}

		$app  = JFactory::getApplication();
		$user = JFactory::getUser();
		$type = $this->type;

		$value = $app->getUserStateFromRequest('com_joomcck.comments.list.limit', 'limit', $this->getState('comments.limit'));
		$this->setState('list.limit', $value);

		// Load the list items.
		$query            = $this->_getListQuery();
		$data             = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
		$child_condisions = array();
		$comments         = array();
		foreach($data AS $k => $comment)
		{
			$comments[$comment->id] = $this->_prepareComent($comment, $type, $user);
			$child_condisions[]     = '(c.lft > ' . $comment->lft . ' AND ' . 'c.rgt < ' . $comment->rgt . ')';
		}

		$private = 'FALSE';
		if(in_array($type->params->get('comments.comments_hide_access'), $user->getAuthorisedViewLevels()))
		{
			$private = 'TRUE';
		}
		if(($user->get('id') == $item->user_id) && $type->params->get('comments.comments_hide_access_author'))
		{
			$private = 'TRUE';
		}

		if(!empty($child_condisions))
		{
			$query_childs = $this->_db->getQuery(TRUE);
			$query_childs->select('c.*');
			$query_childs->from('#__js_res_comments AS c');
			$query_childs->select('u.name AS c_name, u.username AS c_username, u.email AS c_email');
			$query_childs->join('LEFT', '#__users AS u on u.id = c.user_id');
			//    		$query_childs->where('c.published = 1');
			$query_childs->where(implode('OR', $child_condisions));
			if(!MECAccess::allowCommentModer($type, $item))
			{
				$query_childs->where('(c.access IN(' . implode(',', $user->getAuthorisedViewLevels()) . ') OR (c.user_id != 0 AND c.user_id = ' . $user->get('id') . ' ))');
			}
			$query_childs->where("(c.private < 1 OR (c.private = 1 AND ({$private} OR c.user_id = {$user->id})))");
			$query_childs->order('c.lft ASC');
			$this->_db->setQuery($query_childs);


			try{
				$result = $this->_db->loadObjectList();
			}catch (RuntimeException $e){
				\Joomla\CMS\Factory::getApplication()->enqueueMessage($e->getMessage(),'error');
				return false;
			}



			foreach($result as $com)
			{
				$comments[$com->root_id]->sub_comments[] = $this->_prepareComent($com, $type, $user);
			}
		}


		// Add the items to the internal cache.
		$cache[$record_id] = $comments;

		return $cache[$record_id];
	}

	private function _prepareComent($comment, $type, $user)
	{
		$comment->created = JFactory::getDate($comment->ctime);

		$comment->attachment = AttachmentHelper::getAttachments($comment->attachment, $type->params->get('comments.comments_attachment_hit', 0));

		/* if(!$comment->user_id)
		{
			$comment->name = $comment->name;
			$comment->username = $comment->name;
			$comment->email = $comment->email;
		}
		else
		{
			$comment->name = $comment->c_name;
			$comment->username = $comment->c_username;
			$comment->email = $comment->c_email;
		} */
		$comment->avatar      = NULL;
		$comment->candelete   = NULL;
		$comment->canmoderate = NULL;
		$comment->canedit     = NULL;
		$comment->comment = JHtml::_('content.prepare', $comment->comment, null, 'com_joomcck.comment');
		$comment->comment = JFilterInput::getInstance([], [], 1, 1, 1)->clean($comment->comment, 'html');
		$comment->private = (boolean)($comment->private == 1);


		if($comment->user_id)
		{
			if($comment->user_id == $user->get('id') && $type->params->get('comments.comments_comment_author_delete'))
			{
				$comment->candelete = TRUE;
			}
			if($comment->user_id == $user->get('id') && $type->params->get('comments.comments_approve_author'))
			{
				$comment->candelete = TRUE;
			}
			if($comment->user_id == $user->get('id') && $type->params->get('comments.comments_approve_author'))
			{
				$comment->canmoderate = TRUE;
			}
			if($comment->user_id == $user->get('id') && $type->params->get('comments.comments_approve_author'))
			{
				$comment->canedit = TRUE;
			}
			if($comment->user_id == $user->get('id') && $type->params->get('comments.comments_comment_author_edit'))
			{
				$comment->canedit = TRUE;
			}
		}
		if(MECAccess::allowCommentModer($type, $comment->record_id))
		{
			$comment->candelete   = TRUE;
			$comment->canmoderate = TRUE;
			$comment->canedit     = TRUE;
		}

		$comment->canrate = RatingHelp::canRate('comment', $comment->user_id, $comment->id, $type->params->get('comments.comments_rate_rate', 1));

		return $comment;
	}

	public function getStoreId($id = NULL)
	{
		$id	.= ':'.$this->item->id;

		return md5($this->context.':'.$id);
	}

	public function getListQuery()
	{

		$item = $this->item;
		$record_id = $item->id;
		if(!$record_id)
		{
			$this->setError(JText::_('CCOMMRECNOTSET'));

			return FALSE;
		}
		$type = $this->type;

		$user  = JFactory::getUser();
		$db    = $this->getDbo();
		$query = $db->getQuery(TRUE);


		$query->select('c.*');
		$query->from('#__js_res_comments AS c');

		$query->select('u.name AS c_name, u.username AS c_username, u.email AS c_email');
		$query->join('LEFT', '#__users AS u on u.id = c.user_id');

		$query->where('c.record_id = ' . (int)$record_id);

		$query->where('c.parent_id = 1');
		if(!MECAccess::allowCommentModer($type, $item))
		{
			$query->where('(c.access IN(' . implode(',', $user->getAuthorisedViewLevels()) . ') OR (c.user_id != 0 AND c.user_id = ' . $user->get('id') . ' ))');
		}

		if($type->params->get('comments.comments_lang_mode'))
		{
			$query->where('langs = ' . $db->quote(JFactory::getLanguage()->getTag()));
		}

		$private = 'FALSE';
		if(in_array($type->params->get('comments.comments_hide_access'), $user->getAuthorisedViewLevels()))
		{
			$private = 'TRUE';
		}
		if(($user->get('id') == $item->user_id && $item->user_id > 0) && $type->params->get('comments.comments_hide_access_author'))
		{
			$private = 'TRUE';
		}

		$query->where("(c.private < 1 OR (c.private = 1 AND ({$private} OR c.user_id = {$user->id})))");

		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}