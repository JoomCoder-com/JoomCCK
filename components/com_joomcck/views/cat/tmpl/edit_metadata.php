<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;
?>
<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('metadesc'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('metadesc'); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('metakey'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('metakey'); ?>
	</div>
</div>
<?php foreach($this->form->getGroup('metadata') as $field): ?>
	<div class="control-group">
		<?php if ($field->hidden): ?>
			<div class="controls">
				<?php echo $field->input; ?>
			</div>
		<?php else: ?>
			<div class="control-label">
				<?php echo $field->label; ?>
			</div>
			<div class="controls">
				<?php echo $field->input; ?>
			</div>
		<?php endif; ?>
	</div>
<?php endforeach; ?>
