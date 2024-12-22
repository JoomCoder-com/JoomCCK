<?php

defined('_JEXEC') or die();

$app = \Joomla\CMS\Factory::getApplication();
$db = \Joomla\CMS\Factory::getDBO();

$type_id = $params->get('type_id');
$from = (int)$params->get('sect_from');
$to = (int)$params->get('sect_to');

if(!$type_id || !$from || !$to)
{
	$app->enqueueMessage('Error: not all fields are set!', 'error');

	return FALSE;
}

$count  = 0;

$sql = "SELECT * FROM `#__js_res_record` WHERE section_id = '{$from}' AND type_id = '{$type_id}'";
$db->setQuery($sql);
$list = $db->loadObjectList();

foreach ($list as $item)
{

	$updateRecord = new stdClass();
	$updateRecord->id = $item->id;
	$updateRecord->section_id = $to;

	\Joomla\CMS\Factory::getDbo()->updateObject('#__js_res_record', $updateRecord, 'id');

	$count++;
}


$app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('%d record(s) have been corrected.', $count));