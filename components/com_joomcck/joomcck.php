<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;

defined('_JEXEC') or die();

// load JoomCCK new library
require_once __DIR__ . '/libraries/vendor/autoload.php';

// init webassets
Webassets::init();

// load JoomCCK API (will be replaced in future with Joomla API integration
require_once __DIR__ . '/api.php';


$params              = JComponentHelper::getParams('com_joomcck');
$meta                = array();
$meta['description'] = $params->get('metadesc');
$meta['keywords']    = $params->get('metakey');
$meta['author']      = $params->get('author');
$meta['robots']      = $params->get('robots');
$meta['copyright']   = $params->get('rights');

MetaHelper::setMeta($meta);
HTMLFormatHelper::loadHead();
JFactory::getApplication()->setUserState('skipers.all', array());

$session = JFactory::getSession();
if(is_null($session->get('registry')))
{
	$session->set('registry', new JRegistry('session'));
}




$controller = MControllerBase::getInstance('Joomcck');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
