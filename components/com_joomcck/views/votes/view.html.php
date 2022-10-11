<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.filesystem.folder');

JHTML::_('bootstrap.modal', 'a.modal');

class JoomcckViewVotes extends MViewBase
{

	public function display($tpl = NULL)
	{
		JHtml::_('bootstrap.tooltip');

		$this->state      = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			throw new Exception( implode("\n", $errors),500);


		}

		$this->addFilter(JText::_('CFILERVOTETYPE'), 'filter_type', JHtml::_('select.options', JHtml::_('votes.types'), 'value', 'text', $this->state->get('filter.type')));
		$this->addFilter(JText::_('CFILTERVOTE'), 'filter_votes', JHtml::_('select.options', JHtml::_('votes.values'), 'value', 'text', $this->state->get('filter.votes')));
		$this->addFilter(JText::_('CFILTERSECTION'), 'filter_section', JHtml::_('select.options', JHtml::_('joomcck.sections'), 'value', 'text', $this->state->get('filter.section')));

		parent::display($tpl);

	}

	function prepareItems(&$items)
	{
		foreach($items as $key => $item)
		{
			if(JFolder::exists(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components/com_juser'))
			{
				$item->user_href = JURI::root() . 'administrator/index.php?option=com_juser&view=user&task=edit&cid[]=' . $item->userid;
			}
			else
			{
				$item->user_href = JURI::root() . 'administrator/index.php?option=com_user&view=user&task=edit&cid[]=' . $item->userid;
			}

			$items [$key] = $item;
		}

	}

	public  function getSortFields()
	{
		return array(
			'a.id'       => JText::_('ID'),
			'a.ctime'    => JText::_('CVOTED'),
			'r.title'    => JText::_('CRECORD'),
			'u.username' => JText::_('CUSER'),
			'a.vote'     => JText::_('CVOTE'),
			'a.ref_type' => JText::_('CTYPE')
		);

	}

}