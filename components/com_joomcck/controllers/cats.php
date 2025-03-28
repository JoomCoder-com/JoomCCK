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


class JoomcckControllerCats extends MControllerAdmin
{
	public $model_prefix = 'JoomcckBModel';

	public function __construct($config = array())
	{
		parent::__construct($config);

		if(!$this->input)
		{
			$this->input = \Joomla\CMS\Factory::getApplication()->input;
		}
		$this->view_list .= '&section_id='.$this->input->getInt('section_id');
	}

	public function delete()
	{
		parent::delete();

		$section = \Joomla\CMS\Table\Table::getInstance('Section', 'JoomcckTable');
		$section->load($this->input->getInt('section_id'));

		$db = \Joomla\CMS\Factory::getDbo();
		$db->setQuery("SELECT COUNT(*) FROM #__js_res_categories WHERE section_id = ".$this->input->getInt('section_id'));

		$section->categories = $db->loadResult();
		$section->store();
	}

	function getModel($name = 'Cat', $prefix = 'JoomcckModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Rebuild the nested set tree.
	 *
	 * @return	bool	False on failure or error, true on success.
	 * @since	1.6
	 */
	public function rebuild()
	{
		$this->input->checkToken() or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		$section = $this->input->getInt('section_id');
		$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option=com_joomcck&view=categories&section_id='.$section, false));

		// Initialise variables.
		$model = $this->getModel();

		if ($model->rebuild()) {
			// Rebuild succeeded.
			$this->setMessage(\Joomla\CMS\Language\Text::_('COM_JOOMCCK_REBUILD_SUCCESS'));
			return true;
		} else {
			// Rebuild failed.
			$this->setMessage(\Joomla\CMS\Language\Text::_('COM_JOOMCCK_REBUILD_FAILURE'));
			return false;
		}
	}

	/**
	 * Save the manual order inputs from the categories list page.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function saveorder()
	{
		$this->input->checkToken() or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		// Get the arrays from the Request
		$order	= $this->input->get('order', array(), 'array');
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		// Make sure something has changed
		if (!($order === $originalOrder)) {
			parent::saveorder();
		} else {
			// Nothing to reorder
			$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
			return true;
		}
	}

	public function saveOrderAjax()
	{
		\Joomla\CMS\Session\Session::checkToken() or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		// Get the arrays from the Request
		$pks   = $this->input->post->get('cid', null, 'array');
		$order = $this->input->post->get('order', null, 'array');
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		// Make sure something has changed
		if (!($order === $originalOrder)) {
			// Get the model
			$model = $this->getModel();
			// Save the ordering
			$return = $model->saveorder($pks, $order);
			if ($return)
			{
				echo "1";
			}
		}
		// Close the application
		\Joomla\CMS\Factory::getApplication()->close();

	}

	public function close()
	{
		$this->setRedirect(\Joomla\CMS\Router\Route::_('index.php?option='.$this->option.'&view=sections', false));
	}
}