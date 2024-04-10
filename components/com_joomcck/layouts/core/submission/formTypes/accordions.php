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
<div id="joomcck-submission-form" class="jcck-form-accordions">

	<?php echo HTMLHelper::_('bootstrap.startAccordion', 'joomcckformAccordion', ['active' => 'main-tab', 'recall' => true]); ?>

	<!-- main fields tab -->
	<?php echo HTMLHelper::_('bootstrap.addSlide', 'joomcckformAccordion',  Text::_($current->tmpl_params->get('tmpl_params.tab_main', 'Main')),'main-tab'); ?>
	<?php echo Layout::render('core.submission.formParts.mainFields', ['current' => $current, 'k' => $k]) ?>
	<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>

	<!-- grouped fields tabs -->
	<?php if (isset($current->sorted_fields)): // grouped fields ?>
		<?php foreach ($current->sorted_fields as $group_id => $fields) : ?>
			<?php $tabName = HTMLFormatHelper::icon($current->field_groups[$group_id]['icon']).$current->field_groups[$group_id]['name']; ?>
			<?php echo HTMLHelper::_('bootstrap.addSlide', 'joomcckformAccordion', $tabName,'tab-'.$group_id); ?>
			<?php if (!empty($current->field_groups[$group_id]['descr'])): ?>
				<?php echo $current->field_groups[$group_id]['descr']; ?>
			<?php endif; ?>

			<?php foreach ($fields as $field_id => $field): ?>
				<?php echo Layout::render('core.submission.formFields.field', ['current' => $current,'k' => $k, 'field' => $field]) // field part ?>
			<?php endforeach; ?>
			<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
		<?php endforeach; ?>
	<?php endif; ?>


	<!-- metadata fields tab -->
	<?php if (count($current->meta)): ?>
		<?php echo HTMLHelper::_('bootstrap.addSlide', 'joomcckformAccordion', Text::_('CMETADATA'), 'main-meta'); ?>
		<?php echo Layout::render('core.submission.formParts.metaFields', ['current' => $current, 'k' => $k]) // metadata field part ?>
		<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
	<?php endif; ?>

	<!-- admin fields tab -->
	<?php if (count($current->core_admin_fields)): ?>
		<?php echo HTMLHelper::_('bootstrap.addSlide', 'joomcckformAccordion', Text::_('CSPECIALFIELD'), 'main-special'); ?>
		<?php echo Layout::render('core.submission.formParts.adminFields', ['current' => $current, 'k' => $k]) // admin field part ?>
		<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
	<?php endif; ?>

	<!-- core fields tab -->
	<?php if (count($current->core_fields)): ?>
		<?php echo HTMLHelper::_('bootstrap.addSlide', 'joomcckformAccordion', Text::_('CCOREFIELDS'), 'main-core'); ?>
		<?php echo Layout::render('core.submission.formParts.coreFields', ['current' => $current, 'k' => $k]) // core field part ?>
		<?php echo HTMLHelper::_('bootstrap.endSlide'); ?>
	<?php endif; ?>

	<?php echo HTMLHelper::_('bootstrap.endAccordion'); ?>

</div>