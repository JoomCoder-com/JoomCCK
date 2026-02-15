<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla 4 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

namespace Joomcck\Ui\Helpers;

use Joomcck\Assets\Webassets\Webassets;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;

defined('_JEXEC') or die();

/**
 * UI System Helper
 *
 * Handles Modern UI (Vue.js + Tailwind) vs Legacy UI (jQuery + Bootstrap) switching
 */
class UiSystemHelper
{
    /**
     * Cache for UI system setting
     * @var string|null
     */
    protected static $uiSystem = null;

    /**
     * Check if Modern UI system is enabled
     *
     * @return bool
     */
    public static function isModern(): bool
    {
        return self::getUiSystem() === 'modern';
    }

    /**
     * Check if Legacy UI system is enabled
     *
     * @return bool
     */
    public static function isLegacy(): bool
    {
        return self::getUiSystem() === 'legacy';
    }

    /**
     * Get current UI system setting
     *
     * @return string 'legacy' or 'modern'
     */
    public static function getUiSystem(): string
    {
        if (self::$uiSystem === null) {
            $config = ComponentHelper::getParams('com_joomcck');
            self::$uiSystem = $config->get('ui_system', 'legacy');
        }

        return self::$uiSystem;
    }

    /**
     * Load Modern UI assets (Vue.js + Tailwind CSS)
     * Should be called early in the page load process
     *
     * @return void
     */
    public static function loadModernAssets(): void
    {
        if (!self::isModern()) {
            return;
        }

        // Use the already-initialized Webassets manager to ensure registry is available
        $wa = Webassets::$wa;

        if (!$wa) {
            // Fallback: get from Joomla factory if Webassets not yet initialized
            $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        }

        // Load Tailwind CSS + DaisyUI
        $wa->useStyle('com_joomcck.modern-css');

        // Load Vue.js and all components (jquery-bridge includes all dependencies)
        $wa->useScript('com_joomcck.jquery-bridge');
    }

    /**
     * Load specific Modern UI components
     *
     * @param array $components Array of component names to load
     * @return void
     */
    public static function loadComponents(array $components): void
    {
        if (!self::isModern()) {
            return;
        }

        $wa = Webassets::$wa ?: Factory::getApplication()->getDocument()->getWebAssetManager();

        $availableComponents = [
            'form-validator' => 'com_joomcck.form-validator',
            'jquery-bridge'  => 'com_joomcck.jquery-bridge',
        ];

        foreach ($components as $component) {
            if (isset($availableComponents[$component])) {
                $wa->useScript($availableComponents[$component]);
            }
        }
    }

    /**
     * Generate validation rules array from field configuration
     *
     * @param array $fields Array of field objects
     * @return array Validation rules for Vue form validator
     */
    public static function getValidationRules(array $fields): array
    {
        $rules = [];

        foreach ($fields as $field) {
            $fieldRules = [];
            $fieldId = $field->id ?? $field->field_id ?? null;

            if (!$fieldId) {
                continue;
            }

            $params = is_string($field->params ?? null)
                ? json_decode($field->params, true)
                : (array) ($field->params ?? []);

            // Required validation
            if (!empty($field->required) || !empty($params['core']['required'])) {
                $fieldRules[] = ['rule' => 'required'];
            }

            // Min/Max length for text fields
            if (!empty($params['params']['min_length'])) {
                $fieldRules[] = [
                    'rule' => 'minLength',
                    'params' => [(int) $params['params']['min_length']]
                ];
            }

            if (!empty($params['params']['max_length'])) {
                $fieldRules[] = [
                    'rule' => 'maxLength',
                    'params' => [(int) $params['params']['max_length']]
                ];
            }

            // Field type specific validations
            $fieldType = $field->field_type ?? '';

            switch ($fieldType) {
                case 'email':
                    $fieldRules[] = ['rule' => 'email'];
                    break;

                case 'url':
                    $fieldRules[] = ['rule' => 'url'];
                    break;

                case 'digits':
                    $fieldRules[] = ['rule' => 'numeric'];
                    if (!empty($params['params']['min'])) {
                        $fieldRules[] = [
                            'rule' => 'min',
                            'params' => [(float) $params['params']['min']]
                        ];
                    }
                    if (!empty($params['params']['max'])) {
                        $fieldRules[] = [
                            'rule' => 'max',
                            'params' => [(float) $params['params']['max']]
                        ];
                    }
                    break;

                case 'telephone':
                case 'phone':
                    $fieldRules[] = ['rule' => 'phone'];
                    break;
            }

            // Custom regex pattern
            if (!empty($params['params']['pattern'])) {
                $fieldRules[] = [
                    'rule' => 'pattern',
                    'params' => [$params['params']['pattern']]
                ];
            }

            if (!empty($fieldRules)) {
                $rules['field_' . $fieldId] = $fieldRules;
            }
        }

        return $rules;
    }

    /**
     * Output validation rules as JSON for JavaScript consumption
     *
     * @param array $fields Array of field objects
     * @return string JSON-encoded rules
     */
    public static function getValidationRulesJson(array $fields): string
    {
        return json_encode(self::getValidationRules($fields), JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get custom error messages for fields
     *
     * @param array $fields Array of field objects
     * @return array Custom messages by field
     */
    public static function getCustomMessages(array $fields): array
    {
        $messages = [];

        foreach ($fields as $field) {
            $fieldId = $field->id ?? $field->field_id ?? null;

            if (!$fieldId) {
                continue;
            }

            $params = is_string($field->params ?? null)
                ? json_decode($field->params, true)
                : (array) ($field->params ?? []);

            // Get custom error message from field config
            if (!empty($params['params']['error_message'])) {
                $messages['field_' . $fieldId] = [
                    '_default' => $params['params']['error_message']
                ];
            }
        }

        return $messages;
    }

    /**
     * Check if we should use Vue-based validation for a form
     *
     * @return bool
     */
    public static function useVueValidation(): bool
    {
        return self::isModern();
    }

    /**
     * Reset cached UI system value (useful for testing)
     *
     * @return void
     */
    public static function reset(): void
    {
        self::$uiSystem = null;
    }
}
