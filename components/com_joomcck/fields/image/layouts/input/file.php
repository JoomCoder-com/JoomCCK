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

?>

<div class="input-group mb-3">
	<input type="file" class="form-control" name="fields<?php echo $current->id;?>image" id="jformfields<?php echo $current->id;?>image"/>
	<input type="hidden" size="40"  name="jform[fields][<?php echo $current->id;?>][image]" id="jformfields<?php echo $current->id;?>hiddenimage"
	       value="<?php echo (isset($current->value['image']) ? stripslashes($current->value['image']) : '');?>"/>
    <?php if(isset($current->value['image']) && !empty($current->value['image'])): ?>
	<button type="button" class="btn btn-outline-danger" data-image-path="<?php echo $current->value['image']; ?>" data-image-field-id="<?php echo $current->id;?>">
        <?php echo \Joomla\CMS\Language\Text::_('F_DELETE_IMAGE')?>
    </button>
    <?php endif; ?>
</div>

<?php echo \Joomcck\Layout\Helpers\Layout::render('preview',$displayData,__DIR__) ?>