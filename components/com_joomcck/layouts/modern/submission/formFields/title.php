<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Title Field Layout
 *
 * DaisyUI + Tailwind CSS version of the title field.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);
?>

<?php if ($current->type->params->get('properties.item_title', 1) == 1): ?>
    <div class="jcck-form-group mb-4 p-3 rounded transition-colors hover:bg-base-200"
         data-field-name="title"
         data-field-type="text"
         data-required="true">

        <label id="title-lbl" for="jform_title" class="jcck-form-label flex items-center gap-2">
            <?php if ($current->tmpl_params->get('tmpl_core.form_title_icon', 1)): ?>
                <i class="fas fa-heading text-primary"></i>
            <?php endif; ?>

            <span class="flex-grow"><?php echo Text::_($current->tmpl_params->get('tmpl_core.form_label_title', 'Title')); ?></span>

            <span class="jcck-badge jcck-badge-error text-xs" rel="tooltip" title="<?php echo Text::_('CREQUIRED'); ?>">
                <i class="fas fa-asterisk text-xs"></i>
            </span>
        </label>

        <div class="mt-2">
            <div id="field-alert-title" class="jcck-alert jcck-alert-error mb-2 field-error-message" style="display:none"></div>
            <?php echo $current->form->getInput('title'); ?>
        </div>

    </div>
<?php else: ?>
    <input type="hidden" name="jform[title]" value="<?php echo htmlentities(!empty($current->item->title) ? $current->item->title : Text::_('CNOTITLE') . ': ' . time(), ENT_COMPAT, 'UTF-8'); ?>" />
<?php endif; ?>
