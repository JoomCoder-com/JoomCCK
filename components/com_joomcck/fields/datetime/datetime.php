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

require_once JPATH_ROOT . '/components/com_joomcck/library/php/cdate.php';

class JFormFieldCDatetime extends CFormField
{
    public function __construct($field, $default)
    {
        parent::__construct($field, $default);

        $this->format = $this->params->get('params.format', 'YYYY/MM/DD');
        if ((int) $this->format == 100 && $this->params->get('params.custom', '') != '') {
            $this->format = $this->params->get('params.custom');
        }

        $this->filter_format = $this->params->get('params.filter_format', 'YYYY/MM/DD');
        if ((int) $this->filter_format == 100 && $this->params->get('params.custom', '') != '') {
            $this->filter_format = $this->params->get('params.custom');
        }
        $this->is_range = substr($this->params->get('params.template_input'),0,6) == 'range_';
        $this->db_format        = 'YYYY-MM-DD';
        $this->filter_db_format = 'YYYY-MM-DD';
        $this->is_time          = false;
        $this->filter_is_time   = false;
        if (
            stripos($this->format, 'h') !== false ||
            strpos($this->format, 'm') !== false ||
            stripos($this->format, 's') !== false ||
            stripos($this->format, 'k') !== false
        ) {
            $this->is_time = true;
            $this->db_format .= ' HH:mm:ss';
        }
        if (
            stripos($this->filter_format, 'h') !== false ||
            strpos($this->filter_format, 'm') !== false ||
            stripos($this->filter_format, 's') !== false ||
            stripos($this->filter_format, 'k') !== false
        ) {
            $this->filter_is_time = true;
            $this->filter_db_format .= ' HH:mm:ss';
        }
    }

    public function getInput()
    {
        $db         = JFactory::getDbo();
        $doc        = JFactory::getDocument();
        $lang       = strtolower(JFactory::getLanguage()->getTag());
        $short_lang = substr($lang, 0, 2);

        $doc->addScript(JUri::root(true) . '/media/mint/vendors/moment/min/moment.min.js');
        $doc->addScript(JUri::root(true) . '/media/mint/vendors/moment/locale/en-gb.js');
        if (JFile::exists(JPATH_ROOT . '/media/mint/vendors/moment/locale/' . $lang . '.js')) {
            $doc->addScript(JUri::root(true) . '/media/mint/vendors/moment/locale/' . $lang . '.js');
        }
        if (JFile::exists(JPATH_ROOT . '/media/mint/vendors/moment/locale/' . $short_lang . '.js')) {
            $doc->addScript(JUri::root(true) . '/media/mint/vendors/moment/locale/' . $short_lang . '.js');
        }
        $doc->addScript(JUri::root(true) . '/media/mint/vendors/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js');
        $doc->addStyleSheet(JUri::root(true) . '/media/mint/vendors/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css');

        settype($this->value, 'array');

        $this->default = @$this->value[0];
        if ($this->isnew && $this->params->get('params.input_default', 0)) {
            switch ($this->params->get('params.input_default', 0)) {
                case 'now':
                    $sql = 'SELECT NOW()';
                    break;
                case 100:
                    $sql = 'SELECT NOW()';
                    if ($this->params->get('params.custom_input')) {
                        $sql = "SELECT DATE_ADD(NOW(), INTERVAL {$this->params->get('params.custom_input')})";
                    }
                    break;
                default:
                    $sql = "SELECT DATE_ADD(NOW(), INTERVAL {$this->params->get('params.input_default', '1 day')})";
            }
            $db->setQuery($sql);
            $this->default = $db->loadResult();
        }

        $this->attr = ' class="' . $this->params->get('core.field_class', 'inputbox') . ($this->required ? ' required' : null) . '" ';
        $this->attr .= $this->required ? ' required="true" ' : null;

        return $this->_display_input();
    }

