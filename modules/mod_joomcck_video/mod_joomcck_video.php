<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

include_once JPATH_ROOT. DIRECTORY_SEPARATOR .'components'. DIRECTORY_SEPARATOR .'com_joomcck'. DIRECTORY_SEPARATOR .'api.php';

$api = new JoomcckApi();
$result = $api->records($params->def('section_id'), 'all', $params->get('tmpl_core.orderby', 'r.ctime DESC'), NULL, NULL, 0, $params->get('tmpl_core.limit', 5), $params->def('tmpl'));

require JModuleHelper::getLayoutPath('mod_joomcck_video', $params->get('layout', 'default'));
?>