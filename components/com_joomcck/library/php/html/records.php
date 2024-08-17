<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');

/**
 * @package		Joomla.Administrator
 * @subpackage	com_content
 */
abstract class JHtmlRecords
{
	/**
	 * @param	int $value	The state value
	 * @param	int $i
	 */
	static function featured($value = 0, $i, $canChange = true)
	{
		// Array of image, task, title, action
		$states	= array(
			0	=> array('<i class="icon-star-empty"></i>',	'records.featured',	'COM_JOOMCCK_TOGGLE_TO_FEATURE'),
			1	=> array('<i class="icon-star"></i>',		'records.unfeatured','COM_JOOMCCK_TOGGLE_TO_UNFEATURE'),
		);
		$state	= \Joomla\Utilities\ArrayHelper::getValue($states, (int) $value, $states[1]);

		if ($canChange) {
			$html	= '<a href="#" class="btn btn-sm btn-light border rounded" onclick="return Joomla.listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" rel="tooltip" data-bs-original-title="'.\Joomla\CMS\Language\Text::_($state[2]).'">'
					. $state[0].'</a>';
		}

		return $html;
	}
}
