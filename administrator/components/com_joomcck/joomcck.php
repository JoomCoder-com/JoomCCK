<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

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
JHTML::_('behavior.tooltip');

if(!JFactory::getUser()->authorise('core.manage', 'com_joomcck'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller = MControllerBase::getInstance('Joomcck');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>
<div style="clear: both;"></div>
<br/>
<br/>
<center>
	<small>Copyright &copy; 2012 <a target="_blank" href="https://www.joomBoost.com">JoomBoost</a>. All rights reserved
	</small>
</center>