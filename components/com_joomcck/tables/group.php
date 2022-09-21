<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class JoomcckTableGroup extends JTable
{

	public function __construct(&$_db)
	{
		parent::__construct('#__js_res_fields_group', 'id', $_db);
	}

	public function delete($pk = null)
	{
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		if ($pk)
		{
			$this->_db->setQuery("UPDATE #__js_res_fields SET group_id = 0 WHERE group_id = $pk");
			$this->_db->query();
		}

		parent::delete($pk);
	}

}
?>