    public function validateField($value, $record, $type, $section)
    {
        if ($this->params->get('params.max_dates', 0) > 0 && count($value) > $this->params->get('params.max_dates', 0)) {
            $this->setError(JText::sprintf('F_ERROR_MAX', $this->params->get('params.max_dates', 0)));
        }
        if ($this->params->get('params.min_dates', 0) > 0 && count($value) < $this->params->get('params.min_dates', 0)) {
            $this->setError(JText::sprintf('F_ERROR_MIN', $this->params->get('params.min_dates', 0)));
        }
        parent::validateField($value, $record, $type, $section);
    }

    public function onJSValidate()
    {
        $js = '';

        $js .= "\n\t\tvar dat{$this->id} = jQuery('[name^=\"jform\\\\[fields\\\\]\\\\[{$this->id}\\\\]\"]').val();";
        $js .= "\n\t\tvar datlength{$this->id} =  jQuery('[name^=\"jform\\\\[fields\\\\]\\\\[{$this->id}\\\\]\"]').length";
        if ($this->params->get('params.max_dates', 0) > 0) {
            $js .= "\n\t\tif(datlength{$this->id} > " . $this->params->get('params.max_dates', 0) . "){hfid.push({$this->id}); isValid = false; errorText.push('" . JText::sprintf('F_ERROR_MAX', $this->params->get('params.max_dates', 0)) . "');}";
        }
        if ($this->params->get('params.min_dates', 0) > 0) {
            $js .= "\n\t\tif(datlength{$this->id} < " . $this->params->get('params.min_dates', 0) . "){hfid.push({$this->id}); isValid = false; errorText.push('" . JText::sprintf('F_ERROR_MIN', $this->params->get('params.min_dates', 0)) . "');}";
        }

        $js .= "\nconsole.log(jQuery('[name^=\"jform\\\\[fields\\\\]\\\\[{$this->id}\\\\]\"]'));";
        if ($this->required) {
            $js .= "\n\t\tif(!dat{$this->id}){hfid.push({$this->id}); isValid = false; errorText.push('" . JText::sprintf('CFIELDREQUIRED', $this->label) . "');}";
        }

        return $js;
    }

    public function onFilterWornLabel($section)
    {
        $out = [];
        foreach ($this->value as $value) {
            $out[] = $this->formatDate($value);
        }

        switch ((int) $this->params->get('params.filter_type')) {
            case 0:
                $out = $out[0];
                break;
            case 1:
                $out = JText::sprintf('F_WORN_AFTER', $out[0]);
                break;
            case 2:
                $out = JText::sprintf('F_WORN_BEFORE', $out[0]);
                break;
            case 3:
                $out = JText::sprintf('F_WORN_BETWEEN', $out[0], @$out[1]);
                break;
        }

        return $out;
    }

    public function onFilterWhere($section, &$query)
    {
        if (!$this->value) {
            return;
        }
        $db  = JFactory::getDbo();
        $sql = $db->getQuery(true);

        $sql->select('v.record_id');
        $sql->from('#__js_res_record_values AS v');
        $sql->where('v.section_id = ' . $section->id);
        $sql->where('v.field_key = ' . $db->quote($this->key));

        switch ((int) $this->params->get('params.filter_type')) {
            case 0;
                $sql->where(sprintf("%s = %s", $this->_get_d(), $this->_get_d($this->value[0])));
                break;
            case 1;
                $sql->where(sprintf("%s >= %s", $this->_get_d(), $this->_get_d($this->value[0])));
                break;
            case 2;
                $sql->where(sprintf("%s <= %s", $this->_get_d(), $this->_get_d($this->value[0])));
                break;
            case 3;
                if ($this->is_range) {
                    if (count($this->value) > 1) {
                        $sql->where(sprintf("((%s >= %s) AND v.value_index = 0)", $this->_get_d(), $this->_get_d($this->value[0])));
                        $sql->where(sprintf("((%s <= %s) AND v.value_index = 1)", $this->_get_d(), $this->_get_d($this->value[1])));
                    }
                } else {
                    $sql->where(sprintf("%s >= %s", $this->_get_d(), $this->_get_d($this->value[0])));
                    $sql->where(sprintf("%s <= %s", $this->_get_d(), $this->_get_d($this->value[1])));
                }
                break;
        }
        //var_dump((string) $sql);exit;
        $ids = $this->getIds((string) $sql);

        return $ids;
    }

