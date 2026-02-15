<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Category Field Layout
 *
 * DaisyUI + Tailwind CSS version of the category field.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);
?>

<?php if (in_array($current->params->get('submission.allow_category'), $current->user->getAuthorisedViewLevels()) && $current->section->categories): ?>
    <div class="jcck-form-group mb-4 p-3 rounded transition-colors hover:bg-base-200"
         data-field-name="category"
         data-field-type="category"
         data-required="<?php echo (!$current->type->params->get('submission.first_category', 0) && in_array($current->type->params->get('submission.allow_category', 1), $current->user->getAuthorisedViewLevels())) ? 'true' : 'false'; ?>">

        <?php if ($current->tmpl_params->get('tmpl_core.category_label', 1)): ?>
            <label id="category-lbl" for="category" class="jcck-form-label flex items-center gap-2">
                <?php if ($current->tmpl_params->get('tmpl_core.form_category_icon', 1)): ?>
                    <i class="fas fa-folder text-primary"></i>
                <?php endif; ?>

                <span class="flex-grow"><?php echo Text::_($current->tmpl_params->get('tmpl_core.form_label_category', 'Category')); ?></span>

                <?php if (!$current->type->params->get('submission.first_category', 0) && in_array($current->type->params->get('submission.allow_category', 1), $current->user->getAuthorisedViewLevels())): ?>
                    <span class="jcck-badge jcck-badge-error text-xs" rel="tooltip" title="<?php echo Text::_('CREQUIRED'); ?>">
                        <i class="fas fa-asterisk text-xs"></i>
                    </span>
                <?php endif; ?>
            </label>
        <?php endif; ?>

        <div class="mt-2">
            <div id="field-alert-category" class="jcck-alert jcck-alert-error mb-2 field-error-message" style="display:none"></div>

            <?php if (!empty($current->allow_multi_msg)): ?>
                <div class="jcck-alert jcck-alert-warning mb-3">
                    <?php echo Text::_($current->type->params->get('emerald.type_multicat_subscription_msg')); ?>
                    <a href="<?php echo EmeraldApi::getLink('list', true, $current->type->params->get('emerald.type_multicat_subscription')); ?>" class="font-medium underline">
                        <?php echo Text::_('CSUBSCRIBENOW'); ?>
                    </a>
                </div>
            <?php endif; ?>

            <?php echo $current->loadTemplate('category_' . $current->tmpl_params->get('tmpl_params.tmpl_category', 'default')); ?>
        </div>

    </div>

<?php elseif (!empty($current->category->id)): ?>
    <div class="jcck-form-group mb-4 p-3 rounded bg-base-200"
         data-field-name="category"
         data-field-type="category-readonly">

        <label id="category-lbl" for="category" class="jcck-form-label flex items-center gap-2">
            <?php if ($current->tmpl_params->get('tmpl_core.form_category_icon', 1)): ?>
                <i class="fas fa-folder text-secondary"></i>
            <?php endif; ?>

            <span><?php echo Text::_($current->tmpl_params->get('tmpl_core.form_label_category', 'Category')); ?></span>
        </label>

        <div class="mt-2 text-base-content">
            <div id="field-alert-category" class="jcck-alert jcck-alert-error mb-2 field-error-message" style="display:none"></div>
            <span class="font-medium"><?php echo $current->section->name; ?></span>
            <?php echo $current->category->crumbs; ?>
        </div>

    </div>
<?php endif; ?>
