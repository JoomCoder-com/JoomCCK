

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
<div id="joomcck-submission-form" class="jcck-form-fieldsets">

	<fieldset class="border rounded p-3">
		<legend class="float-none w-auto px-3"><?php echo Text::_($current->tmpl_params->get('tmpl_params.tab_main', 'Main')) ?></legend>
		<?php echo Layout::render('core.submission.formParts.mainFields', ['current' => $current, 'k' => $k]) ?>
	</fieldset>

	<!-- grouped fields tabs -->
	<?php if (isset($current->sorted_fields)): // grouped fields ?>
		<?php foreach ($current->sorted_fields as $group_id => $fields) : ?>
			<?php $tabName = HTMLFormatHelper::icon($current->field_groups[$group_id]['icon']).$current->field_groups[$group_id]['name']; ?>
			<fieldset class="border rounded p-3">
				<legend class="float-none w-auto px-3"><?php echo $tabName ?></legend>
				<?php if (!empty($current->field_groups[$group_id]['descr'])): ?>
					<?php echo $current->field_groups[$group_id]['descr']; ?>
				<?php endif; ?>

				<?php foreach ($fields as $field_id => $field): ?>
					<?php echo Layout::render('core.submission.formFields.field', ['k' => $k, 'field' => $field]) // field part ?>
				<?php endforeach; ?>
			</fieldset>
		<?php endforeach; ?>
	<?php endif; ?>

	<!-- metadata fields tab -->
	<?php if (count($current->meta)): ?>
		<fieldset class="border rounded p-3">
			<legend class="float-none w-auto px-3"><?php echo Text::_('CMETADATA') ?></legend>
			<?php echo Layout::render('core.submission.formParts.metaFields', ['current' => $current, 'k' => $k]) // metadata field part ?>
		</fieldset>
	<?php endif; ?>

	<!-- admin fields tab -->
	<?php if (count($current->core_admin_fields)): ?>
		<fieldset class="border rounded p-3">
			<legend class="float-none w-auto px-3"><?php echo Text::_('CSPECIALFIELD') ?></legend>
			<?php echo Layout::render('core.submission.formParts.adminFields', ['current' => $current, 'k' => $k]) // admin field part ?>
		</fieldset>
	<?php endif; ?>

	<!-- core fields tab -->
	<?php if (count($current->core_fields)): ?>
		<fieldset class="border rounded p-3">
			<legend class="float-none w-auto px-3"><?php echo Text::_('CCOREFIELDS') ?></legend>
			<?php echo Layout::render('core.submission.formParts.coreFields', ['current' => $current, 'k' => $k]) // core field part ?>
		</fieldset>
	<?php endif; ?>
</div>