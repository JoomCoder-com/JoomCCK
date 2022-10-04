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
jimport('mint.recaptchalib');
jimport('joomla.mail.helper');
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckfield.php';

class JFormFieldCEmail extends CFormField
{

	public function getInput()
	{
		$document = JFactory::getDocument();
		$document->addScript(JURI::root(TRUE) . '/components/com_joomcck/fields/email/email.js');
		$user = JFactory::getUser();

		if($this->params->get('params.enter_mail', 1) && in_array($this->params->get('params.enter_mail', 1), $user->getAuthorisedViewLevels()))
		{
			if(!$this->value && $user->get('id') && $this->params->get('params.dafault_user_email', 1) && JFactory::getApplication()->input->get('id'))
			{
				$this->value = $user->get('email');
			}

			return $this->_display_input();
		}
	}

	public function onJSValidate()
	{
		$js = "\n\t\tvar efield = jQuery('#field_{$this->id}');
		efield = efield[0]";
		if($this->required)
		{
			$js .= "\n\t\tif(efield.value == ''){hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(JText::sprintf('CFIELDREQUIRED', $this->label)) . "');}";
		}
		$js .= "\n\t\t if( efield.value != '' && !EmailCheck(efield.value) ) {hfid.push({$this->id}); isValid = false; errorText.push('" . addslashes(JText::sprintf('E_ENTEREDINCORRECT', $this->label)) . "');}";

		return $js;
	}

	public function validateField($value, $record, $type, $section)
	{
		if($value && !JMailHelper::isEmailAddress($value))
		{
			$this->setError(JText::sprintf('E_ENTEREDINCORRECT', $this->label));

			return FALSE;
		}

		return parent::validateField($value, $record, $type, $section);
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		$value = filter_var($value, FILTER_SANITIZE_EMAIL);

		return $value;
	}

	public function onStoreValues($validData, $record)
	{
		$this->value = filter_var($this->value, FILTER_SANITIZE_EMAIL);

		return $this->value;
	}

	public function onFilterWornLabel($section)
	{
		return JHtml::_('content.prepare', $this->value);
	}

	public function onFilterWhere($section, &$query)
	{
		$ids = $this->getIds("SELECT record_id FROM #__js_res_record_values WHERE field_value = '{$this->value}' AND section_id = {$section->id} AND field_key = '{$this->key}'");

		return $ids;
	}

	public function onRenderFilter($section, $module = FALSE)
	{

		$document = JFactory::getDocument();
		$document->addScript(JURI::root(TRUE) . '/components/com_joomcck/fields/email/email.js');

		return $this->_display_filter($section, $module);
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		return $value;
	}

