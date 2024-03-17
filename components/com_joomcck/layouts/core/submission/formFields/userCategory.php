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
<?php if ($current->ucategory) : ?>
	<div class="control-group odd<?php echo $k = 1 - $k ?>">
		<label id="ucategory-lbl" for="ucatid" class="control-label">
			<?php if ($current->tmpl_params->get('tmpl_core.form_ucategory_icon', 1)): ?>
				<?php echo HTMLFormatHelper::icon('category.png'); ?>
			<?php endif; ?>

			<?php echo \Joomla\CMS\Language\Text::_($current->tmpl_params->get('tmpl_core.form_label_ucategory', 'Category')) ?>

			<span class="float-end" rel="tooltip"
			      title="<?php echo \Joomla\CMS\Language\Text::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
		</label>
		<div class="controls">
			<div id="field-alert-ucat" class="alert alert-danger" style="display:none"></div>
			<?php echo $current->form->getInput('ucatid'); ?>
		</div>
	</div>
<?php else: ?>
	<?php $current->form->setFieldAttribute('ucatid', 'type', 'hidden'); ?>
	<?php $current->form->setValue('ucatid', null, '0'); ?>
	<?php echo $current->form->getInput('ucatid'); ?>
<?php endif; ?>