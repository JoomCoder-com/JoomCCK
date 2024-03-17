<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomcck\Assets\Webassets\Webassets;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die();

extract($displayData);
?>
<?php if (in_array($current->params->get('submission.allow_category'), $current->user->getAuthorisedViewLevels()) && $current->section->categories): ?>
	<div class="control-group odd<?php echo $k = 1 - $k ?>">
		<?php if ($current->tmpl_params->get('tmpl_core.category_label', 0)): ?>
			<label id="category-lbl" for="category" class="control-label">
				<?php if ($current->tmpl_params->get('tmpl_core.form_category_icon', 1)): ?>
					<?php echo HTMLFormatHelper::icon('category.png'); ?>
				<?php endif; ?>

				<?php echo \Joomla\CMS\Language\Text::_($current->tmpl_params->get('tmpl_core.form_label_category', 'Category')) ?>

				<?php if (!$current->type->params->get('submission.first_category', 0) && in_array($current->type->params->get('submission.allow_category', 1), $current->user->getAuthorisedViewLevels())) : ?>
					<span class="float-end" rel="tooltip"
					      title="<?php echo \Joomla\CMS\Language\Text::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
				<?php endif; ?>
			</label>
		<?php endif; ?>
		<div class="controls">
			<div id="field-alert-category" class="alert alert-danger" style="display:none"></div>
			<?php if (!empty($current->allow_multi_msg)): ?>
				<div class="alert alert-warning">
					<?php echo \Joomla\CMS\Language\Text::_($current->type->params->get('emerald.type_multicat_subscription_msg')); ?>
					<a href="<?php echo EmeraldApi::getLink('list', true, $current->type->params->get('emerald.type_multicat_subscription')); ?>"><?php echo \Joomla\CMS\Language\Text::_('CSUBSCRIBENOW'); ?></a>
				</div>
			<?php endif; ?>
			<?php echo $current->loadTemplate('category_' . $current->tmpl_params->get('tmpl_params.tmpl_category', 'default')); ?>
		</div>
	</div>
<?php elseif (!empty($current->category->id)): ?>
	<div class="control-group odd<?php echo $k = 1 - $k ?>">
		<label id="category-lbl" for="category" class="control-label">
			<?php if ($current->tmpl_params->get('tmpl_core.form_category_icon', 1)): ?>
				<?php echo HTMLFormatHelper::icon('category.png'); ?>
			<?php endif; ?>

			<?php echo \Joomla\CMS\Language\Text::_($current->tmpl_params->get('tmpl_core.form_label_category', 'Category')) ?>

			<?php if (!$current->type->params->get('submission.first_category', 0) && in_array($current->type->params->get('submission.allow_category', 1), $current->user->getAuthorisedViewLevels())) : ?>
				<span class="float-end" rel="tooltip"
				      title="<?php echo \Joomla\CMS\Language\Text::_('CREQUIRED') ?>"></span>
			<?php endif; ?>
		</label>
		<div class="controls">
			<div id="field-alert-category" class="alert alert-danger" style="display:none"></div>
			<?php echo $current->section->name; ?> <?php echo $current->category->crumbs; ?>
		</div>
	</div>
<?php endif; ?>