<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();
jimport('joomla.application.component.view');

/**
 * View information about joomcck.
 *
 * @package        Joomcck
 * @subpackage     com_joomcck
 * @since          6.0
 */
class JoomcckViewTags extends MViewBase
{

	function display($tpl = NULL)
	{
		JHtml::_('bootstrap.tooltip');

		$this->state      = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return FALSE;
		}

		$this->addFilter(JText::_('JOPTION_SELECT_LANGUAGE'), 'filter_language', JHtml::_('select.options', JHtml::_('contentlanguage.existing', false, true), 'value', 'text', $this->state->get('filter.language')));
		$this->addFilter(JText::_('CFILTERSECTION'), 'filter_category', JHtml::_('select.options', JHtml::_('joomcck.sections'), 'value', 'text', $this->state->get('filter.category')));

		parent::display($tpl);
	}

	public function getSortFields()
	{
		return array(
			't.tag'   => JText::_('CTAGNAME'),
			't.id'    => JText::_('ID'),
			't.ctime' => JText::_('CCREATED')
		);
	}
}