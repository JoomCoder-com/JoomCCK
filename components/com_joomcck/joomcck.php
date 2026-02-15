<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomcck\Ui\Helpers\UiSystemHelper;

defined('_JEXEC') or die();

// load JoomCCK new library
require_once __DIR__ . '/libraries/vendor/autoload.php';

// init webassets
Webassets::init();

// Load Modern UI assets (DaisyUI + Tailwind) globally when enabled
UiSystemHelper::loadModernAssets();

// load JoomCCK API (will be replaced in future with Joomla API integration
require_once __DIR__ . '/api.php';


$params              = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');
$meta                = array();
$meta['description'] = $params->get('metadesc');
$meta['keywords']    = $params->get('metakey');
$meta['author']      = $params->get('author');
$meta['robots']      = $params->get('robots');
$meta['copyright']   = $params->get('rights');

MetaHelper::setMeta($meta);
HTMLFormatHelper::loadHead();
\Joomla\CMS\Factory::getApplication()->setUserState('skipers.all', array());

$session = \Joomla\CMS\Factory::getSession();
if(is_null($session->get('registry')))
{
	$session->set('registry', new \Joomla\Registry\Registry('session'));
}




$controller = MControllerBase::getInstance('Joomcck');
$controller->execute(\Joomla\CMS\Factory::getApplication()->input->get('task'));
$controller->redirect();
