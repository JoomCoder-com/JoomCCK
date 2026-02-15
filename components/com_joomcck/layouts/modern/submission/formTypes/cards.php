<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Cards Form Type Layout
 *
 * DaisyUI + Tailwind CSS version of the cards form layout.
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

<div id="joomcck-submission-form" class="jcck-form-cards space-y-6">

    <?php // Main Fields Card ?>
    <div class="jcck-card bg-base-100 shadow-sm border border-base-300 overflow-hidden">
        <div class="jcck-card-header px-4 py-3 bg-gradient-to-r from-primary to-blue-600 text-primary-content">
            <h3 class="text-lg font-semibold flex items-center gap-2 m-0">
                <i class="fas fa-edit"></i>
                <?php echo Text::_($current->tmpl_params->get('tmpl_params.tab_main', 'Main')); ?>
            </h3>
        </div>
        <div class="jcck-card-body">
            <?php echo Layout::render('core.submission.formParts.mainFields', ['current' => $current, 'k' => $k]); ?>
        </div>
    </div>

    <?php // Grouped Fields Cards ?>
    <?php if (isset($current->sorted_fields)): ?>
        <?php foreach ($current->sorted_fields as $group_id => $fields): ?>
            <div class="jcck-card bg-base-100 shadow-sm border border-base-300 overflow-hidden">
                <div class="jcck-card-header px-4 py-3 bg-base-200 border-b border-base-300">
                    <h3 class="text-lg font-semibold text-base-content flex items-center gap-2 m-0">
                        <?php if (!empty($current->field_groups[$group_id]['icon'])): ?>
                            <i class="<?php echo $current->field_groups[$group_id]['icon']; ?> text-primary"></i>
                        <?php endif; ?>
                        <?php echo $current->field_groups[$group_id]['name']; ?>
                    </h3>
                </div>
                <div class="jcck-card-body">
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

    <?php // Metadata Card ?>
    <?php if (count($current->meta)): ?>
        <div class="jcck-card bg-base-100 shadow-sm border border-base-300 overflow-hidden">
            <div class="jcck-card-header px-4 py-3 bg-base-200 border-b border-base-300">
                <h3 class="text-lg font-semibold text-base-content flex items-center gap-2 m-0">
                    <i class="fas fa-tags text-secondary"></i>
                    <?php echo Text::_('CMETADATA'); ?>
                </h3>
            </div>
            <div class="jcck-card-body">
                <?php echo Layout::render('core.submission.formParts.metaFields', ['current' => $current, 'k' => $k]); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php // Admin Fields Card ?>
    <?php if (count($current->core_admin_fields)): ?>
        <div class="jcck-card bg-base-100 shadow-sm border border-warning overflow-hidden">
            <div class="jcck-card-header px-4 py-3 bg-warning/10 border-b border-warning">
                <h3 class="text-lg font-semibold text-warning flex items-center gap-2 m-0">
                    <i class="fas fa-user-shield text-warning"></i>
                    <?php echo Text::_('CSPECIALFIELD'); ?>
                </h3>
            </div>
            <div class="jcck-card-body">
                <?php echo Layout::render('core.submission.formParts.adminFields', ['current' => $current, 'k' => $k]); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php // Core Fields Card ?>
    <?php if (count($current->core_fields)): ?>
        <div class="jcck-card bg-base-100 shadow-sm border border-base-300 overflow-hidden">
            <div class="jcck-card-header px-4 py-3 bg-base-200 border-b border-base-300">
                <h3 class="text-lg font-semibold text-base-content flex items-center gap-2 m-0">
                    <i class="fas fa-cog text-secondary"></i>
                    <?php echo Text::_('CCOREFIELDS'); ?>
                </h3>
            </div>
            <div class="jcck-card-body">
                <?php echo Layout::render('core.submission.formParts.coreFields', ['current' => $current, 'k' => $k]); ?>
            </div>
        </div>
    <?php endif; ?>

</div>
