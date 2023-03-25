<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class JoomcckTablePacks_sections extends JTable
{
	public function __construct(&$_db)
	{
		parent::__construct('#__js_res_packs_sections', 'id', $_db);
		
	
	}
	
	public function bind($data, $ignore = '')
	{
		if (!isset($data['ctime']))
		{
			$data['ctime'] = JFactory::getDate()->toSql();
		}
		
		$data['mtime'] = JFactory::getDate()->toSql();
		
		$params = \Joomla\CMS\Factory::getApplication()->input->post->get('jform', [], 'array');
		$params_types = \Joomla\CMS\Factory::getApplication()->input->post->get('params', [], 'array');
		if(isset($params['params']))
		{
			$result = array_merge($params['params'], $params_types);
			$registry = new JRegistry();
			$registry->loadArray($result);
			$data['params'] = (string)$registry;
		}
		
		return parent::bind($data, $ignore);
	}
	
}
?>
