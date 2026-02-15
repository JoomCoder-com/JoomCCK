<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Fieldsets Form Type Layout
 *
 * DaisyUI + Tailwind CSS version of the fieldsets form layout.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

$k = 0;
?>

<div id="joomcck-submission-form" class="jcck-form-fieldsets space-y-6">

    <?php // Main Fields Fieldset ?>
    <fieldset class="jcck-fieldset bg-base-200 border-base-300 rounded-lg border p-4">
        <legend class="jcck-fieldset-legend flex items-center gap-2">
            <i class="fas fa-edit text-primary"></i>
            <?php echo Text::_($current->tmpl_params->get('tmpl_params.tab_main', 'Main')); ?>
        </legend>
        <?php echo Layout::render('core.submission.formParts.mainFields', ['current' => $current, 'k' => $k]); ?>
    </fieldset>

    <?php // Grouped Fields Fieldsets ?>
    <?php if (isset($current->sorted_fields)): ?>
        <?php foreach ($current->sorted_fields as $group_id => $fields): ?>
            <fieldset class="jcck-fieldset bg-base-200 border-base-300 rounded-lg border p-4">
                <legend class="jcck-fieldset-legend flex items-center gap-2">
                    <?php if (!empty($current->field_groups[$group_id]['icon'])): ?>
                        <i class="<?php echo $current->field_groups[$group_id]['icon']; ?> text-primary"></i>
                    <?php endif; ?>
                    <?php echo $current->field_groups[$group_id]['name']; ?>
                </legend>

                <?php if (!empty($current->field_groups[$group_id]['descr'])): ?>
                    <div class="jcck-alert jcck-alert-info mb-4">
                        <?php echo $current->field_groups[$group_id]['descr']; ?>
                    </div>
                <?php endif; ?>

                <div class="space-y-2">
                    <?php foreach ($fields as $field_id => $field): ?>
                        <?php echo Layout::render('core.submission.formFields.field', ['current' => $current, 'k' => $k, 'field' => $field]); ?>
                    <?php endforeach; ?>
                </div>
            </fieldset>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php // Metadata Fieldset ?>
    <?php if (count($current->meta)): ?>
        <fieldset class="jcck-fieldset bg-base-200 border-base-300 rounded-lg border p-4">
            <legend class="jcck-fieldset-legend flex items-center gap-2">
                <i class="fas fa-tags text-secondary"></i>
                <?php echo Text::_('CMETADATA'); ?>
            </legend>
            <?php echo Layout::render('core.submission.formParts.metaFields', ['current' => $current, 'k' => $k]); ?>
        </fieldset>
    <?php endif; ?>

    <?php // Admin Fields Fieldset ?>
    <?php if (count($current->core_admin_fields)): ?>
        <fieldset class="jcck-fieldset border-warning bg-warning/10 rounded-lg border p-4">
            <legend class="jcck-fieldset-legend flex items-center gap-2 text-warning">
                <i class="fas fa-user-shield text-warning"></i>
                <?php echo Text::_('CSPECIALFIELD'); ?>
            </legend>
            <?php echo Layout::render('core.submission.formParts.adminFields', ['current' => $current, 'k' => $k]); ?>
        </fieldset>
    <?php endif; ?>

    <?php // Core Fields Fieldset ?>
    <?php if (count($current->core_fields)): ?>
        <fieldset class="jcck-fieldset bg-base-200 border-base-300 rounded-lg border p-4">
            <legend class="jcck-fieldset-legend flex items-center gap-2">
                <i class="fas fa-cog text-secondary"></i>
                <?php echo Text::_('CCOREFIELDS'); ?>
            </legend>
            <?php echo Layout::render('core.submission.formParts.coreFields', ['current' => $current, 'k' => $k]); ?>
        </fieldset>
    <?php endif; ?>

</div>
