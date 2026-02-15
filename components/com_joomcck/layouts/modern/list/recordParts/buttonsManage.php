<?php
/**
 * Joomcck by joomcoder
 * Modern UI - Record Management Buttons Layout
 *
 * Tailwind CSS flex + DaisyUI dropdown replacement for Bootstrap btn-group.
 *
 * @copyright Copyright (C) 2020 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die();

extract($displayData);

$user = Factory::getApplication()->getIdentity();
$disabled = !isset($disabled) ? [] : $disabled;

?>

<?php if($user->id):?>
	<div class="user-ctrls">
		<div class="flex items-center gap-1" role="group" style="display: none;">
			<?php echo HTMLFormatHelper::bookmark($item, $submissionTypes[$item->type_id], $params);?>
			<?php echo HTMLFormatHelper::follow($item, $section);?>
			<?php echo HTMLFormatHelper::repost($item, $section);?>
			<?php if(!in_array('compare', $disabled)): ?>
				<?php echo HTMLFormatHelper::compare($item, $submissionTypes[$item->type_id], $section);?>
			<?php endif; ?>
			<?php if($item->controls):?>
				<div class="relative jcck-dropdown-container">
					<button type="button"
							class="bg-white border border-gray-300 text-gray-500 px-2 py-1 rounded text-sm hover:bg-gray-50 transition-colors"
							onclick="this.parentElement.querySelector('.jcck-dropdown-menu').classList.toggle('hidden')">
						<i class="fas fa-ellipsis-v"></i>
					</button>
					<ul class="jcck-dropdown-menu hidden absolute right-0 z-10 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 py-1 min-w-48">
						<?php echo list_controls($item->controls);?>
					</ul>
				</div>
			<?php endif;?>
		</div>
	</div>
<?php endif;?>

<script>
(function() {
	// Close dropdown when clicking outside
	document.addEventListener('click', function(e) {
		if (!e.target.closest('.jcck-dropdown-container')) {
			document.querySelectorAll('.jcck-dropdown-menu').forEach(function(m) { m.classList.add('hidden'); });
		}
	});
})();
</script>
