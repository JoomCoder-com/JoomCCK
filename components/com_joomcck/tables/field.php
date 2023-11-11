<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

jimport('legacy.access.rules');

class JoomcckTableField extends \Joomla\CMS\Table\Table
{

	public function __construct(&$_db )
	{
		parent::__construct('#__js_res_fields', 'id', $_db);
	}

	public function bind($array, $ignore = '')
	{
		$params =\Joomla\CMS\Factory::getApplication()->input->post->get('params', array(), 'array');
		if($params)
		{
			$registry = new \Joomla\Registry\Registry();
			$registry->loadArray($params);
			$this->filter = $registry->get('params.filter_enable', 0);
			$array['params'] = (string)$registry;
		}

		if (isset($array['rules']) && is_array($array['rules'])) {
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		$this->key = 'k'.md5($array['label'].'-'.$array['field_type']);


		return parent::bind($array, $ignore);
	}

	public function delete($pk = null)
	{
		if(parent::delete($pk))
		{
			$sql = "DELETE FROM #__js_res_record_values WHERE field_id = ".($pk ? $pk : $this->id);
			$this->_db->setQuery($sql);
			$this->_db->execute();

			return true;
		}

		return false;
	}
	public function check()
	{
		if(trim($this->label) == '')
		{
			$this->setError(\Joomla\CMS\Language\Text::_('CNOLABEL'));
			return false;
		}

		if(trim($this->user_id) == '') {
			$this->user_id = (int)\Joomla\CMS\Factory::getApplication()->getIdentity()->get('id');
		}

		settype($this->ordering, 'integer');

		return true;
	}

	protected function _getAssetName(){
		$k = $this->_tbl_key;
		return 'com_joomcck.field.'.(int) $this->$k;
	}


	protected function _getAssetTitle()
	{
		return $this->label;
	}

}
?>
