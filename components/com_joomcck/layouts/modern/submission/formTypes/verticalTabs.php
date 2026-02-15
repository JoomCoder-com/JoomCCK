<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Vertical Tabs Form Type Layout
 *
 * Vue.js + Tailwind CSS version of the vertical tabs form layout.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

$k = 0;

// Collect all tabs
$tabs = [];
$tabs[] = ['id' => 'main-tab', 'title' => Text::_($current->tmpl_params->get('tmpl_params.tab_main', 'Main')), 'icon' => 'fas fa-edit'];

if (isset($current->sorted_fields)) {
    foreach ($current->sorted_fields as $group_id => $fields) {
        if ($group_id == 0) continue;
        $tabs[] = [
            'id' => 'tab-' . $group_id,
            'title' => $current->field_groups[$group_id]['name'],
            'icon' => $current->field_groups[$group_id]['icon'] ?? ''
        ];
    }
}

if (count($current->meta)) {
    $tabs[] = ['id' => 'meta-tab', 'title' => Text::_('CMETADATA'), 'icon' => 'fas fa-tags'];
}

if (count($current->core_admin_fields)) {
    $tabs[] = ['id' => 'admin-tab', 'title' => Text::_('CSPECIALFIELD'), 'icon' => 'fas fa-user-shield'];
}

if (count($current->core_fields)) {
    $tabs[] = ['id' => 'core-tab', 'title' => Text::_('CCOREFIELDS'), 'icon' => 'fas fa-cog'];
}
?>

<div id="joomcck-submission-form" class="jcck-form-verticaltabs">
    <div class="flex gap-4">

        <?php // Vertical Tab Navigation ?>
        <div class="w-48 flex-shrink-0">
            <nav class="sticky top-4 space-y-1" role="tablist">
                <?php foreach ($tabs as $index => $tab): ?>
                    <button type="button"
                            class="jcck-vtab-btn w-full flex items-center gap-2 px-4 py-3 rounded-lg text-left text-sm font-medium transition-colors
                                   <?php echo $index === 0 ? 'bg-joomcck-primary text-white shadow-sm' : 'text-gray-700 hover:bg-gray-100'; ?>"
                            data-vtab-target="#vtab-<?php echo $tab['id']; ?>"
                            role="tab"
                            aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                        <?php if (!empty($tab['icon'])): ?>
                            <i class="<?php echo $tab['icon']; ?> w-5"></i>
                        <?php endif; ?>
                        <span class="truncate"><?php echo $tab['title']; ?></span>
                    </button>
                <?php endforeach; ?>
            </nav>
        </div>

        <?php // Tab Content ?>
        <div class="flex-grow min-w-0 border-l border-gray-200 pl-6">

            <?php // Main Fields Panel ?>
            <div id="vtab-main-tab" class="jcck-vtab-panel" role="tabpanel">
                <?php echo Layout::render('core.submission.formParts.mainFields', ['current' => $current, 'k' => $k]); ?>
            </div>

            <?php // Grouped Fields Panels ?>
            <?php if (isset($current->sorted_fields)): ?>
                <?php foreach ($current->sorted_fields as $group_id => $fields): ?>
                    <div id="vtab-tab-<?php echo $group_id; ?>" class="jcck-vtab-panel hidden" role="tabpanel">
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
                <?php endforeach; ?>
            <?php endif; ?>

            <?php // Metadata Panel ?>
            <?php if (count($current->meta)): ?>
                <div id="vtab-meta-tab" class="jcck-vtab-panel hidden" role="tabpanel">
                    <?php echo Layout::render('core.submission.formParts.metaFields', ['current' => $current, 'k' => $k]); ?>
                </div>
            <?php endif; ?>

            <?php // Admin Fields Panel ?>
            <?php if (count($current->core_admin_fields)): ?>
                <div id="vtab-admin-tab" class="jcck-vtab-panel hidden" role="tabpanel">
                    <?php echo Layout::render('core.submission.formParts.adminFields', ['current' => $current, 'k' => $k]); ?>
                </div>
            <?php endif; ?>

            <?php // Core Fields Panel ?>
            <?php if (count($current->core_fields)): ?>
                <div id="vtab-core-tab" class="jcck-vtab-panel hidden" role="tabpanel">
                    <?php echo Layout::render('core.submission.formParts.coreFields', ['current' => $current, 'k' => $k]); ?>
                </div>
            <?php endif; ?>

        </div>

    </div>
</div>

<?php // Vertical Tabs JavaScript ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var container = document.querySelector('#joomcck-submission-form.jcck-form-verticaltabs');
    if (!container) return;

    var tabButtons = container.querySelectorAll('.jcck-vtab-btn');
    var tabPanels = container.querySelectorAll('.jcck-vtab-panel');

    tabButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-vtab-target');
            var targetPanel = container.querySelector(targetId);

            // Deactivate all
            tabButtons.forEach(function(b) {
                b.classList.remove('bg-joomcck-primary', 'text-white', 'shadow-sm');
                b.classList.add('text-gray-700', 'hover:bg-gray-100');
                b.setAttribute('aria-selected', 'false');
            });

            tabPanels.forEach(function(p) {
                p.classList.add('hidden');
            });

            // Activate clicked
            this.classList.add('bg-joomcck-primary', 'text-white', 'shadow-sm');
            this.classList.remove('text-gray-700', 'hover:bg-gray-100');
            this.setAttribute('aria-selected', 'true');

            if (targetPanel) {
                targetPanel.classList.remove('hidden');
            }
        });
    });
});
</script>
