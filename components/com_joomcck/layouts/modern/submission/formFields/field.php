<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Field Layout
 *
 * DaisyUI + Tailwind CSS version of the form field wrapper.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

// Determine label break style
$labelBreak = $field->params->get('core.label_break', 0);
$hasLabelBreak = in_array($labelBreak, [1, 3]);

// CSS classes for the field container
$containerClasses = [
    'jcck-form-group',
    'mb-4',
    'p-3',
    'rounded',
    'transition-colors',
    'hover:bg-base-200',
    'field-' . $field->id,
    $field->fieldclass
];

if ($k = 1 - $k) {
    $containerClasses[] = 'odd';
}

// Build data attributes for Vue
$dataAttributes = [
    'data-field-id' => $field->id,
    'data-field-name' => 'field_' . $field->id,
    'data-field-type' => $field->field_type ?? 'text',
    'data-required' => $field->required ? 'true' : 'false'
];

$dataAttrString = '';
foreach ($dataAttributes as $key => $value) {
    $dataAttrString .= ' ' . $key . '="' . htmlspecialchars($value) . '"';
}
?>

<div id="fld-<?php echo $field->id; ?>"
     class="<?php echo implode(' ', $containerClasses); ?>"
     <?php echo $dataAttrString; ?>>

    <?php if ($field->params->get('core.show_lable') == 1 || $field->params->get('core.show_lable') == 3): ?>
        <label
            id="lbl-<?php echo $field->id; ?>"
            for="field_<?php echo $field->id; ?>"
            class="jcck-form-label flex items-center gap-2 <?php echo $field->class; ?>"
        >
            <?php // Field icon ?>
            <?php if ($current->tmpl_params->get('tmpl_core.item_icon_fields', 1)): ?>
                <?php if (!$field->params->get('core.label_icon_type', 0) && !empty($field->params->get('core.icon', ''))): ?>
                    <?php echo HTMLFormatHelper::icon($field->params->get('core.icon')); ?>
                <?php elseif (!empty($field->params->get('core.label_icon_class', ''))): ?>
                    <i class="<?php echo $field->params->get('core.label_icon_class') ?> text-base-content/60"></i>
                <?php endif; ?>
            <?php endif; ?>

            <?php // Label text ?>
            <span class="flex-grow"><?php echo $field->label; ?></span>

            <?php // Required indicator ?>
            <?php if ($field->required): ?>
                <span class="jcck-badge jcck-badge-error text-xs"
                      rel="tooltip"
                      title="<?php echo Text::_('CREQUIRED') ?>">
                    <i class="fas fa-asterisk text-xs"></i>
                </span>
            <?php endif; ?>

            <?php // Help tooltip ?>
            <?php if ($field->description): ?>
                <span class="text-base-content/40 hover:text-info cursor-help transition-colors"
                      rel="tooltip"
                      title="<?php echo htmlspecialchars(($field->translateDescription ? Text::_($field->description) : $field->description), ENT_COMPAT, 'UTF-8'); ?>">
                    <i class="fas fa-question-circle"></i>
                </span>
            <?php endif; ?>
        </label>

        <?php if ($hasLabelBreak): ?>
            <div class="w-full"></div>
        <?php endif; ?>
    <?php endif; ?>

    <?php // Field input container ?>
    <div class="jcck-field-controls <?php echo $hasLabelBreak ? 'mt-2 w-full' : ''; ?> <?php echo $field->fieldclass ?>">

        <?php // Error message container (hidden by default, shown by Vue) ?>
        <div id="field-alert-<?php echo $field->id ?>"
             class="jcck-alert jcck-alert-error mb-2 field-error-message"
             style="display: none;">
        </div>

        <?php // Field input (rendered by JoomCCK field system) ?>
        <div class="jcck-field-input">
            <?php echo $field->result; ?>
        </div>

    </div>

</div>
