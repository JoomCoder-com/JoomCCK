<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

/**
 * Form Field class to display "Extended version required" warning
 * when extended features are not installed.
 */
class JFormFieldExtendedonly extends \Joomla\CMS\Form\FormField
{
	/**
	 * The form field type.
	 *
	 * @var string
	 */
	protected $type = 'Extendedonly';

	/**
	 * Method to get the field input markup.
	 *
	 * @return string The field input markup.
	 */
	protected function getInput()
	{
		// Check if extended folder exists (indicates extended version is installed)
		$extendedPath = JPATH_SITE . '/components/com_joomcck/extended/autolink.php';

		if (file_exists($extendedPath)) {
			return ''; // Hide when extended is installed
		}

		// Show warning when extended is NOT installed
		$message = \Joomla\CMS\Language\Text::_('F_EXTENDED_VERSION_REQUIRED');
		return '<small class="text-danger">' . $message . '</small>';
	}

	/**
	 * Method to get the field label markup.
	 *
	 * @return string Empty string - no label needed.
	 */
	protected function getLabel()
	{
		return '';
	}
}
