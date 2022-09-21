<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
jimport('mint.mvc.model.admin');

class JoomcckModelSale extends MModelAdmin
{
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_joomcck.sale', 'sale', array('control' => 'jform', 'load_data' => $loadData));
		if(empty($form))
		{
			return false;
		}
		return $form;
	}

	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState('com_joomcck.edit.sale.data', array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	protected function populateState($ordering = null, $direction = null)
	{
		$pk = JFactory::getApplication()->input->getInt('id');
		JFactory::getApplication()->setUserState('com_joomcck.sale.form.id',  $pk);
		$this->setState('com_joomcck.sale.form.id', $pk);

		$this->setState('layout', JFactory::getApplication()->input->getCmd('layout'));
	}

	public function getTable($name = '', $prefix = 'Table', $options = array()){
		return JTable::getInstance('Sales', 'JoomcckTable');
	}

}