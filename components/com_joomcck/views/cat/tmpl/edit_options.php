<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die; 
?>

<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('image'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('image'); ?>
	</div>
</div>

<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('created_user_id'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('created_user_id'); ?>
	</div>
</div>

<?php if (intval($this->item->created_time)) : ?>
	<div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('created_time'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('created_time'); ?>
		</div>
	</div>
<?php endif; ?>

<?php if ($this->item->modified_user_id) : ?>
	<div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('modified_user_id'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('modified_user_id'); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<?php echo $this->form->getLabel('modified_time'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('modified_time'); ?>
		</div>
	</div>
<?php endif; ?>

<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('note'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('note'); ?>
	</div>
</div>

