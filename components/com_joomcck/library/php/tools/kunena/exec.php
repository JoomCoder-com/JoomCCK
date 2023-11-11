<?php
defined('_JEXEC') or die();
$migration = new Kunena2Joomcck();
$migration->migrate($params);

class Kunena2Joomcck {
	public function migrate($params)
	{
		\Joomla\CMS\Table\Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_joomcck/tables');
		JModelLegacy::addIncludePath(JPATH_ROOT . '/components/com_joomcck/models');

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
		$db->setQuery("SELECT * FROM `#__kunena_messages` WHERE parent = 0 LIMIT 1");
		$list = $db->loadObjectList();

		if(empty($list))
		{
			return;
		}

		foreach($list AS $record)
		{
			$catid = $this->cats_ids[$record->catid];
			if(empty($catid))
			{
				continue;
			}

			$this->record->reset();
			$this->record->id = NULL;

			$data['user_id']    = $record->userid;
			$data['type_id']    = $params->get('type_id');
			$data['section_id'] = (int)$params->get('section_id');
			$data['published']  = (int)!$record->locked;
			$data['access']     = 1;
			$data['hits']       = $record->hits;

			$message = $this->_getMessage($record->id);

			$data['title'] = $record->subject;
			$data['ctime'] = JDate::getInstance($record->time)->toSql();
			$data['mtime'] = $record->modified_time ? JDate::getInstance($record->modified_time)->toSql() : $data['ctime'];
			$data['ip']    = $record->ip;


			$category                      = array($catid => $this->_getCatToSave($catid));
			$data['categories']            = json_encode($category);
			$_REQUEST['jform']['category'] = $catid;

			$fields_data = array(
				$params->get('text_field') => $this->_BB2HTML($message)
			);

			$data['fields'] = json_encode($fields_data);

			$this->record->bind($data);
			if(!$this->record->check_cli())
			{
				echo $this->record->getError();
				exit;
			}
			$this->record->store();


			$this->saveCategories($catid);
			$this->saveComments($record->id, $params);
			$this->saveFollows($record->thread);
			$this->saveFields($fields_data);

			$db->setQuery("DELETE FROM `#__kunena_messages` WHERE id = " . $record->id);
			$db->execute();
			$db->setQuery("DELETE FROM `#__kunena_topics` WHERE id = " . $record->thread);
			$db->execute();
		}

		$this->import($params);
	}

