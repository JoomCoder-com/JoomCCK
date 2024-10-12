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

// no need to continue if no fields to show
if (count($current->core_fields) == 0)
	return;


?>
<div class="core">
	<?php foreach ($current->core_fields as $key => $field): ?>
        <div class="control-group odd<?php echo $k = 1 - $k ?>">
            <label id="jform_<?php echo $field ?>-lbl" class="form-label" for="jform_<?php echo $field ?>">
				<?php if ($current->tmpl_params->get('tmpl_core.form_' . $field . '_icon', 1)): ?>
					<?php echo HTMLFormatHelper::icon('core-' . $field . '.png'); ?>
				<?php endif; ?>
				<?php echo strip_tags($current->form->getLabel($field)); ?>
            </label>
            <div class="controls">
				<?php echo $current->form->getInput($field); ?>
            </div>
        </div>
	<?php endforeach; ?>
</div>