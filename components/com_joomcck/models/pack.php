<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\Filesystem\Folder;

defined('_JEXEC') || die();

jimport('mint.mvc.model.admin');
jimport('joomla.archive');

include_once JPATH_ROOT . '/components/com_joomcck/library/php/helpers/template.php';

class JoomcckModelPack extends MModelAdmin
{
    private $_xml_paths = [];

    private $_tpl        = [];
    private $_tpl_config = [];
    private $_subtmpl    = [];
    private $rating      = [];

    public function __construct($config)
    {
        $app = \Joomla\CMS\Factory::getApplication();
        //$app->registerEvent('onContentBeforeDelete', [$this, 'onAfterModuleList']);
        //$config['event_before_delete'] = 'deleteZip';
        $this->option                  = 'com_joomcck';

        return parent::__construct($config);
    }

    public function getTable($type = 'Packs', $prefix = 'JoomcckTable', $config = [])
    {
        return \Joomla\CMS\Table\Table::getInstance($type, $prefix, $config);
    }

    public function getForm($data = [], $loadData = true)
    {
        $app = \Joomla\CMS\Factory::getApplication();

        $form = $this->loadForm('com_joomcck.pack', 'pack', ['control' => 'jform', 'load_data' => $loadData]);
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $data = \Joomla\CMS\Factory::getApplication()->getUserState('com_joomcck.edit.' . $this->getName() . '.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    public function save($data)
    {
        return parent::save($data);
    }

    protected function canDelete($record)
    {
        $user = \Joomla\CMS\Factory::getApplication()->getIdentity();

        return $user->authorise('core.delete', 'com_joomcck.pack.' . (int) $record->id);
    }

    protected function canEditState($record)
    {
        $user = \Joomla\CMS\Factory::getApplication()->getIdentity();

        return $user->authorise('core.edit.state', 'com_joomcck.pack.' . (int) $record->id);
    }

    public function delete(&$pks)
    {
        $result = parent::delete($pks);

        if ($result) {
            $db = \Joomla\CMS\Factory::getDbo();
            $db->setQuery("DELETE FROM #__js_res_packs_sections WHERE pack_id IN (" . implode(',', $pks) . ")");
            $db->execute();
        }
    }

    public function build($pack_id)
    {
        $db         = \Joomla\CMS\Factory::getDBO();
        $this->pack = $this->getItem($pack_id);

        MModelBase::addIncludePath(JPATH_ROOT . '/components/com_joomcck/models');
        $pack_sections = MModelBase::getInstance('Packsections', 'JoomcckModel')->getPackSectoins($pack_id);

        if (!$pack_sections) {
            \Joomla\CMS\Factory::getApplication()->enqueueMessage(\Joomla\CMS\Language\Text::sprintf('CPACKNOSECTIONS', $this->pack->name),'warning');

            return false;
        }

        define('VIEWS_ROOT', JPATH_ROOT . '/components/com_joomcck/views');
        define('PACK_KEY', $this->pack->key);
        define('PACK_ROOT', JPATH_CACHE . DIRECTORY_SEPARATOR . $this->pack->key);
        define('NAME_PART', ($this->pack->addkey ? '_' . PACK_KEY : ''));

        $section_ids = array_keys($pack_sections);
        $add_files   = array_filter(explode("\n", $this->pack->add_files));

        $type_ids = $type_ids_with_content = $type_ids_f_r_tmpls = $type_ids_f_tmpl = $field_ids = [0];

        $types_to_save = [];

        /**
         * List of categories to pack
         */
        $categories = [];

        /**
         * List of total types to pack in all sections and packing parameters
         */
        $type_pack_params = [];

        if (is_dir(PACK_ROOT)) {
            \Joomla\Filesystem\Folder::delete(PACK_ROOT);
        }
        \Joomla\Filesystem\Folder::create(PACK_ROOT);

        foreach ($pack_sections as $ps) {
            $section = ItemsStore::getSection($ps->section_id);
            if (is_array($section->params)) {
                $section->params = new \Joomla\Registry\Registry($section->params);
            }

            $this->_prepare_template($ps->params, $section->params, 'general.tmpl_list', 'records/tmpl', 'default_list_');
            $this->_prepare_template($ps->params, $section->params, 'general.tmpl_category', 'records/tmpl', 'default_cindex_');
            $this->_prepare_template($ps->params, $section->params, 'general.tmpl_compare', 'records/tmpl', 'default_list_');
            $this->_prepare_template($ps->params, $section->params, 'general.tmpl_markup', 'records/tmpl', 'default_markup_');

            $cats = $this->_getItems('categories', ['section_id = ' . $ps->section_id], 'level ASC');
            foreach ($cats as $c) {
                $cat_params = new \Joomla\Registry\Registry($c->params);
                $this->_prepare_templates($ps->params, $cat_params, 'records/tmpl');
                $c->params          = $cat_params->toString();
                $categories[$c->id] = $c;

                if ($c->image) {
                    $add_files[] = $c->image;
                }
            }

            $types = $ps->params->get('types', []);

            foreach ($types as $type_id => $type_settings) {
                $type_ids[$type_id] = $type_id;
                if (empty($type_pack_params[$type_id])) {
                    $type_pack_params[$type_id] = new stdClass();
                }
                // Merge parameters of the same types in different sections overriding if Yes and any of them
                foreach ($type_settings as $key => $value) {
                    $type_pack_params[$type_id]->$key = !empty($type_pack_params[$type_id]->$key) ? $type_pack_params[$type_id]->$key : $type_settings->$key;
                }
            }

            $section->params        = $section->params->toString();
            $sections[$section->id] = $section;
        }

        foreach ($type_pack_params as $type_id => $type_settings) {
            $type = ItemsStore::getType($type_id);
            if (!is_object($type->params)) {
                $type->params = new \Joomla\Registry\Registry($type->params);
            }
            $type_settings = new \Joomla\Registry\Registry($type_settings);
            if ($type_settings->get('copy_content')) {
                $type_ids_with_content[] = $type_id;
            }
            if ($type_settings->get('copy_field_record_templates')) {
                $type_ids_f_r_tmpls[] = $type_id;
            }
            if ($type_settings->get('copy_field_templates')) {
                $type_ids_f_tmpl[] = $type_id;
            }

            $form_tmpl_params = CTmpl::getTemplateParametrs('default_form_', $type->params->get('properties.tmpl_articleform'));
            $this->_prepare_template($type_settings, $form_tmpl_params, 'tmpl_params.tmpl_category', 'form/tmpl', 'default_category_');
            if ($type_settings->get('tmpl_category')) {
                $this->_subtmpl['default_form_' . $type->params->get('properties.tmpl_articleform')] = $form_tmpl_params;
            }

            $this->_prepare_template($type_settings, $type->params, 'properties.tmpl_article', 'record/tmpl', 'default_record_');
            $this->_prepare_template($type_settings, $type->params, 'properties.tmpl_articleform', 'form/tmpl', 'default_form_');
            $this->_prepare_template($type_settings, $type->params, 'properties.tmpl_comment', 'record/tmpl', 'default_comments_');

            if ($type_settings->get('rating')) {
                $this->rating[] = $type->params->get('properties.tmpl_rating');
            }

            $type->params             = $type->params->toString();
            $types_to_save[$type->id] = $type;
        }

        $fields = $this->_getItems('fields', ['type_id IN (' . implode(',', $type_ids) . ')']);

        foreach ($fields as &$field) {
            $f_params = new \Joomla\Registry\Registry($field->params);
            
            foreach ($f_params->toArray() as $key => $param) {
                if (is_array($param)) {
                    foreach ($param as $k => $value) {
                        if (empty($value)) {
                            continue;
                        }

                        if (in_array($field->type_id, $type_ids_f_tmpl) && strstr($k, 'template_') !== FALSE) {
                            $this->_xml_paths['site']['fields'] = TRUE;
                            $this->_copyFiledtemplates($field, $f_params);
                        }
                        
                        if (in_array($field->type_id, $type_ids_f_r_tmpls) && strstr($k, 'tmpl_') !== FALSE) {

                            $parts = explode('.', $value);
                            
                            switch ($k) {
                                case 'tmpl_rating':
                                $this->rating[] = $parts[0];
                                break;
                                case 'tmpl_list':
                                    $this->_prepare_template(null, $f_params, $key.'.'.$k, 'records/tmpl', 'default_list_');
                                break;
                                case 'tmpl_full':
                                    $this->_prepare_template(null, $f_params, $key.'.'.$k, 'record/tmpl', 'default_record_');
                                break;
                            }
                        }
                    }
                }
            }
            
            $field->params = $f_params->toString();

            $field_ids[] = $field->id;
        }

        $records = $this->_getItems('record', ['type_id IN (' . implode(',', $type_ids_with_content) . ') AND section_id IN ( ' . implode(',', $section_ids) . ')']);

        $this->_packFile('sections', $sections);
        $this->_packFile('categories', $categories);
        $this->_packFile('types', $types_to_save);
        $this->_packFile('records', $records);

        /* fields */
        $this->_packFile('fields', $fields);
        $this->_packFile('fields_group', $this->_getItems('fields_group', ['type_id IN (' . implode(',', $type_ids) . ')']));
        $this->_packFile('field_multilevelselect', $this->_getItems('field_multilevelselect', ['field_id IN (' . implode(',', $field_ids) . ')'], 'lft'));
        $this->_packFile('field_stepaccess', $this->_getItems('field_stepaccess', ['field_id IN (' . implode(',', $field_ids) . ')']));

        $user_categories = $this->_getItems('category_user', ['section_id IN ( ' . implode(',', $section_ids) . ')']);
        $this->_packFile('category_user', $user_categories);
        foreach ($user_categories as $uc) {
            if ($uc->icon) {
                $dest   = PACK_ROOT . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . PACK_KEY . DIRECTORY_SEPARATOR . 'usercategories' . DIRECTORY_SEPARATOR . $uc->user_id . DIRECTORY_SEPARATOR . $uc->icon;
                $folder = dirname($dest);
                if (!is_dir($folder)) {
                    \Joomla\Filesystem\Folder::create($folder);
                }
                \Joomla\Filesystem\File::copy(JPATH_ROOT . '/images/usercategories' . DIRECTORY_SEPARATOR . $uc->user_id . DIRECTORY_SEPARATOR . $uc->icon, $dest);
            }
        }

        $record_ids = array_keys($records);
        if (!empty($record_ids)) {
            $comments = $this->_getItems('comments', ['record_id IN (' . implode(',', $record_ids) . ')'], 'level ASC');
            $this->_packFile('comments', $comments);
            $comments[0] = 0;

            $this->_packFile('vote', $this->_getItems('vote', ['(ref_id IN (' . implode(',', $record_ids) . ') AND ref_type = "record") OR (ref_id IN (' . implode(',', array_keys($comments)) . ') AND ref_type = "comment")']));
            $this->_packFile('tags_history', $this->_getItems('tags_history', ['record_id IN (' . implode(',', $record_ids) . ')']));
            $this->_packFile('tags', $this->_getItems('tags', ['id IN ( SELECT tag_id FROM #__js_res_tags_history WHERE record_id IN (' . implode(',', $record_ids) . '))']));
            $this->_packFile('favorite', $this->_getItems('favorite', ['record_id IN (' . implode(',', $record_ids) . ')']));
            $this->_packFile('record_values', $this->_getItems('record_values', ['record_id IN (' . implode(',', $record_ids) . ')']));
            $this->_packFile('record_category', $this->_getItems('record_category', ['record_id IN (' . implode(',', $record_ids) . ')']));
            $this->_packFile('sales', $this->_getItems('sales', ['record_id IN (' . implode(',', $record_ids) . ')']));

            $files = $this->_getItems('files', ['record_id IN (' . implode(',', $record_ids) . ') AND saved = 1']);
            $this->_packFile('files', $files);
            $joomcck_params = \Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck');
            foreach ($files as $file) {
                $params        = new \Joomla\Registry\Registry($fields[$file->field_id]->params);
                $subfolder     = $params->get('params.subfolder', pathinfo($file->filename,PATHINFO_EXTENSION));
                $file_fullpath = str_replace($joomcck_params->get('general_upload') . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR, '', $file->fullpath);
                $dest          = PACK_ROOT . '/uploads/' . $subfolder . DIRECTORY_SEPARATOR . $file_fullpath;
                $folder        = dirname($dest);
                if (!is_dir($folder)) {
                    \Joomla\Filesystem\Folder::create($folder);
                }
                \Joomla\Filesystem\File::copy(JPATH_ROOT . DIRECTORY_SEPARATOR . $joomcck_params->get('general_upload') . DIRECTORY_SEPARATOR . $subfolder . DIRECTORY_SEPARATOR . $file_fullpath, $dest);
            }

            $users = $this->_getUsers($record_ids);
            $this->_packFile('users', $users);
            $user_ids = array_keys($users);

            if ($user_ids) {
                $this->_packFile('notifications', $this->_getItems('notifications', ['ref_2 IN ( ' . implode(',', $section_ids) . ') AND user_id IN(' . implode(',', $user_ids) . ')']));
                $this->_packFile('subscribe', $this->_getItems('subscribe', ['section_id IN ( ' . implode(',', $section_ids) . ') AND user_id IN(' . implode(',', $user_ids) . ')']));
                $this->_packFile('subscribe_user', $this->_getItems('subscribe_user', ['section_id IN ( ' . implode(',', $section_ids) . ') AND (user_id IN(' . implode(',', $user_ids) . ')  OR u_id IN(' . implode(',', $user_ids) . '))']));
                $this->_packFile('subscribe_cat', $this->_getItems('subscribe_cat', ['section_id IN ( ' . implode(',', $section_ids) . ') AND user_id IN(' . implode(',', $user_ids) . ')']));

                $this->_packFile('moderators', $this->_getItems('moderators', ['section_id IN ( ' . implode(',', $section_ids) . ') AND user_id IN(' . implode(',', $user_ids) . ')']));
                $this->_packFile('user_post_map', $this->_getItems('user_post_map', ['section_id IN ( ' . implode(',', $section_ids) . ') AND user_id IN(' . implode(',', $user_ids) . ')']));
                $this->_packFile('user_options', $this->_getItems('user_options', ['user_id IN(' . implode(',', $user_ids) . ')']));
                $this->_packFile('user_options_autofollow', $this->_getItems('user_options_autofollow', ['section_id IN ( ' . implode(',', $section_ids) . ') AND user_id IN(' . implode(',', $user_ids) . ')']));
            }
        }

        if (!empty($add_files)) {
            foreach ($add_files as $file) {
                $file = trim($file);
                if (!$file) {
                    continue;
                }
                $path = JPATH_ROOT . DIRECTORY_SEPARATOR . $file;
                $path =\Joomla\Filesystem\Path::clean($path);

                if (is_file($path)) {
                    $folder =\Joomla\Filesystem\Path::clean(PACK_ROOT . DIRECTORY_SEPARATOR . 'add' . DIRECTORY_SEPARATOR . $file);
                    \Joomla\Filesystem\Folder::create(dirname($folder));
                    \Joomla\Filesystem\File::copy($path, $folder);
                }
                if (is_dir($path)) {
                    \Joomla\Filesystem\Folder::copy($path,\Joomla\Filesystem\Path::clean(PACK_ROOT . DIRECTORY_SEPARATOR . 'add' . DIRECTORY_SEPARATOR . $file), null, true);
                }
            }
        }

        $this->_add_folder_path_additionals();

        foreach ($pack_sections as $ps) {
            $ps->params = $ps->params->toString();
        }

        $this->pack->sections = $pack_sections;
        $this->_packFile('pack', $this->pack);

        \Joomla\Filesystem\File::copy(JPATH_COMPONENT . '/library/php/pack/install.php', PACK_ROOT . '/install.pack.php');

        $this->_copy_tmpls();
        $this->_xml_paths['site']['configs'] = TRUE;
        $this->_generateXml();

        //Archiving
        $zip_filename = JPATH_CACHE . '/pack_joomcck.' . \Joomla\CMS\Filter\OutputFilter::stringURLSafe($this->pack->get('name', 'Pack name')) . '(' . str_replace('pack', '', PACK_KEY) . ').j3.v.9.' . ($this->pack->version + 1) . '.zip';

        $zipper = new Zipper();
        $zipper->open($zip_filename, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
        $zipper->addDir(PACK_ROOT);
        $zipper->close();

        \Joomla\Filesystem\Folder::delete(PACK_ROOT);

        $table = $this->getTable();
        $table->load($this->pack->id);
        $table->btime = \Joomla\CMS\Factory::getDate()->toSql();
        $table->version += 1;
        $table->store();

        return true;
    }

    private function _copyFiledtemplates($field, $params) {
        $map = [
            "template_input" => "input",
            "template_output_list" => "output",
            "template_output_full" => "output",
            "template_filter" => "filter",
            "template_filter_module" => "filter",
            "template_body" => "email",
            "template_marker" => "markers_list",
            "template_window" => "window"
        ];
        foreach($map as $param => $folder) {
            $file = $params->get('params.'.$param);
            if($file) {
                $src  = sprintf(JPATH_ROOT . '/components/com_joomcck/fields/%s/tmpl/%s/%s', $field->field_type, $folder, $file);
                $dest = PACK_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, sprintf('site/fields/%s/tmpl/%s/%s', $field->field_type, $folder, $file));
        
                $folder = dirname($dest);
        
                if (!is_dir($folder)) {
                    \Joomla\Filesystem\Folder::create($folder);
                }
                \Joomla\Filesystem\File::copy($src, $dest);
            }
        }
    }
    private function _getTmplName($name)
    {
        if ($this->pack->addkey) {
            $name    = explode('.', $name);
            $name[0] = $name[0] . NAME_PART;
            if (count($name) < 2) {
                return $name[0];
            }

            return $name[0] . '.' . $name[1];
        } else {
            return $name;
        }
    }

	private function _prepare_templates($params, &$tmpls, $prefix = NULL)
	{
		$list = $tmpls->get($prefix . 'tmpl_list', array());
		settype($list, 'array');
		$_tpl_names = array();
		foreach($list AS $tplname)
		{
			if(!$tplname)
			{
				continue;
			}
			$this->_tpl_config[] = 'default_list_' . $tplname;
			if($params->get('list') && $tmpls->get($prefix . 'tmpl_list', array()))
			{
				$this->_tpl[] = 'records/tmpl/default_list_' . $tplname;
				$_tpl_names[] = $this->_getTmplName($tplname);
			}
		}
		if($_tpl_names)
		{
			$tmpls->set($prefix . 'tmpl_list', $_tpl_names);
		}
		$this->_tpl_config[] = 'default_cindex_' . $tmpls->get($prefix . 'tmpl_category');
		if($params->get('cat_index') && $tmpls->get($prefix . 'tmpl_category'))
		{
			$this->_tpl[] = 'records/tmpl/default_cindex_' . $tmpls->get($prefix . 'tmpl_category');
			$tmpls->set($prefix . 'tmpl_category', $this->_getTmplName($tmpls->get($prefix . 'tmpl_category')));
		}
		$this->_tpl_config[] = 'default_list_' . $tmpls->get($prefix . 'tmpl_compare');
		if($params->get('compare') && $tmpls->get($prefix . 'tmpl_compare'))
		{
			$this->_tpl[] = 'records/tmpl/default_list_' . $tmpls->get($prefix . 'tmpl_compare');
			$tmpls->set($prefix . 'tmpl_compare', $this->_getTmplName($tmpls->get($prefix . 'tmpl_compare')));
		}
		$this->_tpl_config[] = 'default_markup_' . $tmpls->get($prefix . 'tmpl_markup');
		if($params->get('markup') && $tmpls->get($prefix . 'tmpl_markup'))
		{
			$this->_tpl[] = 'records/tmpl/default_markup_' . $tmpls->get($prefix . 'tmpl_markup');
			$tmpls->set($prefix . 'tmpl_markup', $this->_getTmplName($tmpls->get($prefix . 'tmpl_markup')));
		}
	}

    private function _copy_tmpls()
    {
        $this->_tpl = array_unique($this->_tpl);

        foreach ($this->_tpl as $tplname) {
            $this->_copy_tmpl($tplname);
        }
        $this->_copy_tmpl_config();

        $rating = array_unique($this->rating);
        foreach ($rating as $tplname) {
            $this->_copy_rating_tmpl($tplname);
        }

    }

    private function _copy_tmpl($name)
    {
        $name = explode('.', $name);
        if (count($name) < 2) {
            return;
        }
        $name = $name[0];
        $name = str_replace(NAME_PART, '', $name);
        $src  = JPATH_ROOT . '/components/com_joomcck/views' . DIRECTORY_SEPARATOR . $name;
        $dest = PACK_ROOT . DIRECTORY_SEPARATOR . 'site/views' . DIRECTORY_SEPARATOR . $name . NAME_PART;

        $folder = dirname($dest);

        if (!is_dir($folder)) {
            \Joomla\Filesystem\Folder::create($folder);
        }

        \Joomla\Filesystem\File::copy($src . '.php', $dest . '.php');
        $this->_xml_paths['site']['views'] = TRUE;

        if (is_file($src . '.css')) {
            \Joomla\Filesystem\File::copy($src . '.css', $dest . '.css');
        }
        if (is_file($src . '.png')) {
            \Joomla\Filesystem\File::copy($src . '.png', $dest . '.png');
        }
        if (is_file($src . '.xml')) {
            \Joomla\Filesystem\File::copy($src . '.xml', $dest . '.xml');
        }
        if (is_file($src . '.js')) {
            \Joomla\Filesystem\File::copy($src . '.js', $dest . '.js');
        }
        if (is_dir($src)) {
            \Joomla\Filesystem\Folder::copy($src, $dest, '', true);
        }
    }

    private function _copy_tmpl_config()
    {
        $configs = array_unique($this->_tpl_config);
        $src     = JPATH_ROOT . '/components/com_joomcck/configs/';
        $dest    = PACK_ROOT . '/site/configs/';

        $this->_packFile('configs', $configs);

        foreach ($configs as $config) {
            if (is_file($src . $config . '.json')) {
                $cnf = json_decode(file_get_contents($src . $config . '.json'), true);
                foreach ($cnf as $key => $val) {
                    foreach ($val as $k => $v) {
                        $keys = explode('_', $k);
                        if ($keys[0] != 'tmpl') {
                            continue;
                        }

                        unset($keys[0]);
                        $name                = implode('_', $keys);
                        $this->_tpl_config[] = 'default_' . $name . '_' . $v;
                    }
                }
            }
        }
        $configs = array_unique($this->_tpl_config);

        if (!is_dir($dest)) {
            \Joomla\Filesystem\Folder::create($dest);
        }
        foreach ($configs as $config) {
            if (is_file($src . $config . '.json')) {
                \Joomla\Filesystem\File::copy($src . $config . '.json', $dest . $this->_getTmplName($config) . '.json');
            } else {
                $parts = explode('.', $config);
                if (is_file($src . $parts[0] . '.json')) {
                    \Joomla\Filesystem\File::copy($src . $parts[0] . '.json', $dest . $this->_getTmplName($config) . '.json');
                }
            }
        }

        if (!empty($this->_subtmpl)) {
            foreach ($this->_subtmpl as $tmpl => $params) {
                $str = $params->toString();
                \Joomla\Filesystem\File::write($dest . $tmpl . '.json', $str);
            }
        }
    }

    private function _copy_rating_tmpl($name)
    {
        $name = explode('.', $name);
        $name = $name[0];
        $src  = JPATH_ROOT . '/components/com_joomcck/views/rating_tmpls' . DIRECTORY_SEPARATOR;
        $dest = PACK_ROOT . DIRECTORY_SEPARATOR . 'site/views/rating_tmpls' . DIRECTORY_SEPARATOR;

        $folder = dirname($dest);

        if (!is_dir($folder)) {
            \Joomla\Filesystem\Folder::create($folder);
        }

        if (is_dir($src . $name . '_img')) {
            \Joomla\Filesystem\Folder::copy($src . $name . '_img', $dest . $name . '_img', '', true);
        }

        \Joomla\Filesystem\File::copy($src . 'rating_' . $name . '.php', $dest . 'rating_' . $name . '.php');
        \Joomla\Filesystem\File::copy($src . 'rating_' . $name . '.xml', $dest . 'rating_' . $name . '.xml');

        $this->_xml_paths['site']['views'] = TRUE;
    }

    private function _prepare_template($params, &$tmpls, $pname, $root, $prefix, $sname = null)
    {
        $names = explode('.', $pname);

        if (is_array($tmpls->get($pname))) {
            $_tpl_names = [];
            foreach ($tmpls->get($pname) as $tplname) {
                if (!$tplname) {
                    continue;
                }
                $this->_tpl_config[] = $prefix . $tplname;
                if ($params === NULL || $params->get($names[1]) && $tplname) {
                    $this->_tpl[] = $root . '/' . $prefix . $tplname;
                    $_tpl_names[] = $this->_getTmplName($tplname);
                }
            }
            if ($_tpl_names) {
                $tmpls->set($pname, $_tpl_names);
            }
        } else {
            $this->_tpl_config[] = $prefix . $tmpls->get($pname);
            if ($params === NULL || $params->get($sname ? $sname : $names[1])) {
                $this->_tpl[] = $root . '/' . $prefix . $tmpls->get($pname);
                $tmpls->set($pname, $this->_getTmplName($tmpls->get($pname)));
            }
        }

    }

    private function _packFile($filename, $object)
    {
        if (!$object) {
            $object = [];
        }
        $json = json_encode($object);
        \Joomla\Filesystem\File::write(PACK_ROOT . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . PACK_KEY . DIRECTORY_SEPARATOR . $filename . '.json', $json);
    }

    /**
     * Get list of records in joomcck table
     *
     *
     * @param  string  $table_name Joomcck table name without prefix
     * @param  array   $where      list of conditions
     * @param  string  $order      result ordering
     * @return array
     */
    private function _getItems($table_name, $where = [], $order = 'id')
    {
        $db = \Joomla\CMS\Factory::getDbo();
        $db->setQuery("SELECT * FROM `#__js_res_{$table_name}` WHERE " . implode(' AND ', $where) . " ORDER BY {$order}");
        $items = $db->loadObjectList('id');

        return is_array($items) ? $items : [];
    }

    private function _getUsers($record_ids)
    {
        $db    = \Joomla\CMS\Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__users');
        $query->where('id IN (SELECT user_id FROM #__js_res_record WHERE id IN (' . implode(',', $record_ids) . ') )
		OR id IN(SELECT user_id FROM #__js_res_comments WHERE record_id IN (' . implode(',', $record_ids) . ') )');
        $db->setQuery($query);
        $users = $db->loadObjectList('id');

        return $users;
    }

    private function _add_folder_path_additionals()
    {
        $dest = PACK_ROOT . '/add';
        if (is_dir($dest)) {
            $files = \Joomla\Filesystem\Folder::files($dest);

            foreach ($files as $file) {
                $this->_xml_paths['add'][] = $file;
            }

            if ($folders = \Joomla\Filesystem\Folder::folders($dest)) {
                foreach ($folders as $folder) {
                    $this->_xml_paths['addfolder'][] = $folder;
                }
            }
        }

        $dest = PACK_ROOT . '/uploads';
        if (is_dir($dest)) {
            $files = \Joomla\Filesystem\Folder::files($dest);

            foreach ($files as $file) {
                $this->_xml_paths['files'][] = $file;
            }

            if ($folders = \Joomla\Filesystem\Folder::folders($dest)) {
                foreach ($folders as $folder) {
                    $this->_xml_paths['filesfolders'][] = $folder;
                }
            }
        }

    }

    private function _generateXml()
    {
        $install = file_get_contents(JPATH_COMPONENT . '/library/php/pack/install.xml');

        $install = str_replace('[NAME]', $this->pack->get('name', 'Pack name'), $install);
        $install = str_replace('[AUTHOR_NAME]', $this->pack->get('author_name', 'Author name not set'), $install);
        $install = str_replace('[AUTHOR_EMAIL]', $this->pack->get('author_email', 'Author email  not set'), $install);
        $install = str_replace('[AUTHOR_URL]', $this->pack->get('author_url', 'Author url  not set'), $install);
        $install = str_replace('[CTIME]', $this->pack->get('ctime'), $install);
        $install = str_replace('[COPYRIGHT]', $this->pack->get('copyright', ''), $install);
        $install = str_replace('[VERSION]', (int) $this->pack->get('version', '') + 1, $install);
        $install = str_replace('[DESCR]', $this->pack->get('description', ''), $install);
        $install = str_replace('[KEY]', $this->pack->get('key'), $install);

        $replace = '';
        if (!empty($this->_xml_paths['site'])) {
            foreach ($this->_xml_paths['site'] as $folder => $en) {
                $replace .= "\r\n\t\t\t<folder>{$folder}</folder>";
            }
        }
        $install = str_replace('[FRONT]', $replace, $install);

        $replace = '';
        $add     = [];
        if (isset($this->_xml_paths['add'])) {
            foreach ($this->_xml_paths['add'] as $file) {
                $file = str_replace('\\', '/', $file);
                $replace .= "\t\t\t<filename>{$file}</filename>\r\n";
            }
        }
        if (isset($this->_xml_paths['addfolder'])) {
            foreach ($this->_xml_paths['addfolder'] as $folder) {
                $folder = str_replace('\\', '/', $folder);
                $replace .= "\t\t\t<folder>{$folder}</folder>\r\n";
            }
        }
        if ($replace) {
            $add[] = "\t\t<files folder=\"add\">\n" . $replace . "\t\t</files>";
        }

        $replace = '';
        if (isset($this->_xml_paths['files'])) {
            foreach ($this->_xml_paths['files'] as $file) {
                $file = str_replace('\\', '/', $file);
                $replace .= "\t\t\t<filename>{$file}</filename>\r\n";
            }
        }
        if (isset($this->_xml_paths['filesfolders'])) {
            foreach ($this->_xml_paths['filesfolders'] as $folder) {
                $folder = str_replace('\\', '/', $folder);
                $replace .= "\t\t\t<folder>{$folder}</folder>\r\n";
            }
        }
        if ($replace) {
            $add[] = "\t\t<files folder=\"uploads\" target=\"" . trim(\Joomla\CMS\Component\ComponentHelper::getParams('com_joomcck')->get('general_upload'), '/') . "\">\n" . $replace . "\t\t</files>";
        }
        $install = str_replace('[ADD]', implode("\n", $add), $install);

        \Joomla\Filesystem\File::write(PACK_ROOT . DIRECTORY_SEPARATOR . 'pack.xml', $install);
    }
}

class ZipRemover extends Joomla\Event\Event
{
    public function deleteZip($context, $table)
    {

        $filename = JPATH_ROOT . '/cache/pack_' . $table->key . '.zip';
        if (is_file($filename)) {
            \Joomla\Filesystem\File::delete($filename);
        }
    }
}

class Zipper extends ZipArchive
{
    public function addDir($path)
    {
        $nodes = glob($path . '/*');

        foreach ($nodes as $node) {
            if (is_dir($node)) {
                $this->addDir($node);
            } else if (is_file($node)) {
                $this->addFile($node, str_replace(PACK_ROOT, PACK_KEY, $node));
            }
        }
    }

}
