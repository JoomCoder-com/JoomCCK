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

defined('_JEXEC') or die();


extract($displayData);

$user = Factory::getApplication()->getIdentity();
$disabled = !isset($disabled) ? [] : $disabled;


?>


<?php if($user->id):?>
	<div class="user-ctrls">
		<div class="btn-group" role="group" style="display: none;">
			<?php echo HTMLFormatHelper::bookmark($item, $submissionTypes[$item->type_id], $params);?>
			<?php echo HTMLFormatHelper::follow($item, $section);?>
			<?php echo HTMLFormatHelper::repost($item, $section);?>
            <?php if(!in_array('compare', $disabled)): ?>
			<?php echo HTMLFormatHelper::compare($item, $submissionTypes[$item->type_id], $section);?>
            <?php endif; ?>
			<?php if($item->controls):?>
				<button type="button" data-bs-toggle="dropdown" class="dropdown-toggle btn btn-sm bg-light border">
				</button>
				<ul class="dropdown-menu">
					<?php echo list_controls($item->controls);?>
				</ul>
			<?php endif;?>
		</div>
	</div>
<?php endif;?>
