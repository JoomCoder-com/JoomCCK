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

class JoomcckViewModerator extends MViewBase
{
    function display($tpl = null)
    {
        $section_id = \Joomla\CMS\Factory::getApplication()->input->getInt('section_id');
        $user_id = \Joomla\CMS\Factory::getUser()->get('id');

        if(!MECAccess::isModerator($user_id, $section_id))
        {

	        Factory::getApplication()->enqueueMessage( \Joomla\CMS\Language\Text::_('CERR_NOPAGEACCESS'),'warning');
        	return;
        }

        $this->item  = $this->get('Item');
        $this->form  = $this->get('Form');

        if(!$section_id)
        {
        	$section_id = $this->item->section_id;
        }

        if(!$section_id)
        {
        	\Joomla\CMS\Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CNOSECTION'),'warning');
        	return;
        }

        $this->section = ItemsStore::getSection($section_id);

        $this->_prepareDocument();

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

    	$title = isset($this->item->id) ? \Joomla\CMS\Language\Text::_('CEDITMODER') : \Joomla\CMS\Language\Text::_('CADDMODER');

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
}