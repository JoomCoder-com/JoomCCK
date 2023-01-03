<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 *
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') || die();
require_once JPATH_ROOT . '/components/com_joomcck/library/php/fields/joomcckfield.php';

class CFormFieldUpload extends CFormField
{

    public function __construct($field, $default)
    {
        $root      = JPath::clean(JComponentHelper::getParams('com_joomcck')->get('general_upload'));
        $url       = str_replace(JPATH_ROOT, '', $root);
        $url       = str_replace("\\", '/', $url);
        $url       = preg_replace('#^\/#iU', '', $url);
        $this->url = JURI::root(true) . '/' . str_replace("//", "/", $url);

        $this->fieldname = null;

        parent::__construct($field, $default);

        $this->subscriptions = [];
        settype($this->value, 'array');
        if (isset($this->value['subscriptions']) && !empty($this->value['subscriptions'])) {
            $this->subscriptions = $this->value['subscriptions'];
            unset($this->value['subscriptions']);
        }
    }

    public function onJSValidate()
    {
        $js = "jQuery('input[id^=\"" . $this->tmpname . "_tbxFile\"]').remove();";

        return $js;
    }

    protected function getFileUrl($file)
    {
        $out = $this->url . "/{$file->subfolder}/" . $file->fullpath;

        return $out;
    }

    public function getInput()
    {

        $user = JFactory::getUser();
        settype($this->value, 'array');
        $default = [];
        if (isset($this->value[0])) {
            if (is_array($this->value[0])) {
                $default = $this->value;
            } else {
                $default = $this->getFiles($this->record);
            }
        }

        $this->options['autostart']  = $this->params->get('params.autostart');
        $this->options['can_delete'] = $this->_getDeleteAccess();

        $html = JHtml::_('mrelements.' . ($this->params->get('params.uploader', 1) == 1 ? "flow" : "mooupload"),
            "jform[fields][{$this->id}]" . $this->fieldname,
            $default, $this->options, $this);

        if ($this->params->get('params.subscription', 0) && in_array($this->params->get('params.can_select_subscr', 0), $user->getAuthorisedViewLevels())) {
            $html .= JHtml::_('emerald.plans', "jform[fields][{$this->id}][subscriptions][]", $this->params->get('params.subscription', []), $this->subscriptions, 'CRESTRICTIONPLANSDESCR');
        }

        return $html;
    }

    public function _getDeleteAccess()
    {
        $user              = JFactory::getUser();
        $author_can_delete = $this->params->get('params.delete_access', 1);
        $params            = JComponentHelper::getParams('com_joomcck');
        $type              = ItemsStore::getType($this->type_id);
        $app               = JFactory::getApplication();

        $record_id = $app->input->getInt('id', 0);

        if ($author_can_delete && (!$record_id || $user->get('id') == ItemsStore::getRecord($record_id)->user_id)) {
            return 1;
        } else {
            if ($params->get('moderator') == $user->get('id')) {
                return 1;
            }

            if (in_array($type->params->get('properties.item_can_moderate'), $user->getAuthorisedViewLevels())) {
                return 1;
            }

            if (MECAccess::allowUserModerate($user, ItemsStore::getSection($app->input->getInt('section_id')), 'allow_delete')) {
                return 1;
            }
        }

        return 0;
    }

    public function onPrepareSave($value, $record, $type, $section)
    {
        $subscr = false;

        if (isset($value['subscriptions'])) {
            $subscr = $value['subscriptions'];
            unset($value['subscriptions']);
        }
        $result = $this->_getPrepared($value);

        if ($subscr) {
            $result['subscriptions'] = $subscr;
        }

        return $result;
    }

    public function onPrepareFullTextSearch($value, $record, $type, $section)
    {
        $files = $this->_getPrepared($value);

        $out = [];
        settype($files, 'array');
        foreach ($files as $file) {
            $out[] = $file['realname'];
        }

        return implode(', ', $out);
    }

    public function onStoreValues($validData, $record)
    {
        settype($this->value, 'array');
        $out   = $saved   = [];
        $files = JTable::getInstance('Files', 'JoomcckTable');
        foreach ($this->value as $key => $file) {
            if (!\Joomla\String\StringHelper::strcmp($key, 'subscriptions')) {
                continue;
            }
            $out[]   = $file['realname'];
            $saved[] = $file['id'];
        }
        $files->markSaved($saved, $validData, $this->id);

        return $out;
    }

