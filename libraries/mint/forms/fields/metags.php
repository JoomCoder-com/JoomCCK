<?php

/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('JPATH_PLATFORM') || die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldMetags extends JFormField
{
    public $type = 'Metags';

    public function getInput()
    {
        $new   = JFile::exists(JPATH_ROOT . "/media/mint/vendors/flow/flow.js");
        $app   = JFactory::getApplication();
        $model = JModelLegacy::getInstance('Form', 'JoomcckModel');
        $type  = $model->getRecordType($app->input->getInt('type_id'));

        if ($new) {
            if (!$this->value) {
                $this->value = '[]';
            }

            $default = [];
            $value   = json_decode($this->value, 1);
            foreach ($value as $k => $v) {
                $default[] = [
                    "id"   => $k,
                    "text" => $v
                ];
            }

            $this->params = new JRegistry();

            $options['only_suggestions'] = 0;
            $options['can_add']          = 1;
            $options['can_delete']       = 1;
            $options['suggestion_limit'] = 10;
            $options['limit']            = $type->params->get('general.item_tags_max', 25);
            $options['suggestion_url']   = 'index.php?option=com_joomcck&task=ajax.tags_list&tmpl=component';

            return JHtml::_('mrelements.pills', "jform[tags]", "tags", $default, [], $options);
        }

        if (!$this->value) {
            $value = [];
        } else {
            $value = json_decode($this->value, 1);
            if (empty($value)) {
                $value = $this->value;
            }
        }

        if (!is_array($value) && !empty($value)) {
            $value = explode(',', $this->value);
        }
        ArrayHelper::clean_r($value);
        $default = [];
        foreach ($value as $tag) {
            $default[$tag] = $tag;
        }

        $this->params = new JRegistry();

        $options['coma_separate']  = 0;
        $options['only_values']    = 0;
        $options['min_length']     = 1;
        $options['max_result']     = 10;
        $options['case_sensitive'] = 0;
        $options['unique']         = 1;
        $options['highlight']      = 1;

        $options['max_width'] = $this->params->get('params.max_width', 500);
        $options['min_width'] = $this->params->get('params.min_width', 400);
        $options['max_items'] = $type->params->get('general.item_tags_max', 25);

        $options['ajax_url']  = 'index.php?option=com_joomcck&task=ajax.tags_list&tmpl=component';
        $options['ajax_data'] = '';

        return JHtml::_('mrelements.listautocomplete', "jform[tags]", "tags", $default, null, $options);
    }
}
