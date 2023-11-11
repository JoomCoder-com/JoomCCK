<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
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
		\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip');

		$this->state      = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			throw new Exception( implode("\n", $errors),500);


		}

		$this->addFilter(\Joomla\CMS\Language\Text::_('CFILERVOTETYPE'), 'filter_type', \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('votes.types'), 'value', 'text', $this->state->get('filter.type')));
		$this->addFilter(\Joomla\CMS\Language\Text::_('CFILTERVOTE'), 'filter_votes', \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('votes.values'), 'value', 'text', $this->state->get('filter.votes')));
		$this->addFilter(\Joomla\CMS\Language\Text::_('CFILTERSECTION'), 'filter_section', \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('joomcck.sections'), 'value', 'text', $this->state->get('filter.section')));

		parent::display($tpl);

	}

	function prepareItems(&$items)
	{
		foreach($items as $key => $item)
		{
			if(\Joomla\CMS\Filesystem\Folder::exists(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components/com_juser'))
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
			'a.id'       => \Joomla\CMS\Language\Text::_('ID'),
			'a.ctime'    => \Joomla\CMS\Language\Text::_('CVOTED'),
			'r.title'    => \Joomla\CMS\Language\Text::_('CRECORD'),
			'u.username' => \Joomla\CMS\Language\Text::_('CUSER'),
			'a.vote'     => \Joomla\CMS\Language\Text::_('CVOTE'),
			'a.ref_type' => \Joomla\CMS\Language\Text::_('CTYPE')
		);

	}

}