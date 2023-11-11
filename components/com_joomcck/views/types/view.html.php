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

class JoomcckViewTypes extends MViewBase
{
    function display ($tpl = null)
    {
        $app = \Joomla\CMS\Factory::getApplication();
        $doc = \Joomla\CMS\Factory::getDocument();
        
        if (!\Joomla\CMS\Factory::getApplication()->input->getInt('section_id'))
        {
            \Joomla\CMS\Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CNOSECTION'),'warning');
            return;
        }
        
        $model = MModelBase::getInstance('Section', 'JoomcckModel');
        $this->types = $model->getSectionTypes(\Joomla\CMS\Factory::getApplication()->input->getInt('section_id'));
        
        if($errors = $model->getErrors())
        {
	        Factory::getApplication()->enqueueMessage(implode('<br>',$errors),'warning');
        	return FALSE;
        }
        
        if(count($this->types) == 1)
        {
        	$url = 'index.php?option=com_joomcck&view=form&type_id='.$this->types[0]->id.'&section_id='.\Joomla\CMS\Factory::getApplication()->input->getInt('section_id');
        	$app->redirect(\Joomla\CMS\Router\Route::_($url, FALSE));
        	return ;
        }
        
        parent::display($tpl);
    }
}