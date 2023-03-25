<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') or die;
define('_DS', DIRECTORY_SEPARATOR);

jimport('joomla.html.html');
jimport('joomla.form.formfield');
JFormHelper::loadFieldClass('melist');

class JFormFieldMeresourcessection extends JFormMEFieldList
{

	public $type = 'Meresourcessection';

	protected function getOptions()
	{
		$path = JPATH_ROOT . _DS . 'components' . _DS . 'com_joomcck' . _DS . 'library' . _DS . 'php' . _DS . 'html';
        if(JFolder::exists($path)) {
            JHtml::addIncludePath($path);
        }
		$path = JPATH_ROOT . _DS . 'administrator'. _DS . 'components' . _DS . 'com_joomcck' . _DS . 'library' . _DS . 'php' . _DS . 'html';
        if(JFolder::exists($path)) {
            JHtml::addIncludePath($path);
        }
        $sections = JHtml::_('joomcck.sections');

		$options = array();
		if($this->element['select'] == 1)
		{
			$options[] = JHTML::_('select.option', '', JText::_('CSELECTSECTION'));
		}
		foreach($sections as $type)
		{
			$options[] = JHTML::_('select.option', $type->value . ($this->element['alias'] == 1 ? ':' . $type->alias : NULL), $type->text);

		}

		return $options;
	}

	protected function getInput()
	{
		$html = parent::getInput();
		if($this->element['prepend'])
		{
			$html = $this->element['prepend'] . "<br>$html";
		}
		if($this->element['append'])
		{
			$html .= '<br><br>' . $this->element['append'];
		}

		return $html;
	}
}