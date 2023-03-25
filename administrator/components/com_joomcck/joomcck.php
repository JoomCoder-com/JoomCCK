<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

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

if(!JFactory::getUser()->authorise('core.manage', 'com_joomcck'))
{

	throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 404);

}

$controller = MControllerBase::getInstance('Joomcck');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
?>
<div style="clear: both;"></div>
<br/>
<br/>
<center>
	<small>Copyright &copy; 2012 <a target="_blank" href="https://www.joomcoder.com">joomcoder</a>. All rights reserved
	</small>
</center>