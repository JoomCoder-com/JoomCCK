<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Submission Form Entry Layout
 *
 * DaisyUI + Tailwind CSS version of the form entry point.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomcck\Layout\Helpers\Layout;
use Joomcck\Ui\Helpers\UiSystemHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

extract($displayData);

// Map form display types
$formTypes = ['plain', 'tabs', 'accordions', 'fieldsets', 'verticalTabs', 'cards'];

// Load asset manager
$wa = Webassets::$wa;

// Load Modern UI assets (Tailwind CSS + Vue.js)
UiSystemHelper::loadModernAssets();

// Load submission CSS (for any legacy styles still needed)
$wa->useStyle('com_joomcck.submission');

// Load Bootstrap tooltip (still needed for some components)
\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');

// Load custom CSS
if (!empty($current->tmpl_params->get('tmpl_params.css', ''))) {
    $wa->addInlineStyle($current->tmpl_params->get('tmpl_params.css', ''));
}

// Load custom JS
if (!empty($current->tmpl_params->get('tmpl_params.js', ''))) {
    $wa->addInlineScript($current->tmpl_params->get('tmpl_params.js', ''));
}

// Generate validation rules from fields
$validationRules = [];
$customMessages = [];

if (isset($current->sorted_fields)) {
    foreach ($current->sorted_fields as $group_id => $fields) {
        foreach ($fields as $field_id => $field) {
            $fieldRules = UiSystemHelper::getValidationRules([$field]);
            if (!empty($fieldRules)) {
                $validationRules = array_merge($validationRules, $fieldRules);
            }
        }
    }
}

// Also check main fields
if (isset($current->fields)) {
    $mainFieldRules = UiSystemHelper::getValidationRules($current->fields);
    $validationRules = array_merge($validationRules, $mainFieldRules);
}

$customMessages = UiSystemHelper::getCustomMessages($current->fields ?? []);

// Prepare validation config for Vue
$validationConfig = json_encode([
    'rules' => $validationRules,
    'messages' => $customMessages,
    'realTime' => true,
    'validateOn' => ['blur', 'change'],
    'scrollToError' => true,
    'cssClasses' => [
        'fieldError' => 'border-error ring-1 ring-error',
        'fieldValid' => 'border-success',
        'errorMessage' => 'text-error text-sm mt-1',
        'errorContainer' => 'field-error-message'
    ]
], JSON_UNESCAPED_UNICODE);

// Get form ID
$formId = 'adminForm';
?>

<?php // Modern UI wrapper with DaisyUI classes ?>
<div class="jcck-modern-form-wrapper" data-theme="joomcck">

    <?php // Render the form type layout (plain, tabs, etc.) ?>
    <?php echo Layout::render('core.submission.formTypes.' . $formTypes[$current->tmpl_params->get('tmpl_params.form_grouping_type', 0)], ['current' => $current]) ?>

    <?php // Vue validation initialization ?>
    <?php echo Layout::render('core.submission.formParts.validation', [
        'formId' => $formId,
        'validationConfig' => $validationConfig,
        'validationRules' => $validationRules
    ]) ?>

</div>

<?php // Initialize Vue Form Validator ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.JoomcckVue !== 'undefined' && typeof window.JoomcckVue.initFormValidator === 'function') {
        window.JoomcckVue.initFormValidator('<?php echo $formId; ?>', <?php echo $validationConfig; ?>);
    }
});
</script>
