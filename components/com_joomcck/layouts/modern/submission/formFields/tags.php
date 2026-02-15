<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Tags Field Layout
 *
 * DaisyUI + Tailwind CSS version of the tags field.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

// No need to continue if not allowed to view
if (!$current->type->params->get('properties.item_can_view_tag')) {
    return;
}

// No need to continue if not allowed to add
if (
    !MECAccess::allowAccessAuthor($current->type, 'properties.item_can_add_tag', $current->item->user_id) ||
    !MECAccess::allowUserModerate($current->user, $current->section, 'allow_tags')
) {
    return;
}
?>

<div class="jcck-form-group mb-4 p-3 rounded transition-colors hover:bg-gray-50"
     data-field-name="tags"
     data-field-type="tags"
     data-required="false">

    <label id="tags-lbl" for="tags" class="jcck-form-label flex items-center gap-2">
        <?php if ($current->tmpl_params->get('tmpl_core.form_tags_icon', 1)): ?>
            <i class="fas fa-tags text-info"></i>
        <?php endif; ?>

        <span><?php echo Text::_($current->tmpl_params->get('tmpl_core.form_label_tags', 'Tags')); ?></span>
    </label>

    <div class="mt-2">
        <?php echo $current->form->getInput('tags'); ?>
    </div>

</div>
