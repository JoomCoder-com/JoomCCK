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


class JoomcckControllerCat extends MControllerForm
{

	protected $section;
	public $model_prefix = 'JoomcckBModel';


	public function getModel($name = '', $prefix = 'JoomcckModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function postSaveHook(MModelBase $model, $validData = array())
	{
		$app = \Joomla\CMS\Factory::getApplication();
		$section = \Joomla\CMS\Table\Table::getInstance('Section', 'JoomcckTable');
		$section->load($app->input->getInt('section_id'));

		$id = (int)$model->getState($this->context.'.id');

		$db = \Joomla\CMS\Factory::getDbo();

		$query = $db->getQuery(true)
			->select($db->quoteName(['id', 'categories']))
			->from($db->quoteName('#__js_res_record'))
			->where($db->quoteName('id') . ' IN (SELECT record_id FROM ' . $db->quoteName('#__js_res_record_category') . ' WHERE catid = ' . (int)$id . ')');
		$db->setQuery($query);
		$list = $db->loadObjectList();

		foreach ($list as $key => $item)
		{
			$categories = json_decode($item->categories, true);
			$categories[$id] = $validData['title'];
			$query = $db->getQuery(true)
				->update($db->quoteName('#__js_res_record'))
				->set($db->quoteName('categories') . ' = ' . $db->quote(json_encode($categories)))
				->where($db->quoteName('id') . ' = ' . (int)$item->id);
			$db->setQuery($query);
			$db->execute();
		}

		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__js_res_categories'))
			->where($db->quoteName('section_id') . ' = ' . (int)$section->id);
		$db->setQuery($query);
		$section->categories = $db->loadResult();
		$section->store();

		$published = (int)$validData['published'];
		$access = (int)$validData['access'];

		$query = $db->getQuery(true)
			->update($db->quoteName('#__js_res_record_category'))
			->set($db->quoteName('published') . ' = ' . $published)
			->set($db->quoteName('access') . ' = ' . $access)
			->where($db->quoteName('catid') . ' = ' . (int)$id);
		$db->setQuery($query)->execute();

		$query = $db->getQuery(true)
			->update($db->quoteName('#__js_res_categories'))
			->set($db->quoteName('published') . ' = ' . $published)
			->where($db->quoteName('id') . ' = ' . (int)$id);
		$db->setQuery($query)->execute();
	}
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		MController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$app = \Joomla\CMS\Factory::getApplication();
		if(!$this->input)
		{
			$this->input = $app->input;
		}
		if (empty($this->section)) {
			$this->section = $app->input->getInt('section_id');
		}
	}

	/**
	 * Method to check if you can add a new record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param	array	An array of input data.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	protected function allowAdd($data = array())
	{
		return \Joomla\CMS\Factory::getApplication()->getIdentity()->authorise('core.create', 'com_joomcck.category');
	}

	/**
	 * Method to check if you can edit a record.
	 *
	 * Extended classes can override this if necessary.
	 *
	 * @param	array	An array of input data.
	 * @param	string	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$user		= \Joomla\CMS\Factory::getApplication()->getIdentity();
		$userId		= $user->get('id');

		// Check general edit permission first.
		if ($user->authorise('core.edit', 'com_joomcck.category')) {
			return true;
		}

		// Check specific edit permission.
		if ($user->authorise('core.edit', 'com_joomcck.category'.$recordId)) {
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($user->authorise('core.edit.own', 'com_joomcck.category'.$recordId) || $user->authorise('core.edit.own', 'com_joomcck.category')) {
			// Now test the owner is the user.
			$ownerId	= (int) isset($data['created_user_id']) ? $data['created_user_id'] : 0;
			if (empty($ownerId) && $recordId) {
				// Need to do a lookup from the model.
				$record		= $this->getModel()->getItem($recordId);

				if (empty($record)) {
					return false;
				}

				$ownerId = $record->created_user_id;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId) {
				return true;
			}
		}
		return false;
	 }

	/**
	 * Method to run batch opterations.
	 *
	 * @return	void
	 */
	public function batch($model)
	{
		$this->input->checkToken() or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		// Set the model
		$model	= $this->getModel('Category');

		// Preset the redirect
		$this->setRedirect('index.php?option=com_joomcck&view=categories&section_id='.$this->section);

		return parent::batch($model);
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param	int		$recordId	The primary key id for the item.
	 *
	 * @return	string	The arguments to append to the redirect URL.
	 * @since	1.6
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&section_id='.$this->section;

		return $append;
	}

	/**
	 * Gets the URL arguments to append to a list redirect.
	 *
	 * @return	string	The arguments to append to the redirect URL.
	 * @since	1.6
	 */
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$append .= '&section_id='.$this->section;

		return $append;
	}
}