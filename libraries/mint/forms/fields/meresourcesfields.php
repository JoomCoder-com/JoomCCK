<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') or die;
jimport('joomla.html.html');
jimport('joomla.form.formfield');

JFormHelper::loadFieldClass('melist');

class JFormFieldMeresourcesfields extends JFormMEFieldList
{
	public $type = 'Meresourcesfields';
	
	protected function getOptions()
	{
		
		\Joomla\CMS\HTML\HTMLHelper::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'administrator'. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_joomcck'. DIRECTORY_SEPARATOR .'library'. DIRECTORY_SEPARATOR .'php'. DIRECTORY_SEPARATOR .'html');
		$key = ( $this->element['key'] ?  $this->element['key'] : 'key');
		
		$select = ($this->multiple || $this->element['multi']) ? false : true; 
		$sections = \Joomla\CMS\HTML\HTMLHelper::_('joomcck.types', $select, $this->element['filters'], $key, $this->element['client']);
		
		return $sections;
	}
}