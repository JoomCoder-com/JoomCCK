<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

if(!JFactory::getUser()->id) return;

// Include the syndicate functions only once
require_once (dirname(__FILE__) . DIRECTORY_SEPARATOR . 'helper.php');
include_once JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_joomcck'. DIRECTORY_SEPARATOR .'library'. DIRECTORY_SEPARATOR .'php'. DIRECTORY_SEPARATOR .'helpers'. DIRECTORY_SEPARATOR .'helper.php';
$Itemid = \Joomla\CMS\Factory::getApplication()->input->getInt('Itemid');
$section_id = $params->get('section_id');

if($params->get('current_section', 0) && $cur = \Joomla\CMS\Factory::getApplication()->input->getInt('section_id'))
{
	$section_id = $cur;
}

$data = modJoomcckUserStatisticsHelper::getData($params, $section_id);
if(!count($data)) return;

require JModuleHelper::getLayoutPath('mod_joomcck_userstatistics', $params->get('layout', 'default'));
