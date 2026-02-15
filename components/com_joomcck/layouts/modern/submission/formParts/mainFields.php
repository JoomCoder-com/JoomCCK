<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Main Fields Layout
 *
 * Vue.js + Tailwind CSS version of the main fields (title, category, tags, etc.).
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);
?>

<?php // Main fields post description ?>
<?php if ($current->tmpl_params->get('tmpl_params.tab_main_descr')): ?>
    <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg text-blue-800">
        <?php echo $current->tmpl_params->get('tmpl_params.tab_main_descr'); ?>
    </div>
<?php endif; ?>

<div class="space-y-4">

    <?php // Title field ?>
    <?php echo Layout::render('core.submission.formFields.title', ['current' => $current, 'k' => $k]); ?>

    <?php // Anywhere (who can report; where to repost) field ?>
    <?php echo Layout::render('core.submission.formFields.anyWhere', ['current' => $current, 'k' => $k]); ?>

    <?php // Category field ?>
    <?php echo Layout::render('core.submission.formFields.category', ['current' => $current, 'k' => $k]); ?>

    <?php // User Category field ?>
    <?php echo Layout::render('core.submission.formFields.userCategory', ['current' => $current, 'k' => $k]); ?>

    <?php // Multi-rating field ?>
    <?php echo Layout::render('core.submission.formFields.multiRating', ['current' => $current, 'k' => $k]); ?>

    <?php // Non-grouped fields (group 0) ?>
    <?php if (isset($current->sorted_fields[0])): ?>
        <?php foreach ($current->sorted_fields[0] as $field_id => $field): ?>
            <?php echo Layout::render('core.submission.formFields.field', ['current' => $current, 'k' => $k, 'field' => $field]); ?>
        <?php endforeach; ?>
        <?php unset($current->sorted_fields[0]); ?>
    <?php endif; ?>

    <?php // Tags field ?>
    <?php echo Layout::render('core.submission.formFields.tags', ['current' => $current, 'k' => $k]); ?>

</div>
