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

class JoomcckCommentsJlex extends JoomcckComments
{

	public function getNum($type, $item)
	{
		$out = array();
		$factoryx = JPATH_ROOT . '/components/com_jlexreview/load.php';
		if (JFile::exists($factoryx)) {

			$db = JFactory::getDbo();
			$db->setQuery("SELECT COUNT(*) FROM #__jlexreview WHERE object = 'com_joomcck' AND object_id = ".$item->id);
			$out[] = (int)$db->loadResult();

			require_once $factoryx;
			$out[] = JLexReviewLoad::quick_init('com_joomcck', $item->id, $item->section_id, true);
		}

		return implode(" ", $out);
	}

	public function getComments($type, $item)
	{
		$factoryx = JPATH_ROOT . '/components/com_jlexreview/load.php';
		if (JFile::exists($factoryx)) {
			require_once $factoryx;
			return JLexReviewLoad::init($item->title, 'com_joomcck', $item->id, $item->section_id);
		}
	}
}