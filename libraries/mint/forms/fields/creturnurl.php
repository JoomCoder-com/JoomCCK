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

class JFormFieldCreturnurl extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Creturnurl';

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
		$id = JFactory::getApplication()->input->getInt('fid');

		if(!$id)
		{
			return JText::_('RK_SAVETOSEEURL');
		}

		return sprintf('<input type="text" readonly value="%s" >', JUri::root().'index.php?option=com_joomcck&task=pay.returnUrl&lang=en&processor='.$this->element['processor']);
	}
}