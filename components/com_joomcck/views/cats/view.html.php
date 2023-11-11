<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
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
		$app = \Joomla\CMS\Factory::getApplication();

		$this->section 		= $app->input->getInt('section_id');
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');

		if (!$this->section) {
			Factory::getApplication()->enqueueMessage( \Joomla\CMS\Language\Text::_('C_MSG_SELECTSECTIO'),'warning');
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
			$sections[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $val->value, $val->text);
			if($section == $val->value)
			{
				$this->section = $val;
			}
		}
		$this->addFilter(\Joomla\CMS\Language\Text::_('CFILTERSECTION'), 'filter_section', \Joomla\CMS\HTML\HTMLHelper::_('select.options', $sections, 'value', 'text', $this->state->get('filter.section'), true));

		// Levels filter.
		$options	= array();
		$options[]	= \Joomla\CMS\HTML\HTMLHelper::_('select.option', '1', \Joomla\CMS\Language\Text::_('J1'));
		$options[]	= \Joomla\CMS\HTML\HTMLHelper::_('select.option', '2', \Joomla\CMS\Language\Text::_('J2'));
		$options[]	= \Joomla\CMS\HTML\HTMLHelper::_('select.option', '3', \Joomla\CMS\Language\Text::_('J3'));
		$options[]	= \Joomla\CMS\HTML\HTMLHelper::_('select.option', '4', \Joomla\CMS\Language\Text::_('J4'));
		$options[]	= \Joomla\CMS\HTML\HTMLHelper::_('select.option', '5', \Joomla\CMS\Language\Text::_('J5'));
		$options[]	= \Joomla\CMS\HTML\HTMLHelper::_('select.option', '6', \Joomla\CMS\Language\Text::_('J6'));
		$options[]	= \Joomla\CMS\HTML\HTMLHelper::_('select.option', '7', \Joomla\CMS\Language\Text::_('J7'));
		$options[]	= \Joomla\CMS\HTML\HTMLHelper::_('select.option', '8', \Joomla\CMS\Language\Text::_('J8'));
		$options[]	= \Joomla\CMS\HTML\HTMLHelper::_('select.option', '9', \Joomla\CMS\Language\Text::_('J9'));
		$options[]	= \Joomla\CMS\HTML\HTMLHelper::_('select.option', '10', \Joomla\CMS\Language\Text::_('J10'));

		$this->addFilter(\Joomla\CMS\Language\Text::_('XML_SELECT_LEVEL'), 'filter_level', \Joomla\CMS\HTML\HTMLHelper::_('select.options', $options, 'value', 'text', $this->state->get('filter.level'), true));

		$this->addFilter(\Joomla\CMS\Language\Text::_('JOPTION_SELECT_PUBLISHED'), 'filter_published', \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('jgrid.publishedOptions', array(
			'trash' => 0,
			'archived' => 0,
			'all' => 0
		)), 'value', 'text', $this->state->get('filter.published'), true));

		$this->addFilter(\Joomla\CMS\Language\Text::_('JOPTION_SELECT_ACCESS'), 'filter_access', \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'), true));


		parent::display($tpl);
	}

	public function getSortFields()
	{
		return array(
		'a.lft' => \Joomla\CMS\Language\Text::_('JGRID_HEADING_ORDERING'),
		'a.published' => \Joomla\CMS\Language\Text::_('JSTATUS'),
		'a.title' => \Joomla\CMS\Language\Text::_('CTITLE'),
		'a.access' => \Joomla\CMS\Language\Text::_('JGRID_HEADING_ACCESS'),
		'language' => \Joomla\CMS\Language\Text::_('JGRID_HEADING_LANGUAGE'),
		'a.id' => \Joomla\CMS\Language\Text::_('ID'),
		);
	}
}
