<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;
jimport('mint.mvc.controller.form');
class JoomcckControllerAuditlog extends MControllerForm
{
	protected $view_item = 'auditlog';
	protected $view_list = 'auditlog';

	public function __construct($config = array())
	{
		parent::__construct($config);
		
		if(!$this->input)
		{
			$this->input = JFactory::getApplication()->input;
		}
	}
	public function reset()
	{
		$app = JFactory::getApplication();

		$app->setUserState('com_joomcck.auditlog.filter.search', '');
		$app->setUserState('com_joomcck.auditlog.section_id', '');
		$app->setUserState('com_joomcck.auditlog.type_id', '');
		$app->setUserState('com_joomcck.auditlog.user_id', '');
		$app->setUserState('com_joomcck.auditlog.event_id', '');
		$app->setUserState('com_joomcck.auditlog.fcs', '');
		$app->setUserState('com_joomcck.auditlog.fce', '');

		$this->setRedirect(JRoute::_('index.php?option=com_joomcck&view=auditlog', false));
	}
}
