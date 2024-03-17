

<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomcck\Layout\Helpers\Layout;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);

// some inits
$k = 0;

?>
<div id="joomcck-submission-form" class="jcck-form-tabs">

	<?php echo HTMLHelper::_('uitab.startTabSet', 'joomcckformTab', ['active' => 'main-tab', 'recall' => true, 'breakpoint' => 768]); ?>

        <!-- main fields tab -->
        <?php echo HTMLHelper::_('uitab.addTab', 'joomcckformTab', 'main-tab', Text::_($current->tmpl_params->get('tmpl_params.tab_main', 'Main'))); ?>
                <?php echo Layout::render('core.submission.formParts.mainFields', ['current' => $current, 'k' => $k]) ?>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <!-- grouped fields tabs -->
        <?php if (isset($current->sorted_fields)): // grouped fields ?>
                <?php foreach ($current->sorted_fields as $group_id => $fields) : ?>
                    <?php $tabName = HTMLFormatHelper::icon($current->field_groups[$group_id]['icon']).$current->field_groups[$group_id]['name']; ?>
		            <?php echo HTMLHelper::_('uitab.addTab', 'joomcckformTab', 'tab-'.$group_id, $tabName); ?>
                        <?php if (!empty($current->field_groups[$group_id]['descr'])): ?>
                            <?php echo $current->field_groups[$group_id]['descr']; ?>
                        <?php endif; ?>

                        <?php foreach ($fields as $field_id => $field): ?>
                            <?php echo Layout::render('core.submission.formFields.field', ['k' => $k, 'field' => $field]) // field part ?>
                        <?php endforeach; ?>
		            <?php echo HTMLHelper::_('uitab.endTab'); ?>
                <?php endforeach; ?>
        <?php endif; ?>


        <!-- metadata fields tab -->
        <?php if (count($current->meta)): ?>
            <?php echo HTMLHelper::_('uitab.addTab', 'joomcckformTab', 'main-meta', Text::_('CMETADATA')); ?>
                <?php echo Layout::render('core.submission.formParts.metaFields', ['current' => $current, 'k' => $k]) // metadata field part ?>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>
        <?php endif; ?>

        <!-- admin fields tab -->
        <?php if (count($current->core_admin_fields)): ?>
            <?php echo HTMLHelper::_('uitab.addTab', 'joomcckformTab', 'main-special', Text::_('CSPECIALFIELD')); ?>
                    <?php echo Layout::render('core.submission.formParts.adminFields', ['current' => $current, 'k' => $k]) // admin field part ?>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>
        <?php endif; ?>

        <!-- core fields tab -->
        <?php if (count($current->core_fields)): ?>
            <?php echo HTMLHelper::_('uitab.addTab', 'joomcckformTab', 'main-core', Text::_('CCOREFIELDS')); ?>
                    <?php echo Layout::render('core.submission.formParts.coreFields', ['current' => $current, 'k' => $k]) // core field part ?>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>
        <?php endif; ?>

	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

</div>