    protected function _get_d($val = null)
    {
        $db = JFactory::getDbo();
        if ($val === null) {
            $val = 'v.field_value';
        } else {
            $val = $db->quote($val);
        }
        if ($this->filter_is_time) {
            return $val;
        } else {
            return "DATE({$val})";
        }
    }

    public function onRenderFilter($section, $module = false)
    {
        $db         = JFactory::getDbo();
        $doc        = JFactory::getDocument();
        $lang       = strtolower(JFactory::getLanguage()->getTag());
        $short_lang = substr($lang, 0, 2);

        $doc->addScript(JUri::root(true) . '/media/mint/vendors/moment/min/moment.min.js');
        $doc->addScript(JUri::root(true) . '/media/mint/vendors/moment/locale/en-gb.js');
        if (JFile::exists(JPATH_ROOT . '/media/mint/vendors/moment/locale/' . $lang . '.js')) {
            $doc->addScript(JUri::root(true) . '/media/mint/vendors/moment/locale/' . $lang . '.js');
        }
        if (JFile::exists(JPATH_ROOT . '/media/mint/vendors/moment/locale/' . $short_lang . '.js')) {
            $doc->addScript(JUri::root(true) . '/media/mint/vendors/moment/locale/' . $short_lang . '.js');
        }
        $doc->addScript(JUri::root(true) . '/media/mint/vendors/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js');
        $doc->addStyleSheet(JUri::root(true) . '/media/mint/vendors/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css');

        return $this->_display_filter($section, $module);
    }

    public function onPrepareFullTextSearch($value, $record, $type, $section)
    {
        if (empty($value)) {
            return;
        }

        if (isset($value[0]) && is_array($value[0])) {
            $value = $value[0];

            return implode('-', $value);
        }

        return implode(',', $value);
    }

    public function onPrepareSave($value, $record, $type, $section)
    {
        if (!isset($value[0]) || $value[0] == '') {
            return [];
        }

        return $value;
    }

    public function onStoreValues($validData, $record)
    {
        ArrayHelper::clean_r($this->value);
        if (count($this->value) == 0) {
            return;
        }

        if ($this->params->get('params.ovr_ctime', 0)) {
            $value_date = $this->value[0];
            if ($this->params->get('params.type', 'single') == 'multiple') {
                $value = $this->value;
                sort($value);
                $value_date = $value[0];
            }

            $cdate = JFactory::getDate($value_date);

            if ($this->params->get('params.ctime_add', false)) {
                $cdate->add(DateInterval::createFromDateString($this->params->get('params.ctime_add')));
            }
            $record->ctime = $cdate->toSql();
        }
        if ($this->params->get('params.ovr_extime', 0)) {
            $value_date = $this->value[0];
            if ($this->params->get('params.type', 'single') == 'multiple') {
                $value = $this->value;
                sort($value);
                $value_date = $value[0];
            } elseif ($this->params->get('params.template_input', 0) == 'range_date_picker.php') {
                $value_date = $this->value[1];
            }

            $exdate = JFactory::getDate($value_date);

            if ($this->params->get('params.extime_add', false)) {
                $exdate->add(DateInterval::createFromDateString($this->params->get('params.extime_add')));
            }
            $record->extime = $exdate->toSql();
        }

        return $this->value;
    }

    public function onRenderFull($record, $type, $section)
    {
        return $this->_render('full', $record, $type, $section);
    }

