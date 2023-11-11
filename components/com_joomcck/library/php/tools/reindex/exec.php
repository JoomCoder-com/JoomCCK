<?php

defined('_JEXEC') or die();

$types = $params->get('types', FALSE);
set_time_limit(0);
ini_set('max_execution_time', 0);

if(!$types)
{
	return;
}
if(!is_array($types))
{
	settype($types, 'array');
}
$db = \Joomla\CMS\Factory::getDbo();
$db->setQuery('SELECT id, title, type_id, section_id, user_id, fields FROM `#__js_res_record` WHERE type_id IN (' . implode(',', $types) . ')');
$ids   = $db->loadObjectList();
$count = 0;
if(!empty($ids))
{
	require_once JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_joomcck' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'fields.php';
	$fields_model = new JoomcckModelFields();

	foreach($ids as $item)
	{
		$out_fieldsdata = array();
		$fields_list    = $fields_model->getFormFields($item->type_id, $item->id, FALSE);
		$type           = ItemsStore::getType($item->type_id);
		$section        = ItemsStore::getSection($item->section_id);
		if(!is_object($section->params))
		{
			$section->params = new \Joomla\Registry\Registry($section->params);
		}

		foreach($fields_list as $field)
		{
			if($field->params->get('core.searchable'))
			{
				$data = $field->onPrepareFullTextSearch($field->value, $item, $type, $section);
				if(is_array($data))
				{
					$data = implode(', ', $data);
				}
				$out_fieldsdata[$field->id] = $data;
			}

		}

		$user = \Joomla\CMS\Factory::getUser($item->user_id);

		if($section->params->get('more.search_title'))
		{
			$out_fieldsdata[] = $item->title;
		}
		if($section->params->get('more.search_name'))
		{
			$out_fieldsdata[] = $user->get('name');
			$out_fieldsdata[] = $user->get('username');
		}
		if($section->params->get('more.search_email'))
		{
			$out_fieldsdata[] = $user->get('email');
		}
		if($section->params->get('more.search_category') && $item->categories != '[]')
		{
			$cats             = json_decode($item->categories, TRUE);
			$out_fieldsdata[] = implode(', ', array_values($cats));
		}

		if($section->params->get('more.search_comments'))
		{
			$out_fieldsdata[] = CommentHelper::fullText($type, $item);
		}

		$db2 = \Joomla\CMS\Factory::getDbo();
		$db2->setQuery("UPDATE `#__js_res_record` SET fieldsdata = '" . $db2->escape(strip_tags(implode(', ', $out_fieldsdata))) . "' WHERE id = $item->id");
		$db2->execute();

		unset($db2, $out_fieldsdata, $user, $type, $section);

		if($count == 10000)
		{
			//exit;
		}

		$count++;
	}
}

$app = \Joomla\CMS\Factory::getApplication();
$app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('%d record(s) have been reindexed.', $count));