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
$idProperty = isset($idProperty) ? $idProperty : 'id';


?>


<?php foreach ($items as $id => &$item): ?>

	<?php
	$isChecked = in_array($item->{$idProperty}, $default) ? true : false; // is checked ?
	$checked = $isChecked ? "checked" : ''; // add checked attr
	$id    = "$idPrefix-".$item->{$idProperty}; // build id
	$countClass = isset($countProperty) && $countProperty > 0 ? 'success' : 'light border text-dark';
	?>

    <div class="<?php echo $display ?>">
        <input <?php echo $checked ?> id="<?php echo $id ?>" type="checkbox" name="<?php echo $name ?>" class="btn-check" autocomplete="off" value="<?php echo htmlspecialchars($item->{$idProperty}) ?>">
        <label class="btn btn-outline-success" for="<?php echo $id ?>">
            <?php echo $isChecked ? '<i class="fas fa-check"></i> ' : ''  ?> <?php echo $item->{$textProperty} ?> <?php echo isset($countProperty) ? '<span class="badge bg-'.$countClass.'">'.$item->{$countProperty}.'</span>' : '' ?>
        </label>
    </div>


<?php endforeach; ?>





