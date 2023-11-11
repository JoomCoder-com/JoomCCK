<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('mint.mvc.controller.base');

class JoomcckControllerTags extends MControllerBase
{

	public $model_prefix = 'JoomcckBModel';

	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = \Joomla\CMS\Factory::getApplication()->input;
		}
	}
	function delete()
	{

		$model = $this->getModel('tags', 'JoomcckModel');
		$model->_deleteTag();

		$this->setRedirect('index.php?option=com_joomcck&view=tags', \Joomla\CMS\Language\Text::_('C_MSG_TAGDELETEDSUCCESS'));
	}

	function save()
	{
		$model = $this->getModel('tags', 'JoomcckModel');
		if($model->_saveTag())
		{
			$msg = \Joomla\CMS\Language\Text::_('C_MSG_TAGSAVEDSUCCESS');
			$this->setRedirect('index.php?option=com_joomcck&view=tags', $msg);
		}
		else
		{
			throw new GenericDataException($model->_error_msg, 500);
			$this->setRedirect('index.php?option=com_joomcck&view=tags');
		}
	}
}
?>