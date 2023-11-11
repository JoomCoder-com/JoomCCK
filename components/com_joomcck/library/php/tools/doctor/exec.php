<?php
defined('_JEXEC') or die();

$app = \Joomla\CMS\Factory::getApplication();
$db  = \Joomla\CMS\Factory::getDBO();
$msg = array();

$sql = "SELECT user_id, ref_id, count(id) AS num
	FROM `#__js_res_subscribe`
	WHERE `type` = 'record'
	GROUP BY `user_id`, `ref_id`
	HAVING num > 1";
$db->setQuery($sql);
$list = $db->loadObjectList();

foreach($list AS $item)
{
	$db->setQuery("DELETE FROM `#__js_res_subscribe`
		WHERE user_id = {$item->user_id} AND ref_id = {$item->ref_id} AND `type` = 'record'");
	$db->execute();
}

if(count($list) > 0)
{
	$msg[] = count($list).' records in js_res_subscribe has been cleaned';
}

$sql = "SELECT user_id, record_id, count(id) AS num
	FROM `#__js_res_favorite`
	GROUP BY `user_id`, `record_id`
	HAVING num > 1";
$db->setQuery($sql);
$list = $db->loadObjectList();

foreach($list AS $item)
{
	$db->setQuery("DELETE FROM `#__js_res_favorite`
		WHERE user_id = {$item->user_id} AND record_id = {$item->record_id}");
	$db->execute();
}

if(count($list) > 0)
{
	$msg[] = count($list).' records in js_res_favorite has been cleaned';
}

if(count($msg))
{
	$app->enqueueMessage('<ul><li>'.implode('</li><li>', $msg).'</li></ul>');
}
else
{
	$app->enqueueMessage('No anomalies found');
}