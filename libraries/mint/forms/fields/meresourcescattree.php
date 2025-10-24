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
jimport('joomla.form.helper');
\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class JFormFieldMeresourcescattree extends \Joomla\CMS\Form\Field\ListField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Meresourcescattree';
	
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
		
		$db = \Joomla\CMS\Factory::getDbo();
		$query = $db->getQuery(true);
		
		$options = array();
		$query->select('id, name as text');
		$query->from('#__js_res_sections');
		$db->setQuery($query);
		$sections = $db->loadObjectList();
		$i = 0;
		foreach ( $sections as $section )
		{
			$options[$i]->level = 0;
			$options[$i]->value = 1;
			$options[$i]->id = $section->id;
			$options[$i]->name = $section->name;
			$query = $db->getQuery(true);
			// Prevent parenting to children of this item.
			$query->select('id as value, title as text, level');
			$query->from('#__js_res_categories');
			$query->where('section_id = ' . $section->id);
			$query->where('published = 1');
			$query->order('lft ASC');
			$db->setQuery($query);
			$options1 = $db->loadObjectList();
			$options = array_merge($options, $options1);
		}
		//
		if ($id = $this->form->getValue('id'))
		{
			$rowQuery = $db->getQuery(true);
			$rowQuery->select('a.id AS value, a.title AS text, a.level, a.parent_id');
			$rowQuery->from('#__js_res_categories AS a');
			$rowQuery->where('a.id = ' . ( int ) $id);
			$db->setQuery($rowQuery);
			$row = $db->loadObject();
		}

		// Pad the option text with spaces using depth level as a multiplier.
		for ( $i = 0, $n = count($options); $i < $n; $i ++ )
		{
			$options[$i]->text = str_repeat('- ', $options[$i]->level) . $options[$i]->text;
		}
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);
		
		return $options;
	}
}