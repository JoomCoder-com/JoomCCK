<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class JoomcckViewModerator extends MViewBase
{
    function display($tpl = null)
    {
        $section_id = JFactory::getApplication()->input->getInt('section_id');
        $user_id = JFactory::getUser()->get('id');

        if(!MECAccess::isModerator($user_id, $section_id))
        {
        	JError::raise(E_WARNING, 403, JText::_('CERR_NOPAGEACCESS'));
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
        	JError::raiseWarning(404, JText::_('CNOSECTION'));
        	return;
        }

        $this->section = ItemsStore::getSection($section_id);

        $this->_prepareDocument();

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
    		$title = $menu->params->get('page_title', $menu->title);
    		$this->appParams->def('page_heading', $title);
    	}

    	$title = isset($this->item->id) ? JText::_('CEDITMODER') : JText::_('CADDMODER');

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
}