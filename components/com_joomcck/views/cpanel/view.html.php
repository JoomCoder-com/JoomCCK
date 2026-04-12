<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('mint.mvc.view.base');

class JoomcckViewCpanel extends MViewBase
{
	public $hasExtendedVersion = false;

	public $sections         = [];
	public $currentSectionId = 0;
	public $currentRange     = '30d';
	public $dashboard        = [];

	public function display($tpl = null)
	{
		$this->hasExtendedVersion();

		$app   = \Joomla\CMS\Factory::getApplication();
		$input = $app->input;

		// Filter resolution: request → user state → default
		$sectionFromReq = $input->get('section_id', null, 'INT');
		$rangeFromReq   = $input->get('range', null, 'CMD');

		if ($sectionFromReq !== null) {
			$this->currentSectionId = (int) $sectionFromReq;
			$app->setUserState('com_joomcck.cpanel.section_id', $this->currentSectionId);
		} else {
			$this->currentSectionId = (int) $app->getUserState('com_joomcck.cpanel.section_id', 0);
		}

		if ($rangeFromReq && in_array($rangeFromReq, ['7d', '30d', '90d', 'ytd', 'all'], true)) {
			$this->currentRange = $rangeFromReq;
			$app->setUserState('com_joomcck.cpanel.range', $this->currentRange);
		} else {
			$range = $app->getUserState('com_joomcck.cpanel.range', '30d');
			$this->currentRange = in_array($range, ['7d', '30d', '90d', 'ytd', 'all'], true) ? $range : '30d';
		}

		require_once JPATH_COMPONENT_SITE . '/models/cpanel.php';
		$model           = new JoomcckModelCpanel();
		$this->sections  = $model->getSectionsList();
		$this->dashboard = $model->getDashboard($this->currentSectionId, $this->currentRange);

		// Enqueue dashboard assets. Use addScript/addStyleSheet directly — the
		// WebAssetManager is unreliable for late activation on the frontend
		// component render cycle and silently drops the output.
		$doc  = $app->getDocument();
		$base = \Joomla\CMS\Uri\Uri::root(true);
		$doc->addScript($base . '/media/com_joomcck/js/vendor/chart.umd.js?v=4.4.3');
		$doc->addScript($base . '/media/com_joomcck/js/cpanel/dashboard.js?v=1.0');
		$doc->addStyleSheet($base . '/media/com_joomcck/css/cpanel/dashboard.css?v=1.0');

		parent::display($tpl);
	}

	public function hasExtendedVersion()
	{
		$file = JPATH_ROOT . '/modules/mod_joomcck_ifollow/mod_joomcck_ifollow.xml';
		if (is_file($file)) {
			$this->hasExtendedVersion = true;
		}
	}
}
