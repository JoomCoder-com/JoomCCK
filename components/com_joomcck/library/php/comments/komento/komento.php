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
jimport('joomla.filesystem.file');

class JoomcckCommentsKomento extends JoomcckComments
{

	public function getNum($type, $item)
	{
		if(self::enable())
		{
			$model = Komento::getModel('comments');
			$count = $model->getCount('com_joomcck', $item->id);
			return $count;
		}

		return $item->comments;
	}

	public function getComments($type, $item)
	{
		if(self::enable())
		{
			return Komento::commentify('com_joomcck', $item);
		}
	}

	public function getLastComment($type, $item)
	{
		return;
	}

	public function getIndex($type, $item)
	{

		if(self::enable())
		{
			$db = JFactory::getDbo();

			$db->setQuery("SELECT comment FROM #__komento_comments WHERE published = 1 AND cid = {$item->id} AND component = 'com_joomcck'");
			$list = $db->loadColumn();

			return implode(', ', $list);
		}
	}

	private static function enable()
	{
		$path = JPATH_ROOT . '/components/com_komento/bootstrap.php';

		if(JFile::exists($path))
		{
			require_once($path);

			return TRUE;
		}

		return FALSE;
	}
}

