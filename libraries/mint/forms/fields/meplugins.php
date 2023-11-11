<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('JPATH_PLATFORM') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

JFormHelper::loadFieldClass('melist');

class JFormFieldMEplugins extends JFormMEFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	public $type = 'MEplugins';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// Initialize variables.
		$group = $this->element['group'];
		$db = \Joomla\CMS\Factory::getDbo();
		
		$query = $db->getQuery(true);
		$query->select('element, name, folder');
		$query->from('#__extensions');
		$query->where('type = "plugin"');
		$query->where('enabled = 1');
		if ($group)
		{
			$query->where('folder = "' . $group . '"');
		}
		$query->order('ordering ASC');
		$db->setQuery($query);
		$items = $db->loadObjectList();
		$options = array();
		
		$lang = \Joomla\CMS\Factory::getLanguage();
		foreach($items as $item)
		{
			$source = JPATH_PLUGINS . '/' . $item->folder . '/' . $item->element;
			$extension = 'plg_' . $item->folder . '_' . $item->element;
			$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, null, false, false) || $lang->load($extension . '.sys', $source, null, false, false) || $lang->load($extension . '.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false) || $lang->load($extension . '.sys', $source, $lang->getDefault(), false, false);
			$item->name = \Joomla\CMS\Language\Text::_($item->name);
			$options[] = JHTML::_('select.option', $item->element, $item->name);
		}
		
		return $options;
	}

}
