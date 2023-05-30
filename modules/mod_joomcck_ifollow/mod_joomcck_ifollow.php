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

use Joomcck\Assets\Webassets\Webassets;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('jquery.framework');

// load JoomCCK new library
require_once JPATH_ROOT . '/components/com_joomcck/libraries/vendor/autoload.php';

// init webassets
Webassets::init();


if($app->input->getCmd('option') != 'com_joomcck') return;
if($app->input->getCmd('view') != 'records') return;
if(!$app->input->getInt('user_id')) return;

include_once JPATH_ROOT. '/components/com_joomcck/api.php';

$section = ItemsStore::getSection($app->input->getInt('section_id'));

if(!$section->params->get('events.subscribe_user')) return;

$user_id = $app->input->getInt('user_id');

$db = JFactory::getDbo();
$db->setQuery("SELECT u_id FROM `#__js_res_subscribe_user` WHERE user_id = {$user_id} AND section_id = {$section->id} AND exclude = 0 LIMIT 0, ".$params->get('limit', 10));

$list = $db->loadColumn();

if(!$list) return;

require JModuleHelper::getLayoutPath('mod_joomcck_ifollow', $params->get('layout', 'default'));

?>