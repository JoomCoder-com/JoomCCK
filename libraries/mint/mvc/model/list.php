<?php
/**
 * @package     Joomla.Legacy
 * @subpackage  Model
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Pagination\Pagination;

defined('JPATH_PLATFORM') or die;

jimport('mint.mvc.model.base');

/**
 * Model class for handling lists of items.
 *
 * @package     Joomla.Legacy
 * @subpackage  Model
 * @since       12.2
 */
class MModelList extends MModelBase
{
	/**
	 * Internal memory based cache array of data.
	 *
	 * @var    array
	 * @since  12.2
	 */
	protected $cache = array();

	/**
	 * Context string for the model type.  This is used to handle uniqueness
	 * when dealing with the getStoreId() method and caching data structures.
	 *
	 * @var    string
	 * @since  12.2
	 */
	protected $context = null;

	/**
	 * Valid filter fields or ordering.
	 *
	 * @var    array
	 * @since  12.2
	 */
	protected $filter_fields = array();

	/**
	 * An internal cache for the last query used.
	 *
	 * @var    \Joomla\Database\DatabaseQuery
	 * @since  12.2
	 */
	protected $query = array();

	/**
	 * Name of the filter form to load
	 *
	 * @var    string
	 * @since  3.2
	 */
	protected $filterFormName = null;

	/**
	 * Associated HTML form
	 *
	 * @var  string
	 */
	protected $htmlFormName = 'adminForm';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \Joomla\CMS\MVC\Model\BaseDatabaseModel
	 * @since   12.2
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Add the ordering filtering fields white list.
		if (isset($config['filter_fields']))
		{
			$this->filter_fields = $config['filter_fields'];
		}

