<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckfield.php';


class JFormFieldCStatus extends CFormField
{

	public function getInput()
	{
		$doc = JFactory::getDocument();
		$app = JFactory::getApplication();
		if($app->input->get('id', 0))
		{
			$this->record = ItemsStore::getRecord($app->input->get('id'));
		}
		$params = $this->params;

		$this->user    = JFactory::getUser();
		$this->default = !$this->value ? $params->get('params.default', 1) : $this->value;

		$statuses    = $color = array();
		$statuses[1] = $params->get('params.status1');
		$statuses[2] = $params->get('params.status2');
		$statuses[3] = $params->get('params.status3');
		$statuses[4] = $params->get('params.status4');
		$statuses[5] = $params->get('params.status5');
		$statuses[6] = $params->get('params.status6');
		foreach($statuses as $key => $status)
		{
			$val            = explode('^', $status);
			$statuses[$key] = JText::_($val[0]);
			$color[$key]    = isset($val[1]) ? $val[1] : FALSE;
		}
		if($params->get('params.sort') == 2)
		{
			asort($statuses);
		}
		if($params->get('params.sort') == 3)
		{
			rsort($statuses);
		}

		$this->statuses = $statuses;

		return $this->_display_input();

	}

	public function onJSValidate()
	{
		$js = '';
		if($this->required)
		{
			$js .= "\n\t\tif($('field_{$this->id}').value == ''){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(JText::sprintf('CFIELDREQUIRED', $this->label)) . "');}";
		}

		return $js;
	}

	public function validate($value, $record, $type, $section)
	{
		return parent::validate($value, $record, $type, $section);
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		$value = !$value ? $this->params->get('params.default', 1) : $value;

		return $value;
	}

	public function onStoreValues($validData, $record)
	{
		$value = !$this->value ? $this->params->get('params.default', 1) : $this->value;

		if($this->params->get('params.sql_action_on_save', 0))
		{
			$this->_runSql($value, $record);
		}

		if(!JFactory::getApplication()->input->get('id') && JFactory::getApplication()->input->get('view') == 'form')
		{
			$this->_sendAlert($value, $record);
		}

		return $value;
	}

	public function onFilterWornLabel($section)
	{
		$value = $this->value;
		$label = array();
		settype($value, 'array');
		foreach($value as $val)
		{
			$c = explode('^', $this->params->get('params.status' . $val));
			ArrayHelper::clean_r($c);
			$label[] = (isset($c[1]) ? "<SPAN style=\"color:{$c[1]}\">" . JText::_($c[0]) . "</SPAN>" : JText::_($c[0]));
		}
		$value = implode(', ', $label);

		return $value;


	}

	public function onFilterWhere($section, &$query)
	{
		$value = $this->value;
		ArrayHelper::trim_r($value);
		ArrayHelper::clean_r($value);

		if(!$value)
		{
			return NULL;
		}

		foreach($value as $text)
		{
			$sql[] = "field_value = '$text'";
		}

		$ids = $this->getIds("SELECT record_id FROM #__js_res_record_values WHERE (" . implode(' OR ', $sql) . ") AND section_id = {$section->id} AND field_key = '{$this->key}'");

		return $ids;
	}

	public function onRenderFilter($section, $module = FALSE)
	{
		$db     = JFactory::getDbo();
		$params = $this->params;
		$query  = $db->getQuery(TRUE);

		$query->select('field_value');
		$query->from('#__js_res_record_values');
		$query->where("section_id = {$section->id}");
		$query->where("`field_key` = '{$this->key}'");
		$query->group('field_value');

		if($this->params->get('params.filter_show_number', 1))
		{
			$query->select('count(record_id) as num');
		}
		$db->setQuery($query);

		$this->values = $values = $db->loadObjectList();
		if(!$this->values)
		{
			return;
		}

		return $this->_display_filter($section, $module);
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		$value = !$this->value ? $this->params->get('params.default', 1) : $this->value;

		return JText::_($this->params->get('params.status' . $value));

	}

	public function onRenderFull($record, $type, $section)
	{
		$user   = JFactory::getUser();
		$params = $this->params;
		if($params->get('params.moderator', 3) && in_array($params->get('params.moderator', 3), $user->getAuthorisedViewLevels()))
		{
			if($this->value == $params->get('params.default', 1) && $this->request->getCmd('view') != 'diff')
			{
				if($params->get('params.change_default', 2))
				{
					$post = array(
						'from'      => $this->value, 'to' => $params->get('params.change_default', 2),
						'record_id' => $record->id, 'section_id' => $section->id, 'ajax' => FALSE
					);
					if($this->_changeStatus($post) == TRUE)
					{
						$this->value = $params->get('params.change_default', 2);
					}
				}
			}
		}
		$out = $this->_getStatusView('full', $record, $type, $section);

		return $out;
	}

