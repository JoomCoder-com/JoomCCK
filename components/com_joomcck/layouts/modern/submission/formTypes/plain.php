<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Plain Form Type Layout
 *
 * Vue.js + Tailwind CSS version of the plain form layout.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

// Field counter for alternating styles
$k = 0;
?>

<div id="joomcck-submission-form" class="jcck-form-plain space-y-4">

    <?php // Main fields (title, category, tags, etc.) ?>
    <?php echo Layout::render('core.submission.formParts.mainFields', ['current' => $current, 'k' => $k]) ?>

    <?php if (isset($current->sorted_fields)): ?>
        <?php foreach ($current->sorted_fields as $group_id => $fields): ?>

            <?php // Field group container ?>
            <div class="jcck-card bg-base-100 shadow-sm overflow-hidden">

                <?php // Group header (if has title) ?>
                <?php if (!empty($current->field_groups[$group_id]['title'])): ?>
                    <div class="bg-base-200 px-4 py-3 border-b border-base-300">
                        <h3 class="text-lg font-medium flex items-center gap-2">
                            <?php if (!empty($current->field_groups[$group_id]['icon'])): ?>
                                <i class="<?php echo $current->field_groups[$group_id]['icon']; ?> text-primary"></i>
                            <?php endif; ?>
                            <?php echo $current->field_groups[$group_id]['title']; ?>
                        </h3>
                    </div>
                <?php endif; ?>

                <?php // Group description ?>
                <?php if (!empty($current->field_groups[$group_id]['descr'])): ?>
                    <div class="jcck-alert jcck-alert-info rounded-none">
                        <?php echo $current->field_groups[$group_id]['descr']; ?>
                    </div>
                <?php endif; ?>

                <?php // Group fields ?>
                <div class="p-4 space-y-2">
                    <?php foreach ($fields as $field_id => $field): ?>
                        <?php echo Layout::render('core.submission.formFields.field', [
                            'current' => $current,
                            'k' => $k,
                            'field' => $field
                        ]); ?>
                    <?php endforeach; ?>
                </div>

            </div>

        <?php endforeach; ?>
    <?php endif; ?>

    <?php // Meta fields section ?>
    <?php if (count($current->meta)): ?>
        <div class="jcck-card bg-base-100 shadow-sm overflow-hidden">
            <div class="bg-base-200 px-4 py-3 border-b border-base-300">
                <h3 class="text-lg font-medium flex items-center gap-2">
                    <i class="fas fa-tags text-secondary"></i>
                    <?php echo Text::_('CMETA'); ?>
                </h3>
            </div>
            <div class="p-4">
                <?php echo Layout::render('core.submission.formParts.metaFields', ['current' => $current, 'k' => $k]); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php // Admin fields section ?>
    <?php if (count($current->core_admin_fields)): ?>
        <div class="jcck-card bg-base-100 shadow-sm border border-warning overflow-hidden">
            <div class="bg-warning/20 px-4 py-3 border-b border-warning">
                <h3 class="text-lg font-medium text-warning flex items-center gap-2">
                    <i class="fas fa-user-shield"></i>
                    <?php echo Text::_('CADMIN_FIELDS'); ?>
                </h3>
            </div>
            <div class="p-4">
                <?php echo Layout::render('core.submission.formParts.adminFields', ['current' => $current, 'k' => $k]); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php // Core fields section ?>
    <?php if (count($current->core_fields)): ?>
        <div class="jcck-card bg-base-100 shadow-sm overflow-hidden">
            <div class="bg-base-200 px-4 py-3 border-b border-base-300">
                <h3 class="text-lg font-medium flex items-center gap-2">
                    <i class="fas fa-cog text-secondary"></i>
                    <?php echo Text::_('CCORE_FIELDS'); ?>
                </h3>
            </div>
            <div class="p-4">
                <?php echo Layout::render('core.submission.formParts.coreFields', ['current' => $current, 'k' => $k]); ?>
            </div>
        </div>
    <?php endif; ?>

</div>