    public function onRenderList($record, $type, $section)
    {
        return $this->_render('list', $record, $type, $section);
    }

    private function _render($client, $record, $type, $section)
    {
        if (!$this->value) {
            return;
        }
        settype($this->value, 'array');
        natsort($this->value);

        $date_format = $this->params->get('params.format_out', 'd M Y');
        if ((int) $date_format == 100) {
            $date_format = $this->params->get('params.custom_out', 'd M Y');
        }

        $dates = [];
        foreach ($this->value as $value) {
            $date = $this->formatDate($value);
            if ($this->params->get('params.filter_enable')) {
                $tip = ($this->params->get('params.filter_tip') ? JText::sprintf($this->params->get('params.filter_tip'), '<b>' . $this->label . '</b>', '<b>' . $date . '</b>') : null);

                switch ($this->params->get('params.filter_linkage')) {
                    case 1:
                        $date = FilterHelper::filterLink('filter_' . $this->id, $value, $date, $this->type_id, $tip, $section);
                        break;

                    case 2:
                        $date .= ' ' . FilterHelper::filterButton('filter_' . $this->id, $value, $this->type_id, $tip, $section, $this->params->get('params.filter_icon', 'funnel-small.png'));
                        break;
                }
            }
            $dates[] = $date;
        }

        $this->dates = $dates;

        return $this->_display_output($client, $record, $type, $section);
    }

    public function formatDate($value)
    {
        $date_format = $this->params->get('params.format_out', 'd M Y');
        if ((int) $date_format == 100) {
            $date_format = $this->params->get('params.custom_out', 'd M Y');
        }

        return date($date_format, strtotime($value));
    }

    // public function _getFormatted2($value, $only_format = false, $out = null)
    // {
    //     if ($value == '') {
    //         return '';
    //     }
    //     if (is_array($value)) {
    //         return;
    //     }
    //     $params = $this->params;
    //     $format = $params->get('params.format' . $out, 'd M Y');
    //     if ($format == 'custom' && $params->get('params.custom' . $out, '') != '') {
    //         $format = $params->get('params.custom' . $out, '');
    //     }
    //     $date = new CDate($value);
    //     $date = $date->format($date->convertFormat($format));
    //     if ($only_format) {
    //         return $date;
    //     }

    //     $value_date = strtotime($value);
    //     if ($params->get('params.computation', 'day') == 'day' && !$params->get('params.time', false)) {
    //         $value_date = strtotime('+1 day', $value_date);
    //     }

    //     $diff = time() - $value_date;
    //     $days = $diff / 3600 / 24;

    //     if ($days < 0) {
    //         $this->date_type = 'normal';
    //         if (abs($days) <= $params->get('params.notify_days', 30)) {
    //             $this->date_type = 'notify';
    //         }
    //     } else {
    //         $this->date_type = 'past';
    //     }
    //     $days = abs($days);
    //     switch ($params->get('params.computation', 'day')) {
    //         case 'round':
    //             $days = round($days);
    //             break;
    //         case 'int':
    //             $days = intval($days);
    //             break;
    //         case 'ceil':
    //             $days = ceil($days);
    //             break;
    //         case 'day':
    //             $days = intval($days) + 1;
    //             break;
    //     }

    //     switch ($params->get('params.mode', 2)) {
    //         case '1':
    //             $out = $this->_getDay($days);
    //             break;

    //         case '2':
    //             $out = $this->_getDate($date);
    //             break;

    //         case '3':
    //             $_day  = $this->_getDay($days);
    //             $_date = $this->_getDate($date);
    //             if ($params->get('params.show_days', 1) == 1) {
    //                 $out = $_day . ' ' . $params->get('params.date_days_separator', ' ') . ' ' . $_date;
    //             } else {
    //                 $out = $_date . ' ' . $params->get('params.date_days_separator', ' ') . ' ' . $_day;
    //             }
    //             break;
    //         case '4':
    //             $out = $this->_getAge($date, $value);
    //             break;
    //     }

