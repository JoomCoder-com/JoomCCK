<?php

defined('_JEXEC') or die('Restricted access');

use Joomcck\Assets\Webassets\Webassets;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('jquery.framework');

// load JoomCCK new library
require_once JPATH_ROOT . '/components/com_joomcck/libraries/vendor/autoload.php';


// init webassets
Webassets::init();

$joomcck = JPATH_ROOT . '/components/com_joomcck/api.php';
if(!file_exists($joomcck))
{
	return;
}
require_once(dirname(__FILE__) . '/helper.php');
include_once JPATH_ROOT . '/components/com_joomcck/library/php/html/tags.php';

$section  = new \Joomla\CMS\Object\CMSObject();
$category = new \Joomla\CMS\Object\CMSObject();
$app      = \Joomla\CMS\Factory::getApplication();
$lang     = \Joomla\CMS\Factory::getLanguage();

$Itemid = $app->input->getInt('Itemid');
$tag    = $lang->getTag();
$res    = $lang->load('com_joomcck', JPATH_ROOT . '/components/com_joomcck');

$section->id  = $params->get('depends_on_cat', 0) ? $app->input->getInt('section_id') : $params->get('section_id');
$category->id = $app->input->getInt('cat_id');


if($params->get('show_section_name'))
{
	\Joomla\CMS\MVC\Model\BaseDatabaseModel::addIncludePath(JPATH_ROOT . '/components/com_joomcck/models');
	\Joomla\CMS\Table\Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_joomcck/tables');
	$section  = modJoomcckTagcloudHelper::getSection($section->id);
	$category = modJoomcckTagcloudHelper::getCategory($category->id);
}

$list = modJoomcckTagcloudHelper::getTags($section, $params, $category->id);


if(!$list)
{
	return FALSE;
}

$html = $params->get('html_tags', 'H1, H2, H3, H4, H5, H6, strong, b, em, big, small');


require \Joomla\CMS\Helper\ModuleHelper::getLayoutPath('mod_joomcck_tagcloud', $params->get('layout', 'default'));
