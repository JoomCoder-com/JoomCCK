<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Checkboxes Layout
 *
 * Tailwind CSS + DaisyUI replacement for Bootstrap form-check checkboxes.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

extract($displayData);

$display = isset($display) && $display == 'inline' ? 'inline-flex' : 'flex';
$idProperty = isset($idProperty) ? $idProperty : 'id';

?>

<?php foreach ($items as $id => &$item): ?>
	<?php
	$isChecked = in_array($item->{$idProperty}, $default) ? true : false;
	$checked = $isChecked ? "checked" : '';
	$id = "$idPrefix-" . $item->{$idProperty};
	$countClass = isset($countProperty) && $countProperty > 0
		? 'bg-green-100 text-green-800'
		: 'bg-gray-100 text-gray-600 border border-gray-200';
	?>

	<label class="<?php echo $display ?> items-center gap-2 py-1 cursor-pointer" for="<?php echo $id ?>">
		<input <?php echo $checked ?>
			name="<?php echo $name ?>"
			class="jcck-checkbox"
			type="checkbox"
			id="<?php echo $id ?>"
			value="<?php echo htmlspecialchars($item->{$idProperty}) ?>">
		<span class="text-sm text-gray-700"><?php echo $item->{$textProperty} ?></span>
		<?php if (isset($countProperty)): ?>
			<span class="<?php echo $countClass ?> text-xs px-1.5 py-0.5 rounded-full"><?php echo $item->{$countProperty} ?></span>
		<?php endif; ?>
	</label>

<?php endforeach; ?>
