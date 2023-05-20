<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') || die('Restricted access');

use Joomcck\Assets\Webassets\Webassets;

// load JoomCCK new library
require_once JPATH_ROOT . '/components/com_joomcck/libraries/vendor/autoload.php';

// init webassets
Webassets::init();

require_once dirname(__FILE__) . '/helper.php';
include_once JPATH_ROOT . '/components/com_joomcck/library/php/helpers/helper.php';

$Itemid     = \Joomla\CMS\Factory::getApplication()->input->getInt('Itemid');
$section_id = $params->get('section_id');

if ($params->get('current_section', 0) && $cur = \Joomla\CMS\Factory::getApplication()->input->getInt('section_id')) {
    $section_id = $cur;
}

$data = modJoomcckSectionStatisticsHelper::getData($params, $section_id);
if (!count($data)) {
    return;
}

require JModuleHelper::getLayoutPath('mod_joomcck_sectionstatistics', $params->get('layout', 'default'));
