<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.filesystem.file');

$params = JComponentHelper::getParams('com_joomcck');
$router = $params->get('sef_router', 'main_router.php');
 
$lang = JFactory::getLanguage();
$lang->load('com_joomcck');
$lang->load();

include_once JPATH_ROOT. '/components/com_joomcck/routers'. DIRECTORY_SEPARATOR .$router;
