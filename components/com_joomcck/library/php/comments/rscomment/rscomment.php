<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

include_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components/com_joomcck/library/php/joomcckcomments.php';

class JoomcckCommentsRscomments extends JoomcckComments {
	
	public function getNum($type, $item) {
		return 0;
	}
	
	public function getComments($type, $item) {
		$holder = '{rscomments option="com_joomcck" id="'.$item->id.'"}';
		return $holder;
		
		$text = JHTML::_('content.prepare', $holder);
		return $text;
	}
}

