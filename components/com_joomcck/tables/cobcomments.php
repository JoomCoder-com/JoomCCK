<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Table\Nested;

defined('_JEXEC') or die('Restricted access');

jimport('joomla.database.tablenested');

class JoomcckTableCobcomments extends Nested
{
	protected static $root_id = 0;

	public function __construct(&$_db)
	{
		parent::__construct('#__js_res_comments', 'id', $_db);
		$this->ctime = \Joomla\CMS\Factory::getDate()->toSql();
		parent::$root_id = 1;
	}

	public function check()
	{
		$app = \Joomla\CMS\Factory::getApplication();

		$post = $app->input->get('jform', array(), 'array');
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		if(@$post['parent_id'] != 0 && !$this->id)
		{
			$this->setLocation($post['parent_id'], 'last-child');
		}

		if(preg_match('/\[url/iU', $this->comment) || preg_match('/\&lt;a/iU', $this->comment))
		{
			$this->setError('Unsupported characters in the comment. Looks like your are spammer. No way.');

			return FALSE;
		}

		if(!$this->langs)
		{
			$lang        = \Joomla\CMS\Factory::getLanguage();
			$this->langs = $lang->getTag();
		}
		if(!$this->user_id && !$this->name)
		{
			$this->user_id = $user->get('id');
		}

		if(!$this->ip)
		{
			$this->ip = $_SERVER['REMOTE_ADDR'];
		}
		if(!$this->section_id)
		{
			$this->section_id = $app->input->getInt('section_id');
		}

		$item = ItemsStore::getRecord($this->record_id);
		$type = ItemsStore::getType($item->type_id);

		$roots = $this->getPath($this->parent_id, TRUE);
		foreach($roots as $r)
		{
			if($r->level == 1)
			{
				$this->root_id = $r->id;
				break;
			}
		}

		$this->type_id = $item->type_id;

		$files_table = \Joomla\CMS\Table\Table::getInstance('Files', 'JoomcckTable');

		$this->attachment = $files_table->prepareSave($this->attachment);

		if(!in_array($type->params->get('comments.comments_access_access'), $user->getAuthorisedViewLevels()))
		{
			$this->access = $type->params->get('comments.comments_access_view');
		}

		$this->comment = CensorHelper::cleanText($this->comment);
		$this->comment = str_replace('<p>' . chr(194) . chr(160) . '</p>', '', $this->comment);

		$this->published = 1;

		if($type->params->get('comments.comments_approve') && !in_array($type->params->get('comments.comments_access_moderate'), $user->getAuthorisedViewLevels()))
		{
			$this->published = 0;
		}

		$em_api = JPATH_ROOT . '/components/com_emerald/api.php';
		if(
			$this->published == 0 &&
			$type->params->get('comments.type_comment_subscription') &&
			is_file($em_api)
		)
		{
			require_once $em_api;

			if(
			EmeraldApi::hasSubscription(
				$type->params->get('comments.type_comment_subscription'),
				0, NULL, $type->params->get('comments.type_comment_subscription_count'),
				FALSE)
			)
			{
				$this->published = 1;
			}
		}

		return TRUE;
	}

	public function store($updateNulls = FALSE)
	{
		$result = parent::store();

		if($result)
		{
			$files = json_decode($this->attachment, TRUE);
			settype($files, 'array');
			$saved = array();
			foreach($files as $file)
			{
				$saved[] = $file['id'];
			}
			$files_table = \Joomla\CMS\Table\Table::getInstance('Files', 'JoomcckTable');
			$files_table->markSaved($saved, array('id' => $this->record_id));
		}

		return $result;
	}
}