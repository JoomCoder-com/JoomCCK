<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
jimport('mint.mvc.controller.form');
class JoomcckControllerForm extends MControllerForm
{

    /*public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
    {
        if (empty($name))
        {
            $name = $this->context;
        }

        return parent::getModel($name, $prefix, $config);
    }*/

    public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = JFactory::getApplication()->input;
		}
	}

	public function save($key = NULL, $urlVar = NULL)
	{
		$this->view_list = 'records';

		$record  = $this->input->get('jform', array(), 'array');
		$user    = JFactory::getUser();
		$section = ItemsStore::getSection($record['section_id']);
		$this->input->set('section_id', $section->id);
		$type = ItemsStore::getType($record['type_id']);
		$this->input->set('type_id', $type->id);
		$user_id = $user->get('id', @$record['user_id']);

		if($record['id'])
		{
			CEmeraldHelper::allowType('edit', $type, $user_id, $section, TRUE,
				'index.php?option=com_joomcck&view=form&id=' . $record['id'], @$record['user_id'], FALSE);
			$this->text_prefix = $type->params->get('submission.save_msg', 'JLIB_APPLICATION');
		}
		else
		{
			CEmeraldHelper::allowType('submit', $type, $user_id, $section, TRUE,
				'index.php?option=com_joomcck&view=form&section_id=' . $section->id . '&type_id=' . $type->id, @$record['user_id'], FALSE);
			$this->text_prefix = $type->params->get('submission.submit_msg', 'JLIB_APPLICATION');
		}

		$app = JFactory::getApplication();
		if(!$app->getUserState('com_joomcck.edit.form.id'))
		{
			$app->setUserState('com_joomcck.edit.form.id', $record['id']);
		}

		$dispatcher = \Joomla\CMS\Factory::getApplication();
		JPluginHelper::importPlugin('mint');
		$dispatcher->triggerEvent('onBeforeArticleSaved', array(($record['id'] == 0), $record, $section, $type));

		parent::save($key, $urlVar);
	}

	public function postSaveHook(MModelBase $model, $validData = array())
	{
		$db           = JFactory::getDbo();
		$user         = JFactory::getUser();
		$fileds_model = MModelBase::getInstance('Fields', 'JoomcckModel');
		$record_id    = $model->getState('form.id');
		$table        = JTable::getInstance('Record_values', 'JoomcckTable');

		$type    = ItemsStore::getType($validData['type_id']);
		$section = ItemsStore::getSection($validData['section_id']);

		$record = JTable::getInstance('Record', 'JoomcckTable');
		$record->load($record_id);

		$fields = $fileds_model->getFormFields($validData['type_id'], $record_id, FALSE, json_decode($record->fields, TRUE));
		$method = (!empty($validData['id']) ? 'edit' : 'submit');
		$isnew  = empty($validData['id']);

		if(!$user->get('id') && $type->params->get('submission.public_edit'))
		{
			$url = Url::edit($record_id . '&access_key=' . $record->access_key);
			JError::raiseNotice(200, JText::sprintf('CEDITRECORDLINKALERT', JHtml::link($url, JText::_('CEDITLINK'))));
		}

		// Delete values of fields that are deleted.
		/*
		$saved_fields = json_decode($record->fields, TRUE);
		$intersect = array_diff_key($saved_fields, $fields);
		if(!empty($intersect))
		{
			foreach($intersect AS $field_id => $field_data)
			{
				unset($saved_fields[$field_id]);
			}
			$record->fields = json_encode($saved_fields);
		}
		*/


		foreach($fields as $key => $field)
		{
			if($isnew)
			{
				// check if field is email and no user to send edit link
				if($field->type == 'email' && !$user->get('id') && $field->value && $type->params->get('submission.public_edit'))
				{
					$config  = JFactory::getConfig();
					$subject = JMailHelper::cleanSubject(JText::sprintf('CEDITRECORDEMAIL', $type->name, $section->name));
					$body    = JMailHelper::cleanBody(JText::sprintf('CEDITREORDBODY', $type->name, $section->name, $config->get('sitename'),
						preg_replace('/\/$/iU', '', JURI::root(TRUE)) . $url));
					$mailer  = JFactory::getMailer();
					$mailer->sendMail($config->get('mailfrom'), $config->get('fromname'),
						JMailHelper::cleanAddress($field->value), $subject, $body);
					//JError::raiseNotice(100, $subject.$body);
				}

				if(!in_array($field->params->get('core.field_submit_access'), $user->getAuthorisedViewLevels()))
				{
					unset($fields[$key]);
					continue;
				}
			}
			else
			{
				if(!in_array($field->params->get('core.field_edit_access'), $user->getAuthorisedViewLevels()))
				{
					unset($fields[$key]);
					continue;
				}
			}

			if(!CEmeraldHelper::allowField($method, $field, $user->id, $section, $record, true, false))
			{
				if($field->params->get('emerald.field_' . $method . '_subscription'))
				{
					unset($fields[$key]);
					continue;
				}
			}

			$ids[] = $field->id;
		}

		$table->clean($record_id, $ids);

		// if it isNew record valid data haven't id put it in as new_id to get record id in onStoreValues
		// but why not to put it to ID and not check anything in store value?
		$validData['id'] = $record_id;

		foreach($fields as $field)
		{
			$values = $field->onStoreValues($validData, $record);
			settype($values, 'array');

			foreach($values as $key => $value)
			{
				$table->store_value($value, $key, $record, $field);
				$table->reset();
				$table->id = NULL;
			}

			if($values)
			{
				CEmeraldHelper::countLimit('field', $method, $field, $record);
			}
		}


		$categories = json_decode($record->categories, TRUE);
		if($categories)
		{
			settype($categories, 'array');

			$table_cat      = JTable::getInstance('CobCategory', 'JoomcckTable');
			$table_category = JTable::getInstance('Record_category', 'JoomcckTable');

			$cids = array();
			foreach($categories as $key => $category)
			{
				$table_cat->load($key);

				$array = array(
					'catid'      => $key,
					'section_id' => $record->section_id,
					'record_id'  => $record_id
				);
				$table_category->load($array);

				if(!$table_category->id)
				{
					$array['published']  = $table_cat->published;
					$array['access']     = $table_cat->access;
					$array['id']         = NULL;

					$table_category->save($array);
				}
				else
				{
					$table_category->published = $table_cat->published;
					$table_category->access = $table_cat->access;
					$table_category->store();
				}

				$cids[] = $key;

				$table_category->reset();
				$table_category->id = NULL;
			}

			if($cids)
			{
				$sql = 'DELETE FROM #__js_res_record_category WHERE record_id = ' . $record_id . ' AND catid NOT IN (' . implode(',', $cids) . ')';
				$db->setQuery($sql);
				$db->execute();
			}
		}

		
		if(isset($validData['tags']) && $validData['tags'] != '')
		{
            $tag_table     = JTable::getInstance('Tags', 'JoomcckTable');
            $taghist_table = JTable::getInstance('Taghistory', 'JoomcckTable');

            $out = $data = $rtags = $tag_ids = [];

            $data['record_id']  = $record_id;
            $data['section_id'] = $validData['section_id'];
            $data['user_id']    = (int)@$validData['user_id'];

            $i = 0;
            foreach($validData['tags'] as $tag)
            {
                if(empty($tag))
                {
                    continue;
                }

                if($type->params->get('general.item_tags_max', 25) && $i > $type->params->get('general.item_tags_max', 25))
                {
                    break;
                }

                if(!preg_match("/^[0-9]*$/", $tag))
                {
                    $tag_table->reset();
                    $tag_table->id = NULL;
                    $tag_table->load(array(
                        'tag' => $tag
                    ));
                    if(!$tag_table->id)
                    {
                        $tag_table->save(array(
                            'tag' => $tag
                        ));
                    }
                    $tag = $tag_table->id;
                }


                $data['tag_id'] = $out[] = $tag;
                $taghist_table->reset();
                $taghist_table->id = NULL;
                $taghist_table->load($data);
                if(!$taghist_table->id)
                {
                    $taghist_table->save($data);
                }
                $tag_ids[] = $tag;
                $i++;
            }
            if(count($tag_ids)) {
                
                $sql = 'SELECT id, tag FROM #__js_res_tags WHERE id IN (' . implode(',', $tag_ids) . ')';
                $db->setQuery($sql);
                $rtags = $db->loadAssocList('id', 'tag');

                $sql = 'DELETE FROM #__js_res_tags_history WHERE record_id = ' . $record_id . ' AND tag_id NOT IN (' . implode(',', $tag_ids) . ')';
                $db->setQuery($sql);
                $db->execute();
            }

            $record->tags = count($rtags) ? json_encode($rtags) : '';
        }


		$posts = array();
		if($section->params->get('personalize.personalize') && $section->params->get('personalize.post_anywhere'))
		{
			$posts = $this->input->get('wheretopost', array(), 'array');
			if($posts)
			{
				$sql = "DELETE FROM `#__js_res_record_repost` WHERE record_id = $record_id AND is_reposted = 0";
				$db->setQuery($sql);
				$db->execute();

				$data = array('record_id' => $record_id, 'ctime' => JFactory::getDate()->toSql(), 'is_reposted' => 0);
				foreach($posts as $pid)
				{
					$data['host_id'] = $pid;

					$post_table = JTable::getInstance('Reposts', 'JoomcckTable');
					$post_table->save($data);
					$post_table->reset();
				}
			}
			$sql = "SELECT host_id FROM `#__js_res_record_repost` WHERE record_id = {$record_id}";
			$db->setQuery($sql);
			$posts = $db->loadColumn();
		}

		$record->repostedby = json_encode($posts);


		if($type->params->get('audit.versioning'))
		{
			$record->store();
			$versions = JTable::getInstance('Audit_versions', 'JoomcckTable');
			$version  = $versions->snapshot($validData['id'], $type);

			$record->version = $version;
		}

		if($record->parent_id && $type->params->get('properties.rate_access') == -1)
		{
			$query = "SELECT COUNT(*) as total, SUM(votes_result) / COUNT(*) AS rating
                FROM #__js_res_record WHERE parent_id = $record->parent_id AND parent = 'com_joomcck'";
			$db->setQuery($query);
			$new = $db->loadObject();

			$query = "UPDATE #__js_res_record SET votes_result = " . (int)$new->rating . ",  votes = " . (int)$new->total . "
                WHERE id = {$record->parent_id}";
			$db->setQuery($query);
			$db->execute();
		}

		if(!$isnew)
		{
			CEventsHelper::notify('record', CEventsHelper::_RECORD_EDITED, $validData['id'], $validData['section_id'], 0, 0, 0, $record->getProperties());
			ATlog::log($record, ATlog::REC_EDIT);

			// If joomcck article is acting lice a comment we have to add event alert that new comment has been added
			if($record->parent_id && $record->parent == 'com_joomcck')
			{
				$parent = ItemsStore::getRecord($record->parent_id);
				CEventsHelper::notify('record', CEventsHelper::_COMMENT_EDITED, $parent->id, $parent->section_id, 0, 0, 0, get_class_vars($parent));
			}
		}
		else
		{

			if($record->published == 1)
			{
				CEventsHelper::notify('category', CEventsHelper::_RECORD_NEW, $validData['id'], $validData['section_id'], 0, 0, 0, $record->getProperties());
			}
			else
			{
				CEventsHelper::notify('category', CEventsHelper::_RECORD_WAIT_APPROVE, $validData['id'], $validData['section_id'], 0, 0, 0, $record->getProperties());
			}

			ATlog::log($record, ATlog::REC_NEW);

			// event on parent or child added
			if($this->input->getInt('fand') && $this->input->getInt('field_id'))
			{
				$field = JTable::getInstance('Field', 'JoomcckTable');
				$field->load($this->input->getInt('field_id'));
				$field->params = new JRegistry($field->params);

				$fand = JTable::getInstance('Record', 'JoomcckTable');
				$fand->load($this->input->getInt('fand'));

				$fand->added = $record;
				$data        = $fand->getProperties();

				CEventsHelper::notify('record', $field->field_type . '_new', $fand->id, $fand->section_id, 0, 0, $this->input->getInt('field_id'), $data, $field->params->get('params.notify_add'));
			}

			CSubscriptionsHelper::auto_subscribe($record, $validData['section_id']);

			// If joomcck article is acting lice a comment we have to add event alert that new comment has been added
			if($record->parent_id && $record->parent == 'com_joomcck')
			{
				$parent = ItemsStore::getRecord($record->parent_id);
				CEventsHelper::notify('record', CEventsHelper::_COMMENT_NEW, $parent->id, $parent->section_id, 0, 0, 0, get_class_vars($parent));

				$model_record = JModelLegacy::getInstance('Record', 'JoomcckModel');
				$model_record->onComment($record->parent_id, $validData, false);

				/*$db->setQuery("SELECT COUNT(id) FROM `#__js_res_record` WHERE parent_id = {$record->parent_id} AND parent = 'com_joomcck' AND published = 1");
				$parent->comments = $db->loadResult();
				$parent->mtime = JDate::getInstance()->toSql();*/
			}
		}

		$record->index();


		CEmeraldHelper::countLimit('type', $method, $type, $record);
		CSubscriptionsHelper::subscribe_record($record);

		$dispatcher = \Joomla\CMS\Factory::getApplication();
		JPluginHelper::importPlugin('mint');
		$dispatcher->triggerEvent('onAfterArticleSaved', array($isnew, $record, $fields, $section, $type));

		if($this->getTask() == 'save')
		{
			switch($type->params->get('submission.redirect'))
			{
				case 1:
					$url = JRoute::_(Url::records($section->id), FALSE);
					break;

				case 2:
					$url = JRoute::_(Url::record($record_id), FALSE);
					break;

				case 3:
					$url = $type->params->get('submission.redirect_url');
					$url = str_replace(array('[ID]', '[USER_ID]', '[AUTHOR_ID]'), array($record->id, $user->get('id'), $record->user_id), $url);
					break;
			}

			$ret = Url::get_back('return');
			if(!JURI::isInternal($ret))
			{
				$ret = '';
			}
			$type = $model->getRecordType($this->input->getInt('type_id'));

			if(($record->parent_id || $type->params->get('submission.redirect') == 1 || $this->input->get('fand') || !$isnew) && $ret)
			{
				$url = $ret;
			}

			$this->setRedirect($url);
		}
	}

	public function cancel($key = NULL)
	{
		$return = Url::get_back('return');

		if($return)
		{
			$result = parent::cancel($key);
			$this->setRedirect($return);

			return $result;
		}
		else
		{
			$this->view_list = 'records';
			parent::cancel($key);
		}
	}

	protected function allowSave($data = array(), $key = 'id')
	{
		return TRUE;
	}

	protected function allowAdd($data = array(), $key = 'id')
	{
		$user  = JFactory::getUser();
		$allow = $user->authorise('core.create', 'com_joomcck.record');

		if($allow === NULL)
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
		$record = ItemsStore::getRecord($data['id']);

		return MECAccess::allowEdit($record, ItemsStore::getType($record->type_id), ItemsStore::getSection($record->section_id));
		//return JFactory::getUser()->authorise('core.edit', 'com_joomcck.record');
	}

	protected function getRedirectTolistAppend()
	{


		$post  = $this->input->get('jform', array(), 'array');
		$modal = $this->input->getInt('modal');

		$secton_id   = $this->input->getCmd('section_id', $post['section_id']);
		$category_id = $this->input->getCmd('cat_id', @$post['category'][0]);

		$append = '';

		if($secton_id)
		{
			$append .= '&section_id=' . $secton_id;
		}

		if($this->getTask() != 'cancel' && $category_id)
		{
			$append .= '&cat_id=' . $category_id;
		}

		if($modal)
		{
			$append .= '&modal=' . $modal;
		}

		return $append;

	}

	protected function getRedirectToItemAppend($recordId = NULL, $urlVar = 'id')
	{
		$append = '';

		if($recordId)
		{
			$user = JFactory::getUser();
			if(!$user->get('id') && $recordId)
			{
				$record = ItemsStore::getRecord($recordId);
				$append .= '&access_key=' . $record->access_key;
			}

			if($recordId)
			{
				$append .= '&' . $urlVar . '=' . $recordId;
			}
		}
		else
		{
			if($this->input->getInt('type_id'))
			{
				$append .= '&type_id=' . $this->input->getInt('type_id');
			}

			if($this->input->getInt('section_id'))
			{
				$append .= "&section_id=" . $this->input->getInt('section_id');
			}


			$post = $this->input->get('category');

			if($this->input->get('cat_id', @$post[0]))
			{
				$append .= '&cat_id=' . $this->input->get('cat_id', @$post[0]);
			}
		}

		if($this->input->getInt('modal'))
		{
			$append .= '&modal=' . $this->input->getInt('modal');
		}
		
		if($this->input->getInt('fand'))
		{
			$append .= '&fand=' . $this->input->getInt('fand');
		}
		
		if($this->input->getInt('field_id'))
		{
			$append .= '&field_id=' . $this->input->getInt('field_id');
		}

		if($this->input->getInt('parent_id'))
		{
			$append .= '&parent_id=' . $this->input->getInt('parent_id');
		}

		if($this->input->getInt('Itemid'))
		{
			$append .= '&Itemid=' . $this->input->getInt('Itemid');
		}

		if($this->input->get('tmpl'))
		{
			$append .= '&tmpl=' . $this->input->get('tmpl');
		}

		$append .= '&return=' . $this->input->getBase64('return');

		return $append;
	}

	protected function getReturnPage()
	{
		$return = urldecode($this->input->getBase64('return'));

		if(empty($return) || !JUri::isInternal(JoomcckFilter::base64($return)))
		{
			return JURI::base();
		}
		else
		{
			return JoomcckFilter::base64($return);
		}
	}
}