	private function saveComments($id, $params)
	{
		$db = \Joomla\CMS\Factory::getDbo();
		$db->setQuery("SELECT * FROM `#__kunena_messages` WHERE parent = {$id} ORDER BY `time` ASC");

		$list = $db->loadObjectList();


		foreach($list As $item)
		{
			$data = array(
				'user_id'    => $item->userid,
				'record_id'  => $this->record->id,
				'section_id' => (int)$params->get('section_id'),
				'type_id'    => $params->get('type_id'),
				'comment'    => $this->_BB2HTML($this->_getMessage($item->id)),
				'level'      => 1,
				'parent_id'  => 1
			);

			$this->comments->load($data);

			if(!$this->comments->id)
			{
				$this->comments->bind($data);
				$this->comments->ctime     = JDate::getInstance($item->time)->toSql();
				$this->comments->langs     = '*';
				$this->comments->published = (int)!$item->locked;
				$this->comments->access    = 1;
				$this->comments->private   = 0;
				$this->comments->parent_id = 1;
				$this->comments->root_id   = 0;
				$this->comments->ip        = $item->ip;
				$this->comments->email     = \Joomla\CMS\Factory::getUser($item->userid)->get('email');
				$this->comments->name      = \Joomla\CMS\Factory::getUser($item->userid)->get('username');

				$_REQUEST['jform']['parent_id'] = 1;
				$this->comments->check();
				$this->comments->store();
				$this->comments->reset();
				$this->comments->id = NULL;
			}

		}

		$db->setQuery("DELETE FROM `#__kunena_messages` WHERE parent = {$id}");
		$db->execute();

		$db->setQuery('UPDATE `#__js_res_comments` SET parent_id = 1 WHERE record_id = ' . $this->record->id);
		$db->execute();

		$db->setQuery("SELECT COUNT(*)
			  FROM `#__js_res_comments`
		 	 WHERE record_id = {$this->record->id} AND published = 1");

		$this->record->comments = $db->loadResult();
		$this->record->store();
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
			$cats       = json_decode($this->record->categories, TRUE);
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
		$db->setQuery('SELECT * FROM `#__kunena_user_topics` WHERE subscribed = 1 AND  topic_id = ' . $id);

		$list = $db->loadObjectList();

		foreach($list As $item)
		{
			$user = $item->user_id;

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

		$db->setQuery('DELETE FROM `#__kunena_user_topics` WHERE topic_id = ' . $id);
		$db->execute();
	}

	private function getType($id)
	{
		$db = \Joomla\CMS\Factory::getDbo();

		$db->setQuery('SELECT * FROM #__js_res_types WHERE id = ' . (int)$id);

		$type         = $db->loadObject();
		$type->params = new \Joomla\Registry\Registry($type->params);

		return $type;
	}

	private function getSection($id)
	{
		$db = \Joomla\CMS\Factory::getDbo();

		$db->setQuery('SELECT * FROM #__js_res_sections WHERE id = ' . (int)$id);

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
		$db->setQuery("SELECT `message` FROM `#__kunena_messages_text` WHERE mesid = " . $id);

		return $db->loadResult();
	}

	private function _getCats($params)
	{
		$cats = explode("\n", trim($params->get('categories')));
		$out  = array();

		foreach($cats AS $cat)
		{
			if(empty($cat))
			{
				continue;
			}
			$catss                = explode('::', $cat);
			$out[trim($catss[0])] = trim($catss[1]);
		}

		return $out;
	}

	private function _BB2HTML($text)
	{
		$from = array(
			'[i]',
			'[/i]',
			'[u]',
			'[/u]',
			'[strike]',
			'[/strike]',
			'[sub]',
			'[/sub]',
			'[sup]',
			'[/sup]',
			'[ul]',
			'[/ul]',
			'[li]',
			'[/li]',
			'[ol]',
			'[/ol]',
			'[tr]',
			'[/tr]',
			'[td]',
			'[/td]',
			'[b]',
			'[/b]',
			'[/quote]',
			'[/size]',
			'[/color]',
			'[code]',
			'[/code]',
			'[table]',
			'[/table]',
		);
		$to   = array(
			'<i>',
			'</i>',
			'<u>',
			'</u>',
			'<strike>',
			'</strike>',
			'<sub>',
			'</sub>',
			'<sup>',
			'</sup>',
			'<ul>',
			'</ul>',
			'<li>',
			'</li>',
			'<ol>',
			'</ol>',
			'<tr>',
			'</tr>',
			'<td>',
			'</td>',
			'<b>',
			'</b>',
			'</blockquote>',
			'</span>',
			'</span>',
			'<pre><code>',
			'</code></pre>',
			'<table>',
			'</table>',
		);
		$text = str_ireplace($from, $to, $text);

		$text = preg_replace('/\[quote=?"?([^"]*)"?\]/iU', "<blockquote><small>\\1</small><br>", $text);
		$text = preg_replace('/\[size=([0-9]{1,2})\]/iU', "<span style=\"font-size:\\1em\">", $text);
		$text = preg_replace('/\[color=([^\]]*)\]/iU', "<span style=\"color:\\1\">", $text);
		$text = preg_replace('/\[url\]([^\[]*)\[\/url\]/iU', "<a href=\"\\1em\">\\1</a>", $text);
		$text = preg_replace('/\[url=([^\]]*)\]([^\[]*)\[\/url\]/iU', "<a href=\"\\1\">\\2</a>", $text);

		return $text;
	}
}