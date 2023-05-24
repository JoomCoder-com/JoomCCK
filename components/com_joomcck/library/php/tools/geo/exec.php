<?php
defined('_JEXEC') or die();

$app = JFactory::getApplication();

if(!$params->get('field_id_geo'))
{
	$app->enqueueMessage('Field is not selected!', 'error');

	return FALSE;
}

$db = JFactory::getDbo();
$db->setQuery("SELECT * FROM `#__js_res_fields` WHERE id = " . $params->get('field_id_geo'));
$field = $db->loadObject();

if(empty($field->id))
{
	$app->enqueueMessage('Field not found!', 'error');

	return FALSE;
}

$db->setQuery(sprintf("UPDATE `#__js_res_fields` SET field_type = 'geo2', `key` = 'k%s' WHERE id = %d", md5($field->label . '-geo2'), $field->id));
$db->execute();

$db->setQuery("UPDATE `#__js_res_record_values`
			SET	value_index = REPLACE(value_index,'1','')
			WHERE field_id = " . $field->id);
$db->execute();

$db->setQuery(sprintf("UPDATE `#__js_res_record_values` SET
			value_index = CONCAT(value_index, '1'),
			field_type = 'geo2', `field_key` = 'k%s' WHERE field_id = %d", // AND field_type = 'geo'",
	md5($field->label . '-geo2'), $field->id));
$db->execute();

$db->setQuery("SELECT `id`, `fields` FROM `#__js_res_record` WHERE id IN(SELECT r.record_id FROM `#__js_res_record_values` AS r WHERE r.field_id = {$field->id})");
$list = $db->loadAssocList('id', 'fields');

$table = JTable::getInstance('Record', 'JoomcckTable');

foreach($list as $id => $fields)
{
	$value = json_decode($fields, TRUE);
	if(empty($value[$field->id]))
	{
		continue;
	}

	if(!empty($value[$field->id][1]))
	{
		continue;
	}

	$value[$field->id] = array("1" => $value[$field->id]);

	$table->load($id);
	$table->fields = json_encode($value);
	$table->store();
	$table->reset();
	$table->id = NULL;
}
$app->enqueueMessage('Successfully updated all articles!');