<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');

class JFormFieldMEUsercategories extends \Joomla\CMS\Form\FormField
{
    protected $type = 'MEUsercategories';

    protected function getInput()
    {

        $db	= \Joomla\CMS\Factory::getDBO();
        $user = \Joomla\CMS\Factory::getUser(\Joomla\CMS\Factory::getApplication()->input->getInt('cat_user_id',0));
        $section_id = \Joomla\CMS\Factory::getApplication()->input->getInt('section_id',0);

		$sql = "SELECT id AS value, name AS text FROM `#__js_res_category_user`
				WHERE published = 1 AND user_id = {$user->get('id')} AND section_id = {$section_id}
				ORDER BY ordering";
		$db->setQuery($sql);
		$categories = $db->loadObjectList();

		$html = '<div class="form-inline">';
		$html .= \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $categories, $this->name, 'class="form-select"', 'value', 'text', $this->value, $this->id);
		if($this->required)
		{
		    $uri		= \Joomla\CMS\Uri\Uri::getInstance();
            $return		= base64_encode($uri);
		    $html .= '<a class="btn btn-outline-success" href="'.\Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=category&section_id='.\Joomla\CMS\Factory::getApplication()->input->getInt('section_id',0).'&task=usercategory.add&return='.$return).'">
		    			<img src="'.\Joomla\CMS\Uri\Uri::root().'media/com_joomcck/icons/16/plus-button.png" align="absmiddle" alt="'.\Joomla\CMS\Language\Text::_('Add New').'" /> '.\Joomla\CMS\Language\Text::_('Add New').'
					</a></div>';
		}

		return $html;
    }

}