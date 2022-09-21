<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

class JoomcckViewCategories extends MViewBase
{
    function display ($tpl = null)
    {
        
        $model = MModelBase::getInstance('Usercategories', 'JoomcckModel');
        
        $app = JFactory::getApplication();
		$this->section_id = $app->getUserStateFromRequest('com_joomcck.usercategories.section_id', 'section_id', null, 'int');
        $this->section = ItemsStore::getSection($this->section_id);
        $this->items = $model->getItems();
        $this->state = $model->getState();
        $this->pagination = $model->getPagination();

        parent::display($tpl);
    }
    
}
?>