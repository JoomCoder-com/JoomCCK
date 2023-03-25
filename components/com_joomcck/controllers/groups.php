<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

jimport('mint.mvc.controller.admin');
class JoomcckControllerGroups extends MControllerAdmin
{
	public $model_prefix = 'JoomcckBModel';

	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = JFactory::getApplication()->input;
		}
	}

	public function getModel($name = 'Group', $prefix = 'JoomcckModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function cancel()
	{
		$app =  JFactory::getApplication();
		$app->redirect('index.php?option=com_joomcck&view=fields&id='.$this->input->getInt('type_id'));
	}

	public function ordersave()
	{
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		\Joomla\Utilities\ArrayHelper::toInteger($pks);
		\Joomla\Utilities\ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}


		JFactory::getApplication()->close();
	}
}