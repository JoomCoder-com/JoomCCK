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

		JHtml::_('bootstrap.tooltip');

		$uri          = \Joomla\CMS\Uri\Uri::getInstance();
		$this->action = $uri->toString();

		$this->state      = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		if(count($errors = $this->get('Errors')))
		{
			throw new Exception( implode("\n", $errors),500);


		}

		$this->addFilter(JText::_('CFILTERRECORDTYPE'), 'filter_type', JHtml::_('select.options', JHtml::_('joomcck.recordtypes'), 'value', 'text', $this->state->get('filter.type')));
		$this->addFilter(JText::_('CFILTERSECTION'), 'filter_section', JHtml::_('select.options', JHtml::_('joomcck.sections'), 'value', 'text', $this->state->get('filter.section')));
		$this->addFilter(JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_state', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions',
			array(
				'archived' => 0, 'trash' => 0, 'all' => 0,
			)), 'value', 'text', $this->state->get('filter.state'), TRUE));


		parent::display($tpl);

	}

	function prepareItems(&$items)
	{
		foreach($items as $key => $item)
		{
			if(JFolder::exists(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components/com_juser'))
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
		JToolbarHelper::title(JText::_('CCOMMENTS'), 'comments.png');
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
			'id'          => JText::_('ID'),
			'a.ctime'     => JText::_('CCREATED'),
			'r.title'     => JText::_('CRECORD'),
			'u.username'  => JText::_('CUSER'),
			'a.published' => JText::_('JSTATUS'),
			'a.comment'   => JText::_('CSUBJECT')
		);

	}

}