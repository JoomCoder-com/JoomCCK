<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
jimport('mint.mvc.model.admin');

require_once JPATH_ROOT . '/components/com_joomcck/library/php/helpers/helper.php';

class JoomcckModelAuditversion extends MModelAdmin
{
	protected $_context = 'com_joomcck.version';

	public function getTable($type = 'Audit_versions', $prefix = 'JoomcckTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function &getItem($version = 0, $record_id = 0)
	{
		// Initialise variables.
		$user = JFactory::getUser();

		try
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(TRUE);

			$query->select('av.*');
			$query->from('#__js_res_audit_versions AS av');
			$query->where('av.version = ' . (int)$version);
			$query->where('av.record_id = ' . (int)$record_id);

			$db->setQuery($query);

			$data = $db->loadObject();

			if($error = $db->getErrorMsg())
			{
				throw new Exception($error);
			}


			if(empty($data))
			{
				return JError::raiseError(404, JText::_('CERR_VERNOTFOUND') . ': ' . $version);
			}

			$data->record   = json_decode($data->record_serial);
			$data->category = json_decode($data->category_serial);
			$data->tags     = json_decode($data->tags_serial);
		}
		catch(JException $e)
		{
			if($e->getCode() == 404)
			{
				// Need to go thru the error handler to allow Redirect to work.
				JError::raiseError(404, $e->getMessage());
			}
			else
			{
				$this->setError($e);
				$data = FALSE;
			}
		}

		return $data;
	}

	public function getForm($data = array(), $loadData = TRUE)
	{
		// TODO Auto-generated method stub
	}


}
