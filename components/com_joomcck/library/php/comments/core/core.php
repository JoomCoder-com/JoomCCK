<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

include_once JPATH_ROOT. DIRECTORY_SEPARATOR .'components/com_joomcck/library/php/joomcckcomments.php';

class JoomcckCommentsCore extends JoomcckComments {

	public function getNum($type, $item)
	{
		return $item->comments;
	}

	public function getComments($type, $item) {}

	public function getIndex($type, $item) {

		$db = \Joomla\CMS\Factory::getDbo();

		$db->setQuery("SELECT comment FROM #__js_res_comments WHERE published = 1 AND record_id = {$item->id}");
		$list = $db->loadColumn();

		return implode(', ', $list);
	}
}