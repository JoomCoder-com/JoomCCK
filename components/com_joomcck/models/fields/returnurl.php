<?php
/**
 * Emerald by joomcoder
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.folder');

JFormHelper::loadFieldClass('list');

class JFormFieldReturnurl extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Returnurl';

	/**
	 * Method to get the field input markup fora grouped list.
	 * Multiselect is enabled by using the multiple attribute.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		$id = \Joomla\CMS\Factory::getApplication()->input->getInt('id');

		if(!$id)
		{
			return \Joomla\CMS\Language\Text::_('EMR_RETURNURL');
		}

		return sprintf('<input type="text" readonly value="%s" >', \Joomla\CMS\Router\Route::_('index.php?option=com_emerald&Itemid=1&task=payment.back&processor='.$this->element['processor'], TRUE, -1));
	}
}