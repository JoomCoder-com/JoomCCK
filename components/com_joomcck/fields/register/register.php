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
JFactory::getLanguage()->load('com_users');

class JFormFieldCregister extends CFormField
{

	public function getInput()
	{
		$user        = JFactory::getUser();
		$this->group = NULL;

		if(count($this->params->get('params.new_usertype')) > 1)
		{
			$ids = implode(',', $this->params->get('params.new_usertype'));
			$db  = JFactory::getDbo();
			$db->setQuery("SELECT id as value, title as text FROM #__usergroups WHERE id IN({$ids})");
			$list        = $db->loadObjectList();
			$this->group = JHTML::_('select.genericlist', $list, 'jform[fields][' . $this->id . '][group]', 'class="span12"');
		}

		if(!$user->get('id'))
		{
			return $this->_display_input();
		}
	}

	public function onJSValidate()
	{
		ob_start();
		include JPATH_ROOT . '/components/com_joomcck/fields/register/tmpl/js/validate.php';
		$out = ob_get_contents();
		ob_end_clean();

		return $out;
	}

	public function validate($value, $record, $type, $section)
	{
		$user = JFactory::getUser();
		if($user->get('id'))
		{
			return TRUE;
		}

		$values = new JRegistry($value);
		$db     = JFactory::getDbo();

		if($values->get('pass3'))
		{
			if(!$this->params->get('params.field_id_email') && !$values->get('login'))
			{
				$this->setError(JText::_('CR_LOGINREQUIRED'));

				return FALSE;
			}
			if($this->params->get('params.field_id_email') && empty($record['fields'][$this->params->get('params.field_id_email')]))
			{
				$this->setError(JText::_('CR_EMAILREQUIRED'));

				return FALSE;
			}
			if($this->params->get('params.field_id_email') && !JMailHelper::isEmailAddress($record['fields'][$this->params->get('params.field_id_email')]))
			{
				$this->setError(JText::_('CR_ENTEREDINCORRECT'));

				return FALSE;
			}

			$query = $db->getQuery(TRUE);

			$query->select('id, username, password');
			$query->from('#__users');
			if($this->params->get('params.field_id_email'))
			{
				$query->where('email = ' . $db->Quote($record['fields'][$this->params->get('params.field_id_email')]));
			}
			else
			{
				$query->where('username =' . $db->Quote($db->escape($values->get('login'))));
			}
			$db->setQuery($query);

			if(!$result = $db->loadObject())
			{
				$this->setError(JText::_('CR_LOGINUSERNOTFOUND'));

				return FALSE;
			}

			if(!JUserHelper::verifyPassword($values->get('pass3'), $result->password, $result->id))
			{
				$this->setError(JText::_('CR_PASSDOESNOTMATCH'));

				return FALSE;
			}

			return $this->_login($result->username, $values->get('pass3'));
		}

		if(!$values->get('pass') || !$values->get('pass2'))
		{
			$this->setError(JText::_('CR_REGLOGINREQUIRED'));

			return FALSE;
		}

		if($values->get('pass') != $values->get('pass2'))
		{
			$this->setError(JText::_('CR_PASSDOESNOTMATCH'));

			return FALSE;
		}

		$user_params = JComponentHelper::getParams('com_users');

		if($user_params->get('minimum_integers') && (preg_match_all('/[0-9]/', $values->get('pass')) < $user_params->get('minimum_integers')))
		{
			$this->setError(JText::plural('COM_USERS_MSG_NOT_ENOUGH_INTEGERS_N', $user_params->get('minimum_integers')));

			return FALSE;
		}

		if($user_params->get('minimum_symbols') && (preg_match_all('[\W]', $values->get('pass')) < $user_params->get('minimum_symbols')))
		{
			$this->setError(JText::plural('COM_USERS_MSG_NOT_ENOUGH_SYMBOLS_N', $user_params->get('minimum_symbols')));

			return FALSE;
		}

		if($user_params->get('minimum_uppercase') && (preg_match_all("/[A-Z]/", $values->get('pass')) < $user_params->get('minimum_uppercase')))
		{
			$this->setError(JText::plural('COM_USERS_MSG_NOT_ENOUGH_UPPERCASE_LETTERS_N', $user_params->get('minimum_uppercase')));

			return FALSE;
		}

		if($user_params->get('minimum_length') && (JString::strlen((string)$values->get('pass')) < $user_params->get('minimum_length')))
		{
			$this->setError(JText::plural('COM_USERS_MSG_PASSWORD_TOO_SHORT_N', $user_params->get('minimum_length')));

			return FALSE;
		}

		if(!$this->params->get('params.field_id_email') && !$values->get('email'))
		{
			$this->setError(JText::_('CR_EMAILREQUIRED'));

			return FALSE;
		}

		if($values->get('email') && !JMailHelper::isEmailAddress($values->get('email')))
		{
			$this->setError(JText::_('CR_ENTEREDINCORRECT'));

			return FALSE;
		}
		if($this->params->get('params.loginname') && !$values->get('username'))
		{
			$this->setError(JText::_('CR_USERNAMEREQUIRED'));

			return FALSE;
		}
		if($this->params->get('params.name') && !$values->get('name'))
		{
			$this->setError(JText::_('CR_NAMEREQUIRED'));

			return FALSE;
		}

		if($this->params->get('params.field_id_email') && empty($record['fields'][$this->params->get('params.field_id_email')]))
		{
			$this->setError(JText::_('CR_EMAILREQUIRED'));

			return FALSE;
		}

		if($this->params->get('params.field_id_email') && !JMailHelper::isEmailAddress($record['fields'][$this->params->get('params.field_id_email')]))
		{
			$this->setError(JText::_('CR_ENTEREDINCORRECT'));

			return FALSE;
		}

		if($this->params->get('params.field_id_email'))
		{
			$email = $record['fields'][$this->params->get('params.field_id_email')];
		}
		else
		{
			$email = $values->get('email');
		}

		$db->setQuery("SELECT id, username, password FROM #__users WHERE email = '{$email}'");
		$result = $db->loadObject();
		if(!$result)
		{
			if($this->params->get('params.loginname'))
			{
				$db->setQuery("SELECT id, username, password FROM `#__users` WHERE username = '" . $db->escape($values->get('username')) . "'");
			}
			else
			{
				$db->setQuery("SELECT id, username, password FROM `#__users` WHERE username = '" . $db->escape($values->get('email')) . "'");
			}
			$result = $db->loadObject();
		}

		if($result)
		{
			/*
			$parts     = explode(':', $result->password);
			$crypt     = $parts[0];
			$salt      = @$parts[1];
			$testcrypt = JUserHelper::getCryptedPassword($values->get('pass'), $salt);

			if($crypt == $testcrypt)
			{
				return $this->_login($result->username, $values->get('pass'));
			}
			*/

			if(!JUserHelper::verifyPassword($values->get('pass'), $result->password, $result->id))
			{
				$this->setError(JText::_('CR_EMAINAMELEXISTS'));

				return FALSE;
			}


			if(!$this->_login($result->username, $values->get('pass')))
			{
				$this->setError(JText::_('CR_PASSDOESNOTMATCH'));

				return FALSE;
			}

			return TRUE;
		}

		$data['name']      = $values->get('name', $values->get('username', $email));
		$data['username']  = $values->get('username', $email);
		$data['email1']    = $email;
		$data['email2']    = $email;
		$data['password1'] = $values->get('pass');
		$data['password2'] = $values->get('pass');

		$def = $this->params->get('params.new_usertype');
		$ut  = $values->get('group', @$def[0]);

		MModelBase::addIncludePath(JPATH_ROOT . '/components/com_users/models');
		$model  = MModelBase::getInstance('Registration', 'UsersModel');
		$return = $model->register($data);

		if(!$return)
		{
			$this->setError($model->getError());

			return FALSE;
		}


		$query = "UPDATE `#__users` SET block = 0, activation = '' WHERE email = " . $db->q($email, TRUE);
		$db->setQuery($query);
		$db->execute();

		$query = $db->getQuery(TRUE);
		$query->select('id, username, password');
		$query->from('#__users');
		$query->where('username = ' . $db->Quote($data['username']));
		$db->setQuery($query);

		$result = $db->loadObject();

		JUserHelper::addUserToGroup($result->id, $ut);

		if(!JUserHelper::verifyPassword($values->get('pass'), $result->password, $result->id))
		{
			$this->setError(JText::_('CR_PASSDOESNOTMATCH'));

			return FALSE;
		}

		return $this->_login($data['username'], $values->get('pass'));
	}

	private function _login($user, $pass)
	{
		$options             = array();
		$options['remember'] = 1;
		$options['return']   = NULL;
		$options['silent']   = TRUE;
		$options['lifetime'] = 1;

		$credentials             = array();
		$credentials['username'] = $user;
		$credentials['password'] = $pass;

		$result = JFactory::getApplication()->login($credentials, $options);

		if($result == FALSE)
		{
			$this->setError(JText::_('CR_LOGINFAIL'));

			return FALSE;
		}

		return TRUE;
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		return;
	}


	public function onRenderFull($record, $type, $section)
	{
		return;
	}

	public function onRenderList($record, $type, $section)
	{
		return;
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		return;
	}
}