	public function onRenderList($record, $type, $section)
	{
		return $this->_getStatusView('list', $record, $type, $section);
	}

	private function _getStatusView($client, $record, $type, $section)
	{
		if(!$this->value)
		{
			return;
		}
		$this->record = $record;
		$this->user   = JFactory::getUser();
		$params       = $this->params;
		$out          = array();

		$val   = explode('^', $params->get('params.status' . $this->value));
		$value = $value_color = JText::_($val[0]);

		if(isset($val[1]))
		{
			$value = '<span style="color: ' . $val[1] . '">' . $value . '</span>';
		}

		if($this->params->get('params.filter_enable'))
		{
			$tip = ($this->params->get('params.filter_tip') ?
				JText::sprintf($this->params->get('params.filter_tip'), '<b>' . JText::_($this->label) . '</b>', $value) :
				NULL
			);
			switch($this->params->get('params.filter_linkage'))
			{
				case 1 :
					$value = FilterHelper::filterLink('filter_' . $this->id, $this->value, strip_tags($value), $this->type_id, $tip, $section);
					break;

				case 2 :
					$value = $value . ' ' . FilterHelper::filterButton('filter_' . $this->id, $this->value, $this->type_id, $tip, $section, $this->params->get('params.filter_icon', 'funnel-small.png'));
					break;
			}
		}

		$path = JURI::root() . 'components/com_joomcck/fields/status/icons/';

		$type_default = $client == 'full' ? 3 : 1;

		switch($params->get('params.' . $client, $type_default))
		{
			case 1 :
				$out[] = JHtml::image($path . $params->get('params.icon' . $this->value), strip_tags($value), array(
					'class' => 'hasTip',
					'title' => strip_tags($value),
					'align' => 'absmiddle'
				));
				break;
			case 2 :
				$out[] = $value;
				break;
			case 3 :
				$out[] = JHtml::image($path . $params->get('params.icon' . $this->value), strip_tags($value), array(
						'class' => 'hasTip',
						'title' => strip_tags($value),
						'align' => 'absmiddle'
					)) . ' ';
				$out[] = $value;
				break;
		}

		$this->out = implode($out);

		$statuses    = $color = array();
		$statuses[1] = $params->get('params.status1');
		$statuses[2] = $params->get('params.status2');
		$statuses[3] = $params->get('params.status3');
		$statuses[4] = $params->get('params.status4');
		$statuses[5] = $params->get('params.status5');
		$statuses[6] = $params->get('params.status6');
		foreach($statuses as $key => $status)
		{
			$val            = explode('^', $status);
			$statuses[$key] = JText::_($val[0]);
			$color[$key]    = isset($val[1]) ? $val[1] : FALSE;
		}

		if($params->get('params.sort') == 2)
		{
			asort($statuses);
		}
		if($params->get('params.sort') == 3)
		{
			rsort($statuses);
		}

		$this->statuses     = $statuses;
		$this->color        = $color;
		$this->clienttype   = $client;
		$this->type_default = $type_default;

		return $this->_display_output($client, $record, $type, $section);
	}

