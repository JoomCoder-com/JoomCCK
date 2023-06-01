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


$display = isset($display) && $display == 'inline' ? 'form-check-inline' : '';
$idProperty = isset($idProperty) ? $idProperty : 'id';

?>


<?php foreach ($items as $id => &$item): ?>

	<?php
	$isChecked = in_array($item->{$idProperty}, $default) ? true : false; // is checked ?
	$checked = $isChecked ? "checked" : ''; // add checked attr
	$id    = "$idPrefix-".$item->{$idProperty}; // build id
    $countClass = isset($countProperty) && $countProperty > 0 ? 'success' : 'light border text-dark';

	?>

    <div class="form-check <?php echo $display ?>">
        <input <?php echo $checked ?> name="<?php echo $name ?>" class="form-check-input" type="checkbox" id="<?php echo $id ?>" value="<?php echo htmlspecialchars($item->{$idProperty}) ?>">
        <label class="form-check-label" for="<?php echo $id ?>"><?php echo $item->{$textProperty} ?> <?php echo isset($countProperty) ? '<span class="badge bg-'.$countClass.'">'.$item->{$countProperty}.'</span>' : '' ?></label>
    </div>

<?php endforeach; ?>