    protected function _getPrepared($array)
    {
        static $data = [];

        if (empty($array)) {
            return null;
        }

        settype($array, 'array');

        if (isset($array['title'])) {
            $title = $array['title'];
            unset($array['title']);
        }
        if (isset($array['descr'])) {
            $descr = $array['descr'];
            unset($array['descr']);
        }

        $files = JTable::getInstance('Files', 'JoomcckTable');
        if (!empty($array['title']) || !empty($array['descr'])) {
            $files->saveInfo($array['title'], $array['descr']);
        }

        $key = md5(implode(',', $array));

        if (isset($data[$key])) {
            return $data[$key];
        }

        $files = JTable::getInstance('Files', 'JoomcckTable');
        $array = $files->prepareSave($array);

        $data[$key] = json_decode($array, true);
        foreach ($data[$key] as &$file) {
            unset($file['params']);
        }

        return $data[$key];

    }

    public function onBeforeDownload($record, $file_index, $file_id, $return = true)
    {
        $user = JFactory::getUser();
        if (!in_array($this->params->get('params.allow_download', 1), $user->getAuthorisedViewLevels())) {
            $this->setError(JText::_("CNORIGHTSDOWNLOAD"));

            return false;
        }

        if ($this->_ajast_subscr($record)) {
            $em_api = JPATH_ROOT . '/components/com_emerald/api.php';
            if (!JFile::exists($em_api)) {
                return true;
            }

            if (in_array($this->params->get('params.subscr_skip', 3), $user->getAuthorisedViewLevels())) {
                return true;
            }

            if ($this->params->get('params.subscr_skip_author', 1) && $record->user_id && ($record->user_id == $user->id)) {
                return true;
            }
            $section = ItemsStore::getSection($record->section_id);
            if ($this->params->get('params.subscr_skip_moderator', 1) && MECAccess::allowRestricted($user, $section)) {
                return true;
            }

            include_once $em_api;

            if ($this->_is_subscribed($this->_ajast_subscr($record), false)) {
                return true;
            }

            $result = JText::_($this->params->get('params.subscription_msg'));
            $result .= sprintf('<br><small><a href="%s">%s</a></small>',
                EmeraldApi::getLink('list', true, $this->_ajast_subscr($record)),
                JText::_('CSUBSCRIBENOW')
            );

            $this->setError($result);

            return false;
        }

        return $return;
    }

    public function _is_subscribed($plans, $redirect)
    {
        require_once JPATH_ROOT . '/components/com_emerald/api.php';

        return EmeraldApi::hasSubscription(
            $plans,
            $this->params->get('params.subscription_msg'),
            null,
            $this->params->get('params.subscription_count'),
            $redirect);
    }

    public function _ajast_subscr($record)
    {
        if (!$record->user_id) {
            return;
        }

        $user = JFactory::getUser($record->user_id);

        if (in_array($this->params->get('params.can_select_subscr', 0), $user->getAuthorisedViewLevels()) &&
            $this->params->get('params.subscription')
        ) {
            $subscr = $this->subscriptions;
        } else {
            $subscr = $this->params->get('params.subscription');
        }

        ArrayHelper::clean_r($subscr);

        return $subscr;
    }

    public function onCopy($value, $record, $type, $section, $field)
    {
        if (!empty($value)) {
            foreach ($value as $key => $file) {
                $value[$key] = $this->copyFile($file, $field);
            }
        }

        return $value;
    }

