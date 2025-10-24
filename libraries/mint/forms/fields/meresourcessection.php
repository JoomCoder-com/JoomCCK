<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;
define('_DS', DIRECTORY_SEPARATOR);

jimport('joomla.html.html');
jimport('joomla.form.formfield');
\Joomla\CMS\Form\FormHelper::loadFieldClass('melist');

class JFormFieldMeresourcessection extends JFormMEFieldList
{

	public $type = 'Meresourcessection';

	protected function getOptions()
	{


		// load joomcck lang
		$lang = \Joomla\CMS\Factory::getLanguage();
		$lang->load('com_joomcck',JPATH_ROOT,$lang->getTag(),true);

		$path = JPATH_ROOT . _DS . 'components' . _DS . 'com_joomcck' . _DS . 'library' . _DS . 'php' . _DS . 'html';
        if(is_dir($path)) {
            \Joomla\CMS\HTML\HTMLHelper::addIncludePath($path);
        }
		$path = JPATH_ROOT . _DS . 'administrator'. _DS . 'components' . _DS . 'com_joomcck' . _DS . 'library' . _DS . 'php' . _DS . 'html';
        if(is_dir($path)) {
            \Joomla\CMS\HTML\HTMLHelper::addIncludePath($path);
        }
        $sections = \Joomla\CMS\HTML\HTMLHelper::_('joomcck.sections');

		$options = array();
		if($this->element['select'] == 1)
		{
			$options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '', \Joomla\CMS\Language\Text::_('CSELECTSECTION'));
		}
		foreach($sections as $type)
		{
			$options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $type->value . ($this->element['alias'] == 1 ? ':' . $type->alias : NULL), $type->text);

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
			$html .= '<small>' . $this->element['append'].'</small>';
		}

		return $html;
	}
}