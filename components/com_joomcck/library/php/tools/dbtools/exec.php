<?php

defined('_JEXEC') or die();

use Joomla\CMS\Factory;

$app = JFactory::getApplication();

$tables = $params->get('db_tables');
settype($tables[0], 'array');
$tables     = $tables[0];
$table_line = implode(', ', $tables);

if(!$tables)
{

	Factory::getApplication()->enqueueMessage(JText::_('No table selected'),'warning');

	return;
}
$db = JFactory::getDBO();

switch($params->get('db_action'))
{
	case 1:
		$sql = "OPTIMIZE TABLE " . $table_line;
		$db->setQuery($sql);
		$db->execute();
		$app->enqueueMessage(JText::_('Tables Optimized') . ': ' . $table_line);
		break;
	case 2:
		$sql = "REPAIR TABLE " . $table_line;
		$db->setQuery($sql);
		$db->execute();
		$app->enqueueMessage(JText::_('Tables Repaired') . ': ' . $table_line);
		break;
	case 3:
		$sql = "ANALYZE TABLE " . $table_line;
		$db->setQuery($sql);
		$app->enqueueMessage(JText::_('Tables Analized'));
		$res = $db->loadObjectList();
		if($res)
		{
			echo '<table class="adminlist"><thead><TR><th>Table</th><th>Op</th><th>Message Type</th><th>Message</th></tr></thead>';
			foreach($res AS $r)
			{
				echo sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>', $r->Table, $r->Op, $r->Msg_type, $r->Msg_text);
			}
			echo '</table><br />';
		}
		break;
	case 5:
		$sql = "CHECK TABLE " . $table_line;
		$db->setQuery($sql);
		$app->enqueueMessage(JText::_('Tables Checked'));
		$res = $db->loadObjectList();
		if($res)
		{
			echo '<table class="adminlist"><thead><TR><th>Table</th><th>Op</th><th>Message Type</th><th>Message</th></tr></thead>';
			foreach($res AS $r)
			{
				echo sprintf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>', $r->Table, $r->Op, $r->Msg_type, $r->Msg_text);
			}
			echo '</table><br />';
		}
		break;

	case 4:
		foreach($tables AS $table)
		{
			$sql = "TRUNCATE TABLE " . $table;
			$db->setQuery($sql);
			$db->execute();
			$app->enqueueMessage(JText::_('TRUNCATE Table') . ': ' . $table);
		}
		break;
	case 6: // update database structure

		// require install file
		require_once JPATH_ADMINISTRATOR.'/components/com_joomcck/install.php';

		$install = new com_joomcckInstallerScript();
		$install->_updateTables();
		$app->enqueueMessage('Database structure updated!','success');


		break;
}