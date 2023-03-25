<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();

require_once JPATH_ROOT . '/components/com_joomcck/library/php/commerce/mintpay.php';

class MintPay2Checkout extends MintPayAbstract
{

	public function button($field)
	{
		
		$user = JFactory::getUser();
		$options = $field->params;
		
		if ($user->get('id') && $user->get('id') == $options->get('params.skip_for') )
		{
			return;
		}
		
		settype($field->pay, 'array');
		$pay = new JRegistry($field->pay);
		$af = array();
		
		$p2co_amount = $pay->get('amount', $options->get('pay.default_amount', 0));
		$p2co_vendor = $pay->get('vendor', $options->get('pay.default_vendor'));
		$p2co_lang = $pay->get('default_lang', $options->get('pay.default_lang', 'en'));
		$p2co_cur = $pay->get('default_currency', $options->get('pay.default_currency', 'USD'));
		
		if ($p2co_amount == 0)
		{
			return sprintf('<p>%s</p>', JText::_('P2CO_FILES_ARE_FREE'));
		}
		
		if (!$p2co_vendor)
		{
			return sprintf('<p>%s</p>', JText::_('P2CO_NOVENDOR'));
		}
		
		if (!$user->get('id'))
		{
			return sprintf('<img  style="display:block;cursor:pointer" src="http://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif" onclick="alert(\'%s\')" alt="%s" />', JText::_('Please, login to buy now'), JText::_('Please, login to buy now'));
		}
		
		$price = $this->_price($p2co_amount, $p2co_cur);
		$topay = $price;
		
		$out[] = '<div style="clear:both"></div>';
		$out[] = "<p>" . JText::sprintf($options->get('params.btn_label', 'For instant download purchase only %s'), $topay) . "</p>";
		
		$out[] = '<form action="https://www.2checkout.com/2co/buyer/purchase?" method="post">';
		$out[] = $this->_hidden('sid', $p2co_vendor);
		$out[] = $this->_hidden('total', $p2co_amount);
		$out[] = $this->_hidden('cart_order_id', "{$field->record->id}::{$field->id}::" . time());
		$out[] = $this->_hidden('fixed', 'Y');
		$out[] = $this->_hidden('c_prod', "{$field->record->id}::{$field->id}");
		$out[] = $this->_hidden('c_name', $options->get('item_name'));
		$out[] = $this->_hidden('c_description', $options->get('item_name'));
		$out[] = $this->_hidden('c_tangible', 'N');
		$out[] = $this->_hidden('id_type', '1');
		$out[] = $this->_hidden('lang', $p2co_lang);
		$out[] = $this->_hidden('email', $user->email);
		$out[] = $this->_hidden('user_id', $user->id);
		$out[] = $this->_hidden('order_title', $options->get('item_name'));
		$out[] = $this->_hidden('demo', ($options->get('pay.demo') ? 'Y' : 'N'));
		
		if ($field->params->get('pay.allow_amount'))
		{
			$af[] = '<div class="formelm">
				<label>' . JText::_('P2CO_PRICE') . '</label>
				<div class="input-field"><input type="text" size="2" name="c_price" value="' . $p2co_amount . '" />
				</div></div>';
		}
		else
		{
			$out[] = $this->_hidden('c_price', $p2co_amount);
		}
		
		if ($field->params->get('pay.allow_count'))
		{
			$af[] = '<div class="formelm">
				<label>' . JText::_('P2CO_QNT') . '</label>
				<div class="input-field"><input type="text" size="2" name="quantity" value="' . $options->get('pay.default_count', 1) . '" /></div></div>';
		}
		else
		{
			$out[] = $this->_hidden('quantity', $options->get('pay.default_count', 1));
		}
		
		if (in_array($field->params->get('pay.allow_coupon'), $user->getAuthorisedViewLevels()))
		{
			$af[] = '<div class="formelm">
				<label>' . JText::_('P2CO_COUPON') . '</label>
				<div class="input-field"><input type="text" size="20" name="coupon" value="" /></div></div>';
		}
		
		if (count($af))
			$out[] = implode("\n", $af);
		
		$return = JRoute::_('index.php?option=com_joomcck&task=field.call&func=onReceivePayment&field_id=' . $field->id . '&record_id=' . $field->record->id, FALSE, -1);
		$out[] = $this->_hidden('x_Receipt_Link_URL', $return);
		
		$out[] = sprintf('<input style="display:block" type="image" border="0" src="http://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif" alt="%s" title="%s" />', JText::_('Buy Now'), JText::_('Buy Now'));
		$out[] = '</form>';
		
		return implode("\n", $out);
	
	}

