<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Tabs Form Type Layout
 *
 * DaisyUI CSS-only tabbed form layout using radio inputs.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

$k = 0;
$tabIndex = 0;
?>

<div id="joomcck-submission-form" class="jcck-form-tabs">
    <div class="jcck-tabs jcck-tabs-lift">

        <?php // Main Fields Tab ?>
        <label class="jcck-tab">
            <input type="radio" name="joomcck_ui_tabs" checked="checked" />
            <i class="fas fa-edit mr-2"></i> <?php echo Text::_($current->tmpl_params->get('tmpl_params.tab_main', 'Main')); ?>
        </label>
        <div class="jcck-tab-content bg-base-100 border-base-300 p-6">
            <?php echo Layout::render('core.submission.formParts.mainFields', ['current' => $current, 'k' => $k]); ?>
        </div>

        <?php // Grouped Fields Tabs ?>
        <?php if (isset($current->sorted_fields)): ?>
            <?php foreach ($current->sorted_fields as $group_id => $fields): ?>
                <label class="jcck-tab">
                    <input type="radio" name="joomcck_ui_tabs" />
                    <?php if (!empty($current->field_groups[$group_id]['icon'])): ?>
                        <i class="<?php echo $current->field_groups[$group_id]['icon']; ?> mr-2"></i>
                    <?php endif; ?>
                    <?php echo $current->field_groups[$group_id]['name']; ?>
                </label>
                <div class="jcck-tab-content bg-base-100 border-base-300 p-6">
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
            <?php endforeach; ?>
        <?php endif; ?>

        <?php // Metadata Tab ?>
        <?php if (count($current->meta)): ?>
            <label class="jcck-tab">
                <input type="radio" name="joomcck_ui_tabs" />
                <i class="fas fa-tags mr-2"></i> <?php echo Text::_('CMETADATA'); ?>
            </label>
            <div class="jcck-tab-content bg-base-100 border-base-300 p-6">
                <?php echo Layout::render('core.submission.formParts.metaFields', ['current' => $current, 'k' => $k]); ?>
            </div>
        <?php endif; ?>

        <?php // Admin Fields Tab ?>
        <?php if (count($current->core_admin_fields)): ?>
            <label class="jcck-tab">
                <input type="radio" name="joomcck_ui_tabs" />
                <i class="fas fa-user-shield mr-2"></i> <?php echo Text::_('CSPECIALFIELD'); ?>
            </label>
            <div class="jcck-tab-content bg-base-100 border-base-300 p-6">
                <?php echo Layout::render('core.submission.formParts.adminFields', ['current' => $current, 'k' => $k]); ?>
            </div>
        <?php endif; ?>

        <?php // Core Fields Tab ?>
        <?php if (count($current->core_fields)): ?>
            <label class="jcck-tab">
                <input type="radio" name="joomcck_ui_tabs" />
                <i class="fas fa-cog mr-2"></i> <?php echo Text::_('CCOREFIELDS'); ?>
            </label>
            <div class="jcck-tab-content bg-base-100 border-base-300 p-6">
                <?php echo Layout::render('core.submission.formParts.coreFields', ['current' => $current, 'k' => $k]); ?>
            </div>
        <?php endif; ?>

    </div>
</div>