	public function onRenderFull($record, $type, $section)
	{
		return $this->_render('full', $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		return $this->_render('list', $record, $type, $section);
	}

	private function _render($client, $record, $type, $section)
	{
		$params   = $this->params;
		$document = JFactory::getDocument();
		$uri      = \Joomla\CMS\Uri\Uri::getInstance();

		$this->author = JFactory::getUser($record->user_id);
		$this->user   = JFactory::getUser();
		$this->url    = $uri->toString();

		return $this->_display_output($client, $record, $type, $section);

	}

	public function _sendEmail($post, $record)
	{
		$user   = JFactory::getUser();
		$app    = JFactory::getApplication();
		$config = JFactory::getConfig();

		$_from = $app->getCfg('mailfrom');
		$_name = $app->getCfg('fromname');

		$data = @$post['email'][$this->id];
		//$data['captcha'] = $app->input->get('recaptcha_response_field');
		if($user->id)
		{
			if(!$this->params->get('params.change_name_from', 1))
			{
				$data['name'] = CCommunityHelper::getName($user->id, $record->section_id, array('onelink' => 1, 'noonlinestatus' => 1, 'external' => 1));
			}
			if(!$this->params->get('params.change_email_from', 1))
			{
				$data['email_from'] = $user->get('email');
			}
		}

		if(!$user->id && $this->params->get('params.show_captcha', 1))
		{
			$joomcck_params = JComponentHelper::getParams('com_joomcck');
			jimport('mint.recaptchalib');
			$captcha = JCaptcha::getInstance($joomcck_params->get('captcha', 'recaptcha'), array('namespace' => 'email'));
			if(!$captcha->checkAnswer('code'))
			{
				JError::raiseWarning(400, JText::_('E_SECURITYCODEINCORRECT'));

				return FALSE;
			}
		}

		//$schema = explode("\n", str_replace("\r", '', trim($this->params->get('params.additional_fields', ''))));

		ob_start();
		$tpath = JPATH_THEMES . '/' . JFactory::getApplication()->getTemplate() . '/html/com_joomcck/fields/email/email/' . $this->params->get('params.template_body', 'default.php');
		if(!JFile::exists($tpath))
		{
			$tpath = JPATH_ROOT . '/components/com_joomcck/fields/email/tmpl/email/' . $this->params->get('params.template_body', 'default.php');
		}
		include $tpath;
		$body = ob_get_contents();
		ob_end_clean();

		$name = NULL;
		switch($this->params->get('params.to', 1))
		{
			case 1 :
				if(!$this->value)
				{
					JError::raiseWarning(400, JText::_('E_EMAILNOTENTERED'));

					return FALSE;
				}
				$email = $this->value;

				break;
			case 2 :
				$email = $_from;
				$name  = $_name;
				break;
			case 3 :
				if(!$record->user_id)
				{
					JError::raiseWarning(400, JText::_('E_ARTICLEANONYMOUSE'));

					return FALSE;
				}
				$author = JFactory::getUser($record->user_id);
				$email  = $author->get('email');
				$name   = $author->get('name');
				break;
			case 4 :
				if(empty($data['email_to']))
				{
					JError::raiseWarning(400, JText::_('E_SENDTONOTENTERED'));

					return;
				}
				$email = $data['email_to'];
				break;
			case 5 :
				$email = $this->params->get('params.custom');
				if(empty($email))
				{
					JError::raiseWarning(400, JText::_('E_EMAILNOTENTERED'));

					return FALSE;
				}
				break;

			default :
				break;
		}
		if(!JMailHelper::isEmailAddress($email))
		{
			JError::raiseWarning(400, JText::_('E_SENDTOINCORRENT'));

			return;
		}

		$mail = JFactory::getMailer();
		$mail->AddAddress($email);

		if($this->params->get('params.copy_to'))
		{
			$copy_emails = explode(',', '', str_replace(' ', '', $this->params->get('params.copy_to')));
			foreach($copy_emails AS $e)
			{
				if(filter_var($e, FILTER_VALIDATE_EMAIL))
				{
					$mail->addCC($e);
				}
			}
		}

		if($data['name'] == '')
		{
			JError::raiseWarning(400, JText::_('E_SENDERNAMENOTENTERED'));

			return;
		}
		if($data['email_from'] == '')
		{
			JError::raiseWarning(400, JText::_('E_SENDERMAILNOTENTERED'));

			return;
		}
		if(!JMailHelper::isEmailAddress($data['email_from']))
		{
			JError::raiseWarning(400, JText::_('E_SENDERMAILINCORRENT'));

			return;
		}

		$copy_send_msg = '';
		if($this->params->get('params.copy_to_sender', 1) && $data['copy_to_sender'])
		{
			$mail->AddCC($data['email_from']);
		}

		if($this->params->get('params.cc', 0) && $data['cc'] != '')
		{
			$emails = explode(',', $data['cc']);
			if($emails && count($emails) > 0)
			{
				foreach($emails as $e)
				{
					$mail->AddCC($e);
				}
			}
		}

		if($this->params->get('params.subject_style', 1) == 1 && $data['subject'] == '')
		{
			JError::raiseWarning(400, JText::_('E_SUBJNOTENTERED'));

			return;
		}
		elseif($this->params->get('params.subject_style', 1) == 0)
		{
			$data['subject'] = JText::_($this->params->get('params.subject', 'No subject set in email field'));
		}

		$files = new JInputFiles();
		$files = $files->get('email_' . $this->id, array());


		$formats = explode(",", $this->params->get('params.formats'));
		foreach($files as $i => $file)
		{
			if(empty($file['name']))
			{
				continue;
			}
			$ext = JFile::getExt($file['name']);
			ArrayHelper::clean_r($formats);
			if(!in_array(strtolower($ext), $formats))
			{
				JError::raiseWarning(400, JText::_('E_WRONGATTACHEXT'));

				return FALSE;
			}
			if(JFile::exists($file['tmp_name']))
			{
				JFile::copy($file['tmp_name'], JPATH_CACHE . DIRECTORY_SEPARATOR . $file['name']);
				$mail->addAttachment(JPATH_CACHE . DIRECTORY_SEPARATOR . $file['name']);
			}
		}

		$reply[0] = $data['email_from'];
		$reply[1] = strip_tags($data['name']);

		if($this->params->get('params.rep_name') && $this->params->get('params.rep_email'))
		{
			$reply[0] = $this->params->get('params.rep_email');
			$reply[1] = $this->params->get('params.rep_name');
		}

		if(!$reply[1] && !$reply[0])
		{
			if($user->get('id'))
			{
				$reply[0] = $user->get('email');
				$reply[1] = $user->get('name');
			}
			else
			{
				$reply[0] = $_from;
				$reply[1] = $_name;
			}
		}

		$sender[0] = $_from;
		$sender[1] = $_name;

		$mail->setSender($sender);
		$mail->addReplyTo($reply[0], $reply[1]);
		$mail->isHTML(!!$this->params->get('params.email_format'));
		$mail->setBody(JMailHelper::cleanBody($body));
		$mail->setSubject(JMailHelper::cleanSubject($data['subject']));

		if(!$mail->Send())
		{
			JError::raiseWarning(400, JText::_('E_ERRSEND'));

			return FALSE;
		}

		$acemail = JPATH_ADMINISTRATOR . '/components/com_acymailing/helpers/helper.php';

		if($this->params->get('params.acemail') && JFile::exists($acemail))
		{
			include_once $acemail;
			$myUser          = new stdClass();
			$myUser->email   = $reply[0];
			$myUser->name    = $reply[1];
			$subscriberClass = acymailing_get('class.subscriber');
			$subscriberClass->save($myUser);
		}

		return TRUE;
	}

	public function _getForm($record, $section)
	{
		$document = JFactory::getDocument();
		$document->addScript(JURI::root(TRUE) . '/components/com_joomcck/fields/email/email.js');
		$author = JFactory::getUser($record->user_id);
		$user   = JFactory::getUser();
		$params = $this->params;
		$app    = JFactory::getApplication();

		if($app->input->post->count())
		{
			if($this->_sendEmail($_POST, $record))
			{
				JError::raiseNotice(100, JText::_('E_MAILSENT'));
				$js = '<script type="text/javascript">
					jQuery(document).ready(function(){
						parent.iframe_loaded(' . $record->id . $this->id . ', jQuery(\'body\').height());
					})
				</script>';

				return $js;
			}

			$default = @$_POST['email'][$this->id];
		}
		settype($default, 'array');
		$data = new JRegistry();
		$data->loadArray($default);

		$show_emailto = in_array($this->params->get('params.view_mail', 1), $user->getAuthorisedViewLevels());
		ob_start();

		$tpath = JPATH_ROOT . '/components/com_joomcck/fields/' . $this->type . '/tmpl/form/' . $this->params->get('params.template_form', 'default.php');

		include $tpath;
		$out = ob_get_contents();
		ob_end_clean();

		return $out;
	}

	public function onFilterData()
	{
		$db = JFactory::getDbo();

		$q = $this->request->get('q');
		$q = $db->escape($q);

		$query = $db->getQuery(TRUE);

		$query->select("field_value as value, CONCAT(field_value, ' <span class=badge>', count(record_id), '</span>') as label");
		$query->from('#__js_res_record_values');
		$query->where("field_key = '{$this->key}'");
		$query->where("field_value LIKE '%{$q}%'");
		$query->group('field_value');
		$db->setQuery($query, 0, 10);
		$result = $db->loadObjectList();

		return $result;
	}

	public function onImport($value, $params, $record = NULL)
	{
		return filter_var($value, FILTER_SANITIZE_EMAIL);
	}

	public function onImportForm($heads, $defaults)
	{
		return $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id));
	}
}