		// Guess the context as Option.ModelName.
		if (empty($this->context))
		{
			$this->context = strtolower($this->option . '.' . $this->getName());
		}
	}

	/**
	 * Method to cache the last query constructed.
	 *
	 * This method ensures that the query is constructed only once for a given state of the model.
	 *
	 * @return  \Joomla\Database\DatabaseQuery  A \Joomla\Database\DatabaseQuery object
	 *
	 * @since   12.2
	 */
	protected function _getListQuery()
	{
		// Capture the last store id used.
		static $lastStoreId;

		// Compute the current store id.
		$currentStoreId = $this->getStoreId();

		// If the last store id is different from the current, refresh the query.
		if ($lastStoreId != $currentStoreId || empty($this->query))
		{
			$lastStoreId = $currentStoreId;
			$this->query = $this->getListQuery();
		}

		return $this->query;
	}

	/**
	 * Function to get the active filters
	 *
	 * @return  array  Associative array in the format: array('filter_published' => 0)
	 *
	 * @since   3.2
	 */
	public function getActiveFilters()
	{
		$activeFilters = array();

		if (!empty($this->filter_fields))
		{
			foreach ($this->filter_fields as $filter)
			{
				$filterName = 'filter.' . $filter;

				if (property_exists($this->state, $filterName) && (!empty($this->state->{$filterName}) || is_numeric($this->state->{$filterName})))
				{
					$activeFilters[$filter] = $this->state->get($filterName);
				}
			}
		}

		return $activeFilters;
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the list items.
		$query = $this->_getListQuery();

		try
		{


			$items = $this->_getList($query, $this->getStart(), $this->getState('list.limit'));
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	/**
	 * Method to get a \Joomla\Database\DatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  \Joomla\Database\DatabaseQuery   A \Joomla\Database\DatabaseQuery object to retrieve the data set.
	 *
	 * @since   12.2
	 */
	protected function getListQuery()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		return $query;
	}

	/**
	 * Method to get a \Joomla\CMS\Pagination\Pagination object for the data set.
	 *
	 * @return  \Joomla\CMS\Pagination\Pagination  A \Joomla\CMS\Pagination\Pagination object for the data set.
	 *
	 * @since   12.2
	 */
	public function getPagination()
	{
		// Get a storage key.
		$store = $this->getStoreId('getPagination');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Create the pagination object.
		$limit = (int) $this->getState('list.limit') - (int) $this->getState('list.links');
		$page = new \Joomla\CMS\Pagination\Pagination($this->getTotal(), $this->getStart(), $limit);

		// Get the current input to retrieve important parameters
		$app = \Joomla\CMS\Factory::getApplication();
		$input = $app->input;

		// Get section_id and cat_id from input
		$sectionId = $input->get('section_id', null);
		$catId = $input->get('cat_id', null);

		// If section_id and cat_id exist, add them to the pagination parameters
		if ($sectionId) {
			$page->setAdditionalUrlParam('section_id', $sectionId);
		}

		if ($catId) {
			$page->setAdditionalUrlParam('cat_id', $catId);
		}

		// Add the object to the internal cache.
		$this->cache[$store] = $page;

		return $this->cache[$store];
	}

	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   12.2
	 */
	protected function getStoreId($id = '')
	{
		// Add the list state to the store id.
		$id .= ':' . $this->getState('list.start');
		$id .= ':' . $this->getState('list.limit');
		$id .= ':' . $this->getState('list.ordering');
		$id .= ':' . $this->getState('list.direction');

		return md5($this->context . ':' . $id);
	}

	/**
	 * Method to get the total number of items for the data set.
	 *
	 * @return  integer  The total number of items available in the data set.
	 *
	 * @since   12.2
	 */
	public function getTotal()
	{
		// Get a storage key.
		$store = $this->getStoreId('getTotal');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		// Load the total.
		$query = $this->_getListQuery();

		try
		{
			$total = (int) $this->_getListCount($query);
		}
		catch (RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// Add the total to the internal cache.
		$this->cache[$store] = $total;

		return $this->cache[$store];
	}

	/**
	 * Method to get the starting number of items for the data set.
	 *
	 * @return  integer  The starting number of items available in the data set.
	 *
	 * @since   12.2
	 */
	public function getStart()
	{
		$store = $this->getStoreId('getstart');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		$start = $this->getState('list.start');
		$limit = $this->getState('list.limit');
		$total = $this->getTotal();

		if ($start > $total - $limit)
		{
			$start = max(0, (int) (ceil($total / $limit) - 1) * $limit);
		}

		// Add the total to the internal cache.
		$this->cache[$store] = $start;

		return $this->cache[$store];
	}

	/**
	 * Get the filter form
	 *
	 * @param   array    $data      data
	 * @param   boolean  $loadData  load current data
	 *
	 * @return  JForm/false  the \Joomla\CMS\Form\Form object or false
	 *
	 * @since   3.2
	 */
	public function getFilterForm($data = array(), $loadData = true)
	{
		$form = null;

		// Try to locate the filter form automatically. Example: ContentModelArticles => "filter_articles"
		if (empty($this->filterFormName))
		{
			$classNameParts = explode('Model', get_called_class());

			if (count($classNameParts) == 2)
			{
				$this->filterFormName = 'filter_' . strtolower($classNameParts[1]);
			}
		}

		if (!empty($this->filterFormName))
		{
			// Get the form.
			$form = $this->loadForm($this->context . '.filter', $this->filterFormName, array('control' => '', 'load_data' => $loadData));
		}

		return $form;
	}

	/**
	 * Method to get a form object.
	 *
	 * @param   string   $name     The name of the form.
	 * @param   string   $source   The form source. Can be XML string if file flag is set to false.
	 * @param   array    $options  Optional array of options for the form creation.
	 * @param   boolean  $clear    Optional argument to force load a new form.
	 * @param   string   $xpath    An optional xpath to search for the fields.
	 *
	 * @return  mixed  \Joomla\CMS\Form\Form object on success, False on error.
	 *
	 * @see     JForm
	 * @since   3.2
	 */
	protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = false)
	{
		// Handle the optional arguments.
		$options['control'] = \Joomla\Utilities\ArrayHelper::getValue($options, 'control', false);

		// Create a signature hash.
		$hash = md5($source . serialize($options));

		// Check if we can use a previously loaded form.
		if (isset($this->_forms[$hash]) && !$clear)
		{
			return $this->_forms[$hash];
		}

		// Get the form.
		\Joomla\CMS\Form\Form::addFormPath(JPATH_COMPONENT . '/models/forms');
		\Joomla\CMS\Form\Form::addFieldPath(JPATH_COMPONENT . '/models/fields');

		try
		{
			$form = \Joomla\CMS\Form\Form::getInstance($name, $source, $options, false, $xpath);

			if (isset($options['load_data']) && $options['load_data'])
			{
				// Get the data for the form.
				$data = $this->loadFormData();
			}
			else
			{
				$data = array();
			}

			// Allow for additional modification of the form, and events to be triggered.
			// We pass the data because plugins may require it.
			$this->preprocessForm($form, $data);

			// Load the data into the form after the plugins have operated.
			$form->bind($data);
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		// Store the form for later.
		$this->_forms[$hash] = $form;

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return    mixed    The data for the form.
	 *
	 * @since    3.2
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState($this->context, new stdClass);

		// Pre-fill the list options
		if (!property_exists($data, 'list'))
		{
			$data->list = array(
				'direction' => $this->state->{'list.direction'},
				'limit'     => $this->state->{'list.limit'},
				'ordering'  => $this->state->{'list.ordering'},
				'start'     => $this->state->{'list.start'}
			);
		}

		return $data;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   12.2
	 */
	protected function populateState($ordering = null, $direction = null)
	{


		// If the context is set, assume that stateful lists are used.
		if ($this->context)
		{
			$app = Factory::getApplication();

			// Receive & set filters
			if ($filters = $app->getUserStateFromRequest($this->context . '.filter', 'filter', array(), 'array'))
			{
				foreach ($filters as $name => $value)
				{
					$this->setState('filter.' . $name, $value);
				}
			}


			$limit = 0;


			$currentView = Factory::getApplication()->getInput()->get('view', '');

			$itemsViews = ['records'];

			$paramName = !in_array($currentView, $itemsViews) ? 'list_limit' : 'list_limit_items';
			$listLimit = ComponentHelper::getParams('com_joomcck')->get($paramName, 20);

			$limit = $app->getUserStateFromRequest($this->context . '.list.limit', 'limit', $listLimit, 'uint');

			$this->setState('list.limit', $limit);


			// Check if the ordering field is in the white list, otherwise use the incoming value.
			$value = $app->getUserStateFromRequest($this->context . '.ordercol', 'filter_order', $ordering);


			if (!in_array($value, $this->filter_fields))
			{
				$value = $ordering;
				$app->setUserState($this->context . '.ordercol', $value);
			}

			$this->setState('list.ordering', $value);

			// Check if the ordering direction is valid, otherwise use the incoming value.
			$value = $app->getUserStateFromRequest($this->context . '.orderdirn', 'filter_order_Dir', 'DESC');

			if (!in_array(strtoupper($value), array('ASC', 'DESC', '')))
			{
				$value = $direction;
				$app->setUserState($this->context . '.orderdirn', $value);
			}

			$this->setState('list.direction', $value);

			// Support old ordering field
			$oldOrdering = $app->input->get('filter_order');

			if (!empty($oldOrdering) && in_array($value, $this->filter_fields))
			{
				$this->setState('list.ordering', $oldOrdering);
			}

			// Support old direction field
			$oldDirection = $app->input->get('filter_order_Dir');

			if (!empty($oldDirection) && in_array(strtoupper($oldDirection), array('ASC', 'DESC', '')))
			{
				$this->setState('list.direction', $oldDirection);
			}

			$value      = $app->getUserStateFromRequest($this->context . '.limitstart', 'limitstart', 0);
			$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
			$this->setState('list.start', $limitstart);
		}
		else
		{
			$this->setState('list.start', 0);
			$this->setState('list.limit', 0);
		}
	}

	/**
	 * Method to allow derived classes to preprocess the form.
	 *
	 * @param   \Joomla\CMS\Form\Form  $form   A \Joomla\CMS\Form\Form object.
	 * @param   mixed                  $data   The data expected for the form.
	 * @param   string                 $group  The name of the plugin group to import (defaults to "content").
	 *
	 * @return  void
	 *
	 * @throws  Exception if there is an error in the form event.
	 * @since   3.2
	 */
	protected function preprocessForm(\Joomla\CMS\Form\Form $form, $data, $group = 'content')
	{
		// Import the appropriate plugin group.
		\Joomla\CMS\Plugin\PluginHelper::importPlugin($group);

		// Get the dispatcher.
		$dispatcher = Factory::getApplication();

		// Trigger the form preparation event.
		$results = $dispatcher->triggerEvent('onContentPrepareForm', array($form, $data));

		// Check for errors encountered while preparing the form.
		if (count($results) && in_array(false, $results, true))
		{
			// Get the last error.
			$error = $dispatcher->getError();

			if (!($error instanceof Exception))
			{
				throw new Exception($error);
			}
		}
	}

	/**
	 * Gets the value of a user state variable and sets it in the session
	 *
	 * This is the same as the method in JApplication except that this also can optionally
	 * force you back to the first page when a filter has changed
	 *
	 * @param   string   $key        The key of the user state variable.
	 * @param   string   $request    The name of the variable passed in a request.
	 * @param   string   $default    The default value for the variable if not found. Optional.
	 * @param   string   $type       Filter for the variable, for valid values see {@link \Joomla\CMS\Filter\InputFilter::clean()}. Optional.
	 * @param   boolean  $resetPage  If true, the limitstart in request is set to zero
	 *
	 * @return  The request user state.
	 *
	 * @since   12.2
	 */
	public function getUserStateFromRequest($key, $request, $default = null, $type = 'none', $resetPage = true)
	{
		$app       = Factory::getApplication();
		$input     = $app->input;
		$old_state = $app->getUserState($key);
		$cur_state = (!is_null($old_state)) ? $old_state : $default;
		$new_state = $input->get($request, null, $type);

		if (($cur_state != $new_state) && ($resetPage))
		{
			$input->set('limitstart', 0);
		}

		// Save the new value only if it is set in this request.
		if ($new_state !== null)
		{
			$app->setUserState($key, $new_state);
		}
		else
		{
			$new_state = $cur_state;
		}

		return $new_state;
	}
}