    protected function getFiles($record, $show_hits = false)
    {
        $list = $this->value;

        $subfolder = $this->params->get('params.subfolder', false);

        if (!$list) {
            return [];
        }

        if (is_string($list)) {
            $list = json_decode($list);
        }

        $files = JTable::getInstance('Files', 'JoomcckTable');

        if (!is_array(@$list[0])) {
            $list      = $files->getFiles($list, 'filename');
            $show_hits = false;
        }

        if ($show_hits) {
            $in = [];
            foreach ($list as $attach) {
                settype($attach, 'array');
                $in[] = $attach['id'];
            }

            if ($in) {
                $list = $files->getFiles($in);
            }
        }
        foreach ($list as $idx => &$file) {
            if (is_array($file)) {
                $file = \Joomla\Utilities\ArrayHelper::toObject($file);
            }
            if ($this->params->get('params.show_in_browser', 0) == 0) {
                $file->url = $this->getDownloadUrl($record, $file, $idx);
            } else {
                $file->url = JURI::root(true) . '/' . JComponentHelper::getParams('com_joomcck')->get('general_upload') . '/' . $subfolder . '/' . str_replace('\\', '/', $file->fullpath);
            }
            $file->subfolder = $subfolder ? $subfolder : $file->ext;
        }

        $sort = $this->params->get('params.sort', 0);

        $parts = explode(' ', $sort);
        if (!isset($parts[0])) {
            $parts[0] = 0;
        }

        if (!isset($parts[1])) {
            $parts[1] = 'ASC';
        }
        $sortArray = [];
        switch ($parts[0]) {
            case 0:
                $title = $this->params->get('params.allow_edit_title', 0);
                foreach ($list as $val) {
                    $sortArray[] = strtolower($title && $val->title ? $val->title : $val->realname);
                }
                natcasesort($sortArray);
                array_multisort($sortArray, constant('SORT_' . $parts[1]), $list);
                break;

            case 1:
                foreach ($list as $val) {
                    $sortArray[] = $val->size;
                }
                array_multisort($sortArray, constant('SORT_' . $parts[1]), $list);
                break;

            case 2:
                foreach ($list as $val) {
                    $sortArray[] = $val->hits;
                }
                array_multisort($sortArray, constant('SORT_' . $parts[1]), $list);
                break;
            case 3:
                foreach ($list as $val) {
                    $sortArray[] = $val->id;
                }
                array_multisort($sortArray, constant('SORT_' . $parts[1]), $list);
                break;
        }

        return $list;
    }

    protected function getDownloadUrl($record, $file, $idx)
    {
        if (empty($record)) {
            return;
        }
        $url = JURI::root(true) . '/index.php?option=com_joomcck&task=files.download&tmpl=component';
        $url .= '&id=' . $file->id;
        $url .= '&fid=' . $this->id;
        $url .= '&fidx=' . $idx;
        $url .= '&rid=' . $record->id;
        $url .= '&return=' . Url::back();

        return $url;
    }

    /**
     *
     *
     * @param  string $filename Value from column 'filename' in table #__js_res_files
     * @return string Filename of copied file
     */

    protected function copyFile($filename, $field)
    {
        $params      = JComponentHelper::getParams('com_joomcck');
        $files_table = JTable::getInstance('Files', 'JoomcckTable');
        if ($files_table->load(['filename' => $filename])) {
            $time = time();
            //$date = date('Y-m', $time);
            $date      = date($params->get('folder_format', 'Y-m'), $time);
            $ext       = strtolower(JFile::getExt($filename));
            $subfolder = $field->params->get('params.subfolder', $ext);

            $dest  = JPATH_ROOT . DIRECTORY_SEPARATOR . $params->get('general_upload') . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR;
            $index = '<html><body></body></html>';
            if (!JFolder::exists($dest)) {
                JFolder::create($dest, 0755);
                JFile::write($dest . DIRECTORY_SEPARATOR . 'index.html', $index);
            }
            $dest .= $date . DIRECTORY_SEPARATOR;
            if (!JFolder::exists($dest)) {

                JFolder::create($dest, 0755);
                JFile::write($dest . DIRECTORY_SEPARATOR . 'index.html', $index);
            }

            $files_table->id       = null;
            $parts                 = explode('_', $filename);
            $files_table->filename = $time . '_' . $parts[1];

            $copied = JFile::copy(JPATH_ROOT . DIRECTORY_SEPARATOR . $params->get('general_upload') . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR . $files_table->fullpath, $dest . $files_table->filename);

            $files_table->fullpath = JPath::clean($date . DIRECTORY_SEPARATOR . $files_table->filename, '/');
            $files_table->saved    = 0;

            if (!$copied) {
                return false;
            }
            if (!$files_table->store()) {
                return false;
            }

            return $files_table->filename;
        }

        return false;
    }

