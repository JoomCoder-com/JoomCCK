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

class JFormFieldCPasswd extends CFormField
{

	public function getInput()
	{
		$params = $this->params;
		$user   = JFactory::getUser();
		$value  = '';
		if($this->value && ($params->get('params.show_edit', 0) || in_array($params->get('params.allow_view', 0), $user->getAuthorisedViewLevels())))
		{
			$secret_word = $params->get('params.secret_word');
			if($secret_word || !$secret_word == 'joomla!')
			{
				$pass = $secret_word;
			}
			else
			{
				$config = JFactory::getConfig();
				$pass   = $config->getValue('config.secret');
			}
			$value = $this->_md5_decrypt($this->value, $pass);
		}
		$this->encrypt = $value;

		return $this->_display_input();
	}

	public function onJSValidate()
	{
	}

	public function validateField($value, $record, $type, $section)
	{
		return parent::validateField($value, $record, $type, $section);
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		if($value)
		{
			$secret_word = $this->params->get('params.secret_word');
			if($secret_word || !$secret_word == 'joomla!')
			{
				$pass = $secret_word;
			}
			else
			{
				$config = JFactory::getConfig();
				$pass   = $config->getValue('config.secret');
			}
			$value = $this->_md5_encrypt($value, $pass);

			return $value;
		}
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
		$user = JFactory::getUser();
		if(!$this->value)
		{
			return;
		}
		if(!in_array($this->params->get('params.allow_view', 0), $user->getAuthorisedViewLevels()) && !($user->id && $user->id == $record->user_id))
		{
			return;
		}

		$secret_word = $this->params->get('params.secret_word');
		if($secret_word)
		{
			$pass = $secret_word;
		}
		else
		{
			$config = JFactory::getConfig();
			$pass   = $config->getValue('config.secret');
		}
		$this->value = str_repeat('*', strlen($this->_md5_decrypt($this->value, $pass)));

		return $this->_display_output($client, $record, $type, $section);
	}

	//encode - decode passwd
	private function _get_rnd_iv($iv_len)
	{
		$iv = '';
		while($iv_len-- > 0)
		{
			$iv .= chr(mt_rand() & 0xff);
		}

		return $iv;
	}

	private function _md5_encrypt($plain_text, $password, $iv_len = 16)
	{
		$plain_text .= "\x13";
		$n = strlen($plain_text);
		if($n % 16)
		{
			$plain_text .= str_repeat("\0", 16 - ($n % 16));
		}
		$i        = 0;
		$enc_text = $this->_get_rnd_iv($iv_len);
		$iv       = substr($password ^ $enc_text, 0, 512);
		while($i < $n)
		{
			$block = substr($plain_text, $i, 16) ^ pack('H*', md5($iv));
			$enc_text .= $block;
			$iv = substr($block . $iv, 0, 512) ^ $password;
			$i += 16;
		}

		return base64_encode($enc_text);
	}

	public function _md5_decrypt($params, $record)
	{
		$iv_len   = 16;
		$password = $this->params->get('params.secret_word');
		if($password)
		{
			$pass = $password;
		}
		else
		{
			$config = JFactory::getConfig();
			$pass   = $config->getValue('config.secret');
		}
		$enc_text   = JoomcckFilter::base64($this->value);
		$n          = strlen($enc_text);
		$i          = $iv_len;
		$plain_text = '';
		$iv         = substr($password ^ substr($enc_text, 0, $iv_len), 0, 512);
		while($i < $n)
		{
			$block = substr($enc_text, $i, 16);
			$plain_text .= $block ^ pack('H*', md5($iv));
			$iv = substr($block . $iv, 0, 512) ^ $password;
			$i += 16;
		}

		return preg_replace('/\\x13\\x00*$/', '', $plain_text);
	}
}
