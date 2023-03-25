<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Application\ApplicationHelper;

defined('_JEXEC') or die('Restricted access');

class JoomcckTablePacks extends JTable
{
	public function __construct(&$_db)
	{
		parent::__construct('#__js_res_packs', 'id', $_db);
	}
	
	public function bind($data, $ignore = '')
	{
		return parent::bind ( $data, $ignore );
	}
	
	public function check()
	{
		if ($this->ctime == '' || $this->ctime == '0000-00-00 00:00:00')
		{
			$this->ctime = JFactory::getDate()->toSql();
		}
		
		$this->mtime = JFactory::getDate()->toSql();
		
		if (!$this->key)
		{
			$this->key = 'pack'. ApplicationHelper::getHash($this->name);
		}
		
		return parent::check();
	}
}
?>
