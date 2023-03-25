<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldFieldsgroup extends JFormFieldList
{
	
	protected $type = 'Fieldsgroup';

	protected function getOptions()
	{
		$db = JFactory::getDbo();
		$query = 'SELECT id AS value, title AS text' .
				' FROM #__js_res_fields_group' .
				' WHERE type_id = ' . \Joomla\CMS\Factory::getApplication()->input->getInt('type_id',0);
				' ORDER BY ordering';
		$db->setQuery($query);
		$list = $db->loadObjectList();
		
		$opt = JHtml::_('select.option', 0, JText::_('CUNGROUPED'));
		
		array_unshift($list, $opt);
		
		return $list;

	}

}