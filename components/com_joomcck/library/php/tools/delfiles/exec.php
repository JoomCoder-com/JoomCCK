<?php

defined('_JEXEC') or die();

$app         = \Joomla\CMS\Factory::getApplication();
$cp          = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');
$db          = \Joomla\CMS\Factory::getDBO();
$size        = $lost_files        = 0;
$files_ids[] = 0;

if(!defined('DS')) define('DS',DIRECTORY_SEPARATOR);

if ($params->get('deleted_articles')) {
    $sql = "SELECT `id` FROM `#__js_res_fields` WHERE `field_type` IN('uploads','video','audio','gallery','paytodownload','image')";
    $db->setQuery($sql);
    $fields_ids = $db->loadColumn();

    if(!$fields_ids) {
        return;
    }

    $sql = "SELECT `type_id` FROM `#__js_res_fields` WHERE `field_type` IN('uploads','video','audio','gallery','paytodownload','image')";
    $db->setQuery($sql);
    $type_ids = $db->loadColumn();
    
    $sql = "SELECT id, fields FROM `#__js_res_record` WHERE `type_id` IN('" . implode(',', $type_ids) . "')";
    $db->setQuery($sql);
    $records = $db->loadAssocList();

    if(!$records) {
        return;
    }

    $file_ids[] = 0;
    foreach($records AS $record){
        $fields = json_decode($record['fields'], TRUE);
        foreach($fields_ids AS $field_id) {
            if(!empty($fields[(int)$field_id]) && is_array($fields[(int)$field_id])) {
                foreach($fields[(int)$field_id] AS $file) {
                    if(!empty($file['id'])) {
                        $file_ids[] = (int)$file['id'];
                    }
                }
            }
        }
    }

    $sql = sprintf("SELECT * FROM `#__js_res_files` WHERE id NOT IN (%s) AND field_id IN (%s)", implode(',', $file_ids), implode(',', $fields_ids));

    $db->setQuery($sql);
    $files = $db->loadObjectList();
    $size += _deleteFiles($files);
    $app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('Deleted %d file(s) of deleted articles', count($files)));
}

if ($params->get('unsaved_articles')) {
    $sql = "SELECT * FROM `#__js_res_files` WHERE `saved` = 2 OR `saved` = 0";

    $db->setQuery($sql);
    $files = $db->loadObjectList();
    $size += _deleteFiles($files);
    $app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('Deleted %d file(s) of unsaved articles', count($files)));
}

if ($params->get('unlinked')) {
    $files_in_folder = \Joomla\CMS\Filesystem\Folder::files(JPATH_ROOT . DIRECTORY_SEPARATOR . $cp->get('general_upload'), '[0-9]{10}_[a-zA-Z0-9]{32}\..', true, true);
    settype($files_in_folder, 'array');

    $sql = "SELECT filename FROM #__js_res_files";
    $db->setQuery($sql);
    $files_in_db = $db->loadColumn();
    $file_names = [];




    foreach ($files_in_folder as $file) {
        $file_names[basename($file)] = basename($file);
        if (!in_array(basename($file), $files_in_db)) {
            $temp_size = filesize($file);
            if (\Joomla\CMS\Filesystem\File::delete($file)) {
                $size += $temp_size;
                $lost_files++;
                unset($file_names[basename($file)]);
            }
        }
    }

    foreach ($files_in_db as $filename) {
        if (!in_array($filename, $file_names)) {
            $db->setQuery("DELETE FROM `#__js_res_files` WHERE filename = '{$filename}'");
            $db->execute();
        }
    }
    $app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('Deleted %d of unlinked file(s)', $lost_files));
}

$app->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('Total size cleaned %s.', HTMLFormatHelper::formatSize($size)));

function _deleteFiles($files)
{
    $cp          = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');
    $db          = \Joomla\CMS\Factory::getDbo();
    $files_ids[] = 0;
    $size        = 0;
    foreach ($files as $file) {
        $subfolder = _getSubfolder($file->field_id);
        $size += $file->size;
        $to_delete = JPATH_ROOT . DS . $cp->get('general_upload') . DS . $subfolder . DS . $file->fullpath;

        if (\Joomla\CMS\Filesystem\File::exists($to_delete)) {
            if (\Joomla\CMS\Filesystem\File::delete($to_delete)) {
                $files_ids[] = $file->id;
            }
        } else {
            $files_ids[] = $file->id;
        }
    }

    $sql = "DELETE FROM `#__js_res_files` WHERE id IN (" . implode(',', $files_ids) . ")";
    $db->setQuery($sql);
    $db->execute();

    return $size;
}

function _getSubfolder($id)
{
    static $params   = [];
    static $defaults = [];

    if (!isset($params[$id])) {
        $db  = \Joomla\CMS\Factory::getDbo();
        $sql = "SELECT params, field_type FROM #__js_res_fields WHERE id = " . $id;
        $db->setQuery($sql);
        $result        = $db->loadObject();
        $params[$id]   = new \Joomla\Registry\Registry($result->params);
        $defaults[$id] = $result->field_type;
    }

    return $params[$id]->get('params.subfolder', $defaults[$id]);
}
