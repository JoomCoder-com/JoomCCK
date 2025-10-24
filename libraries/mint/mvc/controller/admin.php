<?php
/**
 * @package     Joomla.Legacy
 * @subpackage  Controller
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

/**
 * Base class for a Joomla Administrator Controller
 *
 * Controller (controllers are where you put all the actual code) Provides basic
 * functionality, such as rendering views (aka displaying templates).
 *
 * @package     Joomla.Legacy
 * @subpackage  Controller
 * @since       12.2
 */
class MControllerAdmin extends MControllerBase
{
	/**
	 * The URL option for the component.
	 *
	 * @var    string
	 * @since  12.2
	 */
	protected $option;

	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  12.2
	 */
	protected $text_prefix;

	/**
	 * The URL view list variable.
	 *
	 * @var    string
	 * @since  12.2
	 */
	protected $view_list;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \Joomla\CMS\MVC\Controller\BaseController
	 * @since   12.2
	 * @throws  Exception
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Define standard task mappings.

		// Value = 0
		$this->registerTask('unpublish', 'publish');

		// Value = 2
		$this->registerTask('archive', 'publish');

		// Value = -2
		$this->registerTask('trash', 'publish');

		// Value = -3
		$this->registerTask('report', 'publish');
		$this->registerTask('orderup', 'reorder');
		$this->registerTask('orderdown', 'reorder');

		// Guess the option as com_NameOfController.
		if (empty($this->option))
		{
			$this->option = 'com_' . strtolower($this->getName());
		}

		// Guess the \Joomla\CMS\Language\Text message prefix. Defaults to the option.
		if (empty($this->text_prefix))
		{
			$this->text_prefix = strtoupper($this->option);
		}

		// Guess the list view as the suffix, eg: OptionControllerSuffix.
		if (empty($this->view_list))
		{
			$r = null;
			if (!preg_match('/(.*)Controller(.*)/i', get_class($this), $r))
			{
				throw new Exception(\Joomla\CMS\Language\Text::_('JLIB_APPLICATION_ERROR_CONTROLLER_GET_NAME'), 500);
			}
			$this->view_list = strtolower($r[2]);
		}
	}

	/**
	 * Removes an item.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function delete()
	{
		// Check for request forgeries
		\Joomla\CMS\Session\Session::checkToken() or die(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		// Get items to remove from the request.
		$cid = \Joomla\CMS\Factory::getApplication()->input->get('cid', array(), 'array');

		if (!is_array($cid) || count($cid) < 1)
		{
			\Joomla\CMS\Log\Log::add(\Joomla\CMS\Language\Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), \Joomla\CMS\Log\Log::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			\Joomla\Utilities\ArrayHelper::toInteger($cid);

			// Remove the items.
			if ($model->delete($cid))
			{
				$this->setMessage(\Joomla\CMS\Language\Text::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
			}
			else
			{
				$this->setMessage($model->getError());
			}
		}
		// Invoke the postDelete method to allow for the child class to access the model.
		$this->postDeleteHook($model, $cid);

		$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}

	/**
	 * Function that allows child controller access to model data
	 * after the item has been deleted.
	 *
	 * @param   MModelLegacy  $model  The data model object.
	 * @param   integer       $id     The validated data.
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function postDeleteHook(MModelBase $model, $id = null)
	{
	}

	/**
	 * Display is not supported by this controller.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link \Joomla\CMS\Filter\InputFilter::clean()}.
	 *
	 * @return  \Joomla\CMS\MVC\Controller\BaseController  A \Joomla\CMS\MVC\Controller\BaseController object to support chaining.
	 *
	 * @since   12.2
	 */
	public function display($cachable = false, $urlparams = array())
	{
		return $this;
	}

	/**
	 * Method to publish a list of items
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	public function publish()
	{
		// Check for request forgeries
		\Joomla\CMS\Session\Session::checkToken() or die(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		// Get items to publish from the request.
		$cid = \Joomla\CMS\Factory::getApplication()->input->get('cid', array(), 'array');
		$data = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);
		$task = $this->getTask();
		$value = \Joomla\Utilities\ArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid))
		{
			\Joomla\CMS\Log\Log::add(\Joomla\CMS\Language\Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), \Joomla\CMS\Log\Log::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			\Joomla\Utilities\ArrayHelper::toInteger($cid);

			// Publish the items.
			try
			{
				$model->publish($cid, $value);

				if ($value == 1)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_PUBLISHED';
				}
				elseif ($value == 0)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_UNPUBLISHED';
				}
				elseif ($value == 2)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_ARCHIVED';
				}
				else
				{
					$ntext = $this->text_prefix . '_N_ITEMS_TRASHED';
				}
				$this->setMessage(\Joomla\CMS\Language\Text::plural($ntext, count($cid)));
			}
			catch (Exception $e)
			{
				$this->setMessage(\Joomla\CMS\Language\Text::_('JLIB_DATABASE_ERROR_ANCESTOR_NODES_LOWER_STATE'), 'error');
			}

		}
		$extension = $this->input->get('extension');
		$extensionURL = ($extension) ? '&extension=' . $extension : '';
		$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));
	}

	/**
	 * Changes the order of one or more records.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   12.2
	 */
	public function reorder()
	{
		// Check for request forgeries.
		\Joomla\CMS\Session\Session::checkToken() or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		$ids = \Joomla\CMS\Factory::getApplication()->input->post->get('cid', array(), 'array');
		$inc = ($this->getTask() == 'orderup') ? -1 : 1;

		$model = $this->getModel();
		$return = $model->reorder($ids, $inc);
		if ($return === false)
		{
			// Reorder failed.
			$message = \Joomla\CMS\Language\Text::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
			$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message, 'error');
			return false;
		}
		else
		{
			// Reorder succeeded.
			$message = \Joomla\CMS\Language\Text::_('JLIB_APPLICATION_SUCCESS_ITEM_REORDERED');
			$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message);
			return true;
		}
	}

	/**
	 * Method to save the submitted ordering values for records.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   12.2
	 */
	public function saveorder()
	{
		// Check for request forgeries.
		\Joomla\CMS\Session\Session::checkToken() or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		// Get the input
		$pks = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		\Joomla\Utilities\ArrayHelper::toInteger($pks);
		\Joomla\Utilities\ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return === false)
		{
			// Reorder failed
			$message = \Joomla\CMS\Language\Text::sprintf('JLIB_APPLICATION_ERROR_REORDER_FAILED', $model->getError());
			$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message, 'error');
			return false;
		}
		else
		{
			// Reorder succeeded.
			$this->setMessage(\Joomla\CMS\Language\Text::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'));
			$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
			return true;
		}
	}

	/**
	 * Check in of one or more records.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   12.2
	 */
	public function checkin()
	{
		// Check for request forgeries.
		\Joomla\CMS\Session\Session::checkToken() or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		$ids = \Joomla\CMS\Factory::getApplication()->input->post->get('cid', array(), 'array');

		$model = $this->getModel();
		$return = $model->checkin($ids);
		if ($return === false)
		{
			// Checkin failed.
			$message = \Joomla\CMS\Language\Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError());
			$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message, 'error');
			return false;
		}
		else
		{
			// Checkin succeeded.
			$message = \Joomla\CMS\Language\Text::plural($this->text_prefix . '_N_ITEMS_CHECKED_IN', count($ids));
			$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false), $message);
			return true;
		}
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
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

		// Close the application
		\Joomla\CMS\Factory::getApplication()->close();
	}
}
