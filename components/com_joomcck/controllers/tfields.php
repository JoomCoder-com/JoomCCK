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
class JoomcckControllerTfields extends MControllerAdmin
{
	public $model_prefix = 'JoomcckBModel';

	public function getModel($name = 'Tfield', $prefix = 'JoomcckModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = \Joomla\CMS\Factory::getApplication()->input;
		}
		$this->registerTask('required', 'changeState');
		$this->registerTask('notrequired', 'changeState');
		$this->registerTask('searchable', 'changeState');
		$this->registerTask('notsearchable', 'changeState');
		$this->registerTask('show_intro', 'changeState');
		$this->registerTask('notshow_intro', 'changeState');
		$this->registerTask('show_full', 'changeState');
		$this->registerTask('notshow_full', 'changeState');
	}

	public function ordersave()
	{
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		$pks = \Joomla\Utilities\ArrayHelper::toInteger($pks);
		$order = \Joomla\Utilities\ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}


		\Joomla\CMS\Factory::getApplication()->close();
	}

	public function changeState()
	{
		// Check for request forgeries
		JSession::checkToken() or die(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		// Get items to publish from the request.
		$cid = \Joomla\CMS\Factory::getApplication()->input->get('cid', array(), 'array');
		$data = array(
			'required' => 1, 'notrequired' => 0,
			'searchable' => 1, 'notsearchable' => 0,
			'show_intro' => 1, 'notshow_intro' => 0,
			'show_full' => 1, 'notshow_full' => 0
		);
		$task = $this->getTask();
		$value = \Joomla\Utilities\ArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid))
		{
			JLog::add(\Joomla\CMS\Language\Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			$cid = \Joomla\Utilities\ArrayHelper::toInteger($cid);

			// Publish the items.
			if (!$model->changeState($task, $cid, $value))
			{
				JLog::add($model->getError(), JLog::WARNING, 'jerror');
			}
			else
			{
				$ntext = $this->text_prefix . '_N_ITEMS_UPDATED';
				$this->setMessage(\Joomla\CMS\Language\Text::plural($ntext, count($cid)));
			}
		}
		$extension = $this->input->get('extension');
		$extensionURL = ($extension) ? '&extension=' . $extension : '';
		$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));
	}


}