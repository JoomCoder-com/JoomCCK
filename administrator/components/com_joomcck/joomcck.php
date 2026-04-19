<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

// Include dependancies
jimport('mint.mvc.controller.base');
jimport('mint.mvc.model.base');
jimport('mint.mvc.model.list');
jimport('mint.mvc.view.base');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
jimport('joomla.registry.registry');

require_once JPATH_ROOT . '/administrator/components/com_joomcck/library/helpers/toolbar.php';

if(!\Joomla\CMS\Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_joomcck'))
{

	throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 404);

}

// The admin entry dispatches via MControllerBase::getInstance, which resolves
// to the SHARED frontend JoomcckController (see libraries/mint/mvc/controller/base.php).
// That controller — and the views/models it loads — assume the frontend api.php
// bootstrap has run (JLoader prefix, table/model include paths, and the helper
// classes in components/com_joomcck/library/php/helpers/ like MECAccess). Load
// it here so admin URLs (e.g. the "edit template" modal iframe) work the same
// as frontend ones.
require_once JPATH_ROOT . '/components/com_joomcck/api.php';

// check if config already set
if(!\Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck')->get('general_upload'))
{

	Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CUPLOADREQ'),'warning');
	$this->setRedirect('index.php?option=com_config&view=component&component=com_joomcck');
}


$input = Factory::getApplication()->input;

$input->set('view', $input->get('view', 'start'));

// Force the shared (frontend) component path so the instantiated controller
// resolves views/models/tables/helpers against components/com_joomcck instead
// of the near-empty admin views directory. Without this, MControllerBase's
// constructor seeds $basePath from JPATH_COMPONENT (admin side here) and
// getView() throws "View not found".
$controller = MControllerBase::getInstance('Joomcck', array(
	'base_path' => JPATH_ROOT . '/components/com_joomcck',
));
$controller->execute(\Joomla\CMS\Factory::getApplication()->input->get('task'));
$controller->redirect();

