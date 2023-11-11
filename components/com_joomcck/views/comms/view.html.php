<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class JoomcckViewComms extends MViewBase
{

	public function display($tpl = NULL)
	{

		\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip');

		$uri          = \Joomla\CMS\Uri\Uri::getInstance();
		$this->action = $uri->toString();

		$this->state      = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		if(count($errors = $this->get('Errors')))
		{
			throw new Exception( implode("\n", $errors),500);


		}

		$this->addFilter(\Joomla\CMS\Language\Text::_('CFILTERRECORDTYPE'), 'filter_type', \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('joomcck.recordtypes'), 'value', 'text', $this->state->get('filter.type')));
		$this->addFilter(\Joomla\CMS\Language\Text::_('CFILTERSECTION'), 'filter_section', \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('joomcck.sections'), 'value', 'text', $this->state->get('filter.section')));
		$this->addFilter(\Joomla\CMS\Language\Text::_('JOPTION_SELECT_PUBLISHED'), 'filter_state', \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('jgrid.publishedOptions',
			array(
				'archived' => 0, 'trash' => 0, 'all' => 0,
			)), 'value', 'text', $this->state->get('filter.state'), TRUE));


		parent::display($tpl);

	}

	function prepareItems(&$items)
	{
		foreach($items as $key => $item)
		{
			if(\Joomla\CMS\Filesystem\Folder::exists(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components/com_juser'))
			{
				$item->user_href = JURI::root(TRUE) . '/administrator/index.php?option=com_juser&view=user&task=edit&cid[]=' . $item->userid;
			}
			else
			{
				$item->user_href = JURI::root(TRUE) . '/administrator/index.php?option=com_user&view=user&task=edit&cid[]=' . $item->userid;
			}

			$items[$key] = $item;
		}

	}

	protected function addToolbar()
	{
		JToolbarHelper::title(\Joomla\CMS\Language\Text::_('CCOMMENTS'), 'comments.png');
		JToolBarHelper::editList('comment.edit');
		JToolBarHelper::divider();
		JToolBarHelper::publishList('comments.publish');
		JToolBarHelper::unpublishList('comments.unpublish');
		JToolBarHelper::divider();
		JToolBarHelper::deleteList('', 'comments.delete', 'Delete');

		MRToolBar::addSubmenu('comments');

		\Joomla\CMS\HTML\Helpers\Sidebar::setAction('index.php?option=com_joomcck&view=comments');


	}

	public function getSortFields()
	{
		return array(
			'id'          => \Joomla\CMS\Language\Text::_('ID'),
			'a.ctime'     => \Joomla\CMS\Language\Text::_('CCREATED'),
			'r.title'     => \Joomla\CMS\Language\Text::_('CRECORD'),
			'u.username'  => \Joomla\CMS\Language\Text::_('CUSER'),
			'a.published' => \Joomla\CMS\Language\Text::_('JSTATUS'),
			'a.comment'   => \Joomla\CMS\Language\Text::_('CSUBJECT')
		);

	}

}