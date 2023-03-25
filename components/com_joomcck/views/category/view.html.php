<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class JoomcckViewCategory extends MViewBase
{
    function display($tpl = null)
    {
        
        if (! JFactory::getApplication()->input->getInt('section_id'))
		{
			throw new GenericDataException(JText::_('CNOSECTION'), 500);
			return FALSE;
		}
		
        $model = MModelBase::getInstance('Usercategory', 'JoomcckModel');
        
		$this->section = ItemsStore::getSection(JFactory::getApplication()->input->getInt('section_id'));
        
        $this->item = $model->getItem();
        $this->form = $model->getForm();
        $this->user = JFactory::getUser();
        
        parent::display($tpl);
    }
    
}
?>