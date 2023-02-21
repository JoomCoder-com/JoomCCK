<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckrelate.php';
require_once JPATH_ROOT . '/components/com_joomcck/api.php';

class JFormFieldCDripcontent extends CFormFieldRelate
{

	public function __construct($field, $default)
	{
		parent::__construct($field, $default);

		$this->subscriptions = array();
		if(isset($this->value['subscriptions']) && !empty($this->value['subscriptions']))
		{
			$this->subscriptions = $this->value['subscriptions'];
			//unset($this->value['subscriptions']);
		}
	}

	public function getInput()
	{
		$name         = "jform[fields][$this->id][parent]";
		$this->subscr = @$this->value['subscriptions'];
		$this->method = @$this->value['method'];
		$this->days   = @$this->value['days'];
		$this->quiz   = @$this->value['quiz'];
		$this->value  = @$this->value['parent'];

		ArrayHelper::clean_r($this->value);
		$user = JFactory::getUser();

		$app = JFactory::getApplication();

		$this->plans      = NULL;
		$this->inputvalue = $this->_render_input(
			$this->params->get('params.input_mode'), $name, $app->input->getInt('section_id'),
			array(
				$this->type_id
			), FALSE);

		if($this->params->get('params.subscription', 0) && in_array($this->params->get('params.can_select_subscr', 0), $user->getAuthorisedViewLevels()))
		{
			$this->plans = JHtml::_('emerald.plans', "jform[fields][{$this->id}][subscriptions][]", $this->params->get('params.subscription', array()), $this->subscriptions, 'CRESTRICTIONSTEPSDESCR');
		}

		return $this->_display_input();
	}

