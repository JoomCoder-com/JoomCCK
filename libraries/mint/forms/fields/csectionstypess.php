<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
define('_DS', DIRECTORY_SEPARATOR);

jimport('joomla.html.html');
jimport('joomla.form.formfield');
\Joomla\CMS\Form\FormHelper::loadFieldClass('melist');

class JFormFieldCsectionstypess extends JFormMEFieldList
{

	public $type = 'Csectionstypess';

	protected function getOptions()
	{
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
			$options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', '', \Joomla\CMS\Language\Text::_('- Select Section -'));
		}
		foreach($sections as $type)
		{
			$options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $type->value, $type->text);
		}
		return $options;
	}
	protected function getInput()
	{
		$this->element['onchange'] = 'ajax_reloadTypes(\''.$this->formControl.$this->group.$this->element['type_elem_name'].'\', this.value);';
		
		$html = parent::getInput();
		
		$doc = \Joomla\CMS\Factory::getDocument();
		$uri = \Joomla\CMS\Uri\Uri::getInstance();
		$doc->addScriptDeclaration("
			function ajax_reloadTypes(id, value)
			{
				var sel = $(id);
				new Request.HTML({
					url:'".\Joomla\CMS\Uri\Uri::root()."administrator/index.php?option=com_joomcck&task=ajax.loadsectiontypes&no_html=1',
					method:'post',
					autoCancel:true,
					data:{section_id: value, selected: selected_types},
					update: $(id),
		        }).send();
			}
				
			window.addEvent('domready', function(){
				ajax_reloadTypes('".$this->formControl.$this->group.$this->element['type_elem_name']."', '".$this->value."');
			});
		");
		return $html;
	}
}
?>