    public function onMakeDefault()
    {
        $app = JFactory::getApplication();
        $db  = JFactory::getDbo();

        $record_id = $app->input->getInt('record_id', 0);
        $field_id  = $app->input->getInt('field_id', 0);
        $id        = $app->input->getInt('id', 0);

        $sql = "UPDATE `#__js_res_files` SET `default` = 1 WHERE id = " . $id;
        $db->setQuery($sql);
        $db->execute();

        if (!$record_id) {
            return AjaxHelper::send(1);
        }

        $sql = "UPDATE `#__js_res_files` SET `default` = 0 WHERE field_id = $field_id AND record_id = $record_id AND id <> $id";
        $db->setQuery($sql);
        $db->execute();

        $record_table = JTable::getInstance('Record', 'JoomcckTable');
        $record_table->load($record_id);
        $fields = json_decode($record_table->fields, true);

        if (isset($fields[$field_id])) {
            $files = &$fields[$field_id];
            if (isset($fields[$field_id]['files'])) {
                $files = &$fields[$field_id]['files'];
            }

            foreach ($files as &$file) {
                $file['default'] = (int) ($file['id'] == $id);
            }
            $record_table->fields = json_encode($fields);
            $record_table->store();
        }

        return AjaxHelper::send(1);
    }
    public function onSaveDetails()
    {
        $app         = JFactory::getApplication();
        $db          = JFactory::getDbo();
        $id          = $app->input->getInt('id', 0);
        $title       = CensorHelper::cleanText($app->input->getString('text'));
        $description = CensorHelper::cleanText($app->input->getString('descr', $default = null, $hash = 'default', $type = 'none', $mask = 4));

        $record_id = $app->input->getInt('record_id', 0);
        $field_id  = $app->input->getInt('field_id', 0);

        if ($record_id && $field_id) {
            $record_table = JTable::getInstance('Record', 'JoomcckTable');
            $record_table->load($record_id);
            $fields = json_decode($record_table->fields, true);

            if (isset($fields[$field_id])) {
                $files = &$fields[$field_id];
                if (isset($fields[$field_id]['files'])) {
                    $files = &$fields[$field_id]['files'];
                }

                foreach ($files as &$file) {
                    if ($file['id'] == $id) {
                        $file['title']       = htmlentities($title, ENT_QUOTES, 'UTF-8');
                        $file['description'] = htmlentities($description, ENT_QUOTES, 'UTF-8');
                        break;
                    }
                }
                $record_table->fields = json_encode($fields);
                $record_table->store();
            }

        }
        $db = JFactory::getDbo();
        $db->setQuery("UPDATE `#__js_res_files` SET title = '" . $db->escape(htmlentities($title, ENT_QUOTES, 'UTF-8')) . "', description = '" . $db->escape(htmlentities($description, ENT_QUOTES, 'UTF-8')) . "' WHERE id = {$id}");

        if (!$db->execute()) {
            AjaxHelper::error('DB save error');
        }

        return AjaxHelper::send(1);
    }

