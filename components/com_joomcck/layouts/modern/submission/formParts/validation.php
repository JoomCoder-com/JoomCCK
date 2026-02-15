<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Validation Initialization Layout
 *
 * Initializes Vue.js form validation with generated rules.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

extract($displayData);

// Default values
$formId = $formId ?? 'adminForm';
$validationConfig = $validationConfig ?? '{}';
$validationRules = $validationRules ?? [];

?>
<?php // Vue validation mount point - invisible component ?>
<div id="vue-validator-mount-<?php echo htmlspecialchars($formId); ?>" style="display: none;"></div>

<?php // Store validation rules as data attribute for potential use ?>
<script type="application/json" id="joomcck-validation-rules">
<?php echo json_encode($validationRules, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>

<?php // Add validation feedback styles specific to modern UI ?>
<style>
/* Modern validation feedback */
.jcck-modern-form-wrapper .field-error-message {
    display: none;
}

.jcck-modern-form-wrapper .field-error-message:not(:empty) {
    display: block;
    animation: jcck-shake 0.4s ease-in-out;
}

.jcck-modern-form-wrapper .validation-error {
    display: block;
    padding: 0.25rem 0;
}

.jcck-modern-form-wrapper input.border-red-500,
.jcck-modern-form-wrapper select.border-red-500,
.jcck-modern-form-wrapper textarea.border-red-500 {
    animation: jcck-pulse-error 0.5s ease-in-out;
}

@keyframes jcck-shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-4px); }
    75% { transform: translateX(4px); }
}

@keyframes jcck-pulse-error {
    0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
    50% { box-shadow: 0 0 0 4px rgba(239, 68, 68, 0); }
}

/* Success state */
.jcck-modern-form-wrapper input.border-green-500,
.jcck-modern-form-wrapper select.border-green-500,
.jcck-modern-form-wrapper textarea.border-green-500 {
    animation: jcck-pulse-success 0.3s ease-in-out;
}

@keyframes jcck-pulse-success {
    0%, 100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
    50% { box-shadow: 0 0 0 4px rgba(34, 197, 94, 0); }
}

/* Ensure modern form inputs have base styling */
.jcck-modern-form-wrapper .control-group input:not([type="checkbox"]):not([type="radio"]),
.jcck-modern-form-wrapper .control-group select,
.jcck-modern-form-wrapper .control-group textarea {
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
</style>
