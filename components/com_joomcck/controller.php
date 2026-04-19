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

		// Fullscreen admin UI: toggle + session-state application. Runs for every
		// view (including `form`) so editing a record preserves the sidebar shell.
		// Guarded by MECAccess::isAdmin() so public/guest submissions are unaffected.
		if(MECAccess::isAdmin())
		{
			if($this->input->getCmd('joomcck_fullscreen_toggle'))
			{
				$session = \Joomla\CMS\Factory::getSession();
				$current = $session->get('joomcck_fullscreen', 0);
				$session->set('joomcck_fullscreen', $current ? 0 : 1);

				$uri = \Joomla\CMS\Uri\Uri::getInstance();
				$uri->delVar('joomcck_fullscreen_toggle');
				$app->redirect($uri->toString());
			}

			// Global section switcher: `set_section=<id>` persists the selection
			// in session, then redirects to the clean URL. `0` clears the filter.
			if($this->input->get('set_section', null, 'raw') !== null)
			{
				$session = \Joomla\CMS\Factory::getSession();
				$session->set('joomcck_section_id', (int) $this->input->getInt('set_section'));

				$uri = \Joomla\CMS\Uri\Uri::getInstance();
				$uri->delVar('set_section');
				$app->redirect($uri->toString());
			}

			$session = \Joomla\CMS\Factory::getSession();
			if($session->get('joomcck_fullscreen', 0))
			{
				$this->input->set('tmpl', 'component');
			}

			// Auto-apply the session-stored section id to list views. Each model
			// still reads its own filter via getUserStateFromRequest; we pre-seed
			// the request with both key flavors (section_id, filter_section) so
			// models pick it up without per-model changes. Skipped when the
			// request already carries a section param (per-view filter bars, or
			// links that scope themselves explicitly).
			//
			// Sentinel: `null` means the switcher was never touched — we leave
			// per-view state alone. `0` means "All sections" was explicitly
			// picked — we inject it so models overwrite any stale per-view
			// user state with the unfiltered default.
			$sectionListViews = array(
				'items', 'cats', 'comms', 'votes', 'moderators',
				'auditlog', 'notifications', 'tags', 'ctypes',
				'tfields', 'groups', 'templates', 'packs',
			);
			if(in_array($this->input->getCmd('view'), $sectionListViews, true))
			{
				$savedSection = $session->get('joomcck_section_id', null);
				$reqHasSection = $this->input->get('section_id', null, 'raw') !== null
					|| $this->input->get('filter_section', null, 'raw') !== null;
				if($savedSection !== null && !$reqHasSection)
				{
					$this->input->set('section_id', (int) $savedSection);
					$this->input->set('filter_section', (int) $savedSection);
				}
			}
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
			// The admin entry (administrator/components/com_joomcck/joomcck.php)
			// already enforces core.manage on com_joomcck, so requests that arrived
			// through the administrator application are trusted here. Without this
			// bypass, admin-URL loads (e.g. the "edit template" modal iframe) would
			// fall into the frontend-only MECAccess::isAdmin() check below and
			// redirect to the site homepage for Joomla Super Users who aren't
			// mapped to the configured JoomCCK moderator_group.
			if(!$app->isClient('administrator') && !MECAccess::isAdmin())
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