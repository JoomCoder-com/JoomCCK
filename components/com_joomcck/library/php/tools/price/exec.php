<?php

defined('_JEXEC') or die();

$app = \Joomla\CMS\Factory::getApplication();
$file = new \Joomla\CMS\Input\Files();
$db = \Joomla\CMS\Factory::getDBO();
$table_record = \Joomla\CMS\Table\Table::getInstance('Record', 'JoomcckTable');
$table_value = \Joomla\CMS\Table\Table::getInstance('Record_values', 'JoomcckTable');

$file = $file->get('price', FALSE);

if(empty($file['name']))
{
	$app->enqueueMessage('Error: file not uploaded!', 'error');

	return FALSE;
}

if($file['error'] > 0 OR $file['size'] == 0)
{
	$app->enqueueMessage('Error: uploading file!', 'error');

	return FALSE;
}

if(strtolower(pathinfo($file['name'],PATHINFO_EXTENSION)) != 'json')
{
	$app->enqueueMessage('Error: wrong file extension. Could be only JSON!', 'error');

	return FALSE;
}

if(!$params->get('field_id_price') || !$params->get('field_id_article') || !$params->get('section_id'))
{
	$app->enqueueMessage('Error: fields are not selected!', 'error');

	return FALSE;
}

if(!\Joomla\Filesystem\File::upload($file['tmp_name'], JPATH_ROOT . DIRECTORY_SEPARATOR . 'tmp/'.$file['name']))
{
	$app->enqueueMessage('Error: cannot move file!', 'error');

	return FALSE;
}

file_put_contents( __DIR__."/log.txt", "");
$json = json_decode(file_get_contents( JPATH_ROOT . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . $file['name']));

$map = $articles = $prices =[];
foreach($json as $key => $product)
{
	$map[$product->article] = $key;
	$articles[(string)$product->article] = (string)$product->article;
	$prices[$product->article] = $product->price;
}


$sql = "SELECT * from `#__js_res_record` WHERE section_id = " . (int)$params->get('section_id');
$db->setQuery($sql);
$records = $db->loadObjectList();

$stat_total =  $stat_updated = $stat_noarticle = $stat_nomatch = 0;
foreach($records as $record)
{
	$stat_total++;
	$fields = json_decode($record->fields, TRUE);
	if(!isset($fields[$params->get('field_id_article')]))
	{
		$stat_noarticle++;
		_log($record->id, "Record with no or empty Article field");
		continue;
	}

	if(!in_array($fields[$params->get('field_id_article')], $articles))
	{
		$stat_nomatch++;
		_log($record->id.":".$fields[$params->get('field_id_article')], "Record exists on the site but not in uploaded file");
		continue;
	}

	unset($articles[(string)$fields[$params->get('field_id_article')]]);

	include_once JPATH_ROOT.'/components/com_joomcck/api.php';

	$fields[$params->get('field_id_price')] = str_replace(',', '.', $prices[$fields[$params->get('field_id_article')]]);

	$table_record->load($record->id);
	$table_record->fields = json_encode($fields);
	$table_record->store();
	$table_record->reset();
	$table_record->id = NULL;


	$field = JoomcckApi::getField($params->get('field_id_price'), $record);
	$table_value->clean($record->id, [$params->get('field_id_price')]);
	$table_value->store_value($prices[$fields[$params->get('field_id_article')]], 0, $record, $field);
	$table_value->reset();
	$table_value->id = NULL;

	$stat_updated++;
	_log($record->id.":".$fields[$params->get('field_id_article')], "Record updated successfully");
}

foreach($articles AS $article){
	_log($article, "Record is in uploaded file but not on the site");
}



$app->enqueueMessage('total analyzed: '. $stat_total);
$app->enqueueMessage('total updated: '. $stat_updated);
$app->enqueueMessage('Report: <a href="/plugins/mint/toolset/tools/price/log.txt">Download</a>');

//$app->enqueueMessage('records with no article: ('. count($stat_noarticle) .     ')' . (count($stat_noarticle) > 0 ? ': <ul><li>'. implode('</li><li>',$stat_noarticle).'</li></ul>' : NULL));
//$app->enqueueMessage('records with no article match: ('. count($stat_nomatch) . ')' . (count($stat_nomatch) > 0 ? ': <ul><li>'. implode('</li><li>',$stat_nomatch).'</li></ul>' : NULL));
//$app->enqueueMessage('total updated: '. $stat_updated);

function _log($article, $msg) {
	error_log("[{$article}]: {$msg}", 3, __DIR__."/log.txt");
}