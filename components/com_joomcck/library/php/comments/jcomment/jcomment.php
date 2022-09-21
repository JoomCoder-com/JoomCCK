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

class JoomcckCommentsJcomment extends JoomcckComments {
	
	public function getNum($type, $item) {
		if (self::enable()) 
		{
			$count = JComments::getCommentsCount ( $item->id, 'com_joomcck' );
			return $count ? ('Comments(' . $count . ')') : 'Add comment';
		}
	}
	
	public function getComments($type, $item) {
		if (self::enable()) 
		{
			return JComments::showComments ( $item->id, 'com_joomcck', $item->title );
		}
	}
	public function getLastComment($type, $item) {
		if (self::enable()) 
		{
			$comment = JComments::getLastComment ( $item->id, 'com_joomcck' );
			return 'User "' . $comment->name . '" wrote "' . $comment->comment . '" (' . $comment->date . ')';
		}
	}
	
	private function enable()
	{
		$comments = JPATH_SITE . DIRECTORY_SEPARATOR . 'components/com_jcomments/jcomments.php';
		if (file_exists ( $comments )) {
			require_once ($comments);
			return true;
		}
		return false;
	}

	public function getIndex($type, $item) {

		if (self::enable())
		{
			$db = JFactory::getDbo();

			$db->setQuery("SELECT comment FROM #__js_res_comments WHERE published = 1 AND record_id = {$item->id}");
			$list = $db->loadColumn();

			return implode(', ', $list);
		}
	}
}

