<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Accordions Form Type Layout
 *
 * DaisyUI CSS-only accordion layout using collapse components with checkboxes.
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

<div id="joomcck-submission-form" class="jcck-form-accordions">
    <div class="jcck-join jcck-join-vertical bg-base-100">

        <?php // Main Fields Accordion ?>
        <div class="jcck-collapse jcck-collapse-arrow jcck-join-item border-base-300 border">
            <input type="checkbox" name="joomcck_ui_accordion_main" checked="checked" />
            <div class="jcck-collapse-title font-semibold">
                <i class="fas fa-edit text-primary mr-2"></i>
                <?php echo Text::_($current->tmpl_params->get('tmpl_params.tab_main', 'Main')); ?>
            </div>
            <div class="jcck-collapse-content">
                <?php echo Layout::render('core.submission.formParts.mainFields', ['current' => $current, 'k' => $k]); ?>
            </div>
        </div>

        <?php // Grouped Fields Accordions ?>
        <?php if (isset($current->sorted_fields)): ?>
            <?php foreach ($current->sorted_fields as $group_id => $fields): ?>
                <div class="jcck-collapse jcck-collapse-arrow jcck-join-item border-base-300 border">
                    <input type="checkbox" name="joomcck_ui_accordion_<?php echo $group_id; ?>" />
                    <div class="jcck-collapse-title font-semibold">
                        <?php if (!empty($current->field_groups[$group_id]['icon'])): ?>
                            <i class="<?php echo $current->field_groups[$group_id]['icon']; ?> text-primary mr-2"></i>
                        <?php endif; ?>
                        <?php echo $current->field_groups[$group_id]['name']; ?>
                    </div>
                    <div class="jcck-collapse-content">
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
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php // Metadata Accordion ?>
        <?php if (count($current->meta)): ?>
            <div class="jcck-collapse jcck-collapse-arrow jcck-join-item border-base-300 border">
                <input type="checkbox" name="joomcck_ui_accordion_meta" />
                <div class="jcck-collapse-title font-semibold">
                    <i class="fas fa-tags text-secondary mr-2"></i>
                    <?php echo Text::_('CMETADATA'); ?>
                </div>
                <div class="jcck-collapse-content">
                    <?php echo Layout::render('core.submission.formParts.metaFields', ['current' => $current, 'k' => $k]); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php // Admin Fields Accordion ?>
        <?php if (count($current->core_admin_fields)): ?>
            <div class="jcck-collapse jcck-collapse-arrow jcck-join-item border-warning border">
                <input type="checkbox" name="joomcck_ui_accordion_admin" />
                <div class="jcck-collapse-title font-semibold text-warning">
                    <i class="fas fa-user-shield text-warning mr-2"></i>
                    <?php echo Text::_('CSPECIALFIELD'); ?>
                </div>
                <div class="jcck-collapse-content">
                    <?php echo Layout::render('core.submission.formParts.adminFields', ['current' => $current, 'k' => $k]); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php // Core Fields Accordion ?>
        <?php if (count($current->core_fields)): ?>
            <div class="jcck-collapse jcck-collapse-arrow jcck-join-item border-base-300 border">
                <input type="checkbox" name="joomcck_ui_accordion_core" />
                <div class="jcck-collapse-title font-semibold">
                    <i class="fas fa-cog text-secondary mr-2"></i>
                    <?php echo Text::_('CCOREFIELDS'); ?>
                </div>
                <div class="jcck-collapse-content">
                    <?php echo Layout::render('core.submission.formParts.coreFields', ['current' => $current, 'k' => $k]); ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>
