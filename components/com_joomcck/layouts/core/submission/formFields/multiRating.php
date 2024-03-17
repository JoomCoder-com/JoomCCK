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

// no need to continue if multirating not allowed
if (!$current->multirating)
	return;

?>

<div class="control-group odd<?php echo $k = 1 - $k ?>">
    <label id="jform_multirating-lbl" class="control-label" for="jform_multirating">
		<?php echo strip_tags($current->form->getLabel('multirating')); ?>
        <span class="float-end" rel="tooltip"
              title="<?php echo \Joomla\CMS\Language\Text::_('CREQUIRED') ?>"><?php echo HTMLFormatHelper::icon('asterisk-small.png'); ?></span>
    </label>
    <div class="controls">
        <div id="field-alert-rating" class="alert alert-danger" style="display:none"></div>
		<?php echo $current->multirating; ?>
    </div>
</div>
