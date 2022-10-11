<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckupload.php';
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckcommerce.php';
require_once JPATH_ROOT . '/components/com_joomcck/library/php/commerce/mintpay.php';

class JFormFieldCPaytoaccess extends CFormField implements CFormFieldCommerce
{

	function __construct($field, $default)
	{
		parent::__construct($field, $default);

		$this->gateway = MintPay::getInstance($this->id, $this->params->get('params.default_gateway'), $this->params->get('params.log'));

		parent::__construct($field, $default);

		$this->subscriptions = array();
		if (isset($this->value['subscriptions']) && !empty($this->value['subscriptions'])) {
			$this->subscriptions = $this->value['subscriptions'];
			unset($this->value['subscriptions']);
		}
	}

	public function getInput()
	{
		$this->pay = @$this->value['pay'];

		if ($this->gateway) {
			$this->gateway_form = $this->gateway->form($this);
		} else {
			$this->gateway_form = JText::sprintf('CGATEWAYNOTFOUND', '');
		}

		$user = JFactory::getUser();
		if ($this->params->get('params.subscription', 0) && in_array($this->params->get('params.can_select_subscr', 0), $user->getAuthorisedViewLevels())) {
			$this->plans = JHtml::_('emerald.plans', "jform[fields][{$this->id}][subscriptions][]", $this->params->get('params.subscription', array()), $this->subscriptions, 'CRESTRICTIONPLANSACCESDESCR');
		}

		return $this->_display_input();
	}

	public function onReceivePayment($post, $record, &$controller)
	{
		if (!$this->gateway) return;
		$payment = $this->gateway->receive($this, $post, $record);
		$controller->setRedirect(JRoute::_(Url::record($record), FALSE));
	}

	private function _render($client, $record, $type, $section)
	{
		$this->pay = @$this->value['pay'];
		$this->value = 1;
		$this->subscr = $this->_ajast_subscr($record);

		$this->gateway->prepare_output($this, $client, $record, $type, $section);

		return $this->_display_output(($client == 1 ? 'list' : 'full'), $record, $type, $section);
	}

	public function onOrderList($order, $record)
	{
		return '<div class="alert alert-success">' . JText::_('CFPTAARTICLEUNLOCKED') . '</div>';
	}

	public function onStoreValues($validData, $record)
	{
		return array();
	}

	public function onRenderFull($record, $type, $section)
	{
		$this->_check($record);

		$app = JFactory::getApplication();

		if ($errors = $this->getErrors()) {
			foreach ($errors AS $err) {
				$app->enqueueMessage($err, 'error');
			}
			$url = JRoute::_(Url::records($section, $this->request->getInt('cat_id'), $this->request->getInt('user_id')), FALSE);
			$app->redirect($url);
			return;
		}

		return $this->_render(2, $record, $type, $section);
	}

	public function onRenderList($record, $type, $section)
	{
		return $this->_render(1, $record, $type, $section);
	}

	protected function _check($record)
	{
		if (empty($this->value['pay']['amount'])) {
			return;
		}

		$user = JFactory::getUser();

		if (!$user->get('id')) {
			$this->setError(JText::_('P_LOGINTOACCESSMSG'));
		}

		if ($user->get('id') == $record->user_id) {
			return true;
		}

		if (in_array($this->params->get('params.skip_for'), $user->getAuthorisedViewLevels())) {
			return true;
		}

		$result = new stdClass();
		if ($this->gateway) $result = $this->gateway->get_order($user->get('id'), $record->id, $this->id);

		if (empty($result->id)) {
			$this->setError(JText::_('P_PURCHASETOACCESS'));
		} else {
			switch ($result->status) {
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

		if ($this->getErrors()) {
			$subscr = $this->_ajast_subscr($record);
			ArrayHelper::clean_r($subscr);
			if ($subscr) {
				$em_api = JPATH_ROOT . '/components/com_emerald/api.php';
				if (!JFile::exists($em_api)) {
					throw new Exception( 'File API Emerald not found',404);
				}
				include_once($em_api);
				if ($this->_is_subscribed($subscr, 0)) {
					$this->error = array();
					//EmeraldApi::addCount($this->params->get('params.subscription'), $this->params->get('params.subscription_count'));
				} else {
					$this->setError(JText::_('P_PURCHASETOACCESS2'));
				}
			}
		}

		if (!$this->getErrors() && isset($result->id)) {
			$this->gateway->count($result->id);
		}
	}

	public function _is_subscribed($plans, $redirect)
	{
		$api = JPATH_ROOT . '/components/com_emerald/api.php';
		require_once $api;

		return EmeraldApi::hasSubscription(
			$plans,
			$this->params->get('params.subscription_msg'),
			0,
			$this->params->get('params.subscription_count'),
			$redirect
		);
	}

	public function _ajast_subscr($record)
	{
		static $ajasted = array();

		if (!$record->user_id) return;

		$user = JFactory::getUser($record->user_id);

		$sub = $this->params->get('params.subscription');

		if (in_array($this->params->get('params.can_select_subscr', 0), $user->getAuthorisedViewLevels()) &&
			$this->params->get('params.subscription')
		) {
			$sub = $this->subscriptions;
		}

		ArrayHelper::clean_r($sub);
		return $sub;
	}
}
