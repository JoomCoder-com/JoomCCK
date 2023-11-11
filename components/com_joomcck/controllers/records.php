<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\GenericDataException;

defined('_JEXEC') or die();
jimport('mint.mvc.controller.admin');
class JoomcckControllerRecords extends MControllerAdmin
{

	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = \Joomla\CMS\Factory::getApplication()->input;
		}
	}

	public function getModel($type = 'Record', $prefix = 'JoomcckModel', $config = array())
	{
		return MModelBase::getInstance($type, $prefix, $config);
	}

	public function rectorefile()
	{
		if(!$this->_checkAccess('Restore', $this->input->getInt('id')))
		{
			return;
		}

		$db = \Joomla\CMS\Factory::getDbo();
		$db->setQuery("UPDATE `#__js_res_files` SET saved = 1 WHERE id = " . $this->input->get('fid'));
		$db->execute();

		$db->setQuery('SELECT id, filename, realname, ext, size, title, description, width, height, fullpath, params
		FROM #__js_res_files WHERE id = ' . $this->input->get('fid'));
		$file = $db->loadAssoc();

		if(!$file)
		{
			$this->_finish(\Joomla\CMS\Language\Text::_('CMSG_CANNOTRESTOREFILE'));

			return;
		}

		$fields = json_decode($this->record->fields, TRUE);
		settype($fields[$this->input->get('field_id')], 'array');
		foreach($fields[$this->input->get('field_id')] AS $f)
		{
			if($f['id'] == $this->input->get('fid'))
			{
				$this->_finish(\Joomla\CMS\Language\Text::_('CMSG_FILERESTORED'));

				return;
			}
		}
		$fields[$this->input->get('field_id')][] = $file;

		$this->record->fields = json_encode($fields);
		$this->record->store();

		$this->record->file = $file;
		ATlog::log($this->record, ATlog::REC_FILE_RESTORED);

		$this->_finish(\Joomla\CMS\Language\Text::_('CMSG_FILERESTORED'));

	}

	public function commentsdisable()
	{
		if(!$this->_checkAccess('CommentBlock', $this->input->getInt('id')))
		{
			return;
		}

		$params = new \Joomla\Registry\Registry($this->record->params);
		$params->set('comments.comments_access_post', 0);

		$this->record->params = $params->toString();
		$this->record->store();

		$this->_finish(\Joomla\CMS\Language\Text::_('CMSG_COMMENTDISAB'));
	}

	public function commentsenable()
	{


		if(!$this->_checkAccess('CommentBlock', $this->input->getInt('id')))
		{
			return;
		}

		$params = new \Joomla\Registry\Registry($this->record->params);
		$params->set('comments.comments_access_post', $this->type->params->get('comments.comments_access_post'));

		$this->record->params = $params->toString();
		$this->record->store();

		$this->_finish(\Joomla\CMS\Language\Text::_('CMSG_COMMMENAD'));
	}

	public function depost()
	{
		if(!$this->_checkAccess('Depost', $this->input->getInt('id')))
		{
			return;
		}

		$db = \Joomla\CMS\Factory::getDbo();
		$db->setQuery("DELETE FROM `#__js_res_record_repost` WHERE record_id = {$this->record->id} AND host_id = " . \Joomla\CMS\Factory::getUser()->get('id'));
		$db->execute();

		$this->record->onRepost();

		$this->_finish(\Joomla\CMS\Language\Text::_('CMSG_DEPOSTED'));
	}

	public function restore()
	{
		$db = \Joomla\CMS\Factory::getDbo();
		$db->setQuery("SELECT params FROM #__js_res_audit_log WHERE record_id = " . $this->input->getInt('id'));
		$record = $db->loadResult();
		if(!$record)
		{
			$this->_finish(\Joomla\CMS\Language\Text::_('CERRRECNOTFOUND'), TRUE);

			return;
		}

		$record = json_decode($record);

		if(!MECAccess::allowRestore($record))
		{
			$this->_finish(\Joomla\CMS\Language\Text::_('CERRNOACTIONACCESS'), TRUE);
		}

		$db->setQuery("SELECT * FROM #__js_res_audit_restore WHERE record_id = " . $this->input->getInt('id'));
		$restore = $db->loadObject();

		if(!$restore)
		{
			$this->_finish(\Joomla\CMS\Language\Text::_('CERRRESTORENOTFOUND'), TRUE);

			return;
		}

		$db->setQuery("SELECT * FROM #__js_res_audit_versions WHERE record_id = " . $this->input->getInt('id') . " ORDER BY `version` DESC LIMIT 1");
		$lastversion = $db->loadObject();

		if(!$lastversion)
		{
			$this->_finish(\Joomla\CMS\Language\Text::_('CERRRESTORENOTFOUND'), TRUE);

			return;
		}

		$sql = "INSERT INTO #__js_res_record (id) values (" . $this->input->getInt('id') . ")";
		$db->setQuery($sql);
		$db->execute();

		$this->record = JTable::getInstance('Record', 'JoomcckTable');

		$this->_rollback($lastversion);

		$comments = json_decode($restore->comments, TRUE);
		$this->_restore_table($comments, 'CobComments');

		$favorites = json_decode($restore->favorites, TRUE);
		$this->_restore_table($favorites, 'Favorites');

		$files = json_decode($restore->files, TRUE);
		$this->_restore_table($files, 'Files');

		$hits = json_decode($restore->hits, TRUE);
		$this->_restore_table($hits, 'Hits');

		$subscriptions = json_decode($restore->subscriptions, TRUE);
		$this->_restore_table($subscriptions, 'Subscribe', 'ref_id');

		$votes = json_decode($restore->votes, TRUE);
		$this->_restore_table($votes, 'Votes', 'ref_id');

		$notifications = json_decode($restore->notifications, TRUE);
		$this->_restore_table($notifications, 'Notificat', 'ref_1');

		$db->setQuery("DELETE FROM #__js_res_audit_restore WHERE record_id = " . $this->input->getInt('id'));
		$db->execute();

		ATlog::log($this->record, ATlog::REC_RESTORED);
		$this->_finish(\Joomla\CMS\Language\Text::_('CMSG_RESTORED'));
	}

	public function rollback()
	{
		if(!$this->_checkAccess('Rollback', $this->input->getInt('id')))
		{
			return;
		}

		$version = $this->input->getInt('version');

		if(!$version)
		{
			$this->_finish(\Joomla\CMS\Language\Text::_('CNOTICEVERNOTSET'), TRUE);

			return;
		}

		$db = \Joomla\CMS\Factory::getDbo();
		$db->setQuery("SELECT * FROM #__js_res_audit_versions WHERE `version` = {$version} AND record_id = {$this->record->id}");
		$restore = $db->loadObject();

		if(!$restore)
		{
			$this->_finish(\Joomla\CMS\Language\Text::sprintf('CNOTICEVERSNOTFUOND', $version), TRUE);

			return;
		}

		$this->_rollback($restore);

		ATlog::log($this->record, ATlog::REC_ROLLEDBACK);

		$this->_finish(\Joomla\CMS\Language\Text::sprintf('CMSG_ROLLBACKSUCCESS', $this->record->title, $version));
	}

	private function _rollback($restore)
	{
		$db = \Joomla\CMS\Factory::getDbo();

		$record = json_decode($restore->record_serial, TRUE);

		$this->record->bind($record);
		$this->record->store();


		$tags = json_decode($restore->tags_serial, TRUE);
		$this->_restore_table($tags, 'Taghistory');

		$values = json_decode($restore->values_serial, TRUE);
		$this->_restore_table($values, 'Record_values');

		$categories = json_decode($restore->category_serial, TRUE);
		$this->_restore_table($categories, 'Record_category');
	}

	private function _restore_table($list, $table_class, $ref = 'record_id')
	{
		$table = JTable::getInstance($table_class, 'JoomcckTable');
		$name = $table->getTableName();

		if(!$name)
		{
			throw new GenericDataException('no table:' . $table_class, 500);

			return;
		}

		$db  = \Joomla\CMS\Factory::getDbo();
		$sql = "DELETE FROM {$name} WHERE {$ref} = " . $this->record->id;
		if($table_class == 'Votes')
		{
			$sql .= " AND ref_type = 'record' ";
		}
		$db->setQuery($sql);
		$db->execute();

		foreach($list AS $item)
		{
			if(is_object($item))
			{
				$item = get_object_vars($item);
			}
			unset($item['id']);
			$table->save($item);

			$table->reset();
			$table->id = NULL;
		}
	}

	public function prolong()
	{


		if(!$this->_checkAccess('Extend', $this->input->getInt('id')))
		{
			return;
		}
		$user = \Joomla\CMS\Factory::getUser();
		CEmeraldHelper::allowType('extend', $this->type, $user->id, $this->section, TRUE, '', $this->record->user_id);

		$this->record->extime  = \Joomla\CMS\Factory::getDate("+" . $this->type->params->get('properties.default_extend', 10) . ' day')->toSql();
		$this->record->exalert = 0;

		$type = ItemsStore::getType($this->record->type_id);
		if($type->params->get('properties.item_expire_access'))
		{
			$this->record->access = $type->params->get('submission.access');
		}

		$this->record->store();

		$data = $this->record->getProperties();
		CEventsHelper::notify('record', CEventsHelper::_RECORD_EXTENDED, $this->record->id, $this->record->section_id, 0, 0, 0, $data);

		$db = \Joomla\CMS\Factory::getDbo();
		$db->setQuery("DELETE FROM #__js_res_notifications WHERE `type` = 'record_expired' AND ref_1 = {$this->record->id}");
		$db->execute();
		//CEmeraldHelper::countLimit('type', 'extend', $this->type, $user->id);

		ATlog::log($this->record, ATlog::REC_PROLONGED);

		$this->_finish(\Joomla\CMS\Language\Text::sprintf('CMSG_RECEXTENDED', $this->type->params->get('properties.default_extend', 10)));
	}

	public function shide()
	{


		if(!$this->_checkAccess('Hide', $this->input->getInt('id')))
		{
			return;
		}

		$this->record->hidden = 1;
		$this->record->store();

		ATlog::log($this->record, ATlog::REC_HIDDEN);

		$this->_finish(\Joomla\CMS\Language\Text::_('CMSG_RECHIDDEN'));
	}

	public function sunhide()
	{


		if(!$this->_checkAccess('Hide', $this->input->getInt('id')))
		{
			return;
		}

		$this->record->hidden = 0;
		$this->record->store();

		ATlog::log($this->record, ATlog::REC_UNHIDDEN);

		$this->_finish(\Joomla\CMS\Language\Text::_('CMSG_RECUNHIDDEN'));
	}

	public function sfeatured()
	{
		if(!$this->_checkAccess('Featured', $this->input->getInt('id')))
		{
			return;
		}

		$user = \Joomla\CMS\Factory::getUser();

		CEmeraldHelper::allowType('feature', $this->type, $user->id, $this->section, TRUE, NULL, $this->record->user_id);

		$this->record->featured = 1;
		$this->record->ftime    = \Joomla\CMS\Factory::getDate("+" . $this->type->params->get('emerald.type_feature_subscription_time', 10) . ' day')->toSql();
		$this->record->store();

		$data = $this->record->getProperties();
		CEventsHelper::notify('record', CEventsHelper::_RECORD_FEATURED, $this->record->id, $this->record->section_id, 0, 0, 0, $data);

		//CEmeraldHelper::countLimit('type', 'feature', $this->type, $user->id);

		ATlog::log($this->record, ATlog::REC_FEATURED);


		$this->_finish(\Joomla\CMS\Language\Text::sprintf('CMSG_RECFEATUREDOK', $this->type->params->get('emerald.type_feature_subscription_time', 10)));

	}

	public function sunfeatured()
	{
		if(!$this->_checkAccess('Featured', $this->input->getInt('id')))
		{
			return;
		}
		$user = \Joomla\CMS\Factory::getUser();

		//CEmeraldHelper::allowType('feature', $this->type, $user->id, $this->section, TRUE, $this->record->user_id);

		$this->record->featured = 0;
		$this->record->ftime    = NULL;
		$this->record->store();

		$data = $this->record->getProperties();

		CEventsHelper::notify('record', CEventsHelper::_RECORD_FEATURED_EXPIRED, $this->record->id, $this->record->section_id, 0, 0, 0, $data, 2, $this->record->user_id);

		ATlog::log($this->record, ATlog::REC_UNFEATURED);


		$this->_finish(\Joomla\CMS\Language\Text::_('CMSG_RECUNFEATUREDOK'));

	}

	public function sunpub()
	{


		if(!$this->_checkAccess('Publish', $this->input->getInt('id')))
		{
			return;
		}

		$this->record->published = 0;
		$this->record->store();

		if($this->record->user_id)
		{
			$data = $this->record->getProperties();
			CEventsHelper::notify('record', CEventsHelper::_RECORD_UNPUBLISHED, $this->record->id, $this->record->section_id, 0, 0, 0, $data, 2, $this->record->user_id);
		}

		ATlog::log($this->record, ATlog::REC_UNPUBLISHED);

		$this->_finish(\Joomla\CMS\Language\Text::_('CMSG_RECUNPUBOK'));
	}

	public function spub()
	{
		if(!$this->_checkAccess('Publish', $this->input->getInt('id')))
		{
			return;
		}

		if($this->record->pubtime == '0000-00-00 00:00:00' || $this->record->pubtime == NULL)
		{
			CEventsHelper::notify('category', CEventsHelper::_RECORD_NEW, $this->record->id, $this->record->section_id, 0, 0, 0, $this->record->getProperties());
		}

		$this->record->published = 1;
		$this->record->pubtime   = \Joomla\CMS\Factory::getDate()->toSql();
		$this->record->store();

		if($this->record->user_id)
		{
			$data = $this->record->getProperties();
			CEventsHelper::notify('record', CEventsHelper::_RECORD_APPROVED, $this->record->id, $this->record->section_id, 0, 0, 0, $data, 2, $this->record->user_id);
		}

		ATlog::log($this->record, ATlog::REC_PUBLISHED);

		$this->_finish(\Joomla\CMS\Language\Text::_('CMSG_RECPUBOK'));
	}

	public function sarchive()
	{


		if(!$this->_checkAccess('Archive', $this->input->getInt('id')))
		{
			return;
		}

		$this->record->archive = 1;
		$this->record->store();

		ATlog::log($this->record, ATlog::REC_ARCHIVE);


		$this->_finish(\Joomla\CMS\Language\Text::_('CMSG_RECARCHIVEOK'));

	}

	public function delete()
	{
		if(!$this->_checkAccess('Delete', $this->input->getInt('id')))
		{
			$this->_finish('CNORIGHTSTODELETE');

			return;
		}

		$type = ItemsStore::getType($this->record->type_id);

		if($type->params->get('audit.versioning'))
		{
			$versions = JTable::getInstance('Audit_versions', 'JoomcckTable');
			$version  = $versions->snapshot($this->input->getInt('id'), $type);
		}

		if(!$this->record->delete())
		{
			throw new GenericDataException('Cannot delete, something is wrong', 500);


			return;
		}

		$db = \Joomla\CMS\Factory::getDbo();

		$db->setQuery("DELETE FROM #__js_res_record_category WHERE record_id = " . $this->input->getInt('id'));
		$db->execute();

		$db->setQuery("DELETE FROM #__js_res_record_values WHERE record_id = " . $this->input->getInt('id'));
		$db->execute();

		$db->setQuery("DELETE FROM #__js_res_tags_history WHERE record_id = " . $this->input->getInt('id'));
		$db->execute();

		$db->setQuery("SELECT * FROM #__js_res_files WHERE record_id = " . $this->input->getInt('id'));
		$files = $db->loadObjectList('id');


		if(!empty($files) && !$type->params->get('audit.versioning'))
		{
			$field_table   = JTable::getInstance('Field', 'JoomcckTable');
			$joomcck_params = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');

			foreach($files AS $file)
			{
				$field_table->load($file->field_id);
				$field_params = new \Joomla\Registry\Registry($field_table->params);
				$subfolder    = $field_params->get('params.subfolder', $field_table->field_type);
				if(\Joomla\CMS\Filesystem\File::exists(JPATH_ROOT . DIRECTORY_SEPARATOR . $joomcck_params->get('general_upload') . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR . $file->fullpath))
				{
					unlink(JPATH_ROOT . DIRECTORY_SEPARATOR . $joomcck_params->get('general_upload') . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR . $file->fullpath);
				}
				// deleting image field files
				elseif(\Joomla\CMS\Filesystem\File::exists(JPATH_ROOT . DIRECTORY_SEPARATOR . $file->fullpath))
				{
					unlink(JPATH_ROOT . DIRECTORY_SEPARATOR . $file->fullpath);
				}
			}
			$db->setQuery("DELETE FROM #__js_res_files WHERE id IN (" . implode(',', array_keys($files)) . ")");
			$db->execute();
		}

		if($files)
		{
			$db->setQuery("UPDATE #__js_res_files SET `saved` = 2 WHERE id IN (" . implode(',', array_keys($files)) . ")");
			$db->execute();
		}

		if($type->params->get('audit.versioning'))
		{
			$restore['files']     = json_encode($files);
			$restore['record_id'] = $this->input->getInt('id');
			$restore['dtime']     = \Joomla\CMS\Factory::getDate()->toSql();

			$db->setQuery("SELECT * FROM #__js_res_comments WHERE record_id = " . $this->input->getInt('id'));
			$restore['comments'] = json_encode($db->loadAssocList());

			$db->setQuery("SELECT * FROM #__js_res_favorite WHERE record_id = " . $this->input->getInt('id'));
			$restore['favorites'] = json_encode($db->loadAssocList());

			$db->setQuery("SELECT * FROM #__js_res_hits WHERE record_id = " . $this->input->getInt('id'));
			$restore['hits'] = json_encode($db->loadAssocList());

			$db->setQuery("SELECT * FROM #__js_res_subscribe WHERE type = 'record' AND ref_id = " . $this->input->getInt('id'));
			$restore['subscriptions'] = json_encode($db->loadAssocList());

			$db->setQuery("SELECT * FROM #__js_res_vote WHERE (ref_id = " . $this->input->getInt('id') .
				" AND ref_type = 'record') OR (ref_id IN(SELECT id FROM #__js_res_comments WHERE record_id = " . $this->input->getInt('id') . ") AND ref_type = 'comment')");
			$restore['votes'] = json_encode($db->loadAssocList());

			$db->setQuery("SELECT * FROM #__js_res_notifications WHERE ref_1 = " . $this->input->getInt('id'));
			$restore['notifications'] = json_encode($db->loadAssocList());

			$restore['type_id'] = $type->id;

			$table = JTable::getInstance('Audit_restore', 'JoomcckTable');
			$table->save($restore);
		}

		$db->setQuery("DELETE FROM #__js_res_vote WHERE (ref_id = " . $this->input->getInt('id') .
			" AND ref_type = 'record') OR (ref_id IN(SELECT id FROM #__js_res_comments WHERE record_id = " . $this->input->getInt('id') . ") AND ref_type = 'comment')");
		$db->execute();

		$db->setQuery("DELETE FROM #__js_res_comments WHERE record_id = " . $this->input->getInt('id'));
		$db->execute();

		$db->setQuery("DELETE FROM #__js_res_favorite WHERE record_id = " . $this->input->getInt('id'));
		$db->execute();

		$db->setQuery("DELETE FROM #__js_res_hits WHERE record_id = " . $this->input->getInt('id'));
		$db->execute();

		$db->setQuery("DELETE FROM #__js_res_subscribe WHERE type = 'record' AND ref_id = " . $this->input->getInt('id'));
		$db->execute();

		$db->setQuery("DELETE FROM #__js_res_notifications WHERE ref_1 = " . $this->input->getInt('id'));
		$db->execute();

		$db->setQuery("DELETE FROM #__js_res_record WHERE parent = 'com_joomcck' AND parent_id = " . $this->input->getInt('id'));
		$db->execute();

		if($this->record->user_id)
		{
			$data = $this->record->getProperties();
			//CEventsHelper::notify('record', CEventsHelper::_RECORD_DELETED, $this->record->id, $this->record->section_id, 0, 0, 0, $data, 2, $this->record->user_id);
		}

		ATlog::log($this->record, ATlog::REC_DELETE);
		$this->_finish(\Joomla\CMS\Language\Text::_('CMSG_RECDELETEDOK'));

		JPluginHelper::importPlugin('mint');
		$dispatcher = \Joomla\CMS\Factory::getApplication();
		$dispatcher->triggerEvent('onRecordDelete', array($this->record));

		$this->setRedirect(JoomcckFilter::base64($this->input->getBase64('return')));

		return TRUE;
	}

	public function markread()
	{
		$app  = \Joomla\CMS\Factory::getApplication();
		$user = \Joomla\CMS\Factory::getUser();

		$db = \Joomla\CMS\Factory::getDbo();
		$db->setQuery("UPDATE #__js_res_notifications SET state_new = 0, notified = 1 WHERE user_id = " . $user->get('id') . ' AND ref_2 = ' . $this->input->getInt('section_id'));
		$db->execute();

		$app->enqueueMessage(\Joomla\CMS\Language\Text::_('EVENT_CLEAR'));

		if($this->input->getInt('section_id'))
		{
			$section = ItemsStore::getSection($this->input->getInt('section_id'));
			$url     = Url::records($section);
		}
		else
		{
			$url = $this->_getUrl();
		}
		$this->setRedirect(\Joomla\CMS\Router\Route::_($url, FALSE));
	}

	protected function _finish($msg, $err = FALSE)
	{
		if($err)
		{
			throw new GenericDataException($msg, 500);

		}
		else
		{
			if($msg)
			{
				$app = \Joomla\CMS\Factory::getApplication();
				$app->enqueueMessage($msg);
			}
		}
		$url = Url::get_back('return');
		$this->setRedirect(\Joomla\CMS\Router\Route::_($url, FALSE));
	}

	protected function _checkAccess($control, $id)
	{
		$this->record = JTable::getInstance('Record', 'JoomcckTable');
		$this->record->load($id);

		$this->record;

		if(!$this->record->id)
		{

			Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('No record found'),'warning');

			return FALSE;
		}

		$params               = new \Joomla\Registry\Registry($this->record->params);
		$this->record->params = $params;

		$this->type    = MModelBase::getInstance('Form', 'JoomcckModel')->getRecordType($this->record->type_id);
		$this->section = MModelBase::getInstance('Section', 'JoomcckModel')->getItem($this->record->section_id);

		$control = 'allow' . $control;

		if(!MECAccess::$control($this->record, $this->type, $this->section))
		{
			Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CERRNOACTIONACCESS'),'warning');
			return FALSE;
		}

		return TRUE;
	}


	public function cleanall()
	{
		$this->_clean();
		$app = \Joomla\CMS\Factory::getApplication();
		$app->enqueueMessage(\Joomla\CMS\Language\Text::_('CMSG_FILTERCLEANALL'));
		$url = $this->_getUrl();
		$this->setRedirect(\Joomla\CMS\Router\Route::_($url, FALSE));
	}

	private function _clean()
	{
		$app = \Joomla\CMS\Factory::getApplication();
		$key = FilterHelper::key();

		$sec_model = MModelBase::getInstance('Section', 'JoomcckModel');
		$section   = $sec_model->getItem($this->input->getInt('section_id'));

		$rec_model          = MModelBase::getInstance('Records', 'JoomcckModel');
		$rec_model->section = $section;
		$list               = $rec_model->getResetFilters();
		foreach($list as $filter)
		{
			echo $filter->key;
			$app->setUserState('com_joomcck.section' . $key . '.filter_' . $filter->key, '');
		}

		$app->setUserState('com_joomcck.section' . $key . '.filter_search', '');
		$app->setUserState('com_joomcck.section' . $key . '.filter_type', '');
		$app->setUserState('com_joomcck.section' . $key . '.filter_tag', '');
		$app->setUserState('com_joomcck.section' . $key . '.filter_user', '');
		$app->setUserState('com_joomcck.section' . $key . '.filter_alpha', '');
		$app->setUserState('com_joomcck.section' . $key . '.filter_cat', '');
	}

	public function clean()
	{
		$key   = FilterHelper::key();
		$app   = \Joomla\CMS\Factory::getApplication();
		$clean = $this->input->get('clean', array(), 'array');

		foreach($clean as $name => $val)
		{
			if($val)
			{
				$app->setUserState('com_joomcck.section' . $key . '.' . $name, NULL);
			}
		}

		$url = $this->_getUrl();
		$this->setRedirect(\Joomla\CMS\Router\Route::_($url, FALSE));
	}

	public function filters()
	{
		$key     = FilterHelper::key();
		$db      = \Joomla\CMS\Factory::getDbo();
		$app     = \Joomla\CMS\Factory::getApplication();
		$filters = $this->input->get('filters', array(), 'array');

		$sec_model = MModelBase::getInstance('Section', 'JoomcckModel');
		$section   = $sec_model->getItem($this->input->getInt('section_id'));

		$app->setUserState('com_joomcck.records' . $section->id . '.limitstart', 0);
		$app->setUserState('com_joomcck.section' . $key . '.filter_search', $this->input->get('filter_search', NULL, 'string'));

		//JError::raiseNotice(100, 'com_joomcck.section' . $key . '.filter_type');
		$app->setUserState('com_joomcck.section' . $key . '.filter_type', @$filters['type']);
		unset($filters['type']);

		$tags = @$filters['tags'];
		unset($filters['tags']);
		if(!is_array($tags))
		{
			$tags = explode(',', $tags);

			ArrayHelper::clean_r($tags);
			$tags = \Joomla\Utilities\ArrayHelper::toInteger($tags);
		}
		$app->setUserState('com_joomcck.section' . $key . '.filter_tag', $tags);

		$users = @$filters['users'];
		unset($filters['users']);
		if(!is_array($users))
		{
			$users = explode(',', $users);

			ArrayHelper::clean_r($users);
			$users = \Joomla\Utilities\ArrayHelper::toInteger($users);
		}
		$app->setUserState('com_joomcck.section' . $key . '.filter_user', $users);

		$cats = @$filters['cats'];
		unset($filters['cats']);
		if(!is_array($cats))
		{
			$cats = explode(',', $cats);

			ArrayHelper::clean_r($cats);
			$cats = \Joomla\Utilities\ArrayHelper::toInteger($cats);
		}
		$app->setUserState('com_joomcck.section' . $key . '.filter_cat', $cats);

		settype($filters, 'array');

		$rec_model          = MModelBase::getInstance('Records', 'JoomcckModel');
		$rec_model->section = $section;
		$list               = $rec_model->getFilters();
		$store              = array();
		foreach($list as $fkey => $filter)
		{
			$app->setUserState('com_joomcck.section' . $key . '.filter_' . $fkey, @$filters[$fkey]);
			if($filters)
			{
				$store[$filter->key] = $filter;
			}
		}

		$url = $this->_getUrl();
		$this->setRedirect(\Joomla\CMS\Router\Route::_($url, FALSE));
	}

	public function filter()
	{
		if($this->input->get('clean'))
		{
			$this->_clean();
		}

		$names = $this->input->get('filter_name', array(), 'array');
		$vals  = $this->input->get('filter_val', array(), 'array');

		foreach($names as $k => $name)
		{
			$key = FilterHelper::key();

			if($name == 'filter_tpl')
			{
				$key = $this->input->getInt('section_id');
				if($this->input->getInt('cat_id'))
				{
					$category = ItemsStore::getCategory($this->input->getInt('cat_id'));
					$t        = $category->params->get('tmpl_list');
					ArrayHelper::clean_r($t);
					if($t)
					{
						$key .= '-' . $this->input->getInt('cat_id');
					}
				}

				$oldname = \Joomla\CMS\Factory::getApplication()->getUserState('com_joomcck.section' . $key . '.filter_tpl', 'default');
				if($oldname != $vals[$k])
				{
					$section = ItemsStore::getSection($this->input->getInt('section_id'));
					$section->params->set('general.tmpl_list', $vals[$k]);
					$lparams = CTmpl::prepareTemplate('default_list_', 'general.tmpl_list', $section->params);

					\Joomla\CMS\Factory::getApplication()->setUserState('global.list.limit', $lparams->get('tmpl_core.item_limit_default', 20));
				}
			}
			preg_match('/^filter_([0-9]*)$/iU', $name, $match);
			if(!empty($match[1]))
			{
				$db = \Joomla\CMS\Factory::getDbo();
				$db->setQuery("SELECT `key` FROM #__js_res_fields WHERE id = " . $match[1]);
				$name = 'filter_' . $db->loadResult();
			}

			\Joomla\CMS\Factory::getApplication()->setUserState('com_joomcck.section' . $key . '.' . $name, $vals[$k]);
		}

		$url = $this->_getUrl();
		$this->setRedirect(\Joomla\CMS\Router\Route::_($url, FALSE));
	}

	public function copy()
	{
		$ids = $this->input->get('cid', array(), '', 'array');

		if(empty($ids))
		{
			throw new GenericDataException(\Joomla\CMS\Language\Text::_('JERROR_NO_ITEMS_SELECTED'), 500);
		}
		else
		{
			$model = MModelBase::getInstance('Item', 'JoomcckModel');

			if(!$model->copy($ids))
			{
				throw new GenericDataException($model->getError(), 500);
			}
		}

		if(\Joomla\CMS\Factory::getApplication()->input->getCmd('view') == 'items')
		{
			$url = Url::view('items', FALSE);
		}
		else
		{
			$url = \Joomla\CMS\Router\Route::_(Url::get_back('return'), FALSE);
		}

		$this->setRedirect($url);
	}

	public function checkin()
	{
		$ids = \Joomla\CMS\Factory::getApplication()->input->get('cid', array(), 'array');

		$model = MModelBase::getInstance('Item', 'JoomcckModel');

		$return = $model->checkin($ids);
		if($return === FALSE)
		{
			$message = \Joomla\CMS\Language\Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
		}
		else
		{
			$message = \Joomla\CMS\Language\Text::plural($this->text_prefix . '_N_ITEMS_CHECKED_IN', count($ids));
		}

		if(\Joomla\CMS\Factory::getApplication()->input->getCmd('view') == 'items')
		{
			$url = Url::view('items', FALSE);
		}
		else
		{
			$url = \Joomla\CMS\Router\Route::_(Url::get_back('return'), FALSE);
		}

		$this->setRedirect($url, $message);
	}

	public function reset_hits()
	{
		$this->reset('reset_hits');
	}

	public function reset_com()
	{
		$this->reset('reset_com');
	}

	public function reset_vote()
	{
		$this->reset('reset_vote');
	}

	public function reset_fav()
	{
		$this->reset('reset_fav');
	}

	public function reset_ctime()
	{
		$this->reset('reset_ctime');
	}

	public function reset_mtime()
	{
		$this->reset('reset_mtime');
	}

	public function reset_extime()
	{
		$this->reset('reset_extime');
	}

	public function reset($task)
	{

		$ids    = $this->input->get('cid', array(), '', 'array');
		if(empty($ids))
		{
			throw new GenericDataException(\Joomla\CMS\Language\Text::_('JERROR_NO_ITEMS_SELECTED'), 500);
		}
		else
		{
			$model = MModelBase::getInstance('Item', 'JoomcckModel');
			if(!$model->reset($ids, $task))
			{
				throw new GenericDataException($model->getError(), 500);
			}
		}

		$url = Url::view('items', FALSE);
		$this->setRedirect($url);
	}

	private function _getUrl()
	{
		$url = 'index.php?option=com_joomcck&view=records';
		if($s = $this->input->getInt('section_id'))
		{
			$url .= '&section_id=' . $s;
		}
		if($c = $this->input->getInt('cat_id'))
		{
			$url .= '&cat_id=' . $c;
		}
		if($uc = $this->input->getInt('ucat_id'))
		{
			$url .= '&ucat_id=' . $uc;
		}
		$u = $this->input->get('user_id', NULL);
		if(!is_null($u))
		{
			$u = (int)$u;
		}
		if($u || $u === 0)
		{
			$url .= '&user_id=' . $u;
			if($v = $this->input->get('view_what', 'created'))
			{
				$url .= '&view_what=' . $v;
			}
		}
		if($i = $this->input->getInt('Itemid'))
		{
			$url .= '&Itemid=' . $i;
		}
		if($l = $this->input->getInt('lang'))
		{
			$url .= '&lang=' . $l;
		}

		return $url;
	}
}