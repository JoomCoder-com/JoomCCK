<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 *
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
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
	 * @param    array   $urlparams An array of safe url parameters and their variable types, for valid values see {@link \Joomla\CMS\Filter\InputFilter::clean()}.
	 *
	 * @return    JController        This object to support chaining.
	 * @since    6.0
	 */
	public function display($cachable = FALSE, $urlparams = FALSE)
	{

		$app = \Joomla\CMS\Factory::getApplication();

		HTMLHelper::_('jquery.framework');


		if(!\Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck')->get('general_upload'))
		{


			\Joomla\CMS\Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::_('CUPLOADREQ'),'error');

			return;
		}

		if(in_array($this->input->get('view'),
                array('cpanel', 'items', 'sections', 'section', 'ctypes', 'ctype', 
                    'tfields', 'tfield', 'groups', 'group', 'templates', 'packs', 
                    'pack', 'tools', 'votes', 'tags', 'comms', 'comm', 'moderators',
                    'cats','auditlog','notifications','import'))
		)
		{
			if(!\Joomla\CMS\Factory::getApplication()->getIdentity()->get('id'))
			{
				$app->enqueueMessage(\Joomla\CMS\Language\Text::_('CPLEASELOGIN'),'warning');
				$app->setHeader('status', 403, true);
				$app->redirect(\Joomla\CMS\Router\Route::_('index.php?option=com_users&view=login&return='.base64_encode(\Joomla\CMS\Uri\Uri::getInstance()->toString()), false));
			}
			if(!MECAccess::isAdmin())
			{
				$app->enqueueMessage(\Joomla\CMS\Language\Text::_('CCANNOTACCESSADMINAREA'),'warning');
				$app->setHeader('status', 403, true);
				$app->redirect(\Joomla\CMS\Router\Route::_('/'));
			}

			if(\Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck')->get('tmpl_full'))
			{
				$this->input->set('tmpl', 'component');
			}
		}

		$prefix = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck')->get('tmpl_prefix');
		if($prefix)
		{
			$view   = $this->input->getCmd('view', 'default');
			$layout = $this->input->getCmd('layout', 'default');

			if(is_file(JPATH_ROOT . "/components/com_joomcck/views/{$view}/tmpl/{$prefix}-{$layout}.php"))
			{
				$this->input->set('layout', $prefix.'-'.$layout);
			}
		}

		$display = parent::display();


		if($this->input->get('no_html'))
		{
			\Joomla\CMS\Factory::getApplication()->close();
		}

		return $display;
	}

}