	public function form($field)
	{
		$user = JFactory::getUser();
		
		$value = $field->pay;
		settype($value, 'array');
		$default = new JRegistry($value);
		
		$params = $field->params;
		
		$form = $this->load_form('2checkout', $field->id);
		
		$out[JText::_('P2CO_PRICE')] = '<input type="text" size="4" name="jform[fields][' . $field->id . '][pay][amount]" value="' . $default->get('amount') . '"/> ';
		
		if (in_array($params->get('pay.allow_currency'), $user->getAuthorisedViewLevels()))
		{
			$out[JText::_('P2CO_PRICE')] .= $form->getInput('default_currency', 'pay', $default->get('default_currency', $params->get('pay.default_currency')));
		}
		else
		{
			$out[JText::_('P2CO_PRICE')] .= $params->get('pay.default_currency');
		}
		
		if (in_array($params->get('pay.allow_vendor'), $user->getAuthorisedViewLevels()))
		{
			$out[JText::_('P2CO_VENDOR')] = $form->getInput('default_vendor', 'pay', $default->get('default_vendor', $params->get('pay.default_vendor')));
		}
		
		if (in_array($params->get('pay.allow_sword'), $user->getAuthorisedViewLevels()))
		{
			$out[JText::_('P2CO_SECRETWORD')] = $form->getInput('default_sword', 'pay', $default->get('default_sword', $params->get('pay.default_sword')));
		}
		
		if (in_array($params->get('pay.allow_lang'), $user->getAuthorisedViewLevels()))
		{
			$out[JText::_('P2CO_LANG')] = $form->getInput('default_lang', 'pay', $default->get('default_lang', $params->get('pay.default_lang')));
		}
		
		if (in_array($params->get('pay.allow_block'), $user->getAuthorisedViewLevels()))
		{
			$out[JText::_('P2CO_BLOCK')] = $form->getInput('default_block', 'pay', $default->get('default_block', $params->get('pay.default_block')));
		}
		
		return $this->render_form($out);
	}

	public function receive($field, $post, $record)
	{
		$this->log('start transaction receive', $post);

		$options = $field->params;
		$accept = new JRegistry($post);
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		$pay = new JRegistry($field->pay);
		
		$out['record_id'] = $record->id;
		$out['saler_id'] = $record->user_id;
		$out['section_id'] = $record->section_id;
		$out['field_id'] = $field->id;
		$out['gateway'] = '2CheckOut';
		$out['gateway_id'] = $accept->get('order_number', $accept->get('invoice_id'));
		$out['user_id'] = $accept->get('user_id', $user->id);
		$out['amount'] = $accept->get('total', $accept->get('invoice_list_amount'));//$pay->get('amount', $options->get('pay.default_amount', 0)));
		$out['currency'] = $pay->get('default_currency', $options->get('pay.default_currency', 'USD'));
		$out['status'] = 5;
		$out['name'] = $accept->get('item_name_1');
		
		$vendor = $pay->get('vendor', $options->get('pay.default_vendor'));
		$secret = $pay->get('sword', $options->get('pay.default_sword'));
		
		$key = $accept->get('key');
		$hash = $secret . $vendor . $accept->get('order_number') . $accept->get('total');
		$check = strtoupper(md5($hash));
		if ($check != $key)
		{
			$out['status'] = 2;
			Factory::getApplication()->enqueueMessage(JText::_('P2CO_FAIL'),'warning');
		}
		else
		$app->enqueueMessage(JText::_('P2CO_ORDERCREATED'),'warning');
		if (($accept->get('fraud_status') == 'wait') && $pay->get('default_block', $options->get('pay.default_block', 0)))
		{
			$out['status'] = 3;
			$app->enqueueMessage(JText::_('P2CO_RENDING'),'warning');
		}
		
		if ($accept->get('message_type'))
		{
			// 1 - cancel
			// 2 - fail
			// 3 - pending
			// 4 - refund
			// 5 - completed
			$db = JFactory::getDBO();
			switch ($accept->get('message_type'))
			{
				case 'ORDER_CREATED' :
					$out['status'] = 5;
					$out['comment'] = JText::_('P2CO_ORDERCREATED');
					$this->log('transaction prepared', $out);
					$this->log("transaction finish!\n\n\n\n", $out);
					break;
				
				case 'FRAUD_STATUS_CHANGED' :
					if ($accept->get('fraud_status') == 'pass')
					{
						$out['status'] = 5;
						$out['comment'] = JText::_('P2CO_ORDERPASSED');
						$this->log("case resolved fraud passed", $out);
					}
					else
					{
						$out['status'] = 2;
						$out['comment'] = JText::_('P2CO_FRAUD');
						$this->log("case resolved fraud", $out);					
					}
					break;
				
				case 'INVOICE_STATUS_CHANGED' :
					if ($accept->get('invoice_status') == 'deposited')
					{
						$out['status'] = 4;
						$out['comment'] = JText::_('P2CO_DEPOSITED');
						$this->log("case resolved order deposited", $out);
					}
					if ($accept->get('invoice_status') == 'pending')
					{
						$out['status'] = 3;
						$out['comment'] = JText::_('P2CO_RENDING');
						$this->log("case resolved order pending", $out);
					}
					break;
				
				case 'REFUND_ISSUED' :
					if ($accept->get('item_type_1') == 'refund')
					{
						$out['status'] = 4;
						$out['comment'] = JText::_('P2CO_REFUNDED');
						$this->log("case resolved refund order", $out);
					}
					break;
			}
		}
		$this->new_order($out, $record, $field);
		
		return $out;
	}

}
?>