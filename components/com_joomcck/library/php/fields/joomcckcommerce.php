<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
interface CFormFieldCommerce
{
	/**
	 * This method is used when payment gatewau return order information. 
	 * @param array $post
	 * @param object $record
	 * @param object $controller
	 */
	public function onReceivePayment($post, $record, &$controller);
	
	/**
	 * This method is called when user or saler see list of orders or sales. 
	 * Every order show field data. It means that it should show actual, purchased content.
	 * But do not forget to check order status = 5 (confirmed)
	 * @param object $order
	 * @param object $record
	 */
	public function onOrderList($order, $record);
}
