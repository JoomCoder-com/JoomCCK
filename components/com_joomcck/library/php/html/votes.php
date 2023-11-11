<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class JHTMLVotes
{
	public static function types()
	{
		$out = array();
		$out[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 'record', 'Record');
		$out[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 'comment', 'Comment');
		$out[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 'file', 'File');
		return $out;
	}
	public static function values()
	{
		$out = array();
		$out[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 10, 10);
		$out[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 20, 20);
		$out[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 30, 30);
		$out[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 40, 40);
		$out[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 50, 50);
		$out[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 60, 60);
		$out[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 70, 70);
		$out[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 80, 80);
		$out[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 90, 90);
		$out[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', 100, 100);
		return $out;
	}
}
?>