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

class JFormFieldMertype extends \Joomla\CMS\Form\FormField
{
	protected $type = 'Mertype';
	protected function getInput()
	{
		global $app;
		
		$db			= \Joomla\CMS\Factory::getDBO();
		$doc 		= \Joomla\CMS\Factory::getDocument();
		$template 	= $app->getTemplate();
		$multi    	= $this->element['multi'];		
		
		$query = $db->getQuery ( true );
		
		$query->select ( 'id, name' );
		$query->from ( '#__js_res_types' );		
		$query->where ( 'published = 1' );
		$db->setQuery($query);
		$list = $db->loadObjectList();
		
		$types[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', "", '- Select Type -');
		foreach ($list as $val)
		{
		    $types[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $val->id, $val->name);
		}
				
		$multiselect = '';
		count($types) > 20 ? $n = 20 : $n = count($types);
		if($multi) $multiselect = 'size="'.$n.'" multiple';
		
		
		$html = '';
		$html	.= \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist',  $types, 
            $this->name.($multi ? "[]" : null),  ' class="form-select" '.$multiselect, 'value', 'text', $this->value);
            
		return $html;
	}
}
?>