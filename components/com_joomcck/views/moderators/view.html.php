<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();

class JoomcckViewModerators extends MViewBase
{
	function display($tpl = null)
	{
		$user_id = \Joomla\CMS\Factory::getUser()->get('id');
		$this->state = $this->get('State');
		$params = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');

		if(!MECAccess::isModerator($user_id, \Joomla\CMS\Factory::getApplication()->input->getInt('filter_section', $this->state->get('filter.section', 0))))
		{

			Factory::getApplication()->enqueueMessage( \Joomla\CMS\Language\Text::_('CERR_NOPAGEACCESS'),'warning');
			return;
		}

		$uri = \Joomla\CMS\Uri\Uri::getInstance();
// 		$this->action = $uri->toString();

		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->filter_sections = $this->get('Sections');
		$this->section_model = MModelBase::getInstance('Section', 'JoomcckModel');

		$this->_prepareDocument();

		$this->addFilter(\Joomla\CMS\Language\Text::_('JOPTION_SELECT_PUBLISHED'), 'filter_state', \Joomla\CMS\HTML\HTMLHelper::_('select.options', \Joomla\CMS\HTML\HTMLHelper::_('jgrid.publishedOptions', array(
			'trash'    => 0,
			'archived' => 0,
			'all'      => 0
		)), 'value', 'text', $this->state->get('filter.state'), TRUE));

		if($params->get('moderator', - 1) == \Joomla\CMS\Factory::getUser()->get('id'))
		{
			$this->addFilter(\Joomla\CMS\Language\Text::_('CSELECTSECTION'), 'filter_section', \Joomla\CMS\HTML\HTMLHelper::_('select.options', $this->filter_sections, 'value', 'text', $this->state->get('filter.section'), TRUE));
		}


		parent::display($tpl);
	}

	private function _prepareDocument()
	{
		$app = \Joomla\CMS\Factory::getApplication();
		$doc = \Joomla\CMS\Factory::getDocument();
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

		$title = \Joomla\CMS\Language\Text::_('CMODERLIST');

		$pathway->addItem($title);

		$path = array(array('title' => $title, 'link' => ''));

		if($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = \Joomla\CMS\Language\Text::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = \Joomla\CMS\Language\Text::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}

		$doc->setTitle($title);
	}

	public function getSortFields()
	{
		return array(
			'a.published' => \Joomla\CMS\Language\Text::_('JSTATUS'),
			'a.id'        => \Joomla\CMS\Language\Text::_('ID'),
			'a.name'      => \Joomla\CMS\Language\Text::_('CNAME'),
		);
	}
}
