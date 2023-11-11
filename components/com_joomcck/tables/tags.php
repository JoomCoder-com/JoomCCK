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

class JoomcckTableTags extends \Joomla\CMS\Table\Table
{
	public function __construct(&$_db)
	{
		parent::__construct('#__js_res_tags', 'id', $_db);
		
	
	}
	public function bind($data, $ignore = '')
	{
		if (!isset($data['ctime']))
		{
			$data['ctime'] = \Joomla\CMS\Factory::getDate()->toSql();
		}
		return parent::bind ( $data, $ignore );
	}
}
?>
