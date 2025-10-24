<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
jimport('joomla.html.html');
jimport('joomla.form.formfield');
$document = \Joomla\CMS\Factory::getDocument();
$document->addScript(\Joomla\CMS\Uri\Uri::root(TRUE).'/administrator/components/com_joomcck/library/js/main.js');


class JFormFieldCsectionstypest extends \Joomla\CMS\Form\FormField
{
	protected $type = 'Csectionstypest';
	protected function getInput()
	{
		$doc 		= \Joomla\CMS\Factory::getDocument();
		$multi    	= $this->element['multiple'];
		$required    	= $this->element['required'];

		if(!is_array($this->value))
		{
			settype($this->value, 'array');
		}
		$doc->addScriptDeclaration('
			var selected_types = ['.implode(',', $this->value).'];
		');
		
		$multiselect = $class = '';
		if($multi) $multiselect = ' multiple="multiple" ';
		if($required) $class = 'required ';
		
		$html = '';
		$html	.= \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist',  array(), $this->name.($multi ? "[]" : null),  ' class="inputbox '.$class.'" '.$multiselect);
		
		return $html;
	}
}
?>