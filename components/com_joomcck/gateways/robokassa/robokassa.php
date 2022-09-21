<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

require_once JPATH_ROOT . '/components/com_joomcck/library/php/commerce/mintpay.php';

class MintPayRobokassa extends MintPayAbstract
{

	public function button($field)
	{

		$user    = JFactory::getUser();
		$options = $field->params;

		if($user->get('id') && $user->get('id') == $options->get('params.skip_for'))
		{
			return;
		}

		settype($field->pay, 'array');
		$pay = new JRegistry($field->pay);

		$rk_amount = $pay->get('amount', 0);

		$rk_shopid = $options->get('pay.shopid', 0);
		$rk_lang   = $options->get('pay.lang', $options->get('pay.lang', 'en'));
		$rk_cur    = $options->get('pay.default_currency', 'USD');
		$rk_invid  = time();
		$merpas1   = $options->get('pay.merpas1', 0);

		if($rk_amount == 0)
		{
			return sprintf('<p>%s</p>', JText::_('RK_FILES_ARE_FREE'));
		}

		if(!$rk_shopid)
		{
			return sprintf('<p>%s</p>', JText::_('RK_NOSHOPID'));
		}

		if(!$user->get('id'))
		{
			return sprintf('<img  style="display:block;cursor:pointer" src="http://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif" onclick="alert(\'%s\')" alt="%s" />', JText::_('Please, login to buy now'), JText::_('Please, login to buy now'));
		}

		$price = $this->_price($rk_amount, $rk_cur);
		$topay = $price;

		$out[] = '<div style="clear:both"></div>';
		$out[] = "<p>" . JText::sprintf($options->get('params.btn_label', 'For instant download purchase only %s'), $topay) . "</p>";

		$out[] = '<form action="https://auth.robokassa.ru/Merchant/Index.aspx" method="post">';
		//$out[] = '<form action="http://test.robokassa.ru/Index.aspx" method="post">';
		$out[] = $this->_hidden('MerchantLogin', $rk_shopid);
		$out[] = $this->_hidden('OutSum', $rk_amount);
		$out[] = $this->_hidden('InvId', $rk_invid);
		$out[] = $this->_hidden('InvDesc', $options->get('item_name'));
		$out[] = $this->_hidden('Culture', $rk_lang);
		$out[] = $this->_hidden('shprecord_id', $field->record->id);
		$out[] = $this->_hidden('shpfield_id', $field->id);
		$out[] = $this->_hidden('shpuser_id', $user->get('id'));

		$out[] = $this->_hidden('SignatureValue', md5($rk_shopid . ':' . $rk_amount . ':' . $rk_invid . ':' . $merpas1 . ':shpfield_id=' . $field->id . ':shprecord_id=' . $field->record->id. ':shpuser_id=' . $user->get('id')));

		$out[] = sprintf('<input style="display:block" type="image" border="0" src="http://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif" alt="%s" title="%s" />', JText::_('Buy Now'), JText::_('Buy Now'));
		$out[] = '</form>';

		return implode("\n", $out);

	}

	public function form($field)
	{
		$value = $field->pay;
		settype($value, 'array');
		$default = new JRegistry($value);

		$out[JText::_('ROBOKASSA_PRICE')] = '<input type="text" size="4" name="jform[fields][' . $field->id . '][pay][amount]" value="' . $default->get('amount') . '"/> ';

		return $this->render_form($out);
	}

	public function receive($field, $post, $record)
	{
		$this->log('start transaction receive', $post);

		$options = $field->params;
		$accept  = new JRegistry($post);
		$user    = JFactory::getUser();
		$app     = JFactory::getApplication();

		$out['record_id']  = $record->id;
		$out['saler_id']   = $record->user_id;
		$out['section_id'] = $record->section_id;
		$out['field_id']   = $field->id;
		$out['gateway']    = 'RoboKassa';
		$out['gateway_id'] = $accept->get('InvId');
		$out['user_id']    = $accept->get('shpuser_id', $user->id);
		$out['amount']     = $accept->get('OutSum', 0);
		$out['currency']   = $options->get('pay.default_currency', 'USD');
		$out['status']     = 5;
		$out['name']       = $record->title;

		$key[] = $accept->get('OutSum');
		$key[] = $accept->get('InvId');
		$key[] = $options->get('pay.merpas2');
		$key[] = 'shpfield_id=' . $field->id;
		$key[] = 'shprecord_id=' . $record->id;
		$key[] = 'shpuser_id=' . $accept->get('shpuser_id', $user->id);

		$hash = strtoupper(md5(implode(':', $key)));

		if($hash != strtoupper($accept->get('SignatureValue')))
		{
			$this->log('Robokassa: Verification failed', $_POST);
			$out['status'] = 2;
			JError::raiseWarning(403, JText::_('RK_FAIL'));

			return FALSE;
		}
		else
		{
			$app->enqueueMessage(JText::_('RK_ORDERCREATED'));
		}

		$this->new_order($out, $record, $field);

		echo 'OK'.$accept->get('InvId');

		JFactory::getApplication()->close();
	}

	/**
	 * @param JRegestry $post
	 *
	 * @return int
	 */

	public function getFieldId($post)
	{
		return $post->get('shpfield_id', 0);
	}

	public function getRecordId($post)
	{
		return $post->get('shprecord_id', 0);
	}

}

?>