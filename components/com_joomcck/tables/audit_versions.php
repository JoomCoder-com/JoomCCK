<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined( '_JEXEC' ) or die( 'Restricted access');

/**
* @package JCommerce
*/
class JoomcckTableAudit_versions extends JTable
{

	public function __construct(&$_db)
	{
		parent::__construct('#__js_res_audit_versions', 'id', $_db);
	}

	public function snapshot($record_id, $type)
	{
		$this->record_id = $record_id;
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->ctime = JFactory::getDate()->toSql();
		$this->user_id = JFactory::getUser()->get('id', 0);
		$this->username = JFactory::getUser()->get('username');

		$this->_db->setQuery('SELECT MAX(`version`) FROM #__js_res_audit_versions WHERE record_id = '.$this->record_id);
		$this->version = ($this->_db->loadResult() + 1);

		$this->_db->setQuery('SELECT * FROM #__js_res_record WHERE id = '.$this->record_id);
		$record = $this->_db->loadAssoc();

		if(!$record)
		{
			return;
		}

		$record['version'] = $this->version;
		$this->record_serial = json_encode($record);

		$this->_db->setQuery('SELECT * FROM #__js_res_record_values WHERE record_id = '.$this->record_id);
		$values = $this->_db->loadAssocList();
		settype($values, 'array');
		$this->values_serial = json_encode($values);

		$this->_db->setQuery('SELECT * FROM #__js_res_record_category WHERE record_id = '.$this->record_id);
		$values = $this->_db->loadAssocList();
		settype($values, 'array');
		$this->category_serial = json_encode($values);

		$this->_db->setQuery('SELECT * FROM #__js_res_tags_history WHERE record_id = '.$this->record_id);
		$values = $this->_db->loadAssocList();
		settype($values, 'array');
		$this->tags_serial = json_encode($values);

		$this->store();
		$this->type_id = $type->id;

		$this->_db->setQuery('SELECT COUNT(*) FROM #__js_res_audit_versions WHERE record_id = '.$this->record_id);

		if($type->params->get('audit.versioning_max') > 0 && $type->params->get('audit.versioning_max') < $this->_db->loadResult())
		{
			$this->_db->setQuery('SELECT MIN(version) FROM #__js_res_audit_versions WHERE record_id = '.$this->record_id);
			$v = $this->_db->loadResult();
			$this->_db->setQuery('DELETE FROM #__js_res_audit_versions WHERE record_id = '.$this->record_id.' AND version = '.$v);
			$this->_db->execute();
		}


		return $this->version;
	}
}
?>
