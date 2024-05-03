<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();

require_once JPATH_ROOT . '/components/com_joomcck/library/php/commerce/mintpay.php';

class MintPayPaypal extends MintPayAbstract
{

	public $provider;

	const ADP_PARALLEL = 1;
	const ADP_CHAINED_YOU_PRIMARY = 2;
	const ADP_CHAINED_SELLER_PRIMARY = 3;

	public function button($field)
	{
		$pay     = new \Joomla\Registry\Registry((array) @$field->pay);
		$options = $field->params;
		$user    = \Joomla\CMS\Factory::getApplication()->getIdentity();

		if (!$pay->get('business', $options->get('pay.business')))
		{
			return 'No email';
		}

		$url = \Joomla\CMS\Uri\Uri::getInstance();
		$url->setVar('rid', $field->record->id);
		$url->setVar('fid', $field->id);

		$pp_amount   = $pay->get('amount', $options->get('pay.default_amount', 0));
		$pp_email    = $pay->get('business', $options->get('pay.business'));
		$pp_discount = $pay->get('default_discount', $options->get('pay.default_discount', 0));
		$pp_tax      = $pay->get('default_tax', $options->get('pay.default_tax', 0));
		$pp_cur      = $pay->get('default_currency', $options->get('pay.default_currency', 'USD'));
		$pp_shipping = $pay->get('shipping', $options->get('pay.shipping', 1));

		$tax      = 0;
		$discount = 0;
		$amount   = $this->_price($pp_amount, $pp_cur);;
		$topay = $amount;

		if ($pp_tax)
		{
			$tax = ($options->get('pay.tax_type', 'tax_rate') == 'tax_rate' ? $pp_tax . '%' : $this->_price($pp_tax, $pp_cur));
		}

		if ($pp_discount)
		{
			if ($options->get('pay.discount_type', 'discount_rate') == 'discount_rate')
			{
				$topay    = $this->_price(($pp_amount - ($pp_amount * ($pp_discount / 100))), $pp_cur);
				$discount = "$pp_discount%";
			}
			else
			{
				$topay    = $this->_price(($pp_amount - $pp_discount), $pp_cur);
				$discount = $this->_price($pp_discount);
			}
		}


		$action = 'https://www.' . ($options->get('pay.sandbox') ? 'sandbox.' : null) . 'paypal.com/us/cgi-bin/webscr';

		$hiddenfields[] = $this->_hidden('cmd', '_xclick');
		$hiddenfields[] = $this->_hidden('charset', 'utf-8');
		$hiddenfields[] = $this->_hidden('business', $pp_email);
		$hiddenfields[] = $this->_hidden('currency_code', $pp_cur);
		$hiddenfields[] = $this->_hidden($options->get('pay.discount_type', 'discount_rate'), $pp_discount);
		$hiddenfields[] = $this->_hidden($options->get('pay.tax_type', 'tax_rate'), $pp_tax);
		$hiddenfields[] = $this->_hidden('item_name', \Joomla\CMS\Language\Text::sprintf($options->get('params.item_name', 'SSI_ITEMNAME'), $field->record->title));
		$hiddenfields[] = $this->_hidden('rm', 2);
		$hiddenfields[] = $this->_hidden('custom', $user->get('id'));
		$hiddenfields[] = $this->_hidden('first_name', $user->get('name'));
		$hiddenfields[] = $this->_hidden('email', $user->get('email'));
		$hiddenfields[] = $this->_hidden('invoice', "{$field->record->id}::{$field->id}::" . time());
		$hiddenfields[] = $this->_hidden('no_shipping', $pp_shipping);

		$part           = explode('-', \Joomla\CMS\Factory::getLanguage()->getTag());
		$hiddenfields[] = $this->_hidden('lc', strtoupper($part[0]));

		$url->setVar('result', 'cancel');
		$hiddenfields[] = $this->_hidden('cancel_return', $url->toString());

		$ipn = \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&task=field.call&func=onReceivePayment&field_id=' . $field->id . '&record_id=' . $field->record->id, false, -1);
		if ($options->get('pay.ipn'))
		{
			$url->setVar('result', 'return');
			$hiddenfields[] = $this->_hidden('return', $url->toString());
			$hiddenfields[] = $this->_hidden('notify_url', $ipn);
		}
		else
		{
			$hiddenfields[] = $this->_hidden('return', $ipn);
		}

		if ($field->params->get('pay.allow_amount'))
		{
			$nonehidden[\Joomla\CMS\Language\Text::_('CAMOUNT')] = '<input type="text" onkeyup="Joomcck.formatFloat(this, 2, 11)" class="col-md-2" name="amount" value="' . $pp_amount . '" />';
			$amount                          = 0;
			$discount                        = 0;
			$topay                           = 0;
		}
		else
		{
			$hiddenfields[] = $this->_hidden('amount', $pp_amount);
		}

		if ($field->params->get('pay.allow_count'))
		{
			$nonehidden[\Joomla\CMS\Language\Text::_('PP_QNT')] = '<input type="text" class="col-md-2" onkeyup="Joomcck.formatInt(this)" name="quantity" value="' . $options->get('pay.default_count', 1) . '" />';
		}
		else
		{
			$hiddenfields[] = $this->_hidden('quantity', $options->get('pay.default_count', 1));
		}

		$opt = $options->get('options', array());
		$i   = 0;
		foreach ($opt as $k => $file)
		{
			// paypal supports not more than 9 options.
			if ($k > 9)
			{
				break;
			}
			$hiddenfields[] = $this->_hidden('on' . $i, $k);
			$hiddenfields[] = $this->_hidden('os' . $i++, $file);
		}

		if ($field->params->get('pay.adaptive') && $pp_amount > $field->params->get('pay.minimum_amount'))
		{
			$action = \Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&task=field.call&func=onAdaptivePayment&field_id=' . $field->id . '&record_id=' . $field->record->id);
		}

		ob_start();
		include dirname(__FILE__) . '/tmpl/' . $field->params->get('pay.button_tmpl', 'default.php');
		$html = ob_get_contents();
		ob_end_clean();

		$url->delVar('return');
		$url->delVar('result');
		$url->delVar('rid');
		$url->delVar('fid');

		return $html;

	}

