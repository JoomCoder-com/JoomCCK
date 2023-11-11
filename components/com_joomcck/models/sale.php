<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
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
		$data = \Joomla\CMS\Factory::getApplication()->getUserState('com_joomcck.edit.sale.data', array());

		if(empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	protected function populateState($ordering = null, $direction = null)
	{
		$pk = \Joomla\CMS\Factory::getApplication()->input->getInt('id');
		\Joomla\CMS\Factory::getApplication()->setUserState('com_joomcck.sale.form.id',  $pk);
		$this->setState('com_joomcck.sale.form.id', $pk);

		$this->setState('layout', \Joomla\CMS\Factory::getApplication()->input->getCmd('layout'));
	}

	public function getTable($name = '', $prefix = 'Table', $options = array()){
		return \Joomla\CMS\Table\Table::getInstance('Sales', 'JoomcckTable');
	}

}