<?php
/**
 * by JoomBoost
 * a component for Joomla! 3.x CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2007-2014 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');
$view = JFactory::getApplication()->input->getCmd('view');
if(empty($displayData->_filters)) return;
?>

<div id="list-filters-box" class="collapse fade">
	<div class="filter-container border rounded mb-3 bg-light">
		<?php foreach ($displayData->_filters AS $i => $filter): ?>
			<?php if($i % 4 == 0): ?>
				<div class="row">
			<?php endif; ?>
			<div class="col-md-3">
				<select class="form-control form-control-sm w-100" name="<?php echo $filter['id']; ?>" id="<?php echo $filter['id']; ?>" class="col-md-12 small" onchange="this.form.submit()">
					<option value=""><?php echo $filter['name']; ?></option>
					<?php echo $filter['element']; ?>
				</select>
			</div>
			<?php if($i % 4 == 3): ?>
				</div>
			<?php endif; ?>
		<?php endforeach; ?>
		<?php if($i % 4 != 3): ?>
			</div>
		<?php endif; ?>
	</div>
</div>