    //     return $out;
    // }

    // private function _getColor($param_name)
    // {
    //     if ($color = $this->params->get('params.' . $param_name, '')) {
    //         $color = 'style="color: ' . $color . '"';
    //     }

    //     return $color;
    // }

    // private function _getDate($date)
    // {
    //     $style = $this->params->get('params.date_style');
    //     $out   = JText::sprintf('%s %s %s', JText::_($this->params->get('params.date_before', '')), $date, JText::_($this->params->get('params.date_after', '')));
    //     $out   = JText::sprintf('<%s %s>%s</%s>', $this->params->get('params.' . $this->date_type . '_style', $style), $this->_getColor($this->date_type . '_color'), $out, $this->params->get('params.' . $this->date_type . '_style', $style));

    //     return $out;
    // }

    // private function _getDay($days)
    // {
    //     $style = $this->params->get('params.date_style');
    //     $out   = JText::sprintf('%s %s %s', JText::_($this->params->get('params.' . $this->date_type . '_before')), $days, JText::_($this->params->get('params.' . $this->date_type . '_after')));
    //     if ($days == 1 && $this->date_type == 'notify') {
    //         $out .= ' (' . $this->params->get('params.notify_msg') . ')';
    //     }
    //     $out = JText::sprintf('<%s %s>%s</%s>', $this->params->get('params.' . $this->date_type . '_style', $style), $this->_getColor($this->date_type . '_color'), $out, $this->params->get('params.' . $this->date_type . '_style', $style));

    //     return $out;
    // }

    // private function _getAge($date, $value)
    // {
    //     $now    = JFactory::getDate();
    //     $b_date = JFactory::getDate($value);
    //     $age    = $b_date->diff($now)->y;

    //     $age_notify = $this->params->get('params.age_notify', false);
    //     $age_expire = $this->params->get('params.age_expire', false);

    //     $this->date_type = 'normal';
    //     if ($age_notify && $age > $age_notify) {
    //         $this->date_type = 'notify';
    //     }

    //     if ($age_expire && $age >= $age_expire) {
    //         $this->date_type = 'past';
    //     }

    //     $style = $this->params->get('params.age_style');
    //     $age   = JText::sprintf('%s %s %s', JText::_($this->params->get('params.age_before', '')), $age, JText::_($this->params->get('params.age_after', '')));
    //     $age   = JText::sprintf('<%s %s>%s</%s>', $this->params->get('params.' . $this->date_type . '_style', $style), $this->_getColor($this->date_type . '_color'), $age, $this->params->get('params.' . $this->date_type . '_style', $style));

    //     switch ($this->params->get('params.age_format', '1')) {
    //         case '1':
    //             $result = $age;
    //             break;
    //         case '2':
    //             $result = $age . ' ' . $this->params->get('params.date_age_separator', ' ') . ' ' . $this->_getDate($date);
    //             break;
    //         case '3':
    //             $result = $this->_getDate($date) . ' ' . $this->params->get('params.date_age_separator', ' ') . ' ' . $age;
    //             break;
    //         case 'custom':
    //             $format = $this->params->get('params.age_custom', '[AGE], [DATE]');
    //             $date   = $this->_getDate($date);

    //             $result = str_replace(['[AGE]', '[DATE]'], [$age, $date], JText::_($format));

    //             break;
    //     }

    //     return $result;
    // }

