<?php
/**
 * Emerald by joomcoder
 * a component for Joomla! 3.0 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

\Joomla\CMS\Form\FormHelper::loadFieldClass('list');

class JFormFieldEmrplanslist extends \Joomla\CMS\Form\Field\ListField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Emrplanslist';
	public $extend;

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getOptions()
	{
		// Initialise variables.
		$options = array();
		$app_input = \Joomla\CMS\Factory::getApplication()->input;
		$exclude_id = false;

		if(
			$app_input->getCmd('option') == 'com_emerald' &&
			$app_input->getCmd('view') == 'emplan' &&
			$app_input->getCmd('layout') == 'edit' &&
			$app_input->getInt('id', false) &&
			$this->element['current'] == 0
		)
		{
			$exclude_id = $app_input->getInt('id');
		}

		$db = \Joomla\CMS\Factory::getDbo();
		$db->setQuery('SHOW TABLES LIKE "%_emerald_plans"');
		if(!$db->loadResult())
		{
			return array(\Joomla\CMS\HTML\HTMLHelper::_('select.option', '', \Joomla\CMS\Language\Text::_('C_EM_NT_INSTALLED')));
		}

		$query = $db->getQuery(true);
		$options = array();

		$query->select('p.id, p.name');
		if($this->extend)
		{
			$query->select('p.params');
		}
		$query->select('g.name as group_name');
		$query->from('#__emerald_plans AS p');
		$query->leftJoin('#__emerald_plans_groups AS g ON g.id = p.group_id');
		$query->where('g.published = 1');
		$query->where('p.published = 1');
		if($exclude_id)
		{
			$query->where('p.id <> '.$exclude_id);
		}
		$query->order('g.ordering asc, p.ordering');
		$db->setQuery($query);
		$plans = $db->loadObjectList();
		foreach($plans as $plan)
		{
			if($this->extend)
			{
				$params = new \Joomla\Registry\Registry($plan->params);
				$text = EmeraldApi::getPrice($params->get('properties.price'), $params);
				$text .= " ".\Joomla\CMS\Language\Text::_($plan->name);

				$plan->name = $text;
			}
			$options[\Joomla\CMS\Language\Text::_($plan->group_name)][] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $plan->id, \Joomla\CMS\Language\Text::_($plan->name));
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

		$this->extend = isset($this->element['extend']);
		// Get the field groups.
		$groups = (array)$this->getOptions();

		// Create a read-only list (no name) with a hidden input to store the value.
		if((string)$this->element['readonly'] == 'true')
		{
			$html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.groupedlist', $groups, null, array(
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
			$html[] = \Joomla\CMS\HTML\HTMLHelper::_('select.groupedlist', $groups, $this->name, array(
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