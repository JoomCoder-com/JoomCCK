<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */


defined('_JEXEC') or die();


extract($displayData);

/*
 * This layout display toggle buttons using checkboxes and BS5
 * $items => items list
 * $default => default values
 * $display => inline or block
 */


$display = isset($display) && $display == 'inline' ? 'd-inline' : 'mb-3';


?>


<?php foreach ($items as $id => &$tag): ?>

	<?php

	$value = in_array($tag->id, $default) ? $tag->id : '';
	$id    = "fht-" . $tag->id;

	?>

    <div class="<?php echo $display ?>">
        <input id="<?php echo $id ?>" type="checkbox" name="filters[tags][]" class="btn-check" autocomplete="off" value="<?php echo $value ?>">
        <label class="btn btn-outline-success" for="<?php echo $id ?>"><?php echo $tag->tag ?></label>
    </div>


<?php endforeach; ?>





