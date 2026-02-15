<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Accordions Form Type Layout
 *
 * Vue.js + Tailwind CSS version of the accordion form layout.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

$k = 0;

// Collect all accordion items
$items = [];
$items[] = ['id' => 'main-tab', 'title' => Text::_($current->tmpl_params->get('tmpl_params.tab_main', 'Main')), 'icon' => 'fas fa-edit', 'active' => true];

if (isset($current->sorted_fields)) {
    foreach ($current->sorted_fields as $group_id => $fields) {
        $items[] = [
            'id' => 'tab-' . $group_id,
            'title' => $current->field_groups[$group_id]['name'],
            'icon' => $current->field_groups[$group_id]['icon'] ?? '',
            'active' => false
        ];
    }
}

if (count($current->meta)) {
    $items[] = ['id' => 'main-meta', 'title' => Text::_('CMETADATA'), 'icon' => 'fas fa-tags', 'active' => false];
}

if (count($current->core_admin_fields)) {
    $items[] = ['id' => 'main-special', 'title' => Text::_('CSPECIALFIELD'), 'icon' => 'fas fa-user-shield', 'active' => false];
}

if (count($current->core_fields)) {
    $items[] = ['id' => 'main-core', 'title' => Text::_('CCOREFIELDS'), 'icon' => 'fas fa-cog', 'active' => false];
}
?>

<div id="joomcck-submission-form" class="jcck-form-accordions space-y-2">

    <?php // Main Fields Accordion ?>
    <div class="jcck-accordion-item border border-gray-200 rounded-lg overflow-hidden">
        <button type="button"
                class="jcck-accordion-header w-full flex items-center justify-between px-4 py-3 bg-gray-50 text-left font-medium text-gray-900 hover:bg-gray-100 transition-colors"
                data-accordion-target="#accordion-main-tab"
                aria-expanded="true">
            <span class="flex items-center gap-2">
                <i class="fas fa-edit text-joomcck-primary"></i>
                <?php echo Text::_($current->tmpl_params->get('tmpl_params.tab_main', 'Main')); ?>
            </span>
            <i class="fas fa-chevron-down jcck-accordion-icon transition-transform duration-200"></i>
        </button>
        <div id="accordion-main-tab" class="jcck-accordion-content p-4">
            <?php echo Layout::render('core.submission.formParts.mainFields', ['current' => $current, 'k' => $k]); ?>
        </div>
    </div>

    <?php // Grouped Fields Accordions ?>
    <?php if (isset($current->sorted_fields)): ?>
        <?php foreach ($current->sorted_fields as $group_id => $fields): ?>
            <div class="jcck-accordion-item border border-gray-200 rounded-lg overflow-hidden">
                <button type="button"
                        class="jcck-accordion-header w-full flex items-center justify-between px-4 py-3 bg-gray-50 text-left font-medium text-gray-900 hover:bg-gray-100 transition-colors"
                        data-accordion-target="#accordion-tab-<?php echo $group_id; ?>"
                        aria-expanded="false">
                    <span class="flex items-center gap-2">
                        <?php if (!empty($current->field_groups[$group_id]['icon'])): ?>
                            <i class="<?php echo $current->field_groups[$group_id]['icon']; ?> text-joomcck-primary"></i>
                        <?php endif; ?>
                        <?php echo $current->field_groups[$group_id]['name']; ?>
                    </span>
                    <i class="fas fa-chevron-down jcck-accordion-icon transition-transform duration-200"></i>
                </button>
                <div id="accordion-tab-<?php echo $group_id; ?>" class="jcck-accordion-content hidden p-4">
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
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php // Metadata Accordion ?>
    <?php if (count($current->meta)): ?>
        <div class="jcck-accordion-item border border-gray-200 rounded-lg overflow-hidden">
            <button type="button"
                    class="jcck-accordion-header w-full flex items-center justify-between px-4 py-3 bg-gray-50 text-left font-medium text-gray-900 hover:bg-gray-100 transition-colors"
                    data-accordion-target="#accordion-main-meta"
                    aria-expanded="false">
                <span class="flex items-center gap-2">
                    <i class="fas fa-tags text-joomcck-secondary"></i>
                    <?php echo Text::_('CMETADATA'); ?>
                </span>
                <i class="fas fa-chevron-down jcck-accordion-icon transition-transform duration-200"></i>
            </button>
            <div id="accordion-main-meta" class="jcck-accordion-content hidden p-4">
                <?php echo Layout::render('core.submission.formParts.metaFields', ['current' => $current, 'k' => $k]); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php // Admin Fields Accordion ?>
    <?php if (count($current->core_admin_fields)): ?>
        <div class="jcck-accordion-item border border-amber-200 rounded-lg overflow-hidden">
            <button type="button"
                    class="jcck-accordion-header w-full flex items-center justify-between px-4 py-3 bg-amber-50 text-left font-medium text-amber-800 hover:bg-amber-100 transition-colors"
                    data-accordion-target="#accordion-main-special"
                    aria-expanded="false">
                <span class="flex items-center gap-2">
                    <i class="fas fa-user-shield text-amber-600"></i>
                    <?php echo Text::_('CSPECIALFIELD'); ?>
                </span>
                <i class="fas fa-chevron-down jcck-accordion-icon transition-transform duration-200"></i>
            </button>
            <div id="accordion-main-special" class="jcck-accordion-content hidden p-4">
                <?php echo Layout::render('core.submission.formParts.adminFields', ['current' => $current, 'k' => $k]); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php // Core Fields Accordion ?>
    <?php if (count($current->core_fields)): ?>
        <div class="jcck-accordion-item border border-gray-200 rounded-lg overflow-hidden">
            <button type="button"
                    class="jcck-accordion-header w-full flex items-center justify-between px-4 py-3 bg-gray-50 text-left font-medium text-gray-900 hover:bg-gray-100 transition-colors"
                    data-accordion-target="#accordion-main-core"
                    aria-expanded="false">
                <span class="flex items-center gap-2">
                    <i class="fas fa-cog text-joomcck-secondary"></i>
                    <?php echo Text::_('CCOREFIELDS'); ?>
                </span>
                <i class="fas fa-chevron-down jcck-accordion-icon transition-transform duration-200"></i>
            </button>
            <div id="accordion-main-core" class="jcck-accordion-content hidden p-4">
                <?php echo Layout::render('core.submission.formParts.coreFields', ['current' => $current, 'k' => $k]); ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php // Accordion JavaScript ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var accordionContainer = document.querySelector('#joomcck-submission-form.jcck-form-accordions');
    if (!accordionContainer) return;

    var headers = accordionContainer.querySelectorAll('.jcck-accordion-header');

    headers.forEach(function(header) {
        header.addEventListener('click', function() {
            var targetId = this.getAttribute('data-accordion-target');
            var content = document.querySelector(targetId);
            var icon = this.querySelector('.jcck-accordion-icon');
            var isExpanded = this.getAttribute('aria-expanded') === 'true';

            // Toggle current
            this.setAttribute('aria-expanded', !isExpanded);
            content.classList.toggle('hidden');
            icon.style.transform = isExpanded ? '' : 'rotate(180deg)';
        });
    });
});
</script>
