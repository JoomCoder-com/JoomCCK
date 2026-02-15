<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Toggle Buttons Layout
 *
 * Tailwind CSS replacement for Bootstrap btn-check toggle buttons.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die();

extract($displayData);

$display = isset($display) && $display == 'inline' ? 'inline-flex' : 'flex mb-2';
$idProperty = isset($idProperty) ? $idProperty : 'id';

?>

<div class="flex flex-wrap gap-2">
<?php foreach ($items as $id => &$item): ?>
	<?php
	$isChecked = in_array($item->{$idProperty}, $default) ? true : false;
	$checked = $isChecked ? "checked" : '';
	$id = "$idPrefix-" . $item->{$idProperty};
	$countClass = isset($countProperty) && $countProperty > 0
		? 'bg-green-100 text-green-800'
		: 'bg-gray-100 text-gray-600 border border-gray-200';
	?>

	<label class="<?php echo $display ?> items-center gap-1.5 cursor-pointer select-none" for="<?php echo $id ?>">
		<input <?php echo $checked ?>
			id="<?php echo $id ?>"
			type="checkbox"
			name="<?php echo $name ?>"
			class="peer hidden"
			autocomplete="off"
			value="<?php echo htmlspecialchars($item->{$idProperty}) ?>">
		<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-sm font-medium transition-all
					 border-gray-300 text-gray-700 bg-white hover:bg-gray-50
					 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700">
			<span class="hidden peer-checked:inline">
				<i class="fas fa-check text-xs"></i>
			</span>
			<?php echo $item->{$textProperty} ?>
			<?php if (isset($countProperty)): ?>
				<span class="<?php echo $countClass ?> text-xs px-1.5 py-0.5 rounded-full"><?php echo $item->{$countProperty} ?></span>
			<?php endif; ?>
		</span>
	</label>

<?php endforeach; ?>
</div>
