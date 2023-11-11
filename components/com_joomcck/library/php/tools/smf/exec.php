<?php
defined('_JEXEC') or die();

$migration = new SMF2Joomcck();
$migration->migrate($params);

class SMF2Joomcck {
	public function migrate($params)
	{
		\Joomla\CMS\Table\Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_joomcck/tables');
		\Joomla\CMS\MVC\Model\BaseDatabaseModel::addIncludePath(JPATH_ROOT . '/components/com_joomcck/models');

		$this->record   = \Joomla\CMS\Table\Table::getInstance('Record', 'JoomcckTable');
		$this->category = \Joomla\CMS\Table\Table::getInstance('Record_category', 'JoomcckTable');
		$this->comments = \Joomla\CMS\Table\Table::getInstance('Cobcomments', 'JoomcckTable');
		$this->fields   = $this->getFields($params->get('type_id'));
		$this->values   = \Joomla\CMS\Table\Table::getInstance('Record_values', 'JoomcckTable');
		$this->follow   = \Joomla\CMS\Table\Table::getInstance('Subscribe', 'JoomcckTable');

		$this->type    = $this->getType($params->get('type_id'));
		$this->section = $this->getSection($params->get('section_id'));


		$this->cats_ids = $this->_getCats($params);

		$this->import($params);
	}

	private function import($params)
	{
		$db = \Joomla\CMS\Factory::getDbo();
		$db->setQuery("SELECT * FROM smf_topics LIMIT 1");
		$list = $db->loadObjectList();

		if(empty($list))
		{
			return;
		}

		foreach($list AS $record)
		{
			$catid = $this->cats_ids[$record->ID_BOARD];
			if(empty($catid))
			{
				continue;
			}

			$this->record->reset();
			$this->record->id = NULL;

			$data['user_id']    = $this->getUser($record->ID_MEMBER_STARTED);
			$data['type_id']    = $params->get('type_id');
			$data['section_id'] = $params->get('section_id');
			$data['published']  = 1;
			$data['access']     = 1;
			$data['hits']       = $record->numViews;

			$message = $this->_getMessage($record->ID_FIRST_MSG);

			$data['title'] = $message->subject;
			$data['ctime'] = \Joomla\CMS\Date\Date::getInstance($message->posterTime)->toSql();
			$data['mtime'] = $message->modifiedTime ? \Joomla\CMS\Date\Date::getInstance($message->modifiedTime)->toSql() : $data['ctime'];
			$data['ip']    = $message->posterIP;


			$category                      = array($catid => $this->_getCatToSave($catid));
			$data['categories']            = json_encode($category);
			$_REQUEST['jform']['category'] = $this->cats_ids[$record->ID_BOARD];

			$fields_data = array(
				$params->get('text_field') => $this->_BB2HTML($message->body),
			);

			$data['fields'] = json_encode($fields_data);

			$this->record->bind($data);
			if(!$this->record->check_cli())
			{
				echo $this->record->getError();
				exit;
			}
			$this->record->store();

			$db->setQuery("DELETE FROM smf_topics WHERE ID_TOPIC = " . $record->ID_TOPIC);
			$db->execute();

			$this->saveComments($record->ID_TOPIC, $record->ID_FIRST_MSG, $params);
			$this->saveCategories($catid);
			$this->saveFields($fields_data);
			$this->saveFollows($record->ID_TOPIC);

		}

		$this->import();
	}

	private function saveComments($id, $not, $params)
	{
		$db = \Joomla\CMS\Factory::getDbo();
		$db->setQuery('SELECT * FROM smf_messages WHERE ID_TOPIC = ' . $id . ' AND ID_TOPIC != ' . $not);

		$list = $db->loadObjectList();
		foreach($list As $item)
		{
			$user = $this->getUser($item->ID_MEMBER);

			if(!$item->body)
			{
				continue;
			}

			$data = array(
				'user_id'    => $user,
				'record_id'  => $this->record->id,
				'section_id' => $params->get('section_id'),
				'type_id'    => $params->get('type_id'),
				'comment'    => $this->_BB2HTML($item->body),
				'level'      => 1,
				'parent_id'  => 1
			);

			$this->comments->load($data);
			if(!$this->comments->id)
			{

				$this->comments->bind($data);
				$this->comments->ctime     = \Joomla\CMS\Date\Date::getInstance($item->posterTime)->toSql();
				$this->comments->langs     = '*';
				$this->comments->published = 1;
				$this->comments->access    = 1;
				$this->comments->private   = 0;
				$this->comments->parent_id = 1;


				$this->comments->store();
				$this->comments->reset();
				$this->comments->id = NULL;
			}

		}

		$db->setQuery('DELETE FROM smf_messages WHERE ID_TOPIC = ' . $id);
		$db->execute();

		$db->setQuery('UPDATE #__js_res_comments SET parent_id = 1 WHERE record_id = ' . $this->record->id);
		$db->execute();

		$db->setQuery('UPDATE #__js_res_record SET comments = (SELECT COUNT(id)
			FROM #__js_res_comments WHERE record_id = ' . $this->record->id . ' AND published = 1) WHERE id = ' . $this->record->id);
		$db->execute();

	}

	private function saveCategories($catid)
	{
		$cat_data = array(
			'record_id'  => $this->record->id, 'catid' => $catid,
			'section_id' => $this->record->section_id, 'ordering' => 0
		);
		$this->category->load($cat_data);

		if(!$this->category->id)
		{
			$this->category->save($cat_data);
		}

		$this->category->reset();
		$this->category->id = NULL;
	}

	private function saveFields($fields_data)
	{
		$field_ids = array_keys($fields_data);

		$this->values->clean($this->record->id, $field_ids);

		foreach($this->fields as $field)
		{
			if(empty($fields_data[$field->id]))
			{
				continue;
			}

			if(!in_array($field->id, $field_ids))
			{
				continue;
			}

			$file = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_joomcck/fields/' . $field->field_type . '/' . $field->field_type . '.php';
			if(!\Joomla\CMS\Filesystem\File::exists($file))
			{
				\Joomla\CMS\Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::sprintf("CFIELDNOTFOUND", $field->field_type),'warning');
				continue;
			}
			require_once $file;

			$name = 'JFormFieldC' . ucfirst($field->field_type);
			$obj  = new $name($field, $fields_data[$field->id]);

			if($obj->params->get('core.searchable'))
			{
				$data = $obj->onPrepareFullTextSearch($obj->value, $this->record, $this->type, $this->section);
				if(is_array($data))
				{
					$data = implode(', ', $data);
				}
				$fulltext[$obj->id] = $data;
			}


			$values = $obj->onStoreValues(get_object_vars($this->record), $this->record);
			if(empty($values))
			{
				continue;
			}

			settype($values, 'array');
			foreach($values as $key => $value)
			{
				$this->values->store_value($value, $key, $this->record, $obj);
				$this->values->reset();
				$this->values->id = NULL;
			}
		}

		$user = \Joomla\CMS\Factory::getUser($this->record->user_id);

		if($this->section->params->get('more.search_title'))
		{
			$fulltext[] = $this->record->title;
		}
		if($this->section->params->get('more.search_name'))
		{
			$fulltext[] = $user->get('name');
			$fulltext[] = $user->get('username');
		}
		if($this->section->params->get('more.search_email'))
		{
			$fulltext[] = $user->get('email');
		}
		if($this->section->params->get('more.search_category') && $this->record->categories != '[]')
		{
			$cats = json_decode($this->record->categories, true);
			$fulltext[] = implode(', ', array_values($cats));
		}

		if(!empty($fulltext))
		{
			$this->record->fieldsdata = strip_tags(implode(', ', $fulltext));
			$this->record->store();
		}

		unset($fulltext, $user);

	}

