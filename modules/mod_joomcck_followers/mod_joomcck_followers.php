<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
$app = JFactory::getApplication();


if($app->input->getCmd('option') != 'com_joomcck') return;
if($app->input->getCmd('view') != 'records') return;
if(!$app->input->getInt('user_id')) return;

include_once JPATH_ROOT. '/components/com_joomcck/api.php';

$section = ItemsStore::getSection($app->input->getInt('section_id'));

if(!$section->params->get('events.subscribe_user')) return;


$list = CUsrHelper::getFolowers($app->input->getInt('user_id'), $section);

if(!$list) return;

$list = array_keys($list);
$list = array_splice($list, 0, $params->get('limit', 10));

require JModuleHelper::getLayoutPath('mod_joomcck_followers', $params->get('layout', 'default'));

?>