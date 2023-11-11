<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Platform.
 * Provides radio button inputs
 *
 * @link   http://www.w3.org/TR/html-markup/command.radio.html#command.radio
 * @since  11.1
 */
class JFormFieldMRadio extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $type = 'MRadio';

	/**
	 * Method to get the radio button field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		ob_start();
		include $this->_get_templates();
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	private function _get_templates()
	{
		$params = \Joomla\CMS\Component\ComponentHelper::getParams(\Joomla\CMS\Factory::getApplication()->input->get('option'));
		$prefix = $params->get('tmpl_prefix', 'default');

		$file = __DIR__.'/tmpl/mradio-'.$prefix.'.php';

		if(\Joomla\CMS\Filesystem\File::exists($file))
		{
			return $file;
		}
		return __DIR__.'/tmpl/mradio-default.php';
	}
}