    public function getCalendarEvents($post)
    {
        $app   = JFactory::getApplication();
        $start = date('Y-m-d', ($app->input->get('from') / 1000));
        $end   = date('Y-m-d', ($app->input->get('to') / 1000));

        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        if ($this->params->get('params.type', 'single') == 'range') {

            $query->select('rv1.record_id');
            $query->from('#__js_res_record_values AS rv1');
            $query->leftJoin("#__js_res_record_values AS rv2 ON
				rv2.record_id = rv1.record_id AND
				rv2.value_index = 1 AND
				rv2.field_key = '{$this->key}'");
            $query->where("rv1.field_key = '$this->key'");
            $query->where('rv1.value_index = 0');
            $query->where("(
				(DATE(rv1.field_value) BETWEEN '{$start}' AND '{$end}')
				OR
				(DATE(rv2.field_value) BETWEEN '{$start}' AND '{$end}')
				OR
				(DATE(rv1.field_value) < '{$start}'	AND DATE(rv2.field_value) > '{$end}')
			)");
        } else {
            $query->select('record_id');
            $query->from('#__js_res_record_values');
            $query->where("field_key = '$this->key'");
            $query->where("(DATE(field_value) BETWEEN '{$start}' AND '{$end}' )");
        }

        $db->setQuery($query);
        $ids = $db->loadColumn();

        ArrayHelper::clean_r($ids);
        \Joomla\Utilities\ArrayHelper::toInteger($ids);

        if (empty($ids)) {
            return null;
        }

        $section        = ItemsStore::getSection($this->request->getInt('section_id'));
        $model          = MModelBase::getInstance('Records', 'JoomcckModel');
        $model->section = $section;
        $model->section->params->set('general.show_past_records', 1);
        $model->section->params->set('general.show_future_records', 1);

        $model->_id_limit = $ids;

        $query = str_replace("\n", ' ', $model->getListQuery());

        $db->setQuery($query);
        $list = $db->loadAssocList();

        $db->setQuery("SELECT id FROM `#__js_res_fields` WHERE published = 1 AND `key` = '" . $this->key . "'");
        $fields_ids = $db->loadColumn();

        foreach ($list as &$event) {
            $event['url'] = JRoute::_(Url::record($event['id']));
            $fields       = json_decode($event['fields'], true);

            $class = @$fields[$this->params->get('params.field_id_type')];
            if (is_array($class)) {
                $class = implode('', $class);
            }
            if ($class &&
                in_array(strtolower($class),
                    [
                        'event-warning', 'event-info', 'event-inverse', 'event-success',
                        'event-important'
                    ]
                )
            ) {
                $event['class'] = $class;
            }

            foreach ($fields_ids as $field_id) {
                if (!isset($fields[$field_id])) {
                    continue;
                }
                switch ($this->params->get('params.type', 'single')) {
                    case 'multiple':
                        break;
                    case 'range':
                        $event['start'] = strtotime($this->_getSourceDate($fields[$field_id][0], '00:00:00')) . '100';
                        $event['end']   = !empty($fields[$field_id][1]) ?
                        strtotime($this->_getSourceDate($fields[$field_id][1], '00:00:00')) . '300' :
                        strtotime($this->_getSourceDate($fields[$field_id][0], '00:00:00')) . '300';
                        break;
                    case 'single':
                    default:
                        $event['start'] = strtotime($this->_getSourceDate($fields[$field_id][0], '00:00:00')) . '100';
                        $event['end']   = strtotime($this->_getSourceDate($fields[$field_id][0], '00:00:00')) . '300';
                        break;
                }
                break;
            }
            unset($event['fields']);
        }

        return $list;
    }


    private function _getSourceDate($date, $time = '00:00:00')
    {
        $dates = explode(' ', $date);

        return $dates[0] . ' ' . $time;
    }

    public function onImportData($row, $params)
    {
        $data = $row->get($params->get('field.' . $this->id));
        if (!$data) {
            return null;
        }

        return [JDate::getInstance(strtotime($data))->toSql()];
    }

    public function onImport($value, $params, $record = null)
    {
        return $value;
    }
    public function onImportForm($heads, $defaults)
    {
        return $this->_import_fieldlist($heads, $defaults->get('field.' . $this->id));
    }
}
