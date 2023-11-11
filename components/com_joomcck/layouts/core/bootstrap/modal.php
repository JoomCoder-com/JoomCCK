<?php
/**
 * by joomcoder
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2007-2014 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die();

/*
 * This layout responsible for displaying bootstrap modal.
 */

extract($displayData);

// force bootstrap modal assets loading
HTMLHelper::_('bootstrap.modal');

// button to be added in future. so can be used inside layout or not

?>



<div id="<?php echo $id ?>" class="modal fade" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="<?php echo $id ?>" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title fs-5" id="mtitle_$this->id"><?php echo $title ?></h3>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>

			<div class="modal-body" style="overflow-Y: scroll;">
				<?php echo $body ?>
			</div>

			<div class="modal-footer">
				<button class="btn btn-secondary" data-bs-dismiss="modal"><?php echo \Joomla\CMS\Language\Text::_('CCLOSE') ?></button>
			</div>
		</div>

	</div>

</div>
