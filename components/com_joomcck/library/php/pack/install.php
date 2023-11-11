<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') || die('Restricted access');

/**
 * Script file of Joomcck Pack
 */

\Joomla\CMS\Table\Table::addIncludePath(JPATH_ROOT . '/components/com_joomcck/tables');
include_once JPATH_ROOT . '/components/com_joomcck/api.php';

class packInstallerScript
{

    private $records_with_files = [];

    /**
     * Constructor
     *
     * @param JAdapterInstance $adapter The object responsible for running this script
     */
    public function __construct(JAdapterInstance $adapter)
    {
        \Joomla\CMS\Table\Table::addIncludePath(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components/com_joomcck/tables');
        $this->key = (string) $adapter->getParent()->manifest->key;

        define('PACKS_PATH', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components/com_joomcck/packs');
        if (!is_dir(PACKS_PATH)) {
            \Joomla\Filesystem\Folder::create(PACKS_PATH);
        }
        $this->isNew = false;
        $this->id    = [];
        if (!is_file(PACKS_PATH . DIRECTORY_SEPARATOR . $this->key . '.json')) {
            $this->isNew = true;
        } else {
            $ids       = file_get_contents(PACKS_PATH . DIRECTORY_SEPARATOR . $this->key . '.json');
            $this->ids = json_decode($ids, true);
        }
    }

    /**
     * Called on installation
     *
     *
     * @param  JAdapterInstance $adapter The object responsible for running this script
     * @return boolean          True on success
     */
    public function install(JAdapterInstance $adapter)
    {
        $this->pack = $this->_getObject('pack');
        $this->_loadUsers();

        $this->_loadTable('Type', 'JoomcckTable', 'types');

        $this->_loadTable('Group', 'JoomcckTable', 'fields_group', [
            'type_id' => 'types'
        ]);

        $this->_loadTable('Field', 'JoomcckTable', 'fields', [
            'type_id'  => 'types',
            'group_id' => 'fields_group'
        ]);

        $this->_loadTable('Section', 'JoomcckTable', 'sections');

        if ($this->isNew || (!$this->isNew && $this->pack->demo == 1)) {
            $this->ids['categories'][1] = 1;
            $this->_loadTable('CobCategory', 'JoomcckTable', 'categories', [
                'parent_id'  => 'categories',
                'section_id' => 'sections'
            ]);

            $this->_loadTable('Usercategory', 'JoomcckTable', 'category_user', [
                'user_id'    => 'users',
                'section_id' => 'sections'
            ]);

            $this->_loadTable('Userpostmap', 'JoomcckTable', 'user_post_map', [
                'user_id'    => 'users',
                'section_id' => 'sections'
            ]);

            $this->_loadTable('Useropt', 'JoomcckTable', 'user_options', [
                'user_id' => 'users'
            ]);

            $this->_loadTable('Userautofollow', 'JoomcckTable', 'user_options_autofollow', [
                'user_id'    => 'users',
                'section_id' => 'sections'
            ]);

            $this->_loadTable('Record', 'JoomcckTable', 'records', [
                'user_id'    => 'users',
                'type_id'    => 'types',
                'ucatid'     => 'category_user',
                'section_id' => 'sections',
                'parent_id'  => 'records'
            ]);

            $this->_loadTable('Record_category', 'JoomcckTable', 'record_category', [
                'record_id'  => 'records',
                'catid'      => 'categories',
                'section_id' => 'sections'
            ]);

            $this->_loadTable('Record_values', 'JoomcckTable', 'record_values', [
                'field_id'    => 'fields',
                'record_id'   => 'records',
                'user_id'     => 'users',
                'type_id'     => 'types',
                'category_id' => 'categories',
                'section_id'  => 'sections'
            ]);

            $this->_loadTable('Favorites', 'JoomcckTable', 'favorite', [
                'user_id'    => 'users',
                'record_id'  => 'records',
                'section_id' => 'sections'
            ]);

            $this->_loadTable('Votes', 'JoomcckTable', 'vote', [
                'user_id'    => 'users',
                'section_id' => 'sections'
            ]);

            $this->_loadTable('Tags', 'JoomcckTable', 'tags');

            $this->_loadTable('Taghistory', 'JoomcckTable', 'tags_history', [
                'record_id'  => 'records',
                'tag_id'     => 'tags',
                'user_id'    => 'users',
                'section_id' => 'sections'
            ]);

            $this->_loadTable('Sales', 'JoomcckTable', 'sales', [
                'user_id'    => 'users',
                'saler_id'   => 'users',
                'record_id'  => 'records',
                'field_id'   => 'fields',
                'section_id' => 'sections'
            ]);

            $this->_loadTable('Files', 'JoomcckTable', 'files', [
                'user_id'    => 'users',
                'record_id'  => 'records',
                'field_id'   => 'fields',
                'section_id' => 'sections',
                'type_id'    => 'types'
            ]);

            $this->ids['comments'][1] = 1;
            $this->_loadTable('Cobcomments', 'JoomcckTable', 'comments', [
                'user_id'    => 'users',
                'record_id'  => 'records',
                'section_id' => 'sections',
                'type_id'    => 'types',
                'parent_id'  => 'comments',
                'root_id'    => 'comments'
            ]);

            $this->_loadTable('Moderators', 'JoomcckTable', 'moderators', [
                'user_id'    => 'users',
                'section_id' => 'sections'
            ]);

        }

        if ($this->isNew) {
            $this->_loadTable('Notificat', 'JoomcckTable', 'notifications', [
                'user_id' => 'users',
                'eventer' => 'users',
                'ref_1'   => 'records',
                'ref_2'   => 'sections',
                'ref_3'   => 'categories',
                'ref_4'   => 'comments',
                'ref_5'   => 'fields'
            ]);

        }

        $this->_loadTable('Multilevel', 'JoomcckTable', 'field_multilevelselect', [
            'field_id'  => 'fields',
            'parent_id' => 'field_multilevelselect'
        ]);

        $this->_loadTable('Stepaccess', 'JoomcckTable', 'field_stepaccess', [
            'field_id' => 'fields',
            'user_id'  => 'users',
            'record_d' => 'records'
        ]);

        $this->_moveFiles();
        $this->_touchParams();
        $this->_touchTemplates();
        $this->_touchConfigs();

        $this->_end();
    }

    /**
     * Called on update
     *
     *
     * @param  JAdapterInstance $adapter The object responsible for running this script
     * @return boolean          True on success
     */
    public function update(JAdapterInstance $adapter)
    {
        $this->install($adapter);
    }

    public function preflight()
    {
    }

    /**
     * Called on uninstallation
     *
     * @param JAdapterInstance $adapter The object responsible for running this script
     */
    public function uninstall(JAdapterInstance $adapter)
    {

    }

    private function _end()
    {
        $content = json_encode($this->ids);
        \Joomla\Filesystem\File::write(PACKS_PATH . DIRECTORY_SEPARATOR . $this->key . '.json', $content);

        if (is_dir(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components/com_joomcck/packs' . DIRECTORY_SEPARATOR . $this->key)) {
            \Joomla\Filesystem\Folder::delete(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components/com_joomcck/packs' . DIRECTORY_SEPARATOR . $this->key);
        }

        // create empty folder for correct uninstall pack from extension manager
        \Joomla\Filesystem\Folder::create(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components/com_joomcck/packs' . DIRECTORY_SEPARATOR . $this->key);
    }

    private function _touchConfigs()
    {
        $content = $this->_getObject('configs');
        foreach ($content as $config) {
            $file = JPATH_ROOT . '/components/com_joomcck/configs/' . $config . '.json';
            if (!is_file($file)) {
                continue;
            }

            $array = json_decode(file_get_contents($file), true);

            foreach ($array as $key => $val) {
                foreach ($val as $k => $v) {
                    if (substr($k, 0, 9) == 'field_id_') {
                        if (is_array($v)) {
                            foreach ($v as $id) {
                                $array[$key][$k][] = $this->ids['fields'][$id];
                            }

                        } else {
                            $array[$key][$k] = $this->ids['fields'][$v];
                        }
                    }
                }
            }
            $array = json_encode($array);
            \Joomla\Filesystem\File::write($file, $array);
        }
    }

    private function _touchTemplates()
    {
        $pack = $this->_getObject('pack', true);
        settype($pack['sections'], 'array');
        $key = ($pack['addkey'] ? '_' . $this->key : '');

        $section_table = \Joomla\CMS\Table\Table::getInstance('Section', 'JoomcckTable');
        $type_table    = \Joomla\CMS\Table\Table::getInstance('Type', 'JoomcckTable');

        foreach ($pack['sections'] as $id => $section) {
            $params = json_decode($section['params'], true);
            if (@$params['list']) {
                $new_id = @$this->ids['sections'][$id];
                if ($section_table->load($new_id)) {
                    $tparams = new \Joomla\Registry\Registry($section_table->params);
                    $tmpls   = $tparams->get('general.tmpl_list');
                    settype($tmpls, 'array');
                    foreach ($tmpls as $tmpl) {
                        $t    = explode('.', $tmpl);
                        $file = JPATH_ROOT . '/components/com_joomcck/views/records/tmpl/default_list_' . $t[0] . $key;
                        $this->_touchFile($file);
                    }
                }
            }
            settype($params['types'], 'array');
            foreach ($params['types'] as $id => $type) {
                if (@$type['article']) {
                    $new_id = @$this->ids['types'][$id];
                    if ($type_table->load($new_id)) {
                        $tparams = new \Joomla\Registry\Registry($type_table->params);
                        $t       = explode('.', $tparams->get('properties.tmpl_article'));
                        $file    = JPATH_ROOT . '/components/com_joomcck/views/record/tmpl/default_record_' . $t[0] . $key;
                        $this->_touchFile($file);
                    }
                }
            }
        }
    }

    private function _touchFile($file)
    {
        $php = $file . '.php';
        if (is_file($php)) {
            $content = $original = file_get_contents($php);

            foreach ($this->ids['fields'] as $old_id => $new_id) {
                $content = str_replace([
                    "fields_by_id[{$old_id}]",
                    "fields_keys_by_id[{$old_id}]"
                ], [
                    "fields_by_id[{$new_id}]",
                    "fields_keys_by_id[{$new_id}]"
                ], $content);
            }

            if (md5($original) != md5($content)) {
                \Joomla\Filesystem\File::write($php, $content);
            }
        }
    }

    private function _touchParams()
    {
        $table = \Joomla\CMS\Table\Table::getInstance('Field', 'JoomcckTable');
        foreach (@$this->ids['fields'] as $old_id => $new_id) {
            if ($table->load($new_id)) {
                $save   = null;
                $params = new \Joomla\Registry\Registry($table->params);
                switch ($table->field_type) {
                    case 'child':
                        $save = 1;
                        $params->set('params.parent_field', (int) @$this->ids['fields'][$params->get('params.parent_field')]);
                        $params->set('params.parent_type', (int) @$this->ids['types'][$params->get('params.parent_type')]);
                        $params->set('params.parent_section', (int) @$this->ids['sections'][$params->get('params.parent_section')]);
                        break;
                    case 'parent':
                        $save = 1;
                        $params->set('params.child_field', (int) @$this->ids['fields'][$params->get('params.child_field')]);
                        $params->set('params.child_section', (int) @$this->ids['sections'][$params->get('params.child_section')]);
                        break;
                    case 'record':
                        $save = 1;
                        $params->set('params.type', (int) @$this->ids['types'][$params->get('params.type')]);
                        $params->set('params.section_id', (int) @$this->ids['sections'][$params->get('params.section_id')]);
                        break;
                    case 'readmore':
                        $save = 1;
                        $params->set('params.type', (int) @$this->ids['types'][$params->get('params.type')]);
                        break;
                    case 'datetime':
                        $save = 1;
                        $params->set('params.field_id_type', (int) @$this->ids['fields'][$params->get('params.field_id_type')]);
                        break;
                }

                if ($save) {
                    $table->params = $params->toString();
                    $table->store();
                }
            }
        }

        $table = \Joomla\CMS\Table\Table::getInstance('Type', 'JoomcckTable');

        foreach (@$this->ids['types'] as $old_id => $new_id) {
            if ($table->load($new_id)) {
                $params     = new \Joomla\Registry\Registry($table->params);
                $categories = $params->get('category_limit.category');
                settype($categories, 'array');
	            $categories = \Joomla\Utilities\ArrayHelper::toInteger($categories);
                $ids = [];
                foreach ($categories as $id) {
                    if (!$id) {
                        continue;
                    }

                    if (!isset($this->ids['categories'][$id])) {
                        continue;
                    }

                    $ids[] = $this->ids['categories'][$id];
                }
                if ($ids) {
                    $params->set('category_limit.category', $ids);
                }

                if ($params->get('properties.item_title_composite') && $params->get('properties.item_title') == 2) {
                    if (!is_array($this->ids['fields'])) {
                        continue;
                    }
                    $title = $params->get('properties.item_title_composite');
                    foreach ($this->ids['fields'] as $old => $new) {
                        $title = str_replace("[$old]", "[$new]", $title);
                    }
                    $params->set('properties.item_title_composite', $title);
                }

                if ($params->get('comments.section_id')) {
                    $params->set('comments.section_id', $this->ids['sections'][$params->get('comments.section_id')]);
                }
                if ($params->get('comments.type_id')) {
                    $params->set('comments.type_id', $this->ids['types'][$params->get('comments.type_id')]);
                }

                $table->params = $params->toString();
                $table->store();
            }
        }

        $table = \Joomla\CMS\Table\Table::getInstance('Section', 'JoomcckTable');
        foreach (@$this->ids['sections'] as $old_id => $new_id) {
            if ($table->load($new_id)) {
                $params = new \Joomla\Registry\Registry($table->params);
                $types  = $params->get('general.type');
                settype($types, 'array');
	            $types = \Joomla\Utilities\ArrayHelper::toInteger($types);
                $ids = [];
                foreach ($types as $id) {
                    if (!$id) {
                        continue;
                    }

                    if (isset($this->ids['types'][$id])) {
                        $ids[] = $this->ids['types'][$id];
                    }
                }
                if (!empty($ids)) {
                    $params->set('general.type', $ids);
                    $table->params = $params->toString();
                    $table->store();
                }
            }
        }
        if ($this->isNew || (!$this->isNew && $this->pack->demo == 1)) {
            $table = \Joomla\CMS\Table\Table::getInstance('CobCategory', 'JoomcckTable');
            foreach (@$this->ids['categories'] as $old_id => $new_id) {
                if ($table->load($new_id)) {
                    $params = new \Joomla\Registry\Registry($table->params);
                    $types  = $params->get('posttype');
                    settype($types, 'array');
	                $types = \Joomla\Utilities\ArrayHelper::toInteger($types);
                    $ids = [];
                    foreach ($types as $id) {
                        if (!$id) {
                            continue;
                        }

                        if (isset($this->ids['types'][$id])) {
                            $ids[] = $this->ids['types'][$id];
                        }
                    }
                    if (!empty($ids)) {
                        $params->set('posttype', $ids);
                        $table->params = $params->toString();
                        $table->store();
                    }
                }
            }

            $table = \Joomla\CMS\Table\Table::getInstance('Record', 'JoomcckTable');
            settype($this->ids['records'], 'array');
            foreach (@$this->ids['records'] as $old_id => $new_id) {
                if (!$table->load($new_id)) {
                    continue;
                }

                $categories     = new \Joomla\Registry\Registry($table->categories);
                $categories     = $categories->toArray();
                $new_categories = [];
                foreach ($categories as $id => $value) {
                    if (!$id) {
                        continue;
                    }

                    if (isset($this->ids['categories'][$id])) {
                        $new_categories[$this->ids['categories'][$id]] = $value;
                    }
                }
                if (!empty($new_categories)) {
                    $categories        = new \Joomla\Registry\Registry($new_categories);
                    $table->categories = $categories->toString();
                    $table->store();
                }

                $tags     = new \Joomla\Registry\Registry($table->tags);
                $tags     = $tags->toArray();
                $new_tags = [];
                foreach ($tags as $id => $value) {
                    if (!$id) {
                        continue;
                    }

                    if (isset($this->ids['tags'][$id])) {
                        $new_tags[$this->ids['tags'][$id]] = $value;
                    }
                }
                if (!empty($new_tags)) {
                    $tags              = new \Joomla\Registry\Registry($new_tags);
                    $table->categories = $tags->toString();
                    $table->store();
                }

                $fields     = new \Joomla\Registry\Registry($table->fields);
                $fields     = $fields->toArray();
                $new_fields = [];
                foreach ($fields as $id => $value) {
                    if (!$id) {
                        continue;
                    }

                    if (is_object($value)) {
                        $value = get_object_vars($value);
                    }

                    // If this record has files attached
                    if (!empty($this->records_with_files[$old_id][$id])) {
                        if (is_array(@$value['uploads'])) {
                            $files = &$value['uploads'];
                        } elseif (is_array(@$value['files'])) {
                            $files = &$value['files'];
                        } else {
                            $files = &$value;
                        }
                        if (!is_array($files)) {
                            settype($files, 'array');
                        }
                        foreach ($files as &$file) {
                            if (is_object($file)) {
                                $file = get_object_vars($file);
                            }
                            $nfid = @$this->ids['files'][$file['id']];
                            if ($nfid) {
                                @$file['id'] = $nfid;
                            }
                        }
                    }

                    if (isset($this->ids['fields'][$id])) {
                        $new_fields[$this->ids['fields'][$id]] = $value;
                    }
                }

                if (!empty($new_fields)) {
                    $fields        = new \Joomla\Registry\Registry($new_fields);
                    $table->fields = $fields->toString();
                    $table->store();
                }
            }
        }
    }

    private function _moveFiles()
    {
        $pack_usercategories_path = JPATH_ADMINISTRATOR . '/components/com_joomcck/packs/' . $this->key . '/usercategories';
        if (is_dir($pack_usercategories_path)) {
            $folders = \Joomla\Filesystem\Folder::folders($pack_usercategories_path);
            foreach ($folders as $folder) {
                $new_foldername = $this->ids['users'][$folder];
                $path           = JPATH_ROOT . '/images/usercategories/' . $new_foldername;
                if (!is_dir($path)) {
                    \Joomla\Filesystem\Folder::create($path);
                }
                $files = \Joomla\Filesystem\Folder::files($pack_usercategories_path . DIRECTORY_SEPARATOR . $folder);
                foreach ($files as $file) {
                    $ext          = \Joomla\Filesystem\File::getExt($file);
                    $ucid         = str_replace('.' . $ext, '', $file);
                    $new_filename = $this->ids['category_user'][$ucid];
                    $dest         = JPATH_ROOT . '/images/usercategories' . DIRECTORY_SEPARATOR . $new_foldername . DIRECTORY_SEPARATOR . $new_filename . '.' . $ext;
                    \Joomla\Filesystem\File::copy($pack_usercategories_path . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $file, $dest, '', true);
                }
            }
        }
    }

    private function _getObject($name, $assoc = false)
    {
        $file = JPATH_ADMINISTRATOR . '/components/com_joomcck/packs/' . $this->key . '/' . $name . '.json';
        if (!is_file($file)) {
            return [];
        }
        $content = file_get_contents($file);
        $content = json_decode($content, $assoc);

        return $content;
    }

    private function _loadUsers()
    {
        $users  = $this->_getObject('users', true);
        $params = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');
        $global = $params->get('moderator');

        switch ($this->pack->user) {
            case 1:
                foreach ($users as $user) {
                    $this->ids['users'][$user['id']] = $global;
                }
            case 2:
                foreach ($users as $user) {
                    $match                           = $this->_matchUser($user);
                    $this->ids['users'][$user['id']] = ($match ?: $global);
                }
            case 3:
                foreach ($users as $user) {
                    $match = $this->_matchUser($user);
                    if (!$match) {
                        $match = $this->_createUser($user);
                    }
                    $this->ids['users'][$user['id']] = $match;
                }
        }
    }

    private function _createUser($user)
    {
        \Joomla\CMS\MVC\Model\BaseDatabaseModel::addIncludePath(JPATH_ROOT . '/components/com_users/models/');
        $model = \Joomla\CMS\MVC\Model\BaseDatabaseModel::getInstance('Registration', 'UsersModel');

        $data['name']      = $user['name'];
        $data['username']  = $user['username'];
        $data['email1']    = $user['email'];
        $data['email2']    = $user['email'];
        $data['password1'] = '123456';
        $data['password2'] = '123456';

        $model->register($data);

        $db  = \Joomla\CMS\Factory::getDbo();
        $sql = "SELECT id FROM `#__users` WHERE email = '{$user['email']}' OR username = '{$user['username']}'";
        $db->setQuery($sql);

        return $db->loadResult();
    }
    private function _matchUser($user)
    {
        $db = \Joomla\CMS\Factory::getDbo();

        $sql = "SELECT id FROM `#__users` WHERE email = '{$user['email']}' OR username = '{$user['username']}'";
        $db->setQuery($sql);

        return $db->loadResult();
    }

    private function _loadTable($name, $prefix, $file, $keys = [])
    {
        $db      = \Joomla\CMS\Factory::getDbo();
        $content = $this->_getObject($file, true);

        if ($file == 'categories' || $file == 'comments' || $file == 'field_multilevelselect') {
            $this->ids[$file][1] = 1;
        }

        if (!$content) {
            return;
        }

        include_once JPATH_ROOT . "/components/com_joomcck/tables/" . strtolower($name) . ".php";
        $classname = "JoomcckTable" . ucfirst($name);
        $table     = new $classname($db);
        foreach ($content as $row) {
            $id = @$row['id'];

            // continue if $row empty record [0]=0
            if (!$id) {
                continue;
            }

            $row['id'] = null;
            if (isset($this->ids[$file][$id]) && !$this->isNew) {
                $row['id'] = $this->ids[$file][$id];
            }
            if (!empty($row['asset_id'])) {
                $row['asset_id'] = 0;
            }
            if (array_key_exists('checked_out', $row)) {
                $row['checked_out'] = 0;
                $row['checked_out_time'] = NULL;
            }

            if ($file == 'files') {
                $this->records_with_files[$row['record_id']][$row['field_id']] = 1;
            }
            if ($file == 'tags') {
                $db  = \Joomla\CMS\Factory::getDBO();
                $sql = "SELECT id FROM #__js_res_tags WHERE LOWER(`tag`) = '" . \Joomla\String\StringHelper::strtolower($row['tag']) . "'";
                $db->setQuery($sql);
                $tid = $db->loadResult();
                if ($tid) {
                    $row['id'] = $tid;
                    //$table->id = $tid;
                }
            }

            if ($file == 'record_values' && $row['field_type'] == 'child') {
                $row['field_value'] = @$this->ids['records'][$row['field_value']];
            }

            if ($file == 'vote') {
                $row['ref_id'] = @$this->ids[$row['ref_type'] . 's'][$row['ref_id']];
                if (empty($row['ref_id'])) {
                    continue;
                }

            }
            if ($file == 'subscribe') {
                $row['ref_id'] = @$this->ids[$row['type'] . 's'][$row['ref_id']];
                if (empty($row['ref_id'])) {
                    continue;
                }

            }

            foreach ($keys as $k => $v) {
                if ($k == 'user_id') {
                    $val = (array_key_exists($row[$k], $this->ids['users']) ?
                        $this->ids['users'][$row[$k]] :
                        \Joomla\CMS\Component\ComponentHelper::getPrams('com_joomcck')->get('moderator'));
                } else {
                    $val = @$this->ids[$v][$row[$k]];
                }
                $row[$k] = $val;
            }

            if ($file == 'categories' || $file == 'comments') {
                $table->setLocation($row['parent_id'], 'last-child');
            }

            if ($file == 'comments' && !empty($row['attachment'])) {
                $atachments = json_decode($row['attachment'], true);
                if (!is_array($atachments)) {
                    settype($atachments, 'array');
                }

                foreach ($atachments as $at_key => &$at_value) {
                    $file_id        = @$this->ids['files'][$at_value['id']];
                    $at_value['id'] = $file_id;
                }
                $row['attachment'] = json_encode($atachments);
            }

            $table->bind($row);
            $table->check();
            $table->store();


            if ($file == 'sections') {
                $sql = "SELECT menutype FROM `#__menu_types` ORDER BY id ASC";
                $db->setQuery($sql);
                $menutype = $db->loadResult();

                \Joomla\CMS\Table\Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_menus/tables');
                $menu_table = \Joomla\CMS\Table\Table::getInstance('Menu', '\\Joomla\\CMS\\Table\\Table', []);

                $menu_table->load([
                    "link" => "index.php?option=com_joomcck&view=records&section_id={$table->id}",
                    "type" => 'component'
                ]);

                if (!$menu_table->id) {
                    $menu_table->load([
                        "alias" => $row['alias'] . '-' . $this->key
                    ]);
                }

                $menu_table->link = "index.php?option=com_joomcck&view=records&section_id={$table->id}";

                if (!$menu_table->id) {
                    $menu_table->id = 0;

                    $menu_table->parent_id = 1;
                    $menu_table->menutype  = $menutype;

                    $menu_table->setLocation(1, 'last-child');

                    $menu_table->access       = (int) $table->access;
                    $menu_table->level        = null;
                    $menu_table->lft          = null;
                    $menu_table->rgt          = null;
                    $menu_table->home         = 0;
                    $menu_table->language     = '*';
                    $menu_table->type         = 'component';
                    $menu_table->component_id = \Joomla\CMS\Component\ComponentHelper::getComponent('com_joomcck')->id;
                    $menu_table->published    = 1;
                    $menu_table->title        = $row['title'] ? $row['title'] : $row['name'];
                    $menu_table->alias        = $row['alias'] . '-' . $this->key;
                }

                if (!$menu_table->check()) {
                    throw new Exception( $menu_table->getError(),400);
                }
                if (!$menu_table->store()) {
                    throw new Exception( $menu_table->getError(),400);
                }

                if (!$menu_table->rebuildPath($menu_table->id)) {
                    throw new Exception( $menu_table->getError(),400);
                }

                $params = new \Joomla\Registry\Registry($table->params);
                $params->set('general.category_itemid', $menu_table->id);
                $table->params = $params->toString();
                $table->store();

                $types = $params->get('general.type');
                if (!is_array($types)) {
                    settype($types, 'array');
                }
                $type_table = \Joomla\CMS\Table\Table::getInstance('Type', 'JoomcckTable');
                foreach ($types as $type_id) {
                    $type_table->load($this->ids['types'][$type_id]);
                    $params = new \Joomla\Registry\Registry($type_table->params);
                    $params->set('properties.item_itemid', $menu_table->id);
                    $type_table->params = $params->toString();
                    $type_table->store();
                    $type_table->reset();
                    $type_table->id = null;
                }
            }

            if (@$table->id) {
                $this->ids[$file][$id] = $table->id;
            }

            $table->reset();
            $table->id = null;
        }

        if ($file == 'categories') {
            $row        = array_shift($content);
            $section_id = @$row['section_id'];
            if ($section_id) {
                $query = "UPDATE `#__js_res_categories` SET `path` = REPLACE(`path`, 'root/', '') WHERE `section_id` = {$section_id}";
                $db->setQuery($query);
                $db->execute();
            }
        }

        unset($content);
    }
}
