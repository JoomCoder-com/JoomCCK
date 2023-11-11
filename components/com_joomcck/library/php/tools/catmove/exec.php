<?php

defined('_JEXEC') or die();

$app = \Joomla\CMS\Factory::getApplication();
$db = \Joomla\CMS\Factory::getDBO();

$table_record = \Joomla\CMS\Table\Table::getInstance('Record', 'JoomcckTable');
$table_value = \Joomla\CMS\Table\Table::getInstance('Record_values', 'JoomcckTable');

$section_id = $params->get('section_id');
$from = (int)$params->get('cat_from');
$to = (int)$params->get('cat_to');

if(!$section_id || !$from || !$to)
{
	$app->enqueueMessage('Error: not all fields are set!', 'error');

	return FALSE;
}

$count  = 0;

$sql = "SELECT title FROM `#__js_res_categories` WHERE id = '{$to}'";
$db->setQuery($sql);
$cat = $db->loadObject();
$title = $cat->title;

$sql = "SELECT record_id FROM `#__js_res_record_category` WHERE section_id = '{$section_id}' AND catid = '{$from}'";
$db->setQuery($sql);
$list = $db->loadObjectList();

foreach ($list as $item)
{
	$table_record->load($item->record_id);
	if(!$table_record->id)
	{
		continue;
	}

	$cats = json_decode($table_record->categories, true);
	unset($cats[$from]);
	$cats[$to] = $title;

	$table_record->categories = json_encode($cats);
	$table_record->store();
	$table_record->reset();
	$table_record->id = NULL;

	$sql = "DELETE FROM  #__js_res_record_category WHERE record_id = '{$item->record_id}' AND catid = '{$from}'";
	$db->setQuery($sql);
	$db->execute();

	$sql = "INSERT INTO `#__js_res_record_category` (`id`, `catid`, `record_id`, `ordering`, `otime`, `section_id`, `published`, `access`)
VALUES (NULL, '{$to}', '{$item->record_id}',0, NOW(), '{$section_id}', 1, 1)";
	$db->setQuery($sql);
	$db->execute();

	$count++;
}


$app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('%d record(s) have been moved.', $count));