<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\MVC\View\GenericDataException;

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class JFormFieldCobCategoryParent extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'CobCategoryParent';

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

		$db		= \Joomla\CMS\Factory::getDbo();
		$query	= $db->getQuery(true);
	
		$section = \Joomla\CMS\Factory::getApplication()->input->getInt('section_id',0);
		$query->select('name as text');
		$query->from('#__js_res_sections');
		$query->where('id = '.$section);

		$db->setQuery($query);

		$options = $db->loadObjectList();
		$options[0]->level = 0;
		$options[0]->value = 1;
		
		$query	= $db->getQuery(true);
		// Prevent parenting to children of this item.
		$query->select('id as value, title as text, level');
		$query->from('#__js_res_categories');
		$query->where('section_id = '.$section);
		if ($id = $this->form->getValue('id')) 
		{
			$rowQuery	= $db->getQuery(true);
			$rowQuery->select('a.id AS value, a.title AS text, a.level, a.parent_id');
			$rowQuery->from('#__js_res_categories AS a');
			$rowQuery->where('a.id = ' . (int) $id);
			$db->setQuery($rowQuery);
			$row = $db->loadObject();
		}

		$query->where('published IN (0,1)');
		
		$query->order('lft ASC');
		//echo str_replace('#_', 'jos', $query);

		// Get the options.
		$db->setQuery($query);

		$options1 = $db->loadObjectList();



		try{
			$options1 = $db->loadObjectList();
		}catch (RuntimeException $e){
			throw new GenericDataException($e->getMessage(), 500);
		}


		$options = array_merge($options, $options1);


		// Pad the option text with spaces using depth level as a multiplier.
		for ($i = 0, $n = count($options); $i < $n; $i++)
		{
			$options[$i]->text = str_repeat('- ',$options[$i]->level).$options[$i]->text;
		}
		// Initialise variables.
		$user = \Joomla\CMS\Factory::getUser();

		if (empty($id)) {
			// New item, only have to check core.create.
			foreach ($options as $i => $option)
			{
				// Unset the option if the user isn't authorised for it.
				if (!$user->authorise('core.create', $section.'.category.'.$option->value)) {
					unset($options[$i]);
				}
			}
		}
		else {
			// Existing item is a bit more complex. Need to account for core.edit and core.edit.own.
			foreach ($options as $i => $option)
			{
				// Unset the option if the user isn't authorised for it.
				if (!$user->authorise('core.edit', $section.'.category.'.$option->value)) {
					// As a backup, check core.edit.own
					if (!$user->authorise('core.edit.own', $section.'.category.'.$option->value)) {
						// No core.edit nor core.edit.own - bounce this one
						unset($options[$i]);
					}
					else {
						// TODO I've got a funny feeling we need to check core.create here.
						// Maybe you can only get the list of categories you are allowed to create in?
						// Need to think about that. If so, this is the place to do the check.
					}
				}
			}
		}


		if (isset($row) && !isset($options[0])) {
			/*if ($row->parent_id == '1') {
				$parent = new stdClass();
				$parent->text = \Joomla\CMS\Language\Text::_('JGLOBAL_ROOT_PARENT');
				array_unshift($options, $parent);
			}*/
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}