<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();

class JoomcckViewTypes extends MViewBase
{
    function display ($tpl = null)
    {
        $app = JFactory::getApplication();
        $doc = JFactory::getDocument();
        
        if (!JFactory::getApplication()->input->getInt('section_id'))
        {
            JFactory::getApplication()->enqueueMessage(JText::_('CNOSECTION'),'warning');
            return;
        }
        
        $model = MModelBase::getInstance('Section', 'JoomcckModel');
        $this->types = $model->getSectionTypes(JFactory::getApplication()->input->getInt('section_id'));
        
        if($errors = $model->getErrors())
        {
	        Factory::getApplication()->enqueueMessage(implode('<br>',$errors),'warning');
        	return FALSE;
        }
        
        if(count($this->types) == 1)
        {
        	$url = 'index.php?option=com_joomcck&view=form&type_id='.$this->types[0]->id.'&section_id='.JFactory::getApplication()->input->getInt('section_id');
        	$app->redirect(JRoute::_($url, FALSE));
        	return ;
        }
        
        parent::display($tpl);
    }
}