<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.table.table');

class JoomcckTableSales extends JTable
{

	public function __construct(&$_db)
	{
		parent::__construct('#__js_res_sales', 'id', $_db);
	}

	protected function _getAssetName(){
		$k = $this->_tbl_key;
		return 'com_joomcck.field.'.(int) $this->$k;
	}
	
	protected function _getAssetTitle()
	{
		return $this->label;
	}
	
	public function check()
	{
		$user = trim($this->user_id);
		
		$user_id = 0;
		if(preg_match("/^[0-9]*$/iU", $user))
		{
			$user_id = JFactory::getUser($user)->get('id');
		}
		
		$db = JFactory::getDbo();
		
		if(JMailHelper::isEmailAddress($user))
		{
			$db->setQuery("SELECT id FROM #__users WHERE email = ".$db->quote($user));
			$user_id = $db->loadResult();
		}
		
		if(!$user_id)
		{
			$db->setQuery("SELECT id FROM #__users WHERE username = ".$db->quote($user));
			$user_id = $db->loadResult();
		}
		
		if(!$user_id)
		{
			$db->setQuery("SELECT id FROM #__users WHERE name = ".$db->quote($user));
			$user_id = $db->loadResult();
		}
		
		if(!$user_id)
		{
			$this->setError('AJAX_USERNOTFOUND');
			return FALSE;
		}
		
		$u = JFactory::getUser();
		
		if($u->get('id') == $user_id)
		{
			//$this->setError('CBAYERSALERSAME');
			//return FALSE;
		}
		
		$this->user_id = $user_id;
		
		$record = ItemsStore::getRecord($this->record_id);
		
		if($record->user_id == $user_id)
		{
			$this->setError('CBAYERAUTHORSAME');
			return FALSE;
		}
		
		$fields_model = MModelBase::getInstance('Tfields', 'JoomcckModel');
		$fields = $fields_model->getRecordFields($record);
		$field_id = $item_name = 0;
		foreach ($fields AS $field)
		{
			$i = preg_match('/^pay.*/i', $field->type);
			if($i && $field->published)
			{
				$item_name = $field->params->get('params.item_name', false);
				$field_id = $field->id;
				break;
			}
		}
		
		if(!$field_id)
		{
			$this->setError('Commerce field not found');
			return FALSE;
		}
		
		$this->field_id = $field_id;
		
		$this->ctime = JFactory::getDate()->toSql();
		$this->mtime = JFactory::getDate()->toSql();
		
		$this->section_id = $record->section_id;
		$this->type_id = $record->type_id;
		
		if(!$this->currency)
		{
			$this->currency = 'USD';
		}
		if(!$this->gateway)
		{
			$this->gateway = 'CMANUAL';
		}
		if(!$this->saler_id)
		{
			$this->saler_id = JFactory::getUser()->get('id');
		}
		
		$this->name = $record->title;
		if(strstr($item_name, '%s'))
		{
			$this->name = JText::sprintf($item_name, $record->title);
		}
		
		return true;
	}
}
?>
