<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

jimport('mint.mvc.controller.admin');

class JoomcckControllerCpanel extends MControllerAdmin
{
	public function __construct($config = array())
	{
		parent::__construct($config);
		if (!$this->input) {
			$this->input = \Joomla\CMS\Factory::getApplication()->input;
		}
	}

	/**
	 * AJAX endpoint used by the dashboard when section/range filters change.
	 * URL: index.php?option=com_joomcck&task=cpanel.getStats&section_id=X&range=Y
	 * Persists filter selection to user state (per admin).
	 */
	public function getStats()
	{
		$app   = \Joomla\CMS\Factory::getApplication();
		$input = $app->input;

		$sectionId = $input->getInt('section_id', 0);
		$range     = $input->getCmd('range', '30d');

		if (!in_array($range, ['7d', '30d', '90d', 'ytd', 'all'], true)) {
			$range = '30d';
		}

		$app->setUserState('com_joomcck.cpanel.section_id', $sectionId);
		$app->setUserState('com_joomcck.cpanel.range', $range);

		$model = $this->getModel('Cpanel', 'JoomcckModel');
		$data  = $model->getDashboard($sectionId, $range);

		AjaxHelper::send($data);
	}
}
