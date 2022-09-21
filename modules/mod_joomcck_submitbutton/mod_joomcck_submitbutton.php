<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

include_once JPATH_ROOT . '/components/com_joomcck/library/php/helpers/helper.php';

$app        = JFactory::getApplication();
$section_id = $params->get('section_id');
$section    = ItemsStore::getSection($section_id);
$category   = NULL;

if(!$section->id)
{
	JError::raiseWarning(100, JText::_('MOD_SB_NOSECTION'));

	return FALSE;
}

$types = $section->params->get('general.type');

$param_types = $params->get('types');
ArrayHelper::clean_r($param_types);
if(!empty($param_types))
{
	$types = array_intersect($types, $param_types);
}

if(!$types)
{
	JError::raiseWarning(100, JText::_('MOD_SB_NOTYPE'));

	return FALSE;
}

// Do not show outside of the section
if($params->get('display') && !($app->input->getCmd('option') == 'com_joomcck' && $app->input->getInt('section_id') == $section_id))
{
	return;
}

if($app->input->getCmd('option') == 'com_joomcck' && $app->input->getInt('section_id') == $section_id)
{
	if($params->get('category') && $app->input->getInt('cat_id'))
	{
		$category = ItemsStore::getCategory($app->input->getInt('cat_id'));
	}

	if($params->get('follow'))
	{
		if(!empty($category->id))
		{
			$cat_types = $category->params->get('posttype');
			if($cat_types[0] != 'none' && $cat_types[0] != '')
			{
				$types = array_intersect($types, $cat_types);
			}
		}
		else
		{
			$tmpl_params = CTmpl::prepareTemplate('default_markup_', 'general.tmpl_markup', $section->params);
			if($tmpl_params->get('menu.menu_home') == 0)
			{
				return;
			}
			if(!$tmpl_params->get('menu.menu_newrecord'))
			{
				return;
			}
			if(!in_array($tmpl_params->get('menu.menu_newrecord'), JFactory::getUser()->getAuthorisedViewLevels()))
			{
				return;
			}
		}
	}
}

if(empty($types))
{
	return;
}

foreach($types AS $key => $type)
{
	$types[$key] = ItemsStore::getType($type);
}


require JModuleHelper::getLayoutPath('mod_joomcck_submitbutton', $params->get('layout', 'default'));
