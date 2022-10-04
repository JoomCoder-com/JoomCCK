<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
require_once JPATH_ROOT. '/components/com_joomcck/library/php/fields/joomcckupload.php';
require_once JPATH_ROOT. '/components/com_joomcck/library/php/fields/joomcckcommerce.php';
require_once JPATH_ROOT. '/components/com_joomcck/library/php/commerce/mintpay.php';

class JFormFieldCPayvoucher extends CFormField
{

	function __construct($field, $default)
	{
		parent::__construct($field, $default);
		$this->gateway = MintPay::getInstance($this->id, $this->params->get('params.default_gateway'), $this->params->get('params.log'));
	}

	public function getInput()
	{
		$this->pay = @$this->value['pay'];
		$this->value = @$this->value['vouchers'];
				
		$this->text = htmlspecialchars(stripslashes($this->value), ENT_QUOTES, 'UTF-8');
		
		if($this->gateway)
		{
			$this->gateway_form = $this->gateway->form($this);
		}
		else
		{
			$this->gateway_form = JText::sprintf('CGATEWAYNOTFOUND', '');
		}
		return $this->_display_input();
		
	}

	public function onReceivePayment($post, $record, &$controller)
	{
		if(!$this->gateway)
		{
			return;
		}

		$order = $this->gateway->receive($this, $post, $record);
		
		if(!$this->gateway->table->params && ($this->gateway->table->status == 5 || $this->gateway->table->status == 3))
		{
			$fields = json_decode($record->fields, TRUE);
			//$this->gateway->log('fields', $fields);
			
			$vouchers = explode("\n", $fields[$this->id]['vouchers']);
			$voucher = array_shift($vouchers);
			
			//$this->gateway->log('voucher', $voucher);
			
			settype($vouchers, 'array');
			$fields[$this->id]['vouchers'] = implode("\n", $vouchers);
			
			$table = JTable::getInstance('Record', 'JoomcckTable');
			$table->load($record->id);
			$table->fields = json_encode($fields);
			$table->store();
			
			$this->gateway->table->params = $voucher;
			$this->gateway->table->store();
		}
		
		JFactory::getApplication()->enqueueMessage(JText::_('PV_SUCCESSFULPURCHASED').' '.$voucher);
		
		$controller->setRedirect(JRoute::_(Url::record($record), FALSE));
	}

	private function _render($client, $record, $type, $section)
	{
		// Nesessary to separate field value and pay options
		$this->pay = @$this->value['pay'];
		$this->value = @$this->value['vouchers'];
		$vnum = explode("\n", $this->value);
		ArrayHelper::clean_r($vnum);
		$this->vnum = count($vnum);

		$this->gateway->prepare_output($this, $client, $record, $type, $section);
		
		return $this->_display_output(($client == 1 ? 'list' : 'full'), $record, $type, $section);
	}


	public function onOrderList($order, $record)
	{
		if(!$order->params)
		{
			return;
		}
		
		if($order->status == 3)
		{
			$out[] = '<div class="alert alert-warning">';
			$out[] = JText::_('PV_ORDERNOTCONFIRMED');
		}
		elseif($order->status == 5) 
		{
			$out[] = '<div class="alert alert-success">';
			$out[] = $order->params;
		}
		else 
		{
			$out[] = '<div class="alert alert-error">';
			$out[] = JText::_('PV_ORDERREFUNDEDORFAILED');
		}
		$out[] = '</div>';
		
		return implode(" ", $out);
	}
	public function onRenderFull($record, $type, $section)
	{
		return $this->_render(2, $record, $type, $section);

	}

	public function onRenderList($record, $type, $section)
	{
		return $this->_render(1, $record, $type, $section);

	}

	public function validateField($value, $record, $type, $section)
	{
		$this->_crean_vouchers($value);
		return parent::validateField($value, $record, $type, $section);
	}
	public function onPrepareSave($value, $record, $type, $section)
	{
		$this->_crean_vouchers($value);
		return $value;
	}

	public function onStoreValues($validData, $record)
	{
		return array();
	}
	
	private function _crean_vouchers(&$value)
	{
		$values = explode("\n", str_replace("\r", "", $value['vouchers']));
		ArrayHelper::clean_r($values);
		$value['vouchers'] = implode("\n", $values);
	}
}