	public function onRenderFull($record, $type, $section)
	{
		return $this->_show('full', $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		return $this->_show('list', $record, $type, $section);
	}

	private function _show($client, $record, $type, $section)
	{
		$app           = JFactory::getApplication();
		$db            = JFactory::getDbo();
		$user          = JFactory::getUser();
		$this->default = new JRegistry($this->value);

		if(!$this->default->get('parent') && !$this->_is_parent($record))
		{
			return;
		}

		$has_access    = $this->_has_access($record, $section);
		$this->subscr  = $this->_ajast_subscr($record);
		$this->parent  = $this->_getParentParams();
		$this->text    = JText::_($this->params->get('params.close_text_list'));
		$this->access  = FALSE;
		$this->isadmin = FALSE;

		$db->setQuery("SELECT id, params FROM `#__js_res_fields` WHERE type_id = {$this->type_id} AND field_type = 'related_access'");
		$rf     = $db->loadObject();
		$fields = json_decode($record->fields, TRUE);

		if(!empty($rf->id) && isset($fields[$rf->id]) && $fields[$rf->id] == -1)
		{
			$this->text = JText::_($this->params->get('params.open_text_list'));

			return $this->_display_output($client, $record, $type, $section);
		}

		$this->text   = JText::_($this->params->get('params.open_text_list'));
		$this->access = TRUE;

		if(
			($record->user_id && $record->user_id == $user->get('id') && $this->params->get('params.manual_author')) ||
			(in_array($this->params->get('params.manual_who'), $user->getAuthorisedViewLevels())) ||
			(MECAccess::allowRestricted($user, $section))
		)
		{
			$this->isadmin = TRUE;
		}

		if(!$has_access)
		{
			$this->access = FALSE;

			if(!$user->get('id'))
			{
				$this->text = $this->params->get('params.close_login');
			}
			else
			{
				switch($this->parent->get('method', $this->params->get('params.activ_mode')))
				{
					case 'auto':
						if($has_access === NULL)
						{
							$this->text = str_replace('[RECORD]', $this->_record(), JText::_($this->params->get('params.close_msg_noparent')));
						}
						else
						{
							$this->text = str_replace('[DATE]', $this->_date(), JText::_($this->params->get('params.close_msg_auto')));
						}
						break;
					case 'manual':
						$this->text = str_replace('[RECORD]', $this->_record(), JText::_($this->params->get('params.close_msg_manual')));
						break;

					case 'quiz':
						$this->text = str_replace(array('[QUIZ]', '[RECORD]'), array(
							'<b>' . $this->_quiz() . '</b>',
							$this->_record()
						), JText::_($this->params->get('params.close_msg_quiz')));
						break;
				}
			}

			if($client == 'full')
			{
				if(!$user->get('id'))
				{

					Factory::getApplication()->enqueueMessage(JText::_($this->text),'warning');

					if(!empty($rf->id) && isset($fields[$rf->id]) && $fields[$rf->id] == 1)
					{
						$p_params = new JRegistry($rf->params);
						switch($p_params->get('params.relation'))
						{
							case 0:
								$parent = $record->parent_id;
								break;
							case 1:
								$db->setQuery("SELECT field_value
										FROM `#__js_res_record_values`
									   WHERE record_id = {$record->id}
										 AND field_type = 'child'
										 AND field_id = " . $p_params->get('params.field_parent'));
								$parent = (int)$db->loadResult();
								break;
						}

						if($parent)
						{
							$this->parent = ItemsStore::getRecord($parent);
							$fields       = json_decode($this->parent->fields, TRUE);

							$plans = array_keys($fields[$p_params->get('params.field_plans')]);
							\Joomla\Utilities\ArrayHelper::toInteger($plans);

							$em_api = JPATH_ROOT . '/components/com_emerald/api.php';

							if(JFile::exists($em_api))
							{
								require_once $em_api;
								$app->redirect(EmeraldApi::getLink('emlist', FALSE, $plans));
							}

							return FALSE;

						}

					}

					$app->redirect(JRoute::_('index.php?option=com_users&view=login&return=' . urlencode(base64_encode(JURI::getInstance()->toString()))), FALSE);

					return FALSE;
				}

				if($this->subscr)
				{
					$this->_is_subscribed($record, $section, $this->subscr, 1);
				}

				Factory::getApplication()->enqueueMessage(JText::_($this->text),'warning');

				$url = JRoute::_(Url::records($section, $app->input->get('cat_id'), $app->input->get('user_id')), FALSE);
				if($this->params->get('params.redirect'))
				{
					$db = JFactory::getDbo();
					$db->setQuery(sprintf("SELECT field_value FROM `#__js_res_record_values`
						WHERE field_id = %d AND record_id = %d", $this->params->get('params.redirect'), $record->id));
					$record_id = $db->loadResult();
					if(!empty($record_id))
					{
						$url = JRoute::_(Url::record($record_id), FALSE);
					}
				}
				$app->redirect($url);
			}
		}

		if(!$user->get('id'))
		{
			$this->access = FALSE;
			$this->text   = JText::_($this->params->get('params.close_login'));
		}
		else
		{
			if($this->subscr)
			{
				if(!$this->_is_subscribed($record, $section, $this->subscr, 0))
				{
					$this->access = FALSE;
					$this->text   = JText::_($this->params->get('params.subscription_msg'));
				}
			}

			if($this->access)
			{
				$this->text = JText::_($this->params->get('params.open_text_list'));
				if(!$has_access)
				{
					$this->access = FALSE;
					$this->text   = JText::_($this->params->get('params.close_text_list'));
				}
			}
		}

		return $this->_display_output($client, $record, $type, $section);
	}

	private function _getParentParams()
	{
		if(Empty($this->value['parent']))
		{
			return;
		}
		$record = JTable::getInstance('Record', 'JoomcckTable');
		$record->load($this->value['parent']);
		$values = json_decode($record->fields, TRUE);

		return new JRegistry($values[$this->id]);

	}

	public function _is_parent($record)
	{
		$db = JFactory::getDbo();
		$db->setQuery("SELECT id FROM `#__js_res_record_values` WHERE field_value = {$record->id} AND field_id = {$this->id}");

		return ($db->loadResult() > 0);
	}

	private function _has_access($record, $section)
	{
		static $out = array();

		if(array_key_exists($record->id, $out))
		{
			return $out[$record->id];
		}

		if(!JFactory::getUser()->get('id'))
		{
			$out[$record->id] = FALSE;

			return FALSE;
		}

		$default = new JRegistry($this->value);
		if(!$default->get('parent'))
		{
			$out[$record->id] = TRUE;

			return TRUE;
		}
		$user = JFactory::getUser();
		if(MECAccess::allowRestricted($user, $section))
		{
			$out[$record->id] = TRUE;

			return TRUE;
		}
		if(in_array($this->params->get('params.manual_who'), $user->getAuthorisedViewLevels()))
		{
			$out[$record->id] = TRUE;

			return TRUE;
		}

		if($record->user_id && ($record->user_id == $user->get('id')))
		{
			$out[$record->id] = TRUE;

			return TRUE;
		}

		$default = $this->_getParentParams();

		$db = JFactory::getDbo();
		if($default->get('method', $this->params->get('params.activ_mode')) == 'auto')
		{
			$hour = round($default->get('days', $this->params->get('params.days')) * 24);

			$sql = sprintf("SELECT IF(DATE(`ctime`) + INTERVAL %d HOUR <= DATE('%s'), 1, 0) AS result FROM `#__js_res_hits` WHERE `record_id` = %d AND `user_id` = %d",
				$hour, JDate::getInstance()->toSql(), $this->value['parent'],
				JFactory::getUser()->get('id'));
		}
		else
		{
			$sql = sprintf("SELECT id FROM #__js_res_field_stepaccess WHERE record_id = %d AND user_id = %d AND field_id = %d",
				$this->value['parent'], JFactory::getUser()->get('id'), $this->id);
		}

		$db->setQuery($sql);

		$out[$record->id] = $db->loadResult();

		return $out[$record->id];
	}

	public function _approveUser($post)
	{
		$data['user_id']   = $post['user_id'];
		$data['record_id'] = $post['record_id'];
		$data['field_id']  = $this->id;

		JTable::addIncludePath(JPATH_ROOT . '/components/com_joomcck/fields/dripcontent/tables');
		$table = JTable::getInstance('Stepaccess', 'JoomcckTable');

		$table->load($data);

		if(!$table->id)
		{
			$data['id']    = NULL;
			$data['ctime'] = JFactory::getDate()->toSql();
			$table->reset();
			$table->id = NULL;
			$table->bind($data);
			$table->store();
		}
		else
		{
			$this->setError(JText::_('CSTEPUSERALREADYAPPROVED'));

			return;
		}

		return 1;
	}

	protected function _record()
	{
		$record = ItemsStore::getRecord($this->value['parent']);

		return JHtml::link(Url::record($record), $record->title);
	}

	protected function _render_quiz()
	{
		$id = @$this->value['quiz'];

		if(!$id)
		{
			return JText::_('DC_QUIZNOTSET');
		}

		JLoader::register('JoomlaquizHelper', JPATH_SITE . '/components/com_joomlaquiz/helpers/joomlaquiz.php');
		JoomlaquizHelper::isJoomfish();

		require_once JPATH_SITE . '/components/com_joomlaquiz/models/quiz.php';
		$model       = MModelBase::getInstance('Quiz', 'JoomlaquizModel');
		$quiz_params = $model->getQuizParams($id);

		$db = JFactory::getDbo();
		$db->setQuery("SELECT `template_name` FROM #__quiz_templates WHERE `id` = '" . $quiz_params->c_skin . "'");
		$template_name = $db->loadResult();

		require_once JPATH_SITE . '/components/com_joomlaquiz/views/templates/view.html.php';
		$tmpl = new JoomlaquizViewTemplates($template_name);

		$this->quiz_params   = $quiz_params;
		$this->is_preview    = FALSE;
		$this->preview_quest = 0;
		$this->preview_id    = '';

		@ob_start();
		require_once JPATH_SITE . '/components/com_joomlaquiz/views/quiz/tmpl/default.php';
		$text = @ob_get_contents();
		@ob_end_clean();

		return $text;

	}

	protected function _quiz()
	{
		$id = @$this->value['quiz'];

		if(!$id)
		{
			return JText::_('DC_QUIZNOTSET');
		}

		$db = JFactory::getDbo();

		$db->setQuery('SELECT c_title FROM #__quiz_t_quiz WHERE c_id = ' . $id);

		return $db->loadResult();

	}

	protected function _is_subscribed($record, $section, $plans, $redirect)
	{
		if(empty($plans))
		{
			return TRUE;
		}
		$em_api = JPATH_ROOT . '/components/com_emerald/api.php';
		if(!JFile::exists($em_api))
		{
			return TRUE;
		}
		require_once $em_api;

		$user = JFactory::getUser();

		if($user->get('id') && ($user->get('id') == $record->user_id) && $this->params->get('params.subscr_skip_author'))
		{
			return TRUE;
		}

		if(in_array($this->params->get('params.subscr_skip'), $user->getAuthorisedViewLevels()))
		{
			return TRUE;
		}

		if($this->params->get('params.subscr_skip_moderator', 1) && MECAccess::allowRestricted($user, $section))
		{
			return TRUE;
		}

		/*$url = null;

		if($this->params->get('params.subscription_child') && $this->params->get('params.subscription_redirect') == 0)
		{
			$db = JFactory::getDbo();
			$db->setQuery(sprintf("SELECT field_value FROM `#__js_res_record_values` WHERE field_id = %d AND record_id = %d", $this->params->get('params.subscription_child'), $record->id));
			$record_id = $db->loadResult();
			if(!empty($record_id))
			{
				$url = JRoute::_(Url::record($record_id));
			}
		}*/

		return EmeraldApi::hasSubscription(
			$plans,
			$this->params->get('params.subscription_msg'),
			NULL,
			$this->params->get('params.subscription_count'),
			$redirect);
	}

	public function _ajast_subscr($record)
	{
		if(!$record->user_id)
		{
			return;
		}

		$user = JFactory::getUser($record->user_id);

		if(in_array($this->params->get('params.can_select_subscr', 0), $user->getAuthorisedViewLevels()) &&
			$this->params->get('params.subscription')
		)
		{
			$subscr = $this->subscriptions;
		}
		else
		{
			$subscr = $this->params->get('params.subscription');
		}

		ArrayHelper::clean_r($subscr);

		return $subscr;
	}

	public function onGetList($params)
	{
		$db         = JFactory::getDbo();
		$section_id = JFactory::getApplication()->input->getInt('section_id');

		$query = $db->getQuery(TRUE);
		$query->select('id, title, null, title');
		$query->from('#__js_res_record');
		if(CStatistics::hasUnPublished($section_id))
		{
			$query->where('published = 1');
		}
		$query->where('hidden = 0');
		$query->where('section_id = ' . $section_id);
		$query->where('type_id = ' . $this->type_id);
		if($this->params->get('params.user_strict'))
		{
			$user_id = JFactory::getUser()->get('id');
			$query->where('user_id = ' . ($user_id ? $user_id : 1));
		}
		$db->setQuery($query);

		return $db->loadRowList();
	}

	protected function _date()
	{
		$default = $this->_getParentParams();

		$db  = JFactory::getDbo();
		$hour = round($default->get('days', $this->params->get('params.days')) * 24);
		$sql = sprintf("SELECT UNIX_TIMESTAMP(`ctime` + INTERVAL %d DAY) FROM `#__js_res_hits` WHERE record_id = %d AND user_id = %d",
			$hour, $this->value['parent'], JFactory::getUser()->get('id'));
		$db->setQuery($sql);
		$result = $db->loadResult();

		return JDate::getInstance($result)->format($this->params->get('params.dateformat', $this->params->get('params.dateformat_cus', 'd m Y')));
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		return $value;
	}

	public function onStoreValues($validData, $record)
	{
		if(!empty($this->value['quiz']))
		{
			return array(
				'parent' => $this->value['parent'],
				'quiz'   => $this->value['quiz']
			);
		}

		return $this->value['parent'];
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		if(!empty($value['parent']))
		{
			return parent::onPrepareFullTextSearch($value['parent'], $record, $type, $section);
		}

		return NULL;
	}

	protected function _get_quiz_select($default)
	{
		$db = JFactory::getDbo();

		$query = "(SELECT '- Select quiz -' AS `text`, '- Select quiz -' AS `quiz_id`, '0' AS `value` FROM `#__users` LIMIT 0,1) UNION (SELECT `c_title` AS `text`, `c_title` AS `quiz_id`, `c_id` AS `value` FROM `#__quiz_t_quiz` WHERE `c_id` > 0)";
		$db->setQuery($query);

		$options = $db->loadObjectList();

		return JHtml::_('select.genericlist', $options, "jform[fields][$this->id][quiz]", NULL, 'value', 'text', $default);
	}
}
