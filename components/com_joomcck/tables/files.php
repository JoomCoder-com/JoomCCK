<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined( '_JEXEC' ) or die( 'Restricted access');

jimport('joomla.table.table');
jimport('legacy.access.rules');


class  JoomcckTableFiles extends \Joomla\CMS\Table\Table
{

	public function __construct( &$_db ) {
		parent::__construct('#__js_res_files', 'id', $_db);
	}

	public function check()
	{
		$user = \Joomla\CMS\Factory::getApplication()->getIdentity();
		$this->user_id = $user->get('id');

		$this->ctime = \Joomla\CMS\Factory::getDate()->toSql();
		$this->ip = $_SERVER['REMOTE_ADDR'];

		return true;
	}

	public function getFiles($ids, $key = 'id')
	{
		$query	= $this->_db->getQuery(true);

		$in = "'".implode("', '", $ids)."'";
		$query->select('*');
		$query->from('#__js_res_files');
		$query->where($key." IN ({$in})");
		
		$this->_db->setQuery($query);

		return $this->_db->loadObjectList();
	}
	public function prepareSave($files)
	{
		if(!$files) return;

		settype($files, 'array');

		
		$this->_db->setQuery("SELECT `id`, `filename`, `realname`, `ext`, `size`, `title`, `description`, `width`, `height`, `fullpath`, `params`, `default`
			FROM `#__js_res_files`
			WHERE filename IN('".implode("','", $files)."')");

		return json_encode($this->_db->loadAssocList());
	}
	public function markSaved($files, $record, $field_id = 0)
	{
		if(!$files) return;
		$record_id = (int)$record['id'];
		
		$this->_db->setQuery("UPDATE #__js_res_files SET saved = 1, record_id = {$record_id}, field_id = {$field_id} WHERE id IN('".implode("','", $files)."')");
		$this->_db->execute();
	}

}