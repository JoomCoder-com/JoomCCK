<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Categories view class for the Category package.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @since		1.6
 */
class JoomcckViewCats extends MViewBase
{
	protected $_defaultModel = 'JoomcckModelCategories';

	public function display($tpl = null)
	{
		$app = JFactory::getApplication();

		$this->section 		= $app->input->getInt('section_id');
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		if (!$this->section) {
			Factory::getApplication()->enqueueMessage( JText::_('C_MSG_SELECTSECTIO'),'warning');
			$app->redirect('index.php?option=com_joomcck&view=sections');

		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			throw new Exception( implode("\n", $errors),500);

		}

		// Preprocess the list of items to find ordering divisions.
		foreach ($this->items as &$item) {
			$this->ordering[$item->parent_id][] = $item->id;
		}

		$section	= $this->state->get('filter.section');

		$sections = array();
		$section_list = $this->get('Sections');
		foreach ($section_list as $val)
		{
			$sections[] = JHtml::_('select.option', $val->value, $val->text);
			if($section == $val->value)
			{
				$this->section = $val;
			}
		}
		$this->addFilter(JText::_('CFILTERSECTION'), 'filter_section', JHtml::_('select.options', $sections, 'value', 'text', $this->state->get('filter.section'), true));

		// Levels filter.
		$options	= array();
		$options[]	= JHtml::_('select.option', '1', JText::_('J1'));
		$options[]	= JHtml::_('select.option', '2', JText::_('J2'));
		$options[]	= JHtml::_('select.option', '3', JText::_('J3'));
		$options[]	= JHtml::_('select.option', '4', JText::_('J4'));
		$options[]	= JHtml::_('select.option', '5', JText::_('J5'));
		$options[]	= JHtml::_('select.option', '6', JText::_('J6'));
		$options[]	= JHtml::_('select.option', '7', JText::_('J7'));
		$options[]	= JHtml::_('select.option', '8', JText::_('J8'));
		$options[]	= JHtml::_('select.option', '9', JText::_('J9'));
		$options[]	= JHtml::_('select.option', '10', JText::_('J10'));

		$this->addFilter(JText::_('XML_SELECT_LEVEL'), 'filter_level', JHtml::_('select.options', $options, 'value', 'text', $this->state->get('filter.level'), true));

		$this->addFilter(JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_published', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array(
			'trash' => 0,
			'archived' => 0,
			'all' => 0
		)), 'value', 'text', $this->state->get('filter.published'), true));

		$this->addFilter(JText::_('JOPTION_SELECT_ACCESS'), 'filter_access', JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'), true));


		parent::display($tpl);
	}

	public function getSortFields()
	{
		return array(
		'a.lft' => JText::_('JGRID_HEADING_ORDERING'),
		'a.published' => JText::_('JSTATUS'),
		'a.title' => JText::_('CTITLE'),
		'a.access' => JText::_('JGRID_HEADING_ACCESS'),
		'language' => JText::_('JGRID_HEADING_LANGUAGE'),
		'a.id' => JText::_('ID'),
		);
	}
}
