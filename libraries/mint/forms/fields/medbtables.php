<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('JPATH_PLATFORM') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('melist');

class JFormFieldMedbtables extends JFormMEFieldList
{
	public $type = 'Medbtables';
	
	protected function getOptions()
	{
		$db	= JFactory::getDBO();
		
		$sql = "SHOW tables";
		$db->setQuery($sql);
		$tables = $db->loadRowList();
		
		$this->multiple = true;
		$options = array();
		
		foreach ($tables as $table)
		{
			$options[] = JHTML::_('select.option', $table[0], $table[0]);
		}
		return $options;

	}
}
