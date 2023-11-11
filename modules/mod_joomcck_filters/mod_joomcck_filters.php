<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

use Joomcck\Assets\Webassets\Webassets;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('jquery.framework');

// load JoomCCK new library
require_once JPATH_ROOT . '/components/com_joomcck/libraries/vendor/autoload.php';

// init webassets
Webassets::init();

$app = \Joomla\CMS\Factory::getApplication();

if($app->input->getCmd('option') == 'com_joomcck' && $app->input->getCmd('view') == 'form' && $params->get('form_hide', 1))
{
	return;
}

$lang = \Joomla\CMS\Factory::getLanguage();
$lang->load('com_joomcck', JPATH_ROOT);

require_once JPATH_ROOT . '/components/com_joomcck/api.php';

if(!$params->get('section_id'))
{
	return;
}

$section = ItemsStore::getSection($params->get('section_id'));

$cat_id  = $user_id = $vw = $ucat_id = $f_cats = $f_users = $f_tags = 0;
$f_types = array();
if($section->id == $app->input->getInt('section_id') && $app->input->get('view') == 'records')
{
	$cat_id  = $app->input->get('cat_id');
	$ucat_id = $app->input->get('ucat_id');
	$user_id = $app->input->get('user_id');
	$vw      = $app->input->get('view_what');
}

$sid  = $app->input->get('section_id');
$cid  = $app->input->get('cat_id');
$ucid = $app->input->get('ucat_id');
$uid  = $app->input->get('user_id');
$vwid = $app->input->get('view_what');

$app->input->set('section_id', $section->id);
$app->input->set('cat_id', $cat_id);
$app->input->set('ucat_id', $ucat_id);
$app->input->set('user_id', $user_id);
$app->input->set('view_what', $vw);

$model          = MModelBase::getInstance('Records', 'JoomcckModel');
$model->section = $section;

$filters    = $model->getFilters();
$worns      = $model->getWorns();
$state      = $model->getState();
$keys_by_id = $model->getKeys($section);

if($params->get('filter_type_type'))
{
	$f_types = $model->getAllTypes();
}

require(JModuleHelper::getLayoutPath('mod_joomcck_filters', $params->get('layout', 'default')));

$app->input->set('section_id', $sid);
$app->input->set('cat_id', $cid);
$app->input->set('ucat_id', $ucid);
$app->input->set('user_id', $uid);
$app->input->set('view_what', $vwid);
