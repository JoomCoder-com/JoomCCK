<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Tabs Form Type Layout
 *
 * Vue.js + Tailwind CSS version of the tabbed form layout.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

$k = 0;

// Collect all tabs for navigation
$tabs = [];
$tabs[] = ['id' => 'main-tab', 'title' => Text::_($current->tmpl_params->get('tmpl_params.tab_main', 'Main')), 'icon' => 'fas fa-edit'];

if (isset($current->sorted_fields)) {
    foreach ($current->sorted_fields as $group_id => $fields) {
        $tabs[] = [
            'id' => 'tab-' . $group_id,
            'title' => $current->field_groups[$group_id]['name'],
            'icon' => $current->field_groups[$group_id]['icon'] ?? ''
        ];
    }
}

if (count($current->meta)) {
    $tabs[] = ['id' => 'main-meta', 'title' => Text::_('CMETADATA'), 'icon' => 'fas fa-tags'];
}

if (count($current->core_admin_fields)) {
    $tabs[] = ['id' => 'main-special', 'title' => Text::_('CSPECIALFIELD'), 'icon' => 'fas fa-user-shield'];
}

if (count($current->core_fields)) {
    $tabs[] = ['id' => 'main-core', 'title' => Text::_('CCOREFIELDS'), 'icon' => 'fas fa-cog'];
}
?>

<div id="joomcck-submission-form" class="jcck-form-tabs bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

    <?php // Tab Navigation ?>
    <div class="jcck-tab-list border-b border-gray-200 bg-gray-50">
        <nav class="flex flex-wrap gap-1 p-2" role="tablist">
            <?php foreach ($tabs as $index => $tab): ?>
                <button type="button"
                        class="jcck-tab-btn px-4 py-2 rounded-t text-sm font-medium transition-colors
                               <?php echo $index === 0 ? 'bg-white text-joomcck-primary border border-b-white border-gray-200 -mb-px' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'; ?>"
                        data-tab-target="#<?php echo $tab['id']; ?>"
                        role="tab"
                        aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                    <?php if (!empty($tab['icon'])): ?>
                        <i class="<?php echo $tab['icon']; ?> mr-2"></i>
                    <?php endif; ?>
                    <?php echo $tab['title']; ?>
                </button>
            <?php endforeach; ?>
        </nav>
    </div>

    <?php // Tab Panels ?>
    <div class="jcck-tab-content p-4">

        <?php // Main Fields Tab ?>
        <div id="main-tab" class="jcck-tab-panel" role="tabpanel">
            <?php echo Layout::render('core.submission.formParts.mainFields', ['current' => $current, 'k' => $k]); ?>
        </div>

        <?php // Grouped Fields Tabs ?>
        <?php if (isset($current->sorted_fields)): ?>
            <?php foreach ($current->sorted_fields as $group_id => $fields): ?>
                <div id="tab-<?php echo $group_id; ?>" class="jcck-tab-panel hidden" role="tabpanel">

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

        <?php // Metadata Tab ?>
        <?php if (count($current->meta)): ?>
            <div id="main-meta" class="jcck-tab-panel hidden" role="tabpanel">
                <?php echo Layout::render('core.submission.formParts.metaFields', ['current' => $current, 'k' => $k]); ?>
            </div>
        <?php endif; ?>

        <?php // Admin Fields Tab ?>
        <?php if (count($current->core_admin_fields)): ?>
            <div id="main-special" class="jcck-tab-panel hidden" role="tabpanel">
                <?php echo Layout::render('core.submission.formParts.adminFields', ['current' => $current, 'k' => $k]); ?>
            </div>
        <?php endif; ?>

        <?php // Core Fields Tab ?>
        <?php if (count($current->core_fields)): ?>
            <div id="main-core" class="jcck-tab-panel hidden" role="tabpanel">
                <?php echo Layout::render('core.submission.formParts.coreFields', ['current' => $current, 'k' => $k]); ?>
            </div>
        <?php endif; ?>

    </div>

</div>

<?php // Tab JavaScript ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tabContainer = document.querySelector('#joomcck-submission-form.jcck-form-tabs');
    if (!tabContainer) return;

    var tabButtons = tabContainer.querySelectorAll('.jcck-tab-btn');
    var tabPanels = tabContainer.querySelectorAll('.jcck-tab-panel');

    tabButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var targetId = this.getAttribute('data-tab-target');
            var targetPanel = tabContainer.querySelector(targetId);

            // Deactivate all tabs
            tabButtons.forEach(function(b) {
                b.classList.remove('bg-white', 'text-joomcck-primary', 'border', 'border-b-white', 'border-gray-200', '-mb-px');
                b.classList.add('text-gray-600');
                b.setAttribute('aria-selected', 'false');
            });

            tabPanels.forEach(function(p) {
                p.classList.add('hidden');
            });

            // Activate clicked tab
            this.classList.add('bg-white', 'text-joomcck-primary', 'border', 'border-b-white', 'border-gray-200', '-mb-px');
            this.classList.remove('text-gray-600');
            this.setAttribute('aria-selected', 'true');

            if (targetPanel) {
                targetPanel.classList.remove('hidden');
            }
        });
    });
});
</script>