    /*public function onSaveTitle()
    {
    $app = JFactory::getApplication();

    $id        = $app->input->getInt('id', 0);
    $text      = CensorHelper::cleanText($app->input->getString('text'));
    $record_id = $app->input->getInt('record_id', 0);
    $field_id  = $app->input->getInt('field_id', 0);
    if ($record_id && $field_id) {
    $record_table = JTable::getInstance('Record', 'JoomcckTable');
    $record_table->load($record_id);
    $fields = json_decode($record_table->fields, true);

    if (isset($fields[$field_id])) {
    $files = &$fields[$field_id];
    if (isset($fields[$field_id]['files'])) {
    $files = &$fields[$field_id]['files'];
    }

    foreach ($files as &$file) {
    if ($file['id'] == $id) {
    $file['title'] = $text;
    break;
    }
    }
    $record_table->fields = json_encode($fields);
    $record_table->store();
    }

    }
    $db = JFactory::getDbo();
    $db->setQuery("UPDATE #__js_res_files SET title = '" . $db->escape($text) . "' WHERE id = {$id}");

    if (!$db->execute()) {
    AjaxHelper::error('DB save error');
    }

    return $text;
    }

    public function onSaveDescr()
    {
    $app       = JFactory::getApplication();
    $id        = $app->input->getInt('id', 0);
    $text      = CensorHelper::cleanText($app->input->getString('text', $default = null, $hash = 'default', $type = 'none', $mask = 4));
    $record_id = $app->input->getInt('record_id', 0);
    $field_id  = $app->input->getInt('field_id', 0);
    if ($record_id && $field_id) {
    $record_table = JTable::getInstance('Record', 'JoomcckTable');
    $record_table->load($record_id);
    $fields = json_decode($record_table->fields, true);

    if (isset($fields[$field_id])) {
    $files = &$fields[$field_id];
    if (isset($fields[$field_id]['files'])) {
    $files = &$fields[$field_id]['files'];
    }

    foreach ($files as &$file) {
    if ($file['id'] == $id) {
    $file['description'] = $text;
    break;
    }
    }
    $record_table->fields = json_encode($fields);
    $record_table->store();
    }

    }
    $db = JFactory::getDbo();
    $db->setQuery("UPDATE #__js_res_files SET description = '{$text}' WHERE id = {$id}");

    if (!$db->execute()) {
    AjaxHelper::error('DB save error');
    }

    return $text;
    }*/

    public function onImportData($row, $params)
    {
        return $row->get($params->get('field.' . $this->id . '.fname'));
    }

    public function onImport($value, $params, $record = null)
    {
        $values = explode($params->get('field.' . $this->id . '.separator', ','), $value);
        ArrayHelper::clean_r($values);

        $files = [];
        include_once JPATH_ROOT . '/components/com_joomcck/controllers/files.php';
        $controller = new JoomcckControllerFiles();

        $default = $this->value;
        if (array_key_exists('files', $default)) {
            $default = $default['files'];
        }
        settype($default, 'array');

        foreach ($values as $file) {

            $exists = false;
            foreach ($default as $f) {
                if (basename($file) == $f['realname']) {
                    $files[] = $f['filename'];
                    $exists  = true;
                }
            }

            if ($exists) {
                continue;
            }

            $ext      = \Joomla\String\StringHelper::strtolower(JFile::getExt($file));
            $new_name = JFactory::getDate($record->ctime)->toUnix() . '_' . md5($file) . '.' . $ext;

            $file = $this->_find_import_file($params->get('field.' . $this->id . '.path'), $file);
            if (!$file) {
                continue;
            }

            $sub_folder = $this->params->get('params.subfolder', $this->field_type);

            if (!$controller->savefile(basename($file), $new_name, $sub_folder, $file, $record->id, $record->section_id, $record->type_id, $this->id)) {
                continue;
            }

            $files[] = $new_name;
        }

        if (empty($files)) {
            return null;
        }

        $return = $this->_getPrepared($files);

        if ($this->type == 'paytodownaload' || $this->type == 'video') {
            $out['files'] = $return;
        } else {
            $out = $return;
        }

        return $out;
    }

    public function onImportForm($heads, $defaults)
    {
        $out = $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id . '.fname'), 'fname');
        $out .= sprintf('<div><small>%s</small></div><input type="text" name="import[field][%d][separator]" value="%s" class="col-md-2" >',
            JText::_('CMULTIVALFIELDSEPARATOR'), $this->id, $defaults->get('field.' . $this->id . '.separator', ','));
        $out .= sprintf('<div><small>%s</small></div><input type="text" name="import[field][%d][path]" value="%s" class="col-md-12" >',
            JText::_('CFILESPATH'), $this->id, $defaults->get('field.' . $this->id . '.path', 'files'));

        return $out;
    }

    public function validateField($value, $record, $type, $section)
    {
        $jform = $this->request->get('jform', [], 'array');
        if ($this->required && !isset($jform['fields'][$this->id])) {
            $jform['fields'][$this->id] = '';
            $this->request->set('jform', $jform);
        }
        $jform = $this->request->get('jform', [], 'array');

        parent::validateField($value, $record, $type, $section);
    }
}
