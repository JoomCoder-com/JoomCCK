<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Fieldsets Form Type Layout
 *
 * Vue.js + Tailwind CSS version of the fieldsets form layout.
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
    <fieldset class="border border-gray-300 rounded-lg p-4">
        <legend class="px-3 text-lg font-semibold text-gray-900 flex items-center gap-2">
            <i class="fas fa-edit text-joomcck-primary"></i>
            <?php echo Text::_($current->tmpl_params->get('tmpl_params.tab_main', 'Main')); ?>
        </legend>
        <?php echo Layout::render('core.submission.formParts.mainFields', ['current' => $current, 'k' => $k]); ?>
    </fieldset>

    <?php // Grouped Fields Fieldsets ?>
    <?php if (isset($current->sorted_fields)): ?>
        <?php foreach ($current->sorted_fields as $group_id => $fields): ?>
            <fieldset class="border border-gray-300 rounded-lg p-4">
                <legend class="px-3 text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <?php if (!empty($current->field_groups[$group_id]['icon'])): ?>
                        <i class="<?php echo $current->field_groups[$group_id]['icon']; ?> text-joomcck-primary"></i>
                    <?php endif; ?>
                    <?php echo $current->field_groups[$group_id]['name']; ?>
                </legend>

                <?php if (!empty($current->field_groups[$group_id]['descr'])): ?>
                    <div class="mb-4 p-3 bg-blue-50 text-blue-700 rounded text-sm">
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
        <fieldset class="border border-gray-300 rounded-lg p-4">
            <legend class="px-3 text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-tags text-joomcck-secondary"></i>
                <?php echo Text::_('CMETADATA'); ?>
            </legend>
            <?php echo Layout::render('core.submission.formParts.metaFields', ['current' => $current, 'k' => $k]); ?>
        </fieldset>
    <?php endif; ?>

    <?php // Admin Fields Fieldset ?>
    <?php if (count($current->core_admin_fields)): ?>
        <fieldset class="border border-amber-300 rounded-lg p-4 bg-amber-50/30">
            <legend class="px-3 text-lg font-semibold text-amber-800 flex items-center gap-2">
                <i class="fas fa-user-shield text-amber-600"></i>
                <?php echo Text::_('CSPECIALFIELD'); ?>
            </legend>
            <?php echo Layout::render('core.submission.formParts.adminFields', ['current' => $current, 'k' => $k]); ?>
        </fieldset>
    <?php endif; ?>

    <?php // Core Fields Fieldset ?>
    <?php if (count($current->core_fields)): ?>
        <fieldset class="border border-gray-300 rounded-lg p-4">
            <legend class="px-3 text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-cog text-joomcck-secondary"></i>
                <?php echo Text::_('CCOREFIELDS'); ?>
            </legend>
            <?php echo Layout::render('core.submission.formParts.coreFields', ['current' => $current, 'k' => $k]); ?>
        </fieldset>
    <?php endif; ?>

</div>
