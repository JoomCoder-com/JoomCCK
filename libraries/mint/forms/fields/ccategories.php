<?php
/**
 * Joomcck Categories field list
 * @author		Christophe CROSAZ - Abstrakt Graphics
 * @author		ccrosaz@abstrakt.fr
 * 
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright	Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') or die();

jimport('joomla.html.html');
jimport('joomla.form.form');
jimport('joomla.form.field');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class JFormFieldCcategories extends JFormFieldList
{

	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Ccategories';

	/**
	 * Method to get the field options.
	 *
	 * @return	array	The field option objects.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// Initialise variables.
		$options = array();
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$options = array();
		
		$query->select('id, categories, name as text');
		$query->from('#__js_res_sections');
		$db->setQuery($query);
		$sections = $db->loadObjectList();
		//echo '<pre>';print_r(parent::getOptions());echo '</pre>';
		foreach($sections as $section)
		{
			//Looking for sub categories:
			if(count($section->categories))
			{
				$query = $db->getQuery(true);
				// Prevent parenting to children of this item.
				$query->select("`id` as value, CONCAT(REPEAT('- ', `level`), `title`) as text");
				$query->from('#__js_res_categories');
				$query->where('section_id = ' . $section->id);
				$query->where('published = 1');
				$query->order('lft ASC');
				$db->setQuery($query);
				
				$options[$section->text] = $db->loadObjectList();
			}
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge(array(
			parent::getOptions()
		), $options);
		
		return $options;
	}

	/**
	 * Method to get the field input markup fora grouped list.
	 * Multiselect is enabled by using the multiple attribute.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.1
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';
		
		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string)$this->element['class'] . '"' : '';
		$attr .= ((string)$this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int)$this->element['size'] . '"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';
		
		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string)$this->element['onchange'] . '"' : '';
		
		// Get the field groups.
		$groups = (array)$this->getOptions();
		
		// Create a read-only list (no name) with a hidden input to store the value.
		if((string)$this->element['readonly'] == 'true')
		{
			$html[] = JHtml::_('select.groupedlist', $groups, null, array(
				'list.attr' => $attr, 
				'id' => $this->id, 
				'list.select' => $this->value, 
				'group.items' => null, 
				'option.key.toHtml' => false, 
				'option.text.toHtml' => false
			));
			$html[] = '<input type="hidden" name="' . $this->name . '" value="' . $this->value . '"/>';
		}
		// Create a regular list.
		else
		{
			$html[] = JHtml::_('select.groupedlist', $groups, $this->name, array(
				'list.attr' => $attr, 
				'id' => $this->id, 
				'list.select' => $this->value, 
				'group.items' => null, 
				'option.key.toHtml' => false, 
				'option.text.toHtml' => false
			));
		}
		
		return implode($html);
	}
}