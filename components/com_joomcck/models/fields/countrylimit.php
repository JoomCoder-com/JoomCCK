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

class JFormFieldCountrylimit extends JFormFieldList
{
	
	protected $type = 'countrylimit';

	protected function getOptions()
	{
		$db = \Joomla\CMS\Factory::getDbo();
		$query = 'SELECT id AS value, name AS text' .
				' FROM #__js_res_country ORDER BY name ASC';
		$db->setQuery($query);
		$list = $db->loadObjectList();
		
		//$opt = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '', \Joomla\CMS\Language\Text::_('CUNGROUPED'));
		//array_unshift($list, $opt);
		
		return $list;

	}

}