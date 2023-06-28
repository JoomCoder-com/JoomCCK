<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('mint.mvc.controller.admin');

require_once JPATH_LIBRARIES.'/mint/forms/formhelper.php';

class JoomcckControllerAjax extends MControllerAdmin
{

	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = JFactory::getApplication()->input;
		}
	}

	public function usermention()
	{
		$db    = JFactory::getDbo();
		$input = JFactory::getApplication()->input;

		if($input->get('id'))
		{
			$db->setQuery('SELECT params FROM `#__js_res_fields` WHERE id = ' . $input->getInt('id'));
			$params = new JRegistry($db->loadResult());
			if(!$params->get('params.mention'))
			{
				return;
			}
		}

		$query = "SELECT name, username FROM `#__users` WHERE username LIKE '%" . $db->escape($input->get('q')) . "%' OR name LIKE '%" . $db->escape(JFactory::getApplication()->input->get('q')) . "%'";
		$db->setQuery($query);
		$list = $db->loadObjectList();

		$out = array();
		foreach($list AS $user)
		{

			$out[] = $user;
		}

		echo json_encode($out);
		JFactory::getApplication()->close();
	}

	public function icons()
	{
		$dir = JPATH_ROOT . '/' . $this->input->getPath('dir');

		$out = array();
		if(\Joomla\CMS\Filesystem\Folder::exists($dir))
		{
			if($dh = opendir($dir))
			{
				while(($file = readdir($dh)) !== FALSE)
				{
					$ext = strtolower(substr($file, strrpos($file, '.') + 1));
					if($ext == 'png' || $ext == 'gif')
					{
						$out[] = $file;
					}
				}
				closedir($dh);
			}
		}

		AjaxHelper::send($out);
	}

	public function mainJS()
	{
		//$this->input->set('view', 'assets');
		//$this->input->set('layout', 'mainjs');

		header('content-type: application/javascript');

		include JPATH_ROOT . '/components/com_joomcck/library/js/main.js';

		JFactory::getApplication()->close();
	}

	public function trackcomment()
	{
		$record_id = $this->input->getInt('record_id');

		if(!$record_id)
		{
			return;
		}

		$record = ItemsStore::getRecord($record_id);

		CEventsHelper::notify('record', CEventsHelper::_COMMENT_NEW, $record->id, $record->section_id, 0, 0, 0, array());

		$user = JFactory::getUser();
		if($user->get('id'))
		{
			CSubscriptionsHelper::subscribe_record($record);
		}
		JFactory::getApplication()->close();
	}

	public function checkuser()
	{
		$user = $this->input->get('user');

		if(!$user)
		{
			AjaxHelper::error(JText::_('AJAX_ENTERUSERNAMEOREMAILORID'));
		}

		$user_id = 0;
		if(preg_match("/^[0-9]*$/iU", $user))
		{
			$user_id = JFactory::getUser($user)->get('id');
		}

		$db = JFactory::getDbo();

		if(JMailHelper::isEmailAddress($user))
		{
			$db->setQuery("SELECT id FROM #__users WHERE email = " . $db->quote($user));
			$user_id = $db->loadResult();
		}

		if(!$user_id)
		{
			$db->setQuery("SELECT id FROM #__users WHERE username = " . $db->quote($user));
			$user_id = $db->loadResult();
		}

		if(!$user_id)
		{
			$db->setQuery("SELECT id FROM #__users WHERE name = " . $db->quote($user));
			$user_id = $db->loadResult();
		}

		if(!$user_id)
		{
			AjaxHelper::error(JText::_('AJAX_USERNOTFOUND'));
		}

		AjaxHelper::send($user_id);
	}

	public function status()
	{
		$order = JTable::getInstance('Sales', 'JoomcckTable');
		$order->load($this->input->getInt('order_id'));

		if(!$order->id)
		{
			AjaxHelper::error(JText::_('AJAX_CANNOTLOADORDER'));
		}

		$record = ItemsStore::getRecord($order->record_id);

		$user         = JFactory::getUser();
		$orders_model = MModelBase::getInstance('Orders', 'JoomcckModel');

		if(!in_array($order->section_id, MECAccess::allowChangeSaleStatus($user)) && !$orders_model->isSuperUser($user->get('id')) && !($user->get('id') == $record->user_id))
		{
			AjaxHelper::error(JText::_('AJAX_CANNOTCHANGESTATUS'));
		}

		$order->status = $this->input->getInt('status');
		$order->store();

		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . 'tables/field.php');
		$field = JTable::getInstance('Field', 'JoomcckTable');
		$field->load($order->field_id);
		$field->params = new JRegistry($field->params);

		if($field->params->get('params.order_updated'))
		{
			CEventsHelper::notify('record', 'order_updated', $order->record_id, $order->section_id, 0, 0, $order->field_id, $order, 2, $order->user_id);
		}

		AjaxHelper::send(NULL);
	}

	public function unfollowallsection()
	{
		$user = JFactory::getUser();
		if(!$user->get('id'))
		{
			AjaxHelper::error(JText::_('AJAX_PLEASELOGIN'));
		}

		$db = JFactory::getDbo();
		$db->setQuery("DELETE FROM #__js_res_subscribe WHERE section_id = " . $this->input->getInt('section_id') . " AND user_id = " . $user->get('id'));
		if($db->execute())
		{
			AjaxHelper::send($db->getAffectedRows(), 'rows');
		}
		AjaxHelper::error(JText::_('Error'));
	}

	public function followallsection()
	{
		$user = JFactory::getUser();
		if(!$user->get('id'))
		{
			AjaxHelper::error(JText::_('AJAX_PLEASELOGIN'));
		}

		$db = JFactory::getDbo();
		$db->setQuery("SELECT id FROM #__js_res_record WHERE section_id = " . $this->input->getInt('section_id') . " AND (user_id = " . $user->get('id') . " OR access IN(" . implode(',', $user->getAuthorisedViewLevels()) . "))");
		$list = $db->loadColumn();

		$data  = array(
			'user_id'    => $user->get('id'),
			'type'       => 'record',
			'section_id' => $this->input->getInt('section_id')
		);
		$table = JTable::getInstance('Subscribe', 'JoomcckTable');
		$i     = 0;
		foreach($list as $id)
		{
			$data['ref_id'] = $id;
			$table->load($data);
			if(!$table->id)
			{
				$table->save($data);
				$i++;
			}
			$table->reset();
			$table->id = NULL;
		}

		AjaxHelper::send($i, 'rows');
	}

	public function followsection()
	{
		$user = JFactory::getUser();
		if(!$user->get('id'))
		{
			AjaxHelper::error(JText::_('AJAX_PLEASELOGIN'));
		}

		$data = array(
			'user_id'    => $user->get('id'),
			'type'       => 'section',
			'ref_id'     => $this->input->getInt('section_id'),
			'section_id' => $this->input->getInt('section_id')
		);

		$table = JTable::getInstance('Subscribe', 'JoomcckTable');
		$table->load($data);

		if($table->id)
		{
			$state = 1;
			$text  = JText::_('CSECFOLLOW');
			$table->delete();
		}
		else
		{
			$state = 0;
			$text  = JText::_('CFOLLOWINGSECION');
			$table->save($data);
		}

		$out = array(
			'success' => 1,
			'state'   => $state,
			'title'   => $text,
			'name'    => ItemsStore::getSection($this->input->getInt('section_id'))->name
		);
		echo json_encode($out);
		JFactory::getApplication()->close();
	}

	public function followcat()
	{
		$user = JFactory::getUser();
		if(!$user->get('id'))
		{
			AjaxHelper::error(JText::_('AJAX_PLEASELOGIN'));
		}

		$sdata  = array(
			'user_id'    => $user->get('id'),
			'type'       => 'category',
			'ref_id'     => $this->input->getInt('cat_id'),
			'section_id' => $this->input->getInt('section_id')
		);
		$stable = JTable::getInstance('Subscribe', 'JoomcckTable');
		$stable->load($sdata);

		$table = JTable::getInstance('Subscribecat', 'JoomcckTable');
		$data  = array(
			'user_id'    => $user->get('id'),
			'cat_id'     => $this->input->getInt('cat_id'),
			'section_id' => $this->input->getInt('section_id')
		);
		$table->load($data);

		$state = 0;
		if(!empty($stable->id))
		{
			$state = 1;
		}
		if(!empty($table->id))
		{
			$state = 1;
		}
		if(!empty($table->id) && !empty($table->exclude))
		{
			$state = 0;
		}

		if($table->id)
		{
			$table->exclude = $state;
			$table->store();
		}
		else
		{
			$data['exclude'] = $state;
			$table->save($data);
		}
		$text = $state == 0 ? JText::_('CCATFOLLOWING') : JText::_('CCATFOLLOW');

		$out = array(
			'success' => 1,
			'state'   => $state,
			'title'   => $text,
			'name'    => ItemsStore::getCategory($this->input->getInt('cat_id'))->title
		);

		echo json_encode($out);

		if(empty($stable->id))
		{
			$stable->save($sdata);
		}

		JFactory::getApplication()->close();
	}

	public function followuser()
	{
		$user = JFactory::getUser();
		if(!$user->get('id'))
		{
			AjaxHelper::error(JText::_('AJAX_PLEASELOGIN'));
		}

		$sdata  = array(
			'user_id'    => $user->get('id'),
			'type'       => 'user',
			'ref_id'     => $this->input->getInt('user_id'),
			'section_id' => $this->input->getInt('section_id')
		);
		$stable = JTable::getInstance('Subscribe', 'JoomcckTable');
		$stable->load($sdata);

		$table = JTable::getInstance('Subscribeuser', 'JoomcckTable');
		$data  = array(
			'user_id'    => $user->get('id'),
			'u_id'       => $this->input->getInt('user_id'),
			'section_id' => $this->input->getInt('section_id')
		);
		$table->load($data);

		$state = 0;
		if(!empty($stable->id))
		{
			$state = 1;
		}
		if(!empty($table->id))
		{
			$state = 1;
		}
		if(!empty($table->id) && !empty($table->exclude))
		{
			$state = 0;
		}

		if($table->id)
		{
			$table->exclude = $state;
			$table->store();
		}
		else
		{
			$data['exclude'] = $state;
			$table->save($data);
		}
		$text  = $state == 0 ?
			JText::sprintf('CUSERFOLLOWING', CCommunityHelper::getName($this->input->getInt('user_id'), $this->input->getInt('section_id'), array('nohtml' => 1))) :
			JText::sprintf('CUSERFOLLOW', CCommunityHelper::getName($this->input->getInt('user_id'), $this->input->getInt('section_id'), array('nohtml' => 1)));
		$text2 = JText::sprintf('CUSERUNFOLLOW', CCommunityHelper::getName($this->input->getInt('user_id'), $this->input->getInt('section_id'), array(
			'nohtml' => 1
		)));

		$out = array(
			'success' => 1,
			'state'   => $state,
			'title'   => $text,
			'title2'  => $text2,
			'name'    => CCommunityHelper::getName($this->input->getInt('user_id'), $this->input->getInt('section_id'), array(
				'nohtml' => 1
			))
		);
		echo json_encode($out);

		if(empty($stable->id))
		{
			$stable->save($sdata);
		}

		JFactory::getApplication()->close();
	}

	public function follow()
	{
		$user = JFactory::getUser();
		if(!$user->get('id'))
		{
			AjaxHelper::error(JText::_('AJAX_PLEASELOGIN'));
		}

		$record = JTable::getInstance('Record', 'JoomcckTable');
		$record->load($this->input->getInt('record_id'));

		$data = array(
			'user_id'    => $user->get('id'),
			'type'       => 'record',
			'ref_id'     => $this->input->getInt('record_id'),
			'section_id' => $record->section_id
		);

		$table = JTable::getInstance('Subscribe', 'JoomcckTable');
		$table->load($data);

		if($table->id)
		{
			$state = 0;
			$text  = JText::_('CMSG_CLICKTOFOLLOW');
			$table->delete();
		}
		else
		{
			$state = 1;
			$text  = JText::_('CMSG_CLICKTOUNFOLLOW');
			$table->save($data);
		}

		$record->onFollow();

		$out = array(
			'success' => 1,
			'state'   => $state,
			'title'   => $text,
			'rtitle'  => $record->title
		);
		echo json_encode($out);
		JFactory::getApplication()->close();
	}

	public function compareclean()
	{
		$app = JFactory::getApplication();

		$scope = "compare";
		if($app->input->get('section_id'))
		{
			$scope .= ".set" . $app->input->get('section_id');
		}

		$app->setUserState($scope, NULL);

		AjaxHelper::send(1);
	}

	public function compare()
	{
		$rid = $this->input->getInt('record_id');
		$sid = $this->input->getInt('section_id');

		$app  = JFactory::getApplication();
		$list = $app->getUserState("compare.set{$sid}");
		ArrayHelper::clean_r($list);

		$record = JTable::getInstance('Record', 'JoomcckTable');
		$record->load($rid);

		$type = ItemsStore::getType($record->type_id);

		if(!$type->params->get('properties.item_compare'))
		{
			AjaxHelper::error(JText::_('AJAX_COMPARENOTALLOED'));
		}

		if(count($list) >= $type->params->get('properties.item_compare'))
		{
			AjaxHelper::error(JText::sprintf('AJAX_COMPARELIMIT', $type->params->get('properties.item_compare')));
		}

		$list[] = $rid;
		$app->setUserState("compare.set{$sid}", $list);

		AjaxHelper::send(count($list));
	}

	public function repost()
	{
		$user = JFactory::getUser();
		if(!$user->get('id'))
		{
			AjaxHelper::error(JText::_('AJAX_PLEASELOGIN'));
		}

		$record = JTable::getInstance('Record', 'JoomcckTable');
		$record->load($this->input->getInt('record_id'));

		$data  = array(
			'host_id'   => $user->get('id'),
			'record_id' => $this->input->getInt('record_id')
		);
		$table = JTable::getInstance('Reposts', 'JoomcckTable');
		$table->load($data);

		if(!$table->id)
		{
			$data['ctime']       = JFactory::getDate()->toSql();
			$data['is_reposted'] = 1;
			$table->reset();
			$table->bind($data);
			if(!$table->store())
			{
				AjaxHelper::error(JText::_($table->getError()));
			}
			$record->onRepost();

			$record->data = $data;

			CEventsHelper::notify('record', CEventsHelper::_RECORD_REPOSTED, $this->input->getInt('record_id'), $this->input->getInt('section_id'), 0, 0, 0, $record, 2, $record->user_id);
		}

		$out = array(
			'success' => 1,
			'title'   => $record->title
		);
		echo json_encode($out);
		JFactory::getApplication()->close();
	}

	public function bookmark()
	{
		$user = JFactory::getUser();
		if(!$user->get('id'))
		{
			AjaxHelper::error(JText::_('AJAX_PLEASELOGIN'));
		}

		$record = JTable::getInstance('Record', 'JoomcckTable');
		$record->load($this->input->getInt('record_id'));

		$data  = array(
			'user_id'   => $user->get('id'),
			'record_id' => $this->input->getInt('record_id')
		);
		$table = JTable::getInstance('Favorites', 'JoomcckTable');
		$table->load($data);

		if($table->id)
		{
			$table->delete();
			$state = 0;
			$text  = JText::_('CMSG_ADDBOOKMARK');
		}
		else
		{
			$data['section_id'] = $record->section_id;
			$data['ctime']      = JFactory::getDate()->toSql();
			$data['type_id']    = $record->type_id;
			$table->reset();
			$table->bind($data);
			if(!$table->store())
			{
				AjaxHelper::error(JText::_($table->getError()));
			}
			$state = 1;
			$text  = JText::_('CMSG_REMOVEBOOKMARK');

			$data = $record->getProperties();

			CEventsHelper::notify('record', CEventsHelper::_RECORD_BOOKMARKED, $this->input->getInt('record_id'), $record->section_id, 0, 0, 0, $data);
		}

		$record->onBookmark();

		$out = array(
			'success' => 1,
			'state'   => $state,
			'title'   => $text,
			'rtitle'  => $record->title
		);
		echo json_encode($out);
		JFactory::getApplication()->close();
	}

	public function removeucicon()
	{
		$file     = $this->input->get('file');
		$id       = $this->input->get('id');
		$user     = JFactory::getUser();
		$fullpath = JPATH_ROOT . '/images/usercategories/' . $user->get('id') . DIRECTORY_SEPARATOR . $file;
		if(JFile::exists($fullpath))
		{
			if(JFile::delete($fullpath))
			{
				$db = JFactory::getDbo();
				$db->setQuery('UPDATE #__js_res_category_user SET icon = "" WHERE id = ' . $id);
				$db->execute();
				AjaxHelper::send(1);
			}
		}
		else
		{
			AjaxHelper::error(JText::_('CFILENOTEXISTS'));
		}
	}

	public function category_children()
	{
		$db      = JFactory::getDbo();
		$parent  = $this->input->get('parent');
		$section = $this->input->get('section');

		$db->setQuery(
			"SELECT
				c.id,
				c.title,
				CONCAT(s.name, '/', c.path) AS path,
				c.params,
				c.section_id,
				s.name as section_name,
				(SELECT count(id)
					FROM `#__js_res_categories`
					WHERE parent_id = c.id
				)  as children
			FROM `#__js_res_categories` AS c
			LEFT JOIN `#__js_res_sections` AS s ON s.id = c.section_id
			WHERE c.published = 1
			AND c.section_id = {$section}
			AND c.parent_id = {$parent}
			ORDER BY c.lft ASC
		");

		$categories = $db->loadObjectList();

		foreach($categories as &$cat)
		{
			$cat->params = json_decode($cat->params);
			$cat->title  = htmlentities($cat->title, ENT_QUOTES, 'UTF-8');
			$cat->path   = htmlentities($cat->path, ENT_QUOTES, 'UTF-8');
		}

		echo json_encode($categories);
		JFactory::getApplication()->close();
	}

	public function category_childs()
	{
		require_once JPATH_ROOT . '/components/com_joomcck/models/category.php';
		$cat_model  = new JoomcckModelCategory();
		$cats_model = MModelBase::getInstance('Categories', 'JoomcckModel');

		$db                    = JFactory::getDbo();
		$level                 = $this->input->getInt('level');
		$category              = $cat_model->getItem($this->input->getInt('cat_id'));
		$cats_model->section   = $category->section_id;
		$cats_model->parent_id = $category->id;
		$cats_model->order     = $this->input->get('order', 'c.lft ASC');
		$cats_model->levels    = 1;
		$cats_model->all       = 0;
		$cats_model->nums      = 1;
		$categories            = $cats_model->getItems();

		AjaxHelper::send($categories);
	}

	public function category_filter()
	{
		$section_id = $this->input->getInt('section_id');
		$empty_cats = $this->input->getInt('empty_cats');

		$db = JFactory::getDbo();

		$sql = $db->getQuery(TRUE);
		$sql->select('id, title, path, image, description, params');
		$sql->from('#__js_res_categories');
		$sql->where('published = 1');
		$sql->where('section_id = ' . $section_id);
		$db->setQuery($sql);

		$categories = $db->loadObjectList();

		if(!$empty_cats)
		{
			$section = ItemsStore::getSection($section_id);
			$user = JFactory::getUser();

			$sql = $db->getQuery(true);
			$sql->select('count(rc.catid) as num, rc.catid');
			$sql->from('#__js_res_record_category AS rc');
			$sql->where("rc.section_id = {$section->id}");
			$sql->group('rc.catid');

			if($section->params->get('general.cat_mode'))
			{
				$sql2 = $db->getQuery(true);
				$sql2->select('r.id');
				$sql2->from('#__js_res_record AS r');
				if(CStatistics::hasUnPublished($section->id))
				{
					$sql2->where('r.published = 1');
				}
				$sql2->where('r.hidden = 0');
				$sql2->where('r.section_id = ' . $section->id);

				if(!in_array($section->params->get('general.show_restrict'), $user->getAuthorisedViewLevels()) && !MECAccess::allowRestricted($user, $section))
				{
					$sql2->where("(r.access IN(" . implode(',', $user->getAuthorisedViewLevels()) . ") OR r.user_id = " . $user->get('id') . ")");
				}

				if(!in_array($section->params->get('general.show_future_records'), $user->getAuthorisedViewLevels()))
				{
					$sql2->where("r.ctime < " . $db->quote(JFactory::getDate()->toSql()));
				}

				if(!in_array($section->params->get('general.show_past_records'), $user->getAuthorisedViewLevels()))
				{
					$sql2->where("(r.extime = '0000-00-00 00:00:00' OR ISNULL(extime) OR r.extime > '" . JFactory::getDate()->toSql() . "')");
				}

				if(!in_array($section->params->get('general.show_children'), $user->getAuthorisedViewLevels()))
				{
					$sql2->where("r.parent_id = 0");
				}

				if($section->params->get('general.lang_mode'))
				{
					$sql2->where('r.langs = ' . $db->quote(JFactory::getLanguage()->getTag()));
				}

				$sql->where('rc.record_id IN (' . $sql2 . ')');
			}
			$db->setQuery($sql);

			$nums = $db->loadAssocList("catid", "num");
		}

		foreach($categories as $i => $cat)
		{
			if(!$empty_cats && !isset($nums[$cat->id]))
			{
				unset($categories[$i]);
				continue;
			}
			$params = new JRegistry($cat->params);
			if(!$params->get('submission'))
			{
				continue;
			}

			$html = '';

			if($cat->image)
			{
				$html .= '<img class="float-start img-polaroid" src="' . JURI::root(TRUE) . '/' . $cat->image . '" style="max-width:50px;" hspace="5" align="absmiddle">';
			}
			$html .= $cat->title;
			$html .= '<br><small>' . $cat->path . '</small>';

			if($cat->description)
			{
				$html .= '<br><small>' . substr(trim(strip_tags($cat->description)), 0, 200) . '</small>';
			}
			$html .= '<div class="clearfix"></div>';
			$out[] = array(
				"id" => $cat->id,
				"html" => $html,
				"text" => $cat->title
			);
		}

		AjaxHelper::send($out);
	}

	public function users_filter()
	{
		$section_id = $this->input->getInt('section_id');
		$section    = ItemsStore::getSection($section_id);

		$db = JFactory::getDbo();

		$sql = $db->getQuery(TRUE);
		$sql->select('id, ' . $section->params->get('personalize.author_mode', 'username') . ' AS text');
		$sql->from('#__users');
		$sql->where("id IN(SELECT user_id FROM #__js_res_record WHERE section_id = {$section->id})");

		$db->setQuery($sql);

		$users = $db->loadObjectList();

		foreach($users as $i => $user)
		{
            $users[$i]->suggest = '<img src="' . CCommunityHelper::getAvatar($user->id, 24, 24) . '" /> ' . $user->text;
		}

		AjaxHelper::send($users);
	}

	public function category_records()
	{
		$catid = $this->input->getInt('cat_id');
		$limit = $this->input->getInt('rec_limit');

		$db = JFactory::getDbo();

		$sql = $db->getQuery(TRUE);
		$sql->select('*');
		$sql->from('#__js_res_record');
		$sql->where('published = 1');
		$sql->where('hidden = 0');
		$sql->where("ctime < " . $db->quote(JFactory::getDate()->toSql()));
		$sql->where("(extime = '0000-00-00 00:00:00' OR ISNULL(extime) OR extime > '" . JFactory::getDate()->toSql() . "')");
		$sql->where("id IN (SELECT record_id FROM #__js_res_record_category WHERE catid = '{$catid}')");
		$db->setQuery($sql, 0, ($limit ? $limit + 1 : 0));
		$items = array();
		if($recs = $db->loadObjectList())
		{
			foreach($recs as $rec)
			{
				$rec->url = Url::record($rec);
				$items[]  = $rec;
			}
		}
		AjaxHelper::send($items);
	}

	public function tags_list()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(TRUE);
		$query->select("tag as text, id"); // as html, tag as plain, tag as text");
		$query->from("#__js_res_tags");
		$db->setQuery($query);
		$list   = $db->loadObjectList();
		
		AjaxHelper::send($list);
	}

	public function tags_list_filter()
	{
        $db    = JFactory::getDbo();
        $app = JFactory::getApplication();
        
        $query = $db->getQuery(TRUE);
		$query->select("id, tag as text");
		$query->from(" #__js_res_tags ");
		$query->where("id IN (SELECT tag_id FROM #__js_res_tags_history AS th WHERE th.section_id = " . $app->input->getInt('section_id', 0) . ")");
		$db->setQuery($query);
		$list   = $db->loadObjectList();

		AjaxHelper::send($list);
	}

	public function remove_tag()
	{
		$tag_id = $this->input->getInt('tid');

		// don't allow null keys removing
		if(!$tag_id)
			die('Error');


		$table  = JTable::getInstance('Taghistory', 'JoomcckTable');
		$table->load(array(
			'tag_id'    => $tag_id,
			'record_id' => $this->input->getInt('rid')
		));
		$table->delete();
		$record = JTable::getInstance('Record', 'JoomcckTable');
		$record->load($this->input->getInt('rid'));
		$rtags = ($record->tags != '') ? json_decode($record->tags, 1) : array();
		$new   = $rtags[$tag_id];
		unset($rtags[$tag_id]);
		$record->tags = json_encode($rtags);
        $record->store();

		$record->new = $new;
		ATlog::log($record, ATlog::REC_TAGDELETE);

		AjaxHelper::send(0);
	}

	public function add_tags()
	{
		$user = JFactory::getUser();
		$tag_table     = JTable::getInstance('Tags', 'JoomcckTable');
		$taghist_table = JTable::getInstance('Taghistory', 'JoomcckTable');
        
		$record = JTable::getInstance('Record', 'JoomcckTable');
        $record->load($this->input->getInt('rid'));
        
		$tag_id  = $this->input->get('tid');
        $tag_text  = $this->input->get('text');


        if(!preg_match("/^[0-9]*$/", $tag_id) || empty($tag_id))
        {
            $tag_table->reset();
            $tag_table->id = NULL;
            $tag_table->load(array(
                'tag' => $tag_text
            ));
            if(!$tag_table->id)
            {
                $tag_table->save(array(
                    'tag' => $tag_text
                ));
            }
            $tag_id = $tag_table->id;
        }
        
		$data = $new = [];
        
		$data['record_id']  = $record->id;
		$data['section_id'] = $record->section_id;
		$data['tag_id'] = $tag_id;
        
        $taghist_table->reset();
		$taghist_table->id = NULL;
		$taghist_table->load($data);
		if(!$taghist_table->id)
		{
            $data['user_id'] = $user->id;
			$taghist_table->save($data);
        }
        
        $rtags = ($record->tags != '') ? json_decode($record->tags, 1) : [];
		$rtags[$tag_id] = $tag_text;

		$record->tags = json_encode($rtags);
		$record->store();
        
        $data = $record->getProperties();

		$record->new = [$tag_id => $tag_text];
		ATlog::log($record, ATlog::REC_TAGNEW);

		CEventsHelper::notify('record', CEventsHelper::_RECORD_TAGGED, $record->id, $record->section_id, 0, 0, 0, $data);

		AjaxHelper::send([$tag_id => $tag_text]);
	}

	public function field_call()
	{
		$id = $this->input->getInt('field_id');
		if(!$id)
		{
			AjaxHelper::error(JText::_('AJAX_NOFIELDID'));
		}

		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . 'tables/field.php');
		$field_table = JTable::getInstance('Field', 'JoomcckTable');
		$field_table->load($id);

		$field     = $field_table->field_type;
		$func      = $this->input->get('func');
		$record_id = $this->input->getInt('record_id');
		$params    = $_REQUEST;

		if(!$field)
		{
			AjaxHelper::error(JText::_('AJAX_NOFIELDNAME'));
		}

		if(!$func)
		{
			AjaxHelper::error(JText::_('AJAX_NOFUNCNAME'));
		}

		$field_path = JPATH_ROOT . '/components/com_joomcck/fields' . DIRECTORY_SEPARATOR . $field . DIRECTORY_SEPARATOR . $field . '.php';
		$classname  = 'JFormFieldC' . ucfirst($field);

		if($field == 'upload')
		{
			$field_path = JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckupload.php';
			$classname  = 'CFormFieldUpload';
		}

		if(!JFile::exists($field_path))
		{
			AjaxHelper::error(JText::_('AJAX_FIELDNOTFOUND' . $id));
		}

		require_once $field_path;

		$default = array();
		$record  = NULL;
		if($record_id)
		{
			$record_model = MModelBase::getInstance('Record', 'JoomcckModel');
			$record       = $record_model->getItem($record_id);
			$values       = json_decode($record->fields, TRUE);
			$default      = @$values[$id];
		}

		if(!class_exists($classname))
		{
			AjaxHelper::error(JText::_('CCLASSNOTFOUND'));
		}

		$fieldclass = new $classname($field_table, $default);

		if(!method_exists($fieldclass, $func))
		{
			AjaxHelper::error(JText::_('AJAX_METHODNOTFOUND'));
		}

		$result = $fieldclass->$func($params, $record);

		if($fieldclass->getErrors())
		{
			AjaxHelper::error(JText::_($fieldclass->getError()));
		}


		// adapt to autocomplete
		if(is_array($result)){

			$autoCompleteResult = [];

			foreach ($result as $item){

				if(is_array($item)){
					$autoCompleteResult[] = ['id' => $item['id'], 'text' => $item['title']];
				}else{
					$autoCompleteResult[] = ['id' => $item, 'text' => $item];
				}



			}

			$result = $autoCompleteResult;

		}


		AjaxHelper::send($result);

		JFactory::getApplication()->close();
	}

	public function mark_notification()
	{
		$id         = $this->input->get('id');
		$section_id = $this->input->getInt('section_id');
		$client     = $this->input->getInt('client', 'module');

		if($id == 'all')
		{
			$user  = JFactory::getUser();
			$db    = JFactory::getDbo();
			$query = $db->getQuery(TRUE);
			$query->update('#__js_res_notifications');
			if($client == 'component')
			{
				$query->set('state_new = 0');
			}

			$query->set('notified = 1');
			$query->where('user_id = ' . $user->id);
			if($section_id)
			{
				$query->where('ref_2 = ' . $section_id);
			}
			$db->setQuery($query);
			$db->execute();
			if($db->getErrors())
			{
				AjaxHelper::error(JText::_($db->getError()));
			}
		}
		else
		{

			$table = JTable::getInstance('Notificat', 'JoomcckTable');
			$table->load($id);

			if($client == 'component')
			{
				$table->state_new = 0;
			}

			$table->notified = 1;
			$table->store();
			if($table->getErrors())
			{
				AjaxHelper::error(JText::_($table->getError()));
			}
		}

		AjaxHelper::send(0);
	}

	public function remove_notification()
	{
		$id         = $this->input->getInt('id');
		$section_id = $this->input->getInt('section_id');

		$user  = JFactory::getUser();
		$db    = JFactory::getDbo();
		$query = $db->getQuery(TRUE);
		$query->delete();
		$query->from('#__js_res_notifications');
		$query->where('user_id = ' . $user->id);
		if($section_id)
		{
			$query->where('ref_2 = ' . $section_id);
		}
		if($id != 'all')
		{
			if(!is_array($id))
			{
				settype($id, 'array');
			}
			$query->where('id IN (' . implode(', ', $id) . ')');
		}
		$db->setQuery($query);
		$db->execute();
		if($db->getErrors())
		{
			AjaxHelper::error(JText::_($db->getError()));
		}

		AjaxHelper::send(0);
	}

	public function remove_notification_by()
	{
		$type = $this->input->get('type');
		$list = $this->input->get('list');
		if(!count($list) && $type != 'read')
		{
			echo json_encode(
				array(
					'success' => '0'
				)
			);

			return;
		}
		$section_id = $this->input->getInt('section_id');
		$db         = JFactory::getDbo();
		if($type == 'selected')
		{
			$ids = $list;
		}
		else
		{
			$user  = JFactory::getUser();
			$query = $db->getQuery(TRUE);
			$query->select(' id ');
			$query->from('#__js_res_notifications');
			$query->where(' user_id = ' . $user->id);
			if($section_id)
			{
				$query->where('ref_2 = ' . $section_id);
			}

			switch($type)
			{
				case 'event':
					$query->where("type IN ('" . implode("', '", $list) . "')");
					break;
				case 'record':
					$query->where("ref_1 IN (" . implode(",", $list) . ")");
					break;
				case 'section':
					$query->where("ref_2 IN (" . implode(",", $list) . ")");
					break;
				case 'eventer':
					$query->where("eventer IN (" . implode(",", $list) . ")");
					break;
				case 'read':
					$query->where("state_new = 0");
					break;
			}

			$db->setQuery($query);
			$ids = $db->loadColumn();
		}

		if(!count($ids))
		{
			return;
		}

		$query = $db->getQuery(TRUE);
		$query->delete();
		$query->from('#__js_res_notifications');
		$query->where("id IN (" . implode(", ", $ids) . ")");
		$db->setQuery($query);
		$db->execute();
		if($db->getErrors())
		{
			AjaxHelper::error(JText::_($db->getError()));

			return;
		}

		AjaxHelper::send($ids);
	}

	public function get_notifications()
	{
		$ids        = $this->input->get('exist', array(0), 'array');
		$section_id = $this->input->getInt('section_id');
		$user       = JFactory::getUser();
		$db         = JFactory::getDbo();
		$query      = $db->getQuery(TRUE);

		$query->select('*');
		$query->from('#__js_res_notifications');
		$query->where('user_id = ' . $user->id);
		$query->where('notified = 0');
		if($section_id)
		{
			if(!is_array($section_id))
			{
				settype($section_id, 'array');
			}
			$query->where('ref_2 IN (' . implode(', ', $section_id) . ')');
		}
		if(count($ids))
		{
			$query->where('id NOT IN (' . implode(', ', $ids) . ')');
		}
		$query->order('ctime DESC');

		$query .= " LIMIT 0, " . $this->input->get('notiflimit', 5);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if($db->getError())
		{
			AjaxHelper::error(JText::_($db->getError()));

			return " ";
		}
		$list = array();
		$k    = 0;
		foreach($result as $i => $item)
		{
			$list[$i]       = new stdClass();
			$list[$i]->id   = $item->id;
			$list[$i]->html = CEventsHelper::get_notification($item, FALSE);
		}

		AjaxHelper::send($list);
	}

	public function remove_log()
	{
		$id = $this->input->getInt('id');

		$db    = JFactory::getDbo();
		$query = $db->getQuery(TRUE);
		$query->delete();
		$query->from('#__js_res_audit_log');
		if(!is_array($id))
		{
			settype($id, 'array');
		}
		$query->where('id IN (' . implode(', ', $id) . ')');

		$db->setQuery($query);
		$db->execute();
		if($db->getErrors())
		{
			echo AjaxHelper::error(JText::_($db->getError()));
		}

		AjaxHelper::send(0);
	}
	public function loadcommentparams()
	{
		$folder = $this->input->get('adp');

		if(!$folder)
		{
			exit();
		}

		$file = JPATH_ROOT. '/components/com_joomcck/library/php/comments'. DIRECTORY_SEPARATOR .$folder. DIRECTORY_SEPARATOR .$folder.'.xml';
		if(!JFile::exists($file))
		{
			exit();
		}

		$form = new JForm('params', array(
			'control' => 'params'
		));

		$id = $this->input->getInt('type');

		$default = array();
		if($id)
		{
			$type = MModelBase::getInstance('CType', 'JoomcckModel')->getItem($id);
			$default = $type->params;
		}

		$form->loadFile($file);


		$out = MFormHelper::renderGroup($form, $default, 'comments');
		echo $out;
		JFactory::getApplication()->close();
	}
	public function loadfieldparams()
	{
		$xml = JPATH_ROOT.$this->input->getString('dir').DIRECTORY_SEPARATOR.str_replace('.php', '.xml', $this->input->get('value'));
		$xml = JPath::clean($xml);
		$form = '';
		
		$segments = explode('/', $this->input->getString('dir'));
		FieldHelper::loadLang($segments[4]);
		if(JFile::exists($xml)) {
			$form = MFormHelper::getFieldParams($xml, $this->input->get('fid'), str_replace('.php', '', $this->input->get('value')));
		}

		echo $form;

		JFactory::getApplication()->close();
	}
	public function loadfieldform()
	{
		echo MModelBase::getInstance('TField', 'JoomcckModel')->getFieldForm($this->input->get('field'));
		JFactory::getApplication()->close();
	}

	public function loadcommerce()
	{
		$gateway = $this->input->get('gateway');
		if($gateway == '')
		{
			echo '';
			exit();
		}

		$field = MModelBase::getInstance('TField', 'JoomcckModel')->getItem($this->input->get('fid'));

		$xml = JPATH_ROOT . '/components/com_joomcck/gateways' . DIRECTORY_SEPARATOR . $gateway . DIRECTORY_SEPARATOR . $gateway . '.xml';
		if(! JFile::exists($xml))
		{
			echo "File not found: {$xml}";
		}
		$out = array();

		$form = new JForm('params', array(
			'control' => 'params'
		));

		$form->loadFile($xml);

		$out[] = MEFormHelper::renderGroup($form, ($field ? $field->params : array()), 'pay', FORM_STYLE_TABLE, FORM_SEPARATOR_NONE);

		echo implode(' ', $out);
		JFactory::getApplication()->close();
	}

	public function loadpacksection()
	{
		echo MModelBase::getInstance('Packsection', 'JoomcckModel')->getSectionForm($this->input->get('id'));
		JFactory::getApplication()->close();
	}

	public function loadsectiontypes()
	{
		$section_id = $this->input->getInt('section_id');
		$selected = $this->input->get('selected', array(), 'array');
		if(!$section_id)
		{
			echo '';
			JFactory::getApplication()->close();
		}

		$section = ItemsStore::getSection($section_id);

		$params = new JRegistry($section->params);
		$db = JFactory::getDbo();
		$query = $db->getQuery ( true );

		$query->select ( 'id, name' );
		$query->from ( '#__js_res_types' );
		$query->where ( 'published = 1' );
		$ids = implode(',', $params->get('general.type'));
		$query->where ( 'id IN (0'.($ids ? ','.$ids : '').')' );
		$db->setQuery($query);
		$list = $db->loadObjectList();
		if(empty($list))
		{
			echo '';
			JFactory::getApplication()->close();
		}
		foreach ($list as $val)
		{
			$types[] = JHTML::_('select.option', $val->id, $val->name);
		}

		echo JHTML::_('select.options', $types, 'value', 'text', $selected);
		JFactory::getApplication()->close();
	}
}