	private function saveFollows($id)
	{
		$db = \Joomla\CMS\Factory::getDbo();
		$db->setQuery('SELECT * FROM smf_log_notify WHERE ID_TOPIC = ' . $id);

		$list = $db->loadObjectList();

		foreach($list As $item)
		{
			$user = $this->getUser($item->ID_MEMBER);

			if(!$user)
			{
				continue;
			}

			$data = array(
				'user_id'    => $user,
				'ref_id'     => $this->record->id,
				'section_id' => $this->record->section_id,
				'type'       => 'record'
			);

			$this->follow->load($data);
			if(!$this->follow->id)
			{
				$data['ctime'] = \Joomla\CMS\Factory::getDate()->toSql();

				$this->follow->bind($data);
				$this->follow->store();
				$this->follow->reset();
				$this->follow->id = NULL;
			}

		}

		$db->setQuery('DELETE FROM smf_log_notify WHERE ID_TOPIC = ' . $id);
		$db->execute();
	}

	private function getType($id)
	{
		$db = \Joomla\CMS\Factory::getDbo();

		$db->setQuery('SELECT * FROM #__js_res_types WHERE id = ' .(int) $id);

		$type         = $db->loadObject();
		$type->params = new \Joomla\Registry\Registry($type->params);

		return $type;
	}

	private function getSection($id)
	{
		$db = \Joomla\CMS\Factory::getDbo();

		$db->setQuery('SELECT * FROM #__js_res_sections WHERE id = ' .(int) $id);

		$section         = $db->loadObject();
		$section->params = new \Joomla\Registry\Registry($section->params);

		return $section;
	}

	private function getFields($id)
	{
		$db = \Joomla\CMS\Factory::getDbo();

		$db->setQuery('SELECT * FROM #__js_res_fields WHERE type_id = ' . $id);

		return $db->loadObjectList();
	}

	private function getUser($user_id)
	{
		static $users = array();

		if(array_key_exists($user_id, $users))
		{
			return $users[$user_id];
		}

		$db = \Joomla\CMS\Factory::getDbo();

		$db->setQuery("SELECT u.id FROM #__users AS u WHERE u.email = (SELECT o.emailAddress FROM smf_members AS o WHERE o.ID_MEMBER = " . $db->quote($user_id) . ")");
		$users[$user_id] = $db->loadResult();

		return $users[$user_id];
	}

	private function _getCatToSave($id)
	{
		static $list;

		if(!$list)
		{
			$db = \Joomla\CMS\Factory::getDbo();
			$db->setQuery("SELECT id, title FROM #__js_res_categories");
			$list = $db->loadAssocList('id', 'title');
		}

		return $list[(int)$id];
	}

	private function _getMessage($id)
	{
		$db = \Joomla\CMS\Factory::getDbo();
		$db->setQuery("SELECT * FROM smf_messages WHERE ID_MSG = " . $id);

		return $db->loadObject();
	}

	private function _getCats($params)
	{
		$cats = explode("\n", $params->get('categories'));
		$out  = array();

		foreach($cats AS $cat)
		{
			if(empty($cat))
			{
				continue;
			}
			$catss = explode('::', $cat);
			$out[$catss[0]] = $catss[1];
		}

		return $out;
	}

	private function _BB2HTML($text)
	{
		$from = array(
			'[b]',
			'[/b]',
			'[/quote]',
		);
		$to = array(
			'<b>',
			'</b>',
			'</blockquote>',
		);
		$text = str_ireplace($from, $to, $text);

		$text = preg_replace('/\[quote=?"?([^"]*)"?\]/iU', "<blockquote><small>\\1</small><br>", $text);

		return $text;
	}
}