	public function _changeStatus($post)
	{
		$to     = $post['to'];
		$params = $this->params;

		if(!$this->checkStatus($params->get('params.access' . $to)))
		{
			$this->setError(JText::_('ST_STATUSNORIGHTS'));

			return;
		}
		$record_id  = $post['record_id'];
		$section_id = $post['section_id'];
		$user       = JFactory::getUser();

		$table_rec = JTable::getInstance('Record', 'JoomcckTable');
		$table_rec->load($record_id);

		if(in_array($to, $params->get('params.rsubscribe', array())))
		{
			CSubscriptionsHelper::subscribe_record($table_rec);
		}
		if(in_array($to, $params->get('params.runsubscribe', array())))
		{
			CSubscriptionsHelper::unsubscribe_record($table_rec);
		}

		$rfields            = json_decode($table_rec->fields, TRUE);
		$rfields[$this->id] = $to;
		$table_rec->fields  = json_encode($rfields);

		if($params->get('core.searchable', 0))
		{
			$table_rec->fieldsdata = str_replace(JText::_($this->params->get('params.status' . $this->value)), JText::_($this->params->get('params.status' . $to)), $table_rec->fieldsdata);
		}

		if($params->get('params.block_comment') == $to)
		{
			$rec_params = new JRegistry($table_rec->params);
			$rec_params->set('comments.comments_access_post', 0);

			$table_rec->params = $rec_params->toString();
		}
		$table_rec->store();

		$table = JTable::getInstance('Record_values', 'JoomcckTable');
		$table->load(array('field_id' => $this->id, 'record_id' => $record_id, 'section_id' => $section_id));
		$table->field_value = $to;

		if(!$table->store())
		{
			$this->setError(JText::_('ST_STATUSWASCHANGED'));

			return;
		}

		if($params->get('params.sql_action_s' . $to, FALSE) && $params->get('params.sql_source_s' . $to))
		{
			$db      = JFactory::getDbo();
			$queries = explode(";", $params->get('params.sql_source_s' . $to));
			foreach($queries AS $sql)
			{
				$sql = str_replace(array('[ID]', '[RECORD_ID]', '[USER_ID]', '[AUTHOR_ID]', '[PARENT_ID]'),
					array(
						$table->id,
						$record_id,
						$user->get('id', 0),
						$table_rec->user_id,
						$table_rec->parent_id
					), $sql
				);
				$db->setQuery($sql);
				$db->execute();
			}
		}

		if($params->get('params.notified') && $params->get('params.notify') != '' && in_array($to, $params->get('params.notify')))
		{
			$table_rec->status = $to;
			CEventsHelper::notify('record', CEventsHelper::_FIELDS_STATUS_CHANGED, $record_id, $section_id, $this->request->getInt('cat_id'), 0, $this->id, $table_rec, $params->get('params.notified'), $table_rec->user_id);
		}

		ATlog::log($table_rec, ATlog::FLD_STATUSCHANGE, 0, $this->id);

		$this->_sendAlert($to);

		if($post['ajax'])
		{
			$type         = $post['view_type'];
			$out          = array();
			$value        = explode('^', $params->get('params.status' . $to));
			$status       = (isset($value[1])) ? '<span style="color: ' . $value[1] . '">' . $value[0] . '</span>' : $value[0];
			$value        = $value[0];
			$path         = JURI::root() . 'components/com_joomcck/fields/status/icons/';
			$type_default = $type == 'full' ? 3 : 1;
			switch($params->get('params.' . $type, $type_default))
			{
				case 1 :
					$out[] = JHtml::image($path . $params->get('params.icon' . $to), $value, array(
						'class' => 'hasTip',
						'title' => $value,
						'align' => 'absmiddle'
					));
					break;
				case 2 :
					$out[] = $status;
					break;
				case 3 :
					$out[] = JHtml::image($path . $params->get('params.icon' . $to), $value, array(
							'class' => 'hasTip',
							'title' => $value,
							'align' => 'absmiddle'
						)) . ' ';
					$out[] = $status;
					break;
			}

			return implode('', $out);
		}

		return TRUE;
	}

	public function onComment($record, $section)
	{
		$user   = JFactory::getUser();
		$params = $this->params;
		if($params->get('params.moderator', 3) && in_array($params->get('params.moderator', 3), $user->getAuthorisedViewLevels()))
		{
			if($params->get('params.add_comment', 2))
			{
				$post = array(
					'from'      => $this->value, 'to' => $params->get('params.add_comment', 2),
					'record_id' => $record->id, 'section_id' => $section->id, 'ajax' => FALSE
				);
				$this->_changeStatus($post);
			}
		}
	}

	public function onNotification($text, $event)
	{
		$value = explode('^', $this->params->get('params.status' . $event->params->get('status', 1), ''));
		ArrayHelper::clean_r($value);
		if($value)
		{
			$status = (isset($value[1])) ? '<span style="color: ' . $value[1] . '">' . $value[0] . '</span>' : $value[0];
			$text   = str_replace('[STATUS]', "<b>\"{$status}\"</b>", $text);
		}

		return $text;
	}

