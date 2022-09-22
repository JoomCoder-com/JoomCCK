<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');

class JFormFieldMEUsercategories extends JFormField
{
    protected $type = 'MEUsercategories';

    protected function getInput()
    {
        $db	= JFactory::getDBO();
        $user = JFactory::getUser(JRequest::getInt('cat_user_id'));
        $section_id = JRequest::getInt('section_id');

		$sql = "SELECT id AS value, name AS text FROM `#__js_res_category_user`
				WHERE published = 1 AND user_id = {$user->get('id')} AND section_id = {$section_id}
				ORDER BY ordering";
		$db->setQuery($sql);
		$categories = $db->loadObjectList();

		$html = '<div class="form-inline">';
		$html .= JHtml::_('select.genericlist', $categories, $this->name, 'class="inputbox"', 'value', 'text', $this->value, $this->id);
		if($this->required)
		{
		    $uri		= JFactory::getUri();
            $return		= base64_encode($uri);
		    $html .= '<a class="btn" href="'.JRoute::_('index.php?option=com_joomcck&view=category&section_id='.JRequest::getInt('section_id').'&task=usercategory.add&return='.$return).'">
		    			<img src="'.JURI::root().'media/mint/icons/16/plus-button.png" align="absmiddle" alt="'.JText::_('Add New').'" /> '.JText::_('Add New').'
					</a></div>';
		}

		return $html;
    }

}