	public function form($field)
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();

		$value = $field->pay;
		settype($value, 'array');
		$default = new \Joomla\Registry\Registry($value);

		$params = $field->params;

		$form = $this->load_form('paypal', $field->id);

		$out[\Joomla\CMS\Language\Text::_('CPRICE')] = '<input class="form-control" onkeyup="Joomcck.formatFloat(this, 2, 10)" type="text" size="4" name="jform[fields][' . $field->id . '][pay][amount]" value="' . $default->get('amount') . '"/> ';
		if (in_array($params->get('pay.allow_currency'), $user->getAuthorisedViewLevels()))
		{
			$out[\Joomla\CMS\Language\Text::_('CPRICE')] .= $form->getInput('default_currency', 'pay', $default->get('default_currency', $params->get('pay.default_currency')));
		}
		else
		{
			$out[\Joomla\CMS\Language\Text::_('CPRICE')] .= $params->get('pay.default_currency');
		}

		if (in_array($params->get('pay.allow_email'), $user->getAuthorisedViewLevels()))
		{
			$out[\Joomla\CMS\Language\Text::_('PP_EMAIL')] = $form->getInput('business', 'pay', $default->get('business', $params->get('pay.business', $user->get('email'))));
		}

		if (in_array($params->get('pay.allow_discount'), $user->getAuthorisedViewLevels()))
		{
			$out[\Joomla\CMS\Language\Text::_('CDISCOUNT')] = $form->getInput('default_discount', 'pay', $default->get('default_discount', $params->get('pay.default_discount')));
			$out[\Joomla\CMS\Language\Text::_('CDISCOUNT')] .= ($params->get('pay.discount_type') == 'discount_rate' ? ' 0.001 - 100 %' : ' Fixed flat amount');
		}

		if (in_array($params->get('pay.allow_tax_rate'), $user->getAuthorisedViewLevels()))
		{
			$out[\Joomla\CMS\Language\Text::_('CTAX')] = $form->getInput('default_tax', 'pay', $default->get('default_tax', $params->get('pay.default_tax')));
			$out[\Joomla\CMS\Language\Text::_('CTAX')] .= ($params->get('pay.tax_type') == 'tax_rate' ? ' 0.001 - 100 %' : ' Fixed flat amount');
		}

		return $this->render_form($out);
	}

	public function receive($field, $post, $record)
	{
		$app = \Joomla\CMS\Factory::getApplication();
		$this->log('start transaction receive', $post);

		$accept = new \Joomla\Registry\Registry($this->_decodePayPalIPN());
		if ($field->params->get('pay.ipn'))
		{
			$this->log('IPN recieved. Start check.');
			if (!$this->_IPNcheck($field, $post))
			{

				Factory::getApplication()->enqueueMessage('cannot verify order', 'warning');
				$this->log('cannot verify order');

				return false;
			}
		}
		else
		{
			$this->log('PDT recieved. Start check.');
			if (!$field->params->get('pay.authcode'))
			{
				Factory::getApplication()->enqueueMessage('PDT identity toked is not set', 'warning');
				$this->log('PDT identity toked is not set');

				return false;
			}

			if (false == ($data = $this->_PDTcheck($field, \Joomla\CMS\Factory::getApplication()->input->get('tx', $accept->get('tx')), $field->params->get('pay.authcode'))))
			{
				Factory::getApplication()->enqueueMessage('cannot verify order', 'warning');
				$this->log('cannot verify order');

				return false;
			}
			$accept = new \Joomla\Registry\Registry($data);
		}

		//transaction[0].is_primary_receiver=true
		//&transaction[0].id_for_sender_txn=3GR563365W900734H
		//&transaction[0].receiver=receiver.second@joomcck.com
		//&transaction[0].amount=USD 10.00
		//&transaction[0].status=Completed
		//&transaction[0].id=9A2427424T754790G
		//&transaction[0].status_for_sender_txn=Completed
		//&transaction[0].paymentType=SERVICE
		//&transaction[0].pending_reason=NONE
		//&transaction[1].paymentType=SERVICE
		//&transaction[1].id_for_sender_txn=1ER219060D104564P
		//&transaction[1].is_primary_receiver=false
		//&transaction[1].status_for_sender_txn=Completed
		//&transaction[1].receiver=serg4172-facilitator@mail.ru
		//&transaction[1].amount=USD 5.00
		//&transaction[1].id=07M27254UP135001N
		//&transaction[1].status=Completed
		//&log_default_shipping_address_in_transaction=false
		//&action_type=PAY
		//&charset=windows-1252
		//&transaction_type=Adaptive Payment PAY
		//&notify_version=UNVERSIONED
		//&verify_sign=AiPC9BjkCyDFQXbSkoZcgqH3hpacASwWtDFaFESzpmzmguwitUSnA4gS
		//&sender_email=sender@joomcck.com
		//&fees_payer=SECONDARYONLY
		//&memo=Download files from: Adaptive payment test
		//&reverse_all_parallel_payments_on_error=false
		//&tracking_id=175-1368546958&transaction[1].pending_reason=NONE
		//&pay_key=AP-6U066126GG4249428
		//&status=COMPLETED
		//&test_ipn=1
		//&payment_request_date=Tue May 14 08:56:01 PDT 2013


		$reasons = array(
			'adjustment_reversal'      => 'Reversal of an adjustment',
			'buyer-complaint'          => 'A reversal has occurred on this transaction due to a complaint about the transaction from your customer.',
			'chargeback'               => 'A reversal has occurred on this transaction due to a chargeback by your customer.',
			'chargeback_reimbursement' => 'Reimbursement for a chargeback',
			'chargeback_settlement'    => 'Settlement of a chargeback',
			'guarantee'                => 'A reversal has occurred on this transaction due to your customer triggering a money-back guarantee.',
			'other'                    => 'Non-specified reason.',
			'refund'                   => 'A reversal has occurred on this transaction because you have given the customer a refund.'
		);


		// 1 - cancel
		// 2 - fail
		// 3 - pending|wait
		// 4 - refund
		// 5 - completed

		if ($accept->get('action_type') == 'PAY')
		{
			list($time, $user_id) = explode('-', $accept->get('tracking_id'));

			$out['gateway']    = 'PayPal';
			$out['gateway_id'] = $accept->get('transaction.0.id');
			$out['name']       = $accept->get('memo');
			$out['user_id']    = $user_id;

			list($currency, $amount) = explode(' ', $accept->get('transaction.0.amount'));
			$out['amount']   = $amount;
			$out['currency'] = $currency;

			switch ($accept->get('transaction_type'))
			{
				case 'Adaptive Payment PAY':
					switch ($accept->get('status'))
					{
						case 'COMPLETED':
						case 'INCOMPLETE':
							$out['status'] = 5;
							$app->enqueueMessage(\Joomla\CMS\Language\Text::_('PP_PAYOK'));
							break;

						case 'CREATED':
						case 'PENDING':
						case 'PROCESSING':
							$out['status']  = 3;
							$out['comment'] = \Joomla\CMS\Language\Text::_('PP_PAYSUCCBUT');
							$app->enqueueMessage(\Joomla\CMS\Language\Text::_('PP_PAYSUCCBUT'));
							break;

						case 'ERROR':
						case 'REVERSALERROR':

							Factory::getApplication()->enqueueMessage('Transaction Error', 'warning');

							return false;
							break;

					}
					break;
				case 'Adjustment':
					$this->log('Adaptive ajastment');
					$out['status']  = 1;
					$out['comment'] = $accept->get('reason_code');
					break;
			}

			if (empty($out['status']))
			{
				return false;
			}

			$this->log('adaptiive transaction prepared', $out);
			$this->new_order($out, $record, $field);
			$this->log("transaction finish!\n\n\n\n", $out);

			return $out;
		}

		$out['gateway']    = 'PayPal';
		$out['gateway_id'] = $accept->get('txn_id', $accept->get('tx'));
		$out['user_id']    = $accept->get('custom', $accept->get('cm'));
		$out['amount']     = $accept->get('mc_gross', $accept->get('amount'));
		$out['currency']   = $accept->get('mc_currency');
		$out['name']       = $accept->get('item_name');

		switch ($accept->get('txn_type'))
		{
			case "web_accept" :


				switch ($accept->get('payment_status'))
				{
					case 'Denied' :
					case 'Expired' :
					case 'Failed' :
					case 'Voided' :
						$out['status'] = 2;
						Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('PP_PAYMENTFAIL'), 'warning');
						break;

					case 'Pending' :
						$out['status']  = 3;
						$out['comment'] = $accept->get('pending_reason');
						$app->enqueueMessage(\Joomla\CMS\Language\Text::_('PP_PAYSUCCBUT'));
						break;

					case 'Canceled_Reversal' :
					case 'Reversed' :
					case 'Refunded' :
						$out['status']  = 4;
						$out['comment'] = $reasons[$accept->get('reason_code')];
						Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('PP_REFUND'), 'warning');

						break;

					case 'Created' :
					case 'Processed' :
					case 'Completed' :
						$out['status'] = 5;
						$app->enqueueMessage(\Joomla\CMS\Language\Text::_('PP_PAYOK'));
						break;
				}
				$this->log('transaction prepared', $out);
				$this->new_order($out, $record, $field);
				$this->log("transaction finish!\n\n\n\n", $out);

				break;

			case "new_case" :
				$this->log('case raised cancel order');
				$out['status']  = 1;
				$out['comment'] = \Joomla\CMS\Language\Text::_('PP_DISPUTSTART');
				$this->new_order($out, $record, $field);
				break;

			case "adjustment" :
				$this->log('case resolved confirm order');
				$out['status']  = 5;
				$out['comment'] = \Joomla\CMS\Language\Text::_('PP_DISPUTRSOLV');
				$this->new_order($out, $record, $field);
				break;

		}

		return $out;
	}

	private function _PDTcheck($field, $tx, $auth)
	{
		$request = curl_init();
		$options = array(
			CURLOPT_URL            => 'https://www.' . ($field->params->get('pay.sandbox') ? 'sandbox.' : null) . 'paypal.com/cgi-bin/webscr',
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => http_build_query(array(
				'cmd' => '_notify-synch',
				'tx'  => $tx, 'at' => $auth
			)),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER         => false,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_CAINFO         => 'cacert.pem'
		);
		curl_setopt_array($request, $options);

		$response = curl_exec($request);
		$status   = curl_getinfo($request, CURLINFO_HTTP_CODE);
		curl_close($request);

		$this->log('send CURL confirm');

		if ($status == 200 and strpos($response, 'SUCCESS') === 0)
		{
			$response = substr($response, 7);
			$response = urldecode($response);

			preg_match_all('/^([^=\s]++)=(.*+)/m', $response, $m, PREG_PATTERN_ORDER);
			$response = array_combine($m[1], $m[2]);

			// Fix character encoding if different from UTF-8 (in my case)
			if (isset($response['charset']) and strtoupper($response['charset']) !== 'UTF-8')
			{
				foreach ($response as $key => &$value)
				{
					$value = mb_convert_encoding($value, 'UTF-8', $response['charset']);
				}
				$response['charset_original'] = $response['charset'];
				$response['charset']          = 'UTF-8';
			}

			$this->log('transaction success and return', $response);
			ksort($response);

			return $response;
		}

		$this->log('transaction fail', $response);

		return false;
	}

	private function _IPNcheck($field, $post)
	{
		$req = 'cmd=_notify-validate&' . file_get_contents("php://input");

		$request = curl_init();
		$options = array(
			CURLOPT_URL            => 'https://www.' . ($field->params->get('pay.sandbox') ? 'sandbox.' : null) . 'paypal.com/cgi-bin/webscr',
			CURLOPT_POST           => 1,
			CURLOPT_POSTFIELDS     => $req,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_HEADER         => 0,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_CAINFO         => 'cacert.pem',
			CURLOPT_HTTPHEADER     => array(
				"Content-Type: application/x-www-form-urlencoded",
				"Content-Length: " . strlen($req)
			),
			CURLOPT_TIMEOUT        => 30
		);
		curl_setopt_array($request, $options);

		$this->log('send CURL confirm');

		$response = curl_exec($request);
		$status   = curl_getinfo($request, CURLINFO_HTTP_CODE);
		curl_close($request);

		if (strpos($response, "VERIFIED") !== false)
		{
			$this->log('transaction verified ' . $response . ' - ' . @$post['txn_id']);

			return true;
		}
		else
		{
			$this->log('transaction verification invalid ' . $response);

			return false;
		}
	}

	private function _decodePayPalIPN()
	{
		$raw = file_get_contents("php://input");
		// sometimes we already have & in returnUrl or cancelUrl
		$raw   = str_replace("&amp;", '^^^', $raw);
		$post  = array();
		$pairs = explode('&', $raw);

		foreach ($pairs as $pair)
		{

			list($key, $value) = explode('=', $pair, 2);

			$key   = urldecode($key);
			$value = urldecode($value);

			// This is look for a key as simple as 'return_url' or as complex as 'somekey[x].property'
			preg_match('/(\w+)(?:\[(\d+)\])?(?:\.(\w+))?/', $key, $key_parts);
			switch (count($key_parts))
			{
				case 4:
					// Original key format: somekey[x].property
					// Converting to $post[somekey][x][property]
					$post[$key_parts[1]][$key_parts[2]][$key_parts[3]] = $value;
					break;
				case 3:
					// Original key format: somekey[x] Converting to $post[somkey][x]
					$post[$key_parts[1]][$key_parts[2]] = $value;
					break;
				default:
					// No special format
					$post[$key] = str_replace('^^^', '&', $value);
					break;
			}
		}

		return $post;
	}
}

?>