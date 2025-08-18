<?php

/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

extract($displayData);


if (!isset($current->value['image']) && empty($current->value['image']))
	return;

?>
<div class="card d-inline-block p-2">
    <img
            src="<?php echo(!empty($current->value['image']) ? \Joomla\CMS\Uri\Uri::root(true) . '/' . $current->value['image'] : \Joomla\CMS\Uri\Uri::root(true) . '/media/com_joomcck/blank.png'); ?>"
            alt="<?php echo \Joomla\CMS\Language\Text::_('I_IMGPREVIEW'); ?>"
            name="imagelib<?php echo $current->id; ?>" id="imagelib<?php echo $current->id; ?>"
            class="rounded"
            style="max-width: 440px; border: 0px solid;"
    />
	<?php if ($current->params->get('params.allow_caption')): ?>
        <div class="form-inline">
            <input id="imagetitle<?php echo $current->id; ?>" type="text"
                   placeholder="<?php echo \Joomla\CMS\Language\Text::_('IMAGETITLE'); ?>"
                   name="jform[fields][<?php echo $current->id; ?>][image_title]"
                   value="<?php echo(isset($current->value['image_title']) ? stripslashes($current->value['image_title']) : ''); ?>"
                   class="form-control border-0"/>
        </div>
	<?php endif; ?>
</div>