	public function checkStatus($access, $act = 'submit')
	{
		if(!$access)
		{
			return FALSE;
		}

		$app  = JFactory::getApplication();
		$user = JFactory::getUser();

		if(in_array($this->params->get('params.moderator', 3), $user->getAuthorisedViewLevels()))
		{
			return TRUE;
		}

		if(MECAccess::allowRestricted($user, ItemsStore::getSection($app->input->get('section_id'))))
		{
			return TRUE;
		}

		if($app->input->get('ajax'))
		{
			$act = 'edit';
		}

		if($app->input->get('id'))
		{
			$act = 'edit';
		}

		if(!in_array($this->params->get('core.field_' . $act . '_access', 1), $user->getAuthorisedViewLevels()))
		{
			return FALSE;
		}

		if(in_array($access, $user->getAuthorisedViewLevels()))
		{
			return TRUE;
		}

		if(!isset($this->record->user_id) && $app->input->get('record_id', FALSE))
		{
			$this->record = ItemsStore::getRecord($app->input->get('record_id'));
		}

		if(in_array($access, array(-1, 3)) && ((!empty($this->record->user_id) && $this->record->user_id == $user->get('id')) || !isset($this->record->user_id)))
		{
			return TRUE;
		}

		return FALSE;
	}

	private function _sendAlert($to, $record = NULL)
	{
		$body = $this->params->get('params.emailbody' . $to, '');
		$body = \Joomla\String\StringHelper::trim($body);

		if(!$body)
		{
			return;
		}
		$app          = JFactory::getApplication();
		$this->record = $record ? $record : ItemsStore::getRecord($app->input->get('record_id'));

		$send_to = $this->params->get('params.sendto', 1);
		$emails  = array();
		switch($send_to)
		{
			case 1:
				$emails[] = JFactory::getUser($this->record->user_id)->get('email');
				break;
			case 2:
				$fields_model = MModelBase::getInstance('TFields', 'JoomcckModel');
				$fields       = $fields_model->getRecordFields($this->record);
				foreach($fields as $id => $field)
				{
					if($field->type == 'email')
					{
						$emails[] = $field->value;
					}
				}
				break;
			case 3:
				$emails[] = $this->params->get('params.custom_email', FALSE);
				break;
		}

		ArrayHelper::clean_r($emails);

		if(empty($emails))
		{
			return;
		}

		$mailer   = JFactory::getMailer();
		$from     = JFactory::getConfig()->get('mailfrom');
		$fromName = JFactory::getConfig()->get('fromname');
		$mailer->SetFrom($from, $fromName);
		foreach($emails as $email)
		{
			$mailer->AddAddress($email);
		}
		$mailer->setSubject($this->params->get('params.email_subject'));
		$mailer->IsHTML();

		$url  = JUri::root() . Url::record($this->record);
		$link = JHtml::link($url, $this->record->title);
		$body = JText::_($body);
		$body = str_replace(
			array('[USERNAME]', '[LINK]', '[TITLE]', '[URL]'),
			array(JFactory::getUser($this->record->user_id)->get('name'), $link, $this->record->title, $url),
			$body
		);
		$mailer->setBody($body);
		$mailer->Send();
	}

	protected function _getVal($value, $html = TRUE)
	{
		$value = $this->params->get('params.status' . $value, 0);
		$c     = explode('^', $value);
		ArrayHelper::clean_r($c);

		$label = $c[0];

		$label = JText::_($label);

		if($html && isset($c[1]))
		{
			$label = "<span style=\"color:{$c[1]}\">{$label}</SPAN>";
		}

		return $label;
	}

	public function onImport($value, $params, $record = NULL)
	{
		return (int)$value;
	}

	public function onImportForm($heads, $defaults)
	{
		return $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id));
	}

	private function _runSql($to, $table_rec, $record_values_id = NULL)
	{
		$user = JFactory::getUser();
		if(!$record_values_id)
		{
			$table = JTable::getInstance('Record_values', 'JoomcckTable');
			$table->load(array('field_id' => $this->id, 'record_id' => $table_rec->id, 'section_id' => $table_rec->section_id));
			$record_values_id = $table->id;
		}

		if($this->params->get('params.sql_action_s' . $to, FALSE) && $this->params->get('params.sql_source_s' . $to))
		{
			$db      = JFactory::getDbo();
			$queries = explode(";", $this->params->get('params.sql_source_s' . $to));
			foreach($queries AS $sql)
			{
				$sql = str_replace(array('[ID]', '[RECORD_ID]', '[USER_ID]', '[AUTHOR_ID]', '[PARENT_ID]'),
					array(
						$record_values_id,
						$table_rec->id,
						$user->get('id', 0),
						$table_rec->user_id,
						$table_rec->parent_id
					), $sql
				);
				$db->setQuery($sql);
				$db->execute();
			}
		}
	}
}