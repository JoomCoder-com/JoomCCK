<?php
/**
 * Emerald by joomcoder
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldCPlanslist extends \Joomla\CMS\Form\Field\ListField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'CPlanslist';

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
		$file = JPATH_ROOT.'/components/com_emerald/models/fields/planslist.php';
		if(is_file($file) && \Joomla\CMS\Component\ComponentHelper::isEnabled('com_emerald'))
		{
			include_once $file;
			$element = new JFormFieldPlanslist($this->form);
			$element->setup($this->element, $this->value, $this->group);
			return $element->getInput();
		}

		return '<b>'.\Joomla\CMS\Language\Text::_('Please install JoomSubscription').'</b>';
	}
}