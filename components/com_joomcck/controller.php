<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();

/**
 * Main Controller
 *
 * @package        Joomcck
 * @subpackage     com_joomcck
 * @since          6.0
 */
class JoomcckController extends MControllerBase
{

	/**
	 * Method to display a view.
	 *
	 * @param    boolean $cachable  If true, the view output will be cached
	 * @param    array   $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return    JController        This object to support chaining.
	 * @since    6.0
	 */
	public function display($cachable = FALSE, $urlparams = FALSE)
	{

		$app = JFactory::getApplication();

		HTMLHelper::_('jquery.framework');


		if(!JComponentHelper::getParams('com_joomcck')->get('general_upload'))
		{


			\Joomla\CMS\Factory::getApplication()->enqueueMessage(JText::_('CUPLOADREQ'),'error');

			return;
		}

		if(in_array($this->input->get('view'),
                array('cpanel', 'items', 'sections', 'section', 'ctypes', 'ctype', 
                    'tfields', 'tfield', 'groups', 'group', 'templates', 'packs', 
                    'pack', 'tools', 'votes', 'tags', 'comms', 'comm', 'moderators',
                    'cats','auditlog','notifications','import'))
		)
		{
			if(!JFactory::getUser()->get('id'))
			{
				$app->enqueueMessage(JText::_('CPLEASELOGIN'),'warning');
				$app->setHeader('status', 403, true);
				$app->redirect(JRoute::_('index.php?option=com_users&view=login&return='.base64_encode(JUri::getInstance()->toString()), false));
			}
			if(!MECAccess::isAdmin())
			{
				$app->enqueueMessage(JText::_('CCANNOTACCESSADMINAREA'),'warning');
				$app->setHeader('status', 403, true);
				$app->redirect(JRoute::_('/'));
			}

			if(JComponentHelper::getParams('com_joomcck')->get('tmpl_full'))
			{
				$this->input->set('tmpl', 'component');
			}
		}

		$prefix = JComponentHelper::getParams('com_joomcck')->get('tmpl_prefix');
		if($prefix)
		{
			$view   = $this->input->getCmd('view', 'default');
			$layout = $this->input->getCmd('layout', 'default');

			if(JFile::exists(JPATH_ROOT . "/components/com_joomcck/views/{$view}/tmpl/{$prefix}-{$layout}.php"))
			{
				$this->input->set('layout', $prefix.'-'.$layout);
			}
		}

		$display = parent::display();

		if(JFactory::getApplication()->input->get('tmpl') != 'component' && JComponentHelper::getParams('com_joomcck')->get('general_copyright'))
		{
			$html = '<div class="clearfix"></div><center><small style="font-size: 10px;">%s</small></center>';
			echo sprintf($html, JText::sprintf('CPOWEREDBY', '<a target="_blank" href="https://www.joomBoost.com/joomla-components/joomcck.html">Joomcck</a>'));
		}

		if($this->input->get('no_html'))
		{
			JFactory::getApplication()->close();
		}

		return $display;
	}

}