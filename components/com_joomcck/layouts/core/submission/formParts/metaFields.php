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


// no need to continue if no fields to show
if (count($current->meta) == 0)
	return;

?>
<?php foreach ($current->meta as $label => $meta_name): ?>
	<div class="control-group odd<?php echo $k = 1 - $k ?>">
		<label id="jform_meta_descr-lbl" class="control-label" title="" for="jform_<?php echo $meta_name; ?>">
			<?php echo \Joomla\CMS\Language\Text::_($label); ?>
		</label>
		<div class="controls">
			<?php echo $current->form->getInput($meta_name); ?>
		</div>
	</div>
<?php endforeach; ?>