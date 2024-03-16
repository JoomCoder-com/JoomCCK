<?php
/**
 * Joomcck by joomcoder
 * a component for Joomla! 1.7 - 2.5 CMS (http://www.joomla.org)
 * Author Website: https://www.joomcoder.com/
 * @copyright Copyright (C) 2012 joomcoder (https://www.joomcoder.com). All rights reserved.
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die();

\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tooltip', '*[rel^="tooltip"]');

?>
<h1><?php echo isset($this->item->id) ? \Joomla\CMS\Language\Text::_('CEDITMODER') : \Joomla\CMS\Language\Text::_('CADDMODER');?></h1>


<div class="form-actions">
    <button type="button" class="btn" onclick="Joomla.submitbutton('moderator.apply')">
		<?php echo HTMLFormatHelper::icon('tick-button.png');  ?>
		<?php echo \Joomla\CMS\Language\Text::_('CAPPLY'); ?>
    </button>

    <button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('moderator.save')">
		<?php echo HTMLFormatHelper::icon('disk.png');  ?>
		<?php echo \Joomla\CMS\Language\Text::_('CSAVE'); ?>
    </button>

    <button type="button" class="btn" onclick="Joomla.submitbutton('moderator.cancel')">
		<?php echo HTMLFormatHelper::icon('cross-button.png');  ?>
		<?php echo \Joomla\CMS\Language\Text::_('CCANCEL'); ?>
    </button>
</div>

<form method="post" name="adminForm" id="adminForm" class="form-horizontal">

    <div class="row">
        <div class="col-md-6">
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


            <div class="control-group">
                <legend><?php echo \Joomla\CMS\Language\Text::_('CCATEGORYLIMIT');?></legend>

                <div class="alert alert-info">
		            <?php echo \Joomla\CMS\Language\Text::_('CATLIMITALERT');?>
                </div>

                <div class="control-group">
                    <label class="control-label" for="inputEmail"><?php echo \Joomla\CMS\Language\Text::_('CCATEGORIES'); ?></label>
                    <div class="controls">
			            <?php echo \Joomla\CMS\HTML\HTMLHelper::_('mrelements.catselector', 'jform[category][]', $this->section->id, @$this->item->category, 0); ?>
                    </div>
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

        </div>

        <div class="col-md-6">
            <legend><?php echo \Joomla\CMS\Language\Text::_('Rules');?></legend>
	        <?php $fieldSets = $this->form->getFieldsets('params'); ?>
	        <?php foreach ($fieldSets as $name => $fieldSet) : ?>
                <div class="mt-3">
                    <h6><?php echo \Joomla\CMS\Language\Text::_(ucfirst($name));?></h6>
                    <div class="mb-3">

	                    <?php if (isset($fieldSet->description) && trim($fieldSet->description)) : ?>
                            <p class="text-info"><?php echo $this->escape(\Joomla\CMS\Language\Text::_($fieldSet->description));?></p>
	                    <?php endif; ?>

                        <ul class="list-group">

	                        <?php foreach ($this->form->getFieldset($name) as $field) :   ?>
                                <li class="list-group-item">
                                    <input class="form-check-input me-1" id="<?php echo $field->id ?>" type="checkbox" value="<?php echo $field->value ?>" name="<?php echo $field->name ?>">

                                    <label class="form-check-label" for="<?php echo $field->id ?>"><?php echo $field->description ? 'rel="tooltip" data-bs-original-title="'.htmlentities(\Joomla\CMS\Language\Text::_($field->description), ENT_QUOTES, 'UTF-8').'"' : NULL; ?><?php echo strip_tags($field->label); ?></label>
                                </li>
	                        <?php endforeach; ?>
                        </ul>




                    </div>
                </div>
	        <?php endforeach; ?>

        </div>
    </div>










    <input type="hidden" name="task" value="" />
    <input type="hidden" name="Itemid" value="<?php echo \Joomla\CMS\Factory::getApplication()->input->getInt('Itemid');?>" />
    <input type="hidden" name="return" value="<?php echo \Joomla\CMS\Factory::getApplication()->input->getString('return');?>" />
    <input type="hidden" name="id" value="<?php echo $this->item->id;?>" />
    <?php echo $this->form->getInput('id');?>
    <?php echo $this->form->getInput('section_id');?>
    <?php echo \Joomla\CMS\HTML\HTMLHelper::_( 'form.token' ); ?>
</form>
<script type="text/javascript">
	Joomcck.redrawBS();
</script>