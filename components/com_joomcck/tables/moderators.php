<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

// Include library dependencies
jimport('joomla.filter.input');

class JoomcckTableModerators extends JTable
{
	public function __construct(&$_db)
	{
		parent::__construct('#__js_res_moderators', 'id', $_db);
	}
	
	public function bind($array, $ignore = '')
	{

		$array['params'] = (array) $array['params'];

		if(isset($array['allow']))
		{
			@$array['params']['allow'] = @$array['allow'];
		}
		if(isset($array['category_limit_mode']))
		{
			@$array['params']['category_limit_mode'] = @$array['category_limit_mode'];
		}
		if(isset($array['category']))
		{
			@$array['params']['category'] = @$array['category'];
		}
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry;
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}
	
		return parent::bind($array, $ignore);
	}
	
	public function check()
	{
		if ($this->ctime == '' || $this->ctime == '0000-00-00 00:00:00' || is_null($this->ctime))
		{
			$this->ctime = JFactory::getDate()->toSql();
		}
		$this->_db->setQuery("SELECT id FROM #__js_res_moderators WHERE user_id = {$this->user_id} AND section_id = {$this->section_id}");
		$result = $this->_db->loadResult();
		if(!$this->id && $result)
		{
			$this->setError(JText::sprintf('C_MSG_MODEREXISTS', $result));
			return false;
		}
		
		return parent::check();
	}
}