<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class JoomcckViewModerators extends MViewBase
{
	function display($tpl = null)
	{
		$user_id = JFactory::getUser()->get('id');
		$this->state = $this->get('State');
		$params = JComponentHelper::getParams('com_joomcck');

		if(!MECAccess::isModerator($user_id, JFactory::getApplication()->input->getInt('filter_section', $this->state->get('filter.section', 0))))
		{
			JError::raise(E_WARNING, 403, JText::_('CERR_NOPAGEACCESS'));
			return;
		}

		$uri = \Joomla\CMS\Uri\Uri::getInstance();
// 		$this->action = $uri->toString();

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->filter_sections = $this->get('Sections');
		$this->section_model = MModelBase::getInstance('Section', 'JoomcckModel');

		$this->_prepareDocument();

		$this->addFilter(JText::_('JOPTION_SELECT_PUBLISHED'), 'filter_state', JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array(
			'trash'    => 0,
			'archived' => 0,
			'all'      => 0
		)), 'value', 'text', $this->state->get('filter.state'), TRUE));

		if($params->get('moderator', - 1) == JFactory::getUser()->get('id'))
		{
			$this->addFilter(JText::_('CSELECTSECTION'), 'filter_section', JHtml::_('select.options', $this->filter_sections, 'value', 'text', $this->state->get('filter.section'), TRUE));
		}


		parent::display($tpl);
	}

	private function _prepareDocument()
	{
		$app = JFactory::getApplication();
		$doc = JFactory::getDocument();
		$menus = $app->getMenu();
		$menu = $menus->getActive();
		$pathway = $app->getPathway();
		$this->appParams = $app->getParams();

		$title = null;
		$path = array();

		if($menu)
		{
			$title = $menu->getParams()->get('page_title', $menu->title);
			$this->appParams->def('page_heading', $title);
		}

		$title = JText::_('CMODERLIST');

		$pathway->addItem($title);

		$path = array(array('title' => $title, 'link' => ''));

		if($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$doc->setTitle($title);
	}

	public function getSortFields()
	{
		return array(
			'a.published' => JText::_('JSTATUS'),
			'a.id'        => JText::_('ID'),
			'a.name'      => JText::_('CNAME'),
		);
	}
}
