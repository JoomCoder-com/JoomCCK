<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckupload.php';
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckcommerce.php';
require_once JPATH_ROOT . '/components/com_joomcck/library/php/commerce/mintpay.php';

class JFormFieldCPaytodownload extends CFormFieldUpload implements CFormFieldCommerce
{

	function __construct($field, $default)
	{
		parent::__construct($field, $default);

		$this->gateway = MintPay::getInstance($this->id, $this->params->get('params.default_gateway'), $this->params->get('params.log'));
	}

	public function getInput()
	{
		/*
		 * if(!$this->gateway) { return JText::sprintf('CGATEWAYNOTFOUND', '');
		 * }
		 */
		$this->pay   = @$this->value['pay'];
		$this->value = @$this->value['files'];

		if($this->gateway)
		{
			$this->gateway_form = $this->gateway->form($this);
		}
		else
		{
			$this->gateway_form = JText::sprintf('CGATEWAYNOTFOUND', '');
		}

		$params['width']  = $this->params->get('params.width', 0);
		$params['height'] = $this->params->get('params.height', 0);

		$params['max_size']         = ($this->params->get('params.max_size', 2000) * 1024);
		$params['method']           = $this->params->get('params.method', 'auto');
		$params['max_count']        = $this->params->get('params.max_count', 0);
		$params['file_formats']     = $this->params->get('params.file_formats', 'zip, jpg, png, gif, bmp');
		$params['allow_edit_title'] = $this->params->get('params.allow_edit_title', 1);
		$params['allow_add_descr']  = $this->params->get('params.allow_add_descr', 1);

		$this->options   = $params;
		$this->fieldname = '[files]';

		$this->upload = parent::getInput();

		return $this->_display_input();
	}

	public function onReceivePayment($post, $record, &$controller)
	{
		if(!$this->gateway)
		{
			return;
		}
		$payment = $this->gateway->receive($this, $post, $record);
		$controller->setRedirect(JRoute::_(Url::record($record), FALSE));
	}

	public function onBeforeDownload($record, $file_index, $file_id, $return = TRUE)
	{
		$user = JFactory::getUser();

		if(empty($this->value['pay']['amount']))
		{
			return TRUE;
		}


		if(!$user->get('id'))
		{
			$this->setError(JText::_('P_LOGINTODOWNLOADMSG'));

			return FALSE;
		}

		if($user->get('id') == $record->user_id)
		{
			return TRUE;
		}

		if(in_array($this->params->get('params.skip_for'), $user->getAuthorisedViewLevels()))
		{
			return TRUE;
		}

		if(!in_array($this->params->get('params.allow_download', 1), $user->getAuthorisedViewLevels()))
		{
			$this->setError(JText::_("CNORIGHTSDOWNLOAD"));

			return FALSE;
		}

		$result = new stdClass();
		if($this->gateway)
		{
			$result = $this->gateway->get_order($user->get('id'), $record->id, $this->id);
		}

		if(empty($result->id))
		{
			$this->setError(JText::_('P_PURCHASETODOWNLOAD'));
		}
		else
		{
			switch($result->status)
			{
				case 1:
					$this->setError(JText::_('P_PURCHASECANCELED'));
					break;
				case 2:
					$this->setError(JText::_('P_PURCHASEFAILED'));
					break;
				case 3:
					$this->setError(JText::_('P_PURCHASEPENDING'));
					break;
				case 4:
					$this->setError(JText::_('P_PURCHASEREFUNDED'));
					break;
			}
		}

		if($this->getErrors())
		{
			if(parent::onBeforeDownload($record, $file_index, $file_id, FALSE))
			{
				$this->error = array();
			}
		}

		if(!$this->getErrors() && !empty($result->id))
		{
			MintPay::getInstance($this->id, $this->params->get('params.default_gateway'), $this->params->get('params.log'))->count($result->id);
		}
	}

	public function onOrderList($order, $record)
	{
		$section = ItemsStore::getSection($record->section_id);
		$type    = ItemsStore::getType($record->type_id);
		$out     = $this->_render(1, 0, $record, $type, $section);

		return $out;
	}

	public function onPrepareSave($value, $record, $type, $section)
	{
		if(isset($value['files']))
		{
			$value['files'] = $this->_getPrepared($value['files']);
		}

		return $value;
	}

	public function onStoreValues($validData, $record)
	{
		$value = @$this->value['files'];
		settype($value, 'array');
		$out = $saved = array();
		foreach($value as $file)
		{
			$out[]   = $file['realname'];
			$saved[] = $file['id'];
		}

		$files = JTable::getInstance('Files', 'JoomcckTable');
		$files->markSaved($saved, $validData, $this->id);

		return $out;
	}

	public function onCopy($value, $record, $type, $section, $field)
	{
		if(!empty($value))
		{
			foreach($value['files'] as $key => $file)
			{

				$value['files'][$key] = $this->copyFile($file);
			}
		}

		return $value;
	}

	public function onRenderFull($record, $type, $section)
	{
		return $this->_render(2, 0, $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		return $this->_render(1, $this->params->get('params.list_limit', 5), $record, $type, $section);
	}

	public function onPrepareFullTextSearch($value, $record, $type, $section)
	{
		return parent::onPrepareFullTextSearch(@$value['files'], $record, $type, $section);
	}

	private function _render($client, $limit, $record, $type, $section)
	{
		// Necessary to separate field value and pay options
		$this->pay   = @$this->value['pay'];
		$this->value = (array)@$this->value['files'];

		// Set options for descritpion of the order
		$options = array();
		foreach($this->value as $k => $file)
		{
			$options[JText::_('CFILE') . ' ' . ($k + 1)] = $file['realname'];
		}
		$this->params->set('options', $options);
		$this->subscr = $this->_ajast_subscr($record);


		$this->gateway->prepare_output($this, $client, $record, $type, $section);

		$hits  = in_array($this->params->get('params.show_hit'), array(3, $client));
		$files = $this->getFiles($record, $hits);

		if($limit)
		{
			$files = array_slice($files, 0, $limit);
		}

		if(!$files)
		{
			return;
		}
		$this->files = $files;


		return $this->_display_output(($client == 1 ? 'list' : 'full'), $record, $type, $section);
	}
}