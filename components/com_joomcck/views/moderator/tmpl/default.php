<?php
/**
 * Joomcck by JoomBoost
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomBoost.com/
 * @copyright Copyright (C) 2012 JoomBoost (https://www.joomBoost.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();
?>
<h1><?php echo isset($this->item->id) ? JText::_('CEDITMODER') : JText::_('CADDMODER');?></h1>

<form method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal" >

    <div class="control-group">
    	<label class="control-label" for="inputEmail"><?php echo strip_tags($this->form->getLabel('user_id')); ?></label>
    	<div class="controls">
    		<?php echo $this->form->getInput('user_id') ; ?>
    	</div>
    </div>

    <div class="control-group">
    	<label class="control-label" for="inputEmail"><?php echo strip_tags($this->form->getLabel('description')); ?></label>
    	<div class="controls">
    		<?php echo $this->form->getInput('description') ; ?>
    	</div>
    </div>

    <div class="control-group">
    	<label class="control-label" for="inputEmail"><?php echo strip_tags($this->form->getLabel('icon')); ?></label>
    	<div class="controls">
    		<?php echo $this->form->getInput('icon') ; ?>
    	</div>
    </div>

    <div class="control-group">
    	<label class="control-label" for="inputEmail"><?php echo strip_tags($this->form->getLabel('published')); ?></label>
    	<div class="controls">
	    	<?php echo $this->form->getInput('published') ; ?>
    	</div>
    </div>

    <legend><?php echo JText::_('Rules');?></legend>
	<?php $fieldSets = $this->form->getFieldsets('params'); ?>
	<?php foreach ($fieldSets as $name => $fieldSet) : ?>
	    <div class="control-group">
		    <label class="control-label"><?php echo JText::_(ucfirst($name));?></label>
		    <div class="controls">

				<?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
					<p class="tip"><?php echo $this->escape(JText::_($fieldSet->description));?></p>
				<?php endif; ?>

				<?php foreach ($this->form->getFieldset($name) as $field) : ?>
					<div class="rule-el">
						<label class="checkbox">
							<span <?php echo $field->description ? 'rel="tooltip" data-original-title="'.htmlentities(JText::_($field->description), ENT_QUOTES, 'UTF-8').'"' : NULL; ?>><?php echo strip_tags($field->label); ?></span>
							<?php echo $field->input; ?>
						</label>
					</div>
				<?php endforeach; ?>
		    </div>
	    </div>
	<?php endforeach; ?>

	<legend><?php echo JText::_('CCATEGORYLIMIT');?></legend>

	<div class="alert clearfix">
		<?php echo JText::_('CATLIMITALERT');?>
	</div>

	<div class="control-group">
    	<label class="control-label" for="inputEmail"><?php echo JText::_('CCATEGORIES'); ?></label>
    	<div class="controls">
	    	<?php echo JHtml::_('mrelements.catselector', 'jform[category][]', $this->section->id, @$this->item->category, 0); ?>
    	</div>
    </div>

	<div class="control-group">
    	<label class="control-label" for="inputEmail"><?php echo strip_tags($this->form->getLabel('allow')); ?></label>
    	<div class="controls">
	    	<?php echo $this->form->getInput('allow') ; ?>
    	</div>
    </div>

	<div class="control-group">
    	<label class="control-label" for="inputEmail"><?php echo strip_tags($this->form->getLabel('category_limit_mode')); ?></label>
    	<div class="controls">
	    	<?php echo $this->form->getInput('category_limit_mode') ; ?>
    	</div>
    </div>




	<div class="form-actions">
		<button type="button" class="btn" onclick="Joomla.submitbutton('moderator.apply')">
			<?php echo HTMLFormatHelper::icon('tick-button.png');  ?>
    		<?php echo JText::_('CAPPLY'); ?>
    	</button>

    	<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('moderator.save')">
			<?php echo HTMLFormatHelper::icon('disk.png');  ?>
    		<?php echo JText::_('CSAVE'); ?>
    	</button>

    	<button type="button" class="btn" onclick="Joomla.submitbutton('moderator.cancel')">
			<?php echo HTMLFormatHelper::icon('cross-button.png');  ?>
    		<?php echo JText::_('CCANCEL'); ?>
	   	</button>
	</div>

    <input type="hidden" name="task" value="" />
    <input type="hidden" name="Itemid" value="<?php echo JFactory::getApplication()->input->getInt('Itemid');?>" />
    <input type="hidden" name="return" value="<?php echo JFactory::getApplication()->input->getString('return');?>" />
    <input type="hidden" name="id" value="<?php echo $this->item->id;?>" />
    <?php echo $this->form->getInput('id');?>
    <?php echo $this->form->getInput('section_id');?>
    <?php echo JHtml::_( 'form.token' ); ?>
</form>
<script type="text/javascript">
	Joomcck.redrawBS();
</script>