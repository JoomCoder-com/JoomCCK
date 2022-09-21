<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

include_once dirname(__DIR__).'/joomcckcomments.php';

class CommentHelper {
	public static function listComments($type, $item)
	{
		return self::_getclass($type->params->get('comments.comments'))->getComments($type, $item);
	}
	public static function numComments($type, $item)
	{
		return self::_getclass($type->params->get('comments.comments'))->getNum($type, $item);
	}
	public static function fullText($type, $item)
	{
		return self::_getclass($type->params->get('comments.comments'))->getIndex($type, $item);
	}

	private static function _getclass($name)
	{
		$file = JPATH_ROOT. '/components/com_joomcck/library/php/comments/'.$name. DIRECTORY_SEPARATOR .$name.'.php';
		if(JFile::exists($file))
		{
			include_once $file;
			$name = 'JoomcckComments'.ucfirst($name);
			if(class_exists($name))
			{
				return new $name();
			}
		}

		return new JoomcckComments